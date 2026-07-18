<?php

namespace App\Services;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonobankService
{
    public static function updateOrderFromMonobankData(Order $order, array $data)
    {
        // Обрабатываем случай, когда modifiedDate может быть null
        $newModifiedDate = isset($data['modifiedDate']) && $data['modifiedDate'] !== null
            ? Carbon::parse($data['modifiedDate'])
            : null;

        $currentPaymentData = $order->payment_data ?? [];
        $lastModified = isset($currentPaymentData['monobank_modified_date'])
            ? Carbon::parse($currentPaymentData['monobank_modified_date'])
            : null;

        // Если modifiedDate null, обновляем только если нет предыдущей даты или если есть признаки ошибки
        $shouldUpdate = false;
        if ($newModifiedDate === null) {
            // Если modifiedDate null, но есть failureReason или errCode - это ошибка, нужно обновить
            if (isset($data['failureReason']) || isset($data['errCode'])) {
                $shouldUpdate = true;
            } elseif ($lastModified === null) {
                // Если нет предыдущей даты, обновляем
                $shouldUpdate = true;
            }
        } elseif (! $lastModified || $newModifiedDate->greaterThanOrEqualTo($lastModified)) {
            $shouldUpdate = true;
        }

        if ($shouldUpdate) {
            // Определяем статус заказа
            $statusMap = [
                'created' => 'pending payment',
                'processing' => 'pending payment',
                'hold' => 'hold',
                'success' => 'paid',
                'failure' => 'failed',
                'reversed' => 'refunded',
                'expired' => 'expired',
            ];

            // Если status null, но есть failureReason или errCode - это ошибка
            $monobankStatus = $data['status'];
            if ($monobankStatus === null && (isset($data['failureReason']) || isset($data['errCode']))) {
                $monobankStatus = 'failure';
            }

            if ($order->status != 'processing' && $order->status != 'completed') {
                $order->status = $statusMap[$monobankStatus] ?? $monobankStatus ?? $order->status;
            }

            // Обновляем метаданные
            if ($newModifiedDate !== null) {
                $currentPaymentData['monobank_modified_date'] = $data['modifiedDate'];
            }
            $currentPaymentData['monobank_status'] = $monobankStatus;
            if (isset($data['failureReason'])) {
                $currentPaymentData['monobank_failure_reason'] = $data['failureReason'];
            }
            if (isset($data['errCode'])) {
                $currentPaymentData['monobank_err_code'] = $data['errCode'];
            }
            $order->payment_data = $currentPaymentData;
            $order->save();

//            Log::info('Order status updated from Monobank', [
//                'order_id' => $order->id,
//                'invoiceId' => $order->invoice_id ?? null,
//                'status' => $order->status,
//                'monobank_status' => $monobankStatus,
//                'failure_reason' => $data['failureReason'] ?? null,
//                'err_code' => $data['errCode'] ?? null,
//            ]);

            return true;
        } else {
//            Log::info('Monobank status update skipped (older modifiedDate)', [
//                'order_id' => $order->id,
//                'new_date' => $data['modifiedDate'] ?? null,
//                'old_date' => $currentPaymentData['monobank_modified_date'] ?? null,
//            ]);

            return false;
        }
    }

    public static function checkOrderStatus($orderReference)
    {
        $token = config('services.monobank.token');
        if (! $token) {
            Log::error('Monobank token not found in config');

            return null;
        }
        $order = Order::find($orderReference);
        if (! $order) {
            return null;
        }
        $invoiceId = $order->invoice_id;
        if (! $invoiceId) {
            return null;
        }
        $response = Http::withHeaders([
            'X-Token' => $token,
        ])->withoutVerifying()
            ->get('https://api.monobank.ua/api/merchant/invoice/status', [
                'invoiceId' => $invoiceId,
            ]);

        if (! $response->successful()) {
            Log::error('Monobank status check failed', [
                'order_id' => $order->id,
                'invoiceId' => $invoiceId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return $response->json();
        }

        $data = $response->json();

        self::updateOrderFromMonobankData($order, $data);

        return $data;
    }

    /**
     * Получить курс валютной пары из Monobank (rateBuy или rateCross).
     *
     * @param  int  $currencyCodeA  Код валюты «из» (ISO 4217, например 840 = USD)
     * @param  int  $currencyCodeB  Код валюты «в» (ISO 4217, например 980 = UAH)
     * @return float|null rateBuy или rateCross для пары, null при ошибке/отсутствии
     */
    public static function getCurrency(int $currencyCodeA = 840, int $currencyCodeB = 980): ?float
    {
        $response = Http::withoutVerifying()
            ->get('https://api.monobank.ua/bank/currency');

        if (! $response->successful()) {
            Log::warning('Monobank currency API failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        }

        $list = $response->json();
        if (! is_array($list)) {
            return null;
        }

        foreach ($list as $item) {
            if ((int) ($item['currencyCodeA'] ?? 0) !== $currencyCodeA
                || (int) ($item['currencyCodeB'] ?? 0) !== $currencyCodeB
            ) {
                continue;
            }

            // Для пар с курсом покупки/продажи берём rateBuy, иначе rateCross
            if (isset($item['rateBuy'])) {
                return (float) $item['rateBuy'];
            }
            if (isset($item['rateCross'])) {
                return (float) $item['rateCross'];
            }

            return null;
        }

        Log::warning('Monobank: currency pair not found', [
            'currencyCodeA' => $currencyCodeA,
            'currencyCodeB' => $currencyCodeB,
        ]);

        return null;
    }
}
