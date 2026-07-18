<?php

namespace App\Observers;

use App\Http\Controllers\ReportController;
use App\Models\Order;
use App\Services\MailService;
use App\Services\UserService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function updated(Order $order): void
    {
        // Переход в paid: сразу переводим в processing и запускаем обработку (без второго вызова updated)
        if ($order->isDirty('status') && $order->status === 'paid') {
            $order->status = 'processing';
            $order->saveQuietly();
//            Log::info('OrderObserver: Order ID '.$order->id.' status changed to processing.');
            $this->processOrder($order);
            return;
        }

        // Прямой переход в processing (например, из админки)
        if ($order->isDirty('status') && $order->status === 'processing') {
            $this->processOrder($order);
        }
    }

    /**
     * Обработка заказа в статусе processing: генерация отчёта, пополнение баланса и т.д.
     * Вызывается при переходе в processing (из paid или при ручной смене статуса).
     */
    private function processOrder(Order $order): void
    {
        switch ($order->order_purpose) {
            case 'report':
                $vinReport = ReportController::getVinReport($order->vin, $order->report_type);

                if ($vinReport && $vinReport['success']) {
                    $order->report_key = $vinReport['saved_report_key'];
                    $order->status = 'completed';
                    $order->saveQuietly();

                    MailService::sendEmailReport($order->id);
                } else {
                    $order->status = 'paid';
                    $order->saveQuietly();
                }
                break;

            case 'topup_report_balance':
                if ($order->user_id) {
                    try {
                        DB::transaction(function () use ($order) {
                            UserService::topupBalance($order->user_id, 'topup_report_balance', $order->report_to_add);
                            $order->status = 'completed';
                            $order->saveQuietly();
                        });
                    } catch (\Exception $e) {
                        Log::error('OrderObserver: Failed to topup report balance.', ['error' => $e->getMessage(), 'order' => $order->id]);
                        $order->status = 'failed';
                        $order->saveQuietly();
                    }
                } else {
//                    Log::info('Try to topup report balance, there is no user_id in the order.', ['order' => $order->id]);
                }
                break;

            case 'topup_balance':
                if ($order->user_id) {
                    try {
                        DB::transaction(function () use ($order) {
                            UserService::topupBalance($order->user_id, 'topup_balance', $order->total_price);
                            $order->status = 'completed';
                            $order->saveQuietly();
                        });
                    } catch (\Exception $e) {
                        Log::error('OrderObserver: Failed to topup balance.', ['error' => $e->getMessage(), 'order' => $order->id]);
                        $order->status = 'failed';
                        $order->saveQuietly();
                    }
                } else {
                    Log::info('Try to topup balance, there is no user_id in the order.', ['order' => $order->id]);
                }
                break;
        }
    }
}
