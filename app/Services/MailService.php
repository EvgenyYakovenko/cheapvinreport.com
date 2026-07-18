<?php

namespace App\Services;

use App\Mail\ReportMail;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class MailService
{
    public static function sendEmailReport($order_id)
    {
        $order = Order::with('user')->find($order_id);
        if (! $order) {
            Log::error('MailService: Order not found', ['order_id' => $order_id]);

            return false;
        }

        // Проверяем, что есть report_key для отправки ссылки
        if (! $order->report_key) {
            Log::warning('MailService: Report key not found for order', ['order_id' => $order_id]);

            return false;
        }

        $email = $order->email;
        if (! $email) {
            Log::error('MailService: Email not found', ['order_id' => $order_id, 'email' => $email]);

            return false;
        }

        try {
            $orderLocale = $order->locale ?? LaravelLocalization::getDefaultLocale();
            $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
            if (!in_array($orderLocale, $supportedLocales, true)) {
                $orderLocale = LaravelLocalization::getDefaultLocale();
            }

            Mail::to($email)
                ->locale($orderLocale)
                ->send(new ReportMail($order));
//            Log::info('MailService: Report email sent successfully', [
//                'order_id' => $order_id,
//                'email' => $email,
//                'report_key' => $order->report_key,
//            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('MailService: Failed to send report email', [
                'order_id' => $order_id,
                'email' => $email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
