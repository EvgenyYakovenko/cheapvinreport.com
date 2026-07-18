<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\MonobankService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MonobankController extends Controller
{
    public static function checkOrderStatus(Request $request)
    {
        $orderId = $request->input('id') ?? $request->input('orderReference'); // Поддержка старого параметра
        $order = $orderId ? Order::find($orderId) : null;
        if (!$order) {
            return response()->json(['success' => false, 'error' => 'Order not found'], 404);
        }

        // Если заказ уже оплачен, обработан или завершен, просто возвращаем статус
        // НО все равно возвращаем order_purpose и report_url для единообразия с OrderController
        if ($order->status == 'paid' || $order->status == 'processing' || $order->status == 'completed') {
            $response = [
                'success' => true,
                'status' => $order->status,
                'order_purpose' => $order->order_purpose,
            ];

            // Если заказ на отчет и есть report_key, возвращаем ссылку на отчет
            if ($order->order_purpose === 'report' && $order->report_key) {
                $response['report_key'] = $order->report_key;
                $response['report_url'] = route('view-report', ['report_key' => $order->report_key]);
            }

            return response()->json($response);
        }

        // Если заказ еще не оплачен, проверяем статус через Monobank API
        $data = MonobankService::checkOrderStatus((string)$order->id);
        $order->refresh();

        $response = [
            'success' => true,
            'status' => $order->status,
            'order_purpose' => $order->order_purpose,
            'data' => $data,
        ];

        // Если заказ на отчет и есть report_key, возвращаем ссылку на отчет
        if ($order->order_purpose === 'report' && $order->report_key) {
            $response['report_key'] = $order->report_key;
            $response['report_url'] = route('view-report', ['report_key' => $order->report_key]);
        }

        return response()->json($response);
    }

    public function monobankCallback(Request $request)
    {
        $data = $request->all();
//        Log::info('Monobank callback received', ['data' => $data]);

        // Знаходимо замовлення по invoiceId
        $invoiceId = $data['invoiceId'] ?? null;
        if (! $invoiceId) {
//            Log::error('Monobank callback: invoiceId missing');

            return response()->json(['status' => 'error', 'message' => 'invoiceId missing'], 400);
        }

        $order = Order::where('invoice_id', $invoiceId)->first();
        if (! $order) {
//            Log::error('Monobank callback: order not found', ['invoiceId' => $invoiceId]);

            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // Оновлюємо статус замовлення
        MonobankService::updateOrderFromMonobankData($order, $data);

        return response()->json(['status' => 'ok']);
    }
}
