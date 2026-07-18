<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\PlatonService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class PlatonController extends Controller
{
    public function createPaymentLinkForTopup(
        Order $order,
        string $email,
        string $currency,
        ?string $locale = null
    ): array
    {
        $description = 'Report balance top-up: '.$order->report_to_add.' reports';

        return $this->createPaymentLink($order, $email, $currency, $description, $locale);
    }

    public function createPaymentLinkForOrder(
        Order $order,
        string $reportType,
        string $email,
        string $vin,
        string $currency,
        ?string $locale = null
    ): array
    {
        $description = 'Report for VIN: '.$vin.', Report type: '.$reportType;

        return $this->createPaymentLink($order, $email, $currency, $description, $locale);
    }

    private function createPaymentLink(
        Order $order,
        string $email,
        string $currency,
        string $description,
        ?string $locale = null
    ): array {
        $config = config('services.platon');
        $merchantKey = (string) ($config['merchant_id'] ?? '');
        $merchantPassword = (string) ($config['password'] ?? '');
        $apiUrl = (string) ($config['api_url'] ?? '');
        $configuredResultUrl = (string) ($config['result_url'] ?? '');
        $targetCurrency = strtoupper((string) ($config['currency'] ?? 'UAH'));
        $language = $this->normalizeLanguage((string) ($config['language'] ?? 'EN'));

        if (! (bool) ($config['enabled'] ?? false)) {
            return [
                'success' => false,
                'error' => 'Platon is disabled. Set PLATON_ENABLED=true in .env',
            ];
        }

        if ($merchantKey === '' || $merchantPassword === '' || $apiUrl === '') {
            return [
                'success' => false,
                'error' => 'Missing required Platon config values (merchant_id, password, api_url)',
            ];
        }

        if (! $order->order_key) {
            $order->generateOrderKey();
            $order->refresh();
        }

        $currentLocale = $locale ?? app()->getLocale();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        if (! in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getDefaultLocale();
        }

        $thankYouUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/thank-you');
        $baseUrl = $thankYouUrl;
        if ($configuredResultUrl !== '') {
            $baseUrl = rtrim($configuredResultUrl, '?&');
        }
        $resultUrl = $baseUrl.(str_contains($baseUrl, '?') ? '&' : '?').'id='.$order->id.'&key='.$order->order_key;

        $convertedAmount = SettingService::convert((float) $order->total_price, $currency, strtolower($targetCurrency));
        $amount = number_format($convertedAmount, 2, '.', '');

        $dataPayload = [
            'amount' => $amount,
            'currency' => $targetCurrency,
            'description' => $description,
        ];

        $data = base64_encode(json_encode($dataPayload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        $payload = [
            'key' => $merchantKey,
            'payment' => 'CC',
            'data' => $data,
            'url' => $resultUrl,
            'order' => (string) $order->id,
            'email' => $email,
            'lang' => $language,
        ];
        $payload['sign'] = $this->buildSign($payload, $merchantPassword);

        return [
            'success' => true,
            'payment_form' => [
                'action' => $apiUrl,
                'method' => 'POST',
                'fields' => $payload,
            ],
            'payload' => $payload,
            'data_payload' => $dataPayload,
            'error' => null,
        ];
    }

    public function platonCallback(Request $request)
    {
        $data = $request->all();
        $success = PlatonService::processPlatonCallback($data);

        if (! $success) {
            return response('Invalid sign', 400);
        }

        return response('OK');
    }

    private function buildSign(array $payload, string $merchantPassword): string
    {
        return md5(
            strtoupper(
                strrev((string) ($payload['key'] ?? ''))
                .strrev((string) ($payload['payment'] ?? ''))
                .strrev((string) ($payload['data'] ?? ''))
                .strrev((string) ($payload['url'] ?? ''))
                .strrev($merchantPassword)
            )
        );
    }

    private function normalizeLanguage(string $language): string
    {
        $language = strtoupper(substr($language, 0, 2));

        return in_array($language, ['EN', 'UK'], true) ? $language : 'EN';
    }
}
