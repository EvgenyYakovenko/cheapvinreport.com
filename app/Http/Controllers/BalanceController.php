<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class BalanceController extends Controller
{
    public function topupReportBalance(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);
        $amount = $request->input('amount');
        $user = auth()->user();
        $email = $user->email;
        if (! $user) {
            Log::error('Tried to topup report balance unauthorized');

            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }
        if (! $amount) {
            Log::error('Tried to topup report balance without an amount');

            return response()->json([
                'success' => false,
                'error' => 'Report to add is required',
            ], 400);
        }
        $setting = SettingController::getSetting('topup_report_balance_price');
        if (! $setting) {
            Log::error('Tried to topup report balance without a setting');

            return response()->json([
                'success' => false,
                'error' => 'Setting not found',
            ], 500);
        }

        $packagePrices = null;
        if (is_array($setting)) {
            $packagePrices = $setting;
        } elseif (is_string($setting)) {
            $decoded = json_decode($setting, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $packagePrices = $decoded;
            }
        }

        if (is_array($packagePrices) && ! empty($packagePrices)) {
            $keys = array_keys($packagePrices);
            $hasNumericPackages = array_reduce($keys, function ($carry, $key) {
                return $carry && ctype_digit((string) $key);
            }, true);

            if ($hasNumericPackages &&
                ! array_key_exists((string) $amount, $packagePrices) &&
                ! array_key_exists((int) $amount, $packagePrices)
            ) {
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid report package selected',
                ], 422);
            }
        }
        // Определяем валюту с учетом настройки multi_currency_enabled
        $currency = SettingController::getCurrency();

        $price = Setting::getPriceFromAmountOfReports($amount, $currency);
        if (! $price) {
            Log::error('Tried to topup report balance without a price');

            return response()->json([
                'success' => false,
                'error' => 'Price not found',
            ], 500);
        }

        // Определяем локаль
        $currentLocale = app()->getLocale();

        // Определяем метод оплаты (по умолчанию platon, можно добавить выбор)
        $paymentMethod = $request->input('payment_method', 'platon');
        // Сейчас для topup поддерживаем только Platon, но legacy-ветки оставляем в коде.
        if ($paymentMethod !== 'platon') {
            return response()->json([
                'success' => false,
                'error' => 'Invalid payment method',
            ], 400);
        }

        // Создаем заказ (Laravel сам сгенерирует ID)
        $orderData = [
            'email' => $email,
            'vin' => null,
            'report_type' => null,
            'currency' => $currency,
            'locale' => $currentLocale,
            'status' => 'pending payment',
            'total_price' => $price,
            'payment_method' => $paymentMethod,
            'order_purpose' => 'topup_report_balance',
            'report_to_add' => $amount,
            'invoice_id' => null,
        ];

        $order = $user->orders()->create($orderData);

//        Log::info('Topup report balance: Order created.', [
//            'order_id' => $order->id,
//            'amount' => $amount,
//            'price' => $price,
//            'currency' => $currency,
//            'payment_method' => $paymentMethod,
//        ]);

        // Создаем ссылку на оплату в зависимости от метода оплаты
        $paymentController = new PaymentController;
        $platonController = new PlatonController;
        $paymentUrl = null;

        switch ($paymentMethod) {
            case 'platon':
                $platonResponse = $platonController->createPaymentLinkForTopup(
                    $order,
                    $email,
                    $currency,
                    $currentLocale
                );

                if (! ($platonResponse['success'] ?? false) || empty($platonResponse['payment_form'])) {
                    Log::error('Failed to create Platon payment URL for topup', [
                        'order_id' => $order->id,
                        'error' => $platonResponse['error'] ?? 'Unknown error',
                    ]);

                    return response()->json([
                        'success' => false,
                        'error' => $platonResponse['error'] ?? 'Failed to create Platon payment URL',
                    ], 500);
                }

                $paymentData = $order->payment_data ?? [];
                $paymentData['payment_form'] = $platonResponse['payment_form'];
                $paymentData['payment_method'] = 'platon';
                $paymentData['platon_request_payload'] = $platonResponse['payload'] ?? null;
                $paymentData['platon_data_payload'] = $platonResponse['data_payload'] ?? null;
                $order->payment_data = $paymentData;
                $order->save();
                $paymentForm = $platonResponse['payment_form'];
                break;

            case 'monobank':
                $monobankResponse = $paymentController->createInvoiceLinkMonobankForTopup(
                    $amount,
                    $email,
                    (string)$order->id,
                    $currency,
                    $currentLocale
                );
                if ($monobankResponse['success'] && $monobankResponse['url']) {
                    $order->invoice_id = $monobankResponse['invoice_id'] ?? null;
                    // Сохраняем invoice_id и payment_url в payment_data
                    $paymentData = $order->payment_data ?? [];
                    $paymentData['invoice_id'] = $monobankResponse['invoice_id'] ?? null;
                    $paymentData['payment_url'] = $monobankResponse['url'];
                    $paymentData['payment_method'] = 'monobank';
                    $order->payment_data = $paymentData;
                    $order->save();
                    $paymentUrl = $monobankResponse['url'];
                } else {
                    Log::error('Failed to create Monobank invoice for topup', [
                        'order_id' => $order->id,
                        'error' => $monobankResponse['error'] ?? 'Unknown error',
                    ]);
                    return response()->json([
                        'success' => false,
                        'error' => 'Failed to create payment order',
                    ], 500);
                }
                break;

            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid payment method',
                ], 400);
        }

        // Явно сохраняем сессию перед ответом
        $request->session()->save();

        if (isset($paymentForm)) {
            return response()->json([
                'success' => true,
                'payment_form' => $paymentForm,
            ]);
        }

        return response()->json([
            'success' => true,
            'url' => $paymentUrl,
        ]);
    }
}
