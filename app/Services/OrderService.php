<?php

namespace App\Services;

use App\Http\Controllers\SettingController;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public static function processStripeOrder($data, $paymentMethod = 'stripe')
    {
        if ($paymentMethod === 'stripe') {
            $sessionId = $data['session_id'] ?? null;
            $paymentStatus = $data['payment_status'] ?? null;

            $newStatus = ($paymentStatus === 'paid') ? 'paid' : (($paymentStatus === 'unpaid') ? 'failed' : null);
            
            if (!$sessionId) {
                Log::error('OrderService: Missing session_id', ['data' => $data]);
                return response()->json([
                    'success' => false,
                    'error' => 'Missing session_id',
                ], 400);
            }
            
            // Ищем заказ по session_id в payment_data
            $order = Order::where('payment_method', 'stripe')
                ->whereJsonContains('payment_data->session_id', $sessionId)
                ->first();
        } else {
            Log::error('OrderService: Invalid payment method', ['method' => $paymentMethod]);

            return response()->json([
                'success' => false,
                'error' => 'Invalid payment method',
            ], 400);
        }

        if (! $order) {
            // Для Stripe создаем заказ если его нет
            if ($paymentMethod === 'stripe') {
                $email = $data['email'] ?? null;
                $vin = $data['vin'] ?? null;
                $reportType = $data['report_type'] ?? null;

                if (! $email || ! $vin || ! $reportType) {
                    Log::error('process Order: Missing required data for Stripe order creation', ['data' => $data]);

                    return response()->json([
                        'success' => false,
                        'error' => 'Missing required data',
                    ], 400);
                }

                $vin = strtoupper(str_replace([' ', '-', '.'], '', $vin));
                $user = User::where('email', $email)->first();

                // Получаем валюту из данных или используем дефолтную
                $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
                $currency = $data['currency'] ?? $defaultCurrency;

                // Получаем цены из настроек (теперь это массивы по валютам)
                $carfaxPrices = SettingController::getSetting('carfax_price') ?? [];
                $autocheckPrices = SettingController::getSetting('autocheck_price') ?? [];
                $auctionsPrices = SettingController::getSetting('auctions_price') ?? [];
                $stickerPrices = SettingController::getSetting('sticker_price') ?? [];

                // Извлекаем цену для нужного типа отчета и валюты
                $price = null;
                if ($reportType === 'carfax' && is_array($carfaxPrices)) {
                    $price = $carfaxPrices[$currency] ?? $carfaxPrices[$defaultCurrency] ?? null;
                } elseif ($reportType === 'autocheck' && is_array($autocheckPrices)) {
                    $price = $autocheckPrices[$currency] ?? $autocheckPrices[$defaultCurrency] ?? null;
                } elseif ($reportType === 'auctions' && is_array($auctionsPrices)) {
                    $price = $auctionsPrices[$currency] ?? $auctionsPrices[$defaultCurrency] ?? null;
                } elseif ($reportType === 'sticker' && is_array($stickerPrices)) {
                    $price = $stickerPrices[$currency] ?? $stickerPrices[$defaultCurrency] ?? null;
                }

                if (! $price) {
                    return response()->json([
                        'success' => false,
                        'error' => 'Invalid report type or price not found',
                    ], 400);
                }

                $orderData = [
                    'email' => $email,
                    'vin' => $vin,
                    'report_type' => $reportType,
                    'status' => 'pending payment',
                    'total_price' => $price,
                    'currency' => $currency,
                    'locale' => $data['locale'] ?? app()->getLocale(),
                    'payment_method' => 'stripe',
                    'order_purpose' => 'report',
                    'payment_data' => json_encode(array_merge($data, ['session_id' => $sessionId])), // Сохраняем session_id в payment_data
                ];

                $order = $user ? $user->orders()->create($orderData) : Order::create($orderData);
                Log::info('process Order: Stripe order created', ['order_id' => $order->id, 'session_id' => $sessionId]);
            } else {
                Log::warning('process Order: Order not found', ['session_id' => $sessionId, 'method' => $paymentMethod]);

                return response()->json([
                    'success' => false,
                    'error' => 'Order not found',
                ], 404);
            }
        }

        // Обновляем данные платежа
        $order->payment_data = json_encode($data);

        // Обновляем валюту, если она есть в данных
        if (isset($data['currency']) && ! empty($data['currency'])) {
            $order->currency = $data['currency'];
        }

        $order->save();

        // Обновляем статус если нужно
        if ($newStatus && $order->status !== $newStatus) {
            $protectedStatuses = ['paid', 'processing', 'completed', 'refund'];
            if ($newStatus === 'paid' && ! in_array($order->status, $protectedStatuses)) {
                $order->status = 'paid';
            } elseif ($newStatus === 'failed' && $order->status !== 'failed' && $order->status !== 'expired') {
                $order->status = 'failed';
            } elseif ($newStatus === 'refund' && $order->status !== 'refund') {
                $order->status = 'refund';
            } elseif ($newStatus === 'expired' && $order->status !== 'expired') {
                $order->status = 'expired';
            } elseif ($newStatus === 'fraud' && $order->status !== 'fraud') {
                $order->status = 'fraud';
            }
            $order->save();
        }

        return response()->json([
            'success' => true,
            'status' => $order->status,
            'order_id' => $order->id,
        ], 200);
    }
}
