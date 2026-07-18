<?php

namespace App\Http\Controllers;

use App\Helpers\CurrencyHelper;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(Request $request): View
    {
        // Админ-панель: показываем ВСЕ заказы всех пользователей
        $query = Order::query();

        // Поиск по email, id, vin, order_key (Order Reference)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%'.addcslashes($search, '%_\\').'%')
                    ->orWhere('vin', 'like', '%'.addcslashes($search, '%_\\').'%')
                    ->orWhere('order_key', 'like', '%'.addcslashes($search, '%_\\').'%');
                // Поиск по ID только если строка — число
                if (is_numeric($search)) {
                    $q->orWhere('id', (int) $search);
                }
            });
        }

        // Фильтр по статусу
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Фильтр по цене (от и до)
        if ($request->filled('price_from')) {
            $query->where('total_price', '>=', $request->input('price_from'));
        }
        if ($request->filled('price_to')) {
            $query->where('total_price', '<=', $request->input('price_to'));
        }

        // Фильтр по дате
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Сортировка
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $orders = $query->paginate(15)->withQueryString();

        // Добавляем символ валюты для каждого заказа
        $orders->each(function ($order) {
            $currency = $order->currency ?? 'usd';
            $order->currency_symbol = CurrencyHelper::getCurrencySymbol($currency);
        });

        // Получаем общую статистику для всех заказов (без фильтров)
        $totalOrders = Order::count();
        $pendingPaymentOrders = Order::where('status', 'pending payment')->count();
        $paidOrders = Order::where('status', 'paid')->count();
        $processingOrders = Order::where('status', 'processing')->count();
        $completedOrders = Order::where('status', 'completed')->count();
        $failedOrders = Order::where('status', 'failed')->count();
        $refundOrders = Order::where('status', 'refund')->count();
        $expiredOrders = Order::where('status', 'expired')->count();
        $fraudOrders = Order::where('status', 'fraud')->count();

        return view('dashboard', compact('orders', 'totalOrders', 'pendingPaymentOrders', 'paidOrders', 'processingOrders', 'completedOrders', 'failedOrders', 'refundOrders', 'expiredOrders', 'fraudOrders'));
    }

    public function createOrder(Request $request)
    {
        // Валидация данных
        $request->validate([
            'vin' => 'required|string|min:17|max:17',
            'email' => 'required|email',
            'report_type' => 'required|in:carfax,autocheck,auctions,sticker',
        ], [
            'vin.required' => 'VIN обязателен для заполнения',
            'vin.min' => 'VIN должен содержать 17 символов',
            'vin.max' => 'VIN должен содержать 17 символов',
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Некорректный формат email',
            'report_type.required' => 'Тип отчета обязателен',
            'report_type.in' => 'Некорректный тип отчета',
        ]);

        $vin = strtoupper($request->input('vin'));
        $vin = str_replace([' ', '-', '.'], '', $vin);

        // Проверка формата VIN
        if (!preg_match('/^[0-9A-Z]{17}$/', $vin)) {
            return response()->json([
                'success' => false,
                'error' => 'Некорректный формат VIN. Должен содержать 17 символов (цифры и заглавные буквы)',
            ], 400);
        }

        $email = $request->input('email');
        $reportType = $request->input('report_type');

        // Проверяем, есть ли пользователь с таким email
        $user = User::where('email', $email)->first();

        // Получаем дефолтную валюту и локаль
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
        $locale = app()->getLocale();

        // Создаем заказ сначала со статусом 'pending payment'
        $orderData = [
            'email' => $email,
            'vin' => $vin,
            'report_type' => $reportType,
            'status' => 'pending payment', // Сначала создаем со статусом 'pending payment'
            'total_price' => 0, // Для ручных заказов цена 0
            'currency' => $defaultCurrency,
            'locale' => $locale,
            'payment_method' => 'admin_manual', // Метод оплаты для ручных заказов
            'order_purpose' => 'report',
            'payment_data' => [
                'created_by' => auth()->user()->email,
                'created_at' => now()->toIso8601String(),
                'manual' => true,
            ],
        ];

        // Если пользователь найден, связываем заказ с ним
        if ($user) {
            $order = $user->orders()->create($orderData);
        } else {
            $order = Order::create($orderData);
        }

        // Обновляем статус на 'paid', чтобы OrderObserver автоматически обработал заказ
        // Это вызовет событие 'updated' и запустит генерацию отчета
        $order->status = 'paid';
        $order->save();

//        Log::info('Admin created manual order', [
//            'order_id' => $order->id,
//            'vin' => $vin,
//            'email' => $email,
//            'report_type' => $reportType,
//            'admin' => auth()->user()->email,
//        ]);

        return response()->json([
            'success' => true,
            'message' => 'Заказ успешно создан. Отчет будет сгенерирован автоматически.',
            'order' => [
                'id' => $order->id,
                'vin' => $order->vin,
                'email' => $order->email,
                'report_type' => $order->report_type,
                'status' => $order->status,
            ],
        ]);
    }
}
