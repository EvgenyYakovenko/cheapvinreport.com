<?php

namespace App\Services;

use App\Models\Order;
use App\Services\SettingService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PlatonService
{
    /**
     * Маппинг статусов Platon на статусы заказа.
     * По документации: https://platon.atlassian.net/wiki/spaces/docs/pages/1315733632/Client+-+Server#Callback
     */
    private const STATUS_MAP = [
        'SALE' => 'paid',           // Успешная оплата (карта, Apple Pay, Google Pay, оплата частями)
        'REFUND' => 'refund',       // Возврат
        'REVERSAL' => 'refund',     // Отмена/возврат
        'REVERSED' => 'refund',     // Отменённая транзакция
        'SETTLED' => 'paid',        // Успешная оплата по API проверки статуса
        'DECLINED' => 'failed',     // Отклонено
        'REJECTED' => 'failed',     // Отклонено
        'FAILED' => 'failed',
        'ERROR' => 'failed',
    ];

    private const STATUS_CHECK_THROTTLE_SECONDS = 60;

    public static function syncPaymentStatus(Order $order): bool
    {
        if ($order->payment_method !== 'platon' || $order->status !== 'pending payment') {
            return false;
        }

        $paymentData = is_array($order->payment_data) ? $order->payment_data : [];
        $lastCheckedAt = $paymentData['platon_status_checked_at'] ?? null;
        if ($lastCheckedAt && Carbon::parse($lastCheckedAt)->diffInSeconds(now()) < self::STATUS_CHECK_THROTTLE_SECONDS) {
            return false;
        }

        $config = config('services.platon');
        $merchantKey = (string) ($config['merchant_id'] ?? '');
        $merchantPassword = (string) ($config['password'] ?? '');
        $statusUrl = (string) ($config['status_url'] ?? '');

        if ($merchantKey === '' || $merchantPassword === '' || $statusUrl === '') {
            Log::error('Platon status check: Missing required config values', [
                'order_id' => $order->id,
                'has_merchant_key' => $merchantKey !== '',
                'has_password' => $merchantPassword !== '',
                'has_status_url' => $statusUrl !== '',
            ]);

            return false;
        }

        $orderId = (string) $order->id;
        $payload = [
            'action' => 'GET_TRANS_STATUS_BY_ORDER',
            'client_key' => $merchantKey,
            'order_id' => $orderId,
        ];
        $payload['hash'] = md5(strtoupper($merchantPassword.$orderId));

        try {
            $response = Http::asForm()
                ->timeout(10)
                ->post($statusUrl, $payload);
        } catch (\Throwable $exception) {
            self::storeStatusCheckResult($order, [
                'result' => 'HTTP_EXCEPTION',
                'error_message' => $exception->getMessage(),
            ]);

            Log::warning('Platon status check: HTTP exception', [
                'order_id' => $order->id,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }

        $responseData = $response->json();
        if (! is_array($responseData)) {
            $responseData = [
                'result' => 'INVALID_JSON',
                'http_status' => $response->status(),
                'body' => mb_substr($response->body(), 0, 1000),
            ];
        }

        self::storeStatusCheckResult($order, $responseData);

        if (! $response->successful() || strtoupper((string) ($responseData['result'] ?? '')) !== 'SUCCESS') {
            Log::info('Platon status check: Non-success response', [
                'order_id' => $order->id,
                'http_status' => $response->status(),
                'result' => $responseData['result'] ?? null,
                'error_message' => $responseData['error_message'] ?? null,
            ]);

            return false;
        }

        $statusData = self::extractStatusData($responseData);
        if (! $statusData) {
            return false;
        }

        $status = (string) ($statusData['status'] ?? '');
        if ($status === '') {
            return false;
        }

        if (isset($statusData['amount']) && ! self::verifyCallbackAmount($statusData, $order)) {
            Log::error('Platon status check: Amount mismatch', [
                'order_id' => $order->id,
                'status_amount' => $statusData['amount'] ?? null,
            ]);

            return false;
        }

        $freshOrder = $order->fresh();
        if (! $freshOrder) {
            return false;
        }

        return self::applyStatus($freshOrder, $status, ['platon_status_check' => $responseData]);
    }

    public static function processPlatonCallback(array $data): bool
    {
        $orderId = $data['order'] ?? null;
        $status = $data['status'] ?? null;
        $sign = $data['sign'] ?? null;

        if (! $orderId || ! $status || ! $sign) {
            Log::warning('Platon callback: Missing required fields', [
                'has_order' => (bool) $orderId,
                'has_status' => (bool) $status,
                'has_sign' => (bool) $sign,
            ]);
            return false;
        }

        $order = Order::find($orderId);
        if (! $order) {
            Log::error('Platon callback: Order not found', ['order_id' => $orderId]);
            return false;
        }

        if ($order->payment_method !== 'platon') {
            Log::warning('Platon callback: Order is not Platon payment', ['order_id' => $orderId]);
            return false;
        }

        if (! self::verifyCallbackSign($data)) {
            Log::error('Platon callback: Invalid signature', ['order_id' => $orderId]);
            return false;
        }

        // Сверяем сумму только для успешной оплаты (SALE)
        if ($status === 'SALE' && ! self::verifyCallbackAmount($data, $order)) {
            Log::error('Platon callback: Amount mismatch', [
                'order_id' => $orderId,
                'callback_amount' => $data['amount'] ?? null,
                'callback_currency' => $data['currency'] ?? null,
            ]);
            return false;
        }

        return self::applyStatus($order, $status, ['platon_callback' => $data]);
    }

    /**
     * Проверка подписи callback по документации Platon.
     * Для карты/Apple Pay/Google Pay: md5(strtoupper(strrev($email).$pass.$order.strrev(substr($card,0,6).substr($card,-4))))
     * Для "Оплата частинами": md5(strtoupper($pass.$order))
     */
    private static function verifyCallbackSign(array $data): bool
    {
        $pass = (string) (config('services.platon.password') ?? '');
        if ($pass === '') {
            Log::error('Platon callback: PLATON_PASSWORD not configured');
            return false;
        }

        $order = (string) ($data['order'] ?? '');
        $email = (string) ($data['email'] ?? '');
        $card = (string) ($data['card'] ?? '');
        $number = $data['number'] ?? null; // Для "оплата частинами" — маска телефона
        $receivedSign = (string) ($data['sign'] ?? '');

        // "Оплата частинами" (Monobank, ПриватБанк, A-Bank): status=SALE и есть number
        if (($data['status'] ?? '') === 'SALE' && $number !== null && $number !== '') {
            $localSign = md5(strtoupper($pass.$order));
        } else {
            // Карта, Apple Pay, Google Pay
            $cardPart = '';
            if ($card !== '') {
                $cardPart = strrev(substr($card, 0, 6).substr($card, -4));
            }
            $localSign = md5(strtoupper(strrev($email).$pass.$order.$cardPart));
        }

        return hash_equals($localSign, $receivedSign);
    }

    /**
     * Проверка суммы в callback — сверяем с ожидаемой суммой заказа.
     */
    private static function verifyCallbackAmount(array $data, Order $order): bool
    {
        $callbackAmount = (float) ($data['amount'] ?? 0);
        $callbackCurrency = strtolower((string) ($data['currency'] ?? config('services.platon.currency', 'uah')));

        $expectedAmount = SettingService::convert(
            (float) $order->total_price,
            strtolower((string) $order->currency),
            $callbackCurrency
        );

        return abs($callbackAmount - $expectedAmount) < 0.01;
    }

    private static function applyStatus(Order $order, string $status, array $paymentData): bool
    {
        $newStatus = self::STATUS_MAP[$status] ?? null;
        if (! $newStatus) {
            $order->payment_data = array_merge(
                is_array($order->payment_data) ? $order->payment_data : [],
                $paymentData
            );
            $order->save();

            Log::info('Platon status: Unknown status, skipping', [
                'order_id' => $order->id,
                'platon_status' => $status,
            ]);

            return true;
        }

        if ($newStatus === 'paid' && in_array($order->status, ['paid', 'processing', 'completed'], true)) {
            return true;
        }

        if ($newStatus === 'refund') {
            $order->payment_data = array_merge(
                is_array($order->payment_data) ? $order->payment_data : [],
                $paymentData
            );
            $order->status = 'refund';
            $order->save();

            return true;
        }

        if ($newStatus === 'failed' && ! in_array($order->status, ['paid', 'processing', 'completed', 'refund'], true)) {
            $order->payment_data = array_merge(
                is_array($order->payment_data) ? $order->payment_data : [],
                $paymentData
            );
            $order->status = 'failed';
            $order->save();

            return true;
        }

        if ($newStatus === 'paid' && $order->status === 'pending payment') {
            $order->payment_data = array_merge(
                is_array($order->payment_data) ? $order->payment_data : [],
                $paymentData
            );
            $order->status = 'paid';
            $order->save();

            Log::info('Platon status: Order status updated to paid', [
                'order_id' => $order->id,
                'platon_status' => $status,
            ]);
        }

        return true;
    }

    private static function extractStatusData(array $responseData): ?array
    {
        if (isset($responseData['status'])) {
            return $responseData;
        }

        $orders = $responseData['orders'] ?? null;
        if (is_array($orders) && is_array($orders[0] ?? null)) {
            return $orders[0];
        }

        $transactions = $responseData['transactions'] ?? $responseData['transaction'] ?? null;
        if (is_array($transactions) && is_array($transactions[0] ?? null)) {
            return $transactions[0];
        }

        if (is_array($transactions) && isset($transactions['status'])) {
            return $transactions;
        }

        return null;
    }

    private static function storeStatusCheckResult(Order $order, array $responseData): void
    {
        $order->payment_data = array_merge(
            is_array($order->payment_data) ? $order->payment_data : [],
            [
                'platon_status_checked_at' => now()->toIso8601String(),
                'platon_status_response' => $responseData,
            ]
        );
        $order->save();
    }
}
