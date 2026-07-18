<?php

namespace App\Http\Controllers;

use App\Mail\ReportMail;
use App\Models\Order;
use App\Services\PlatonService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class OrderController extends Controller
{
    private $key;

    public function __construct()
    {
        $this->key = config('services.merchant_secret_key');
    }

    public function checkOrderStatus(Request $request)
    {
        $orderId = $request->input('id') ?? $request->input('orderReference'); // Поддержка старого параметра для обратной совместимости
        $order = $orderId ? Order::find($orderId) : null;

        if (!$order) {
            return [
                'success' => false,
                'error' => 'Order not found',
            ];
        }

        // Уникальная логика для report_balance: переводим заказ в 'paid' при первом polling
        // чтобы OrderObserver подхватил изменение и обработал заказ
        // ВАЖНО: проверяем payment_method из БД для безопасности
        if ($order->payment_method === 'report_balance' && $order->status === 'pending payment') {
            $order->status = 'paid';
            $order->save(); // Это вызовет OrderObserver::updated(), который переведет в 'processing'

//            Log::info('checkOrderStatus: Order status updated to paid for report_balance', [
//                'order_id' => $order->id,
//                'payment_method' => $order->payment_method,
//            ]);

            // Обновляем заказ из БД, чтобы получить актуальный статус после обработки Observer
            $order->refresh();
        }

        if ($order->payment_method === 'platon' && $order->status === 'pending payment') {
            PlatonService::syncPaymentStatus($order);
            $order->refresh();
        }

        $response = [
            'success' => true,
            'status' => $order->status,
            'order_purpose' => $order->order_purpose,
        ];

        // Если заказ на отчет и есть report_key, возвращаем ссылку на отчет
        // ВАЖНО: возвращаем report_url для всех статусов, если есть report_key
        if ($order->order_purpose === 'report' && $order->report_key) {
            $response['report_key'] = $order->report_key;
            $response['report_url'] = route('view-report', ['report_key' => $order->report_key]);

            Log::info('checkOrderStatus: Returning report_url', [
                'order_id' => $order->id,
                'status' => $order->status,
                'report_key' => $order->report_key,
                'report_url' => $response['report_url'],
            ]);
        } else {
            Log::info('checkOrderStatus: No report_url', [
                'order_id' => $order->id,
                'status' => $order->status,
                'order_purpose' => $order->order_purpose,
                'has_report_key' => !empty($order->report_key),
            ]);
        }

        return $response;
    }

    public function getPaymentUrl(Request $request)
    {
        $orderId = $request->input('id');
        $order = $orderId ? Order::find($orderId) : null;

        if (!$order) {
            return response()->json([
                'success' => false,
                'error' => 'Order not found',
            ], 404);
        }

        $paymentData = is_array($order->payment_data) ? $order->payment_data : json_decode($order->payment_data, true);
        $paymentForm = $paymentData['payment_form'] ?? null;
        if ($paymentForm) {
            return response()->json([
                'success' => true,
                'payment_form' => $paymentForm,
            ]);
        }

        $paymentUrl = $paymentData['payment_url'] ?? null;

        if (!$paymentUrl) {
            return response()->json([
                'success' => false,
                'error' => 'Payment URL not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'url' => $paymentUrl,
        ]);
    }

    public function purchaseReport(Request $request)
    {
        $vin = $request->input('vin');
        $reportType = $request->input('report_type');
        $email = $request->input('email');
        $paymentMethod = $request->input('payment_method');
        $language = $request->header('Accept-Language') ?? 'en';

        $currentLocale = $request->input('locale');
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        if (!$currentLocale || !in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getCurrentLocale() ?? app()->getLocale();
        }
        if (!$currentLocale || !in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getDefaultLocale();
        }

//        Log::info('OrderController: Locale determined', [
//            'currentLocale' => $currentLocale,
//            'requestLocale' => $request->input('locale'),
//            'referer' => $request->header('Referer'),
//            'sessionLocale' => session('laravellocalization.locale') ?? session('locale'),
//            'appLocale' => app()->getLocale(),
//            'laravelLocalizationLocale' => LaravelLocalization::getCurrentLocale(),
//            'acceptLanguage' => $language,
//        ]);
        $currency = SettingController::getCurrency($currentLocale);

        if (!$vin || !$reportType || !$email || !$paymentMethod) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid request. All fields are required.',
            ], 400);
        }

        $allowedPaymentMethods = ['platon', 'report_balance'];
        if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid payment method',
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid email address',
            ], 400);
        }

        $status = 'pending payment';
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';

        // Получаем цены (теперь это массивы по валютам)
        $carfaxPrices = SettingController::getSetting('carfax_price') ?? [];
        $autocheckPrices = SettingController::getSetting('autocheck_price') ?? [];
        $auctionsPrices = SettingController::getSetting('auctions_price') ?? [];
        $stickerPrices = SettingController::getSetting('sticker_price') ?? [];

        switch ($reportType) {
            case 'carfax':
                $price = (is_array($carfaxPrices) && isset($carfaxPrices[$currency]))
                    ? $carfaxPrices[$currency]
                    : (is_array($carfaxPrices) && isset($carfaxPrices[$defaultCurrency])
                        ? $carfaxPrices[$defaultCurrency]
                        : null);
                break;
            case 'autocheck':
                $price = (is_array($autocheckPrices) && isset($autocheckPrices[$currency]))
                    ? $autocheckPrices[$currency]
                    : (is_array($autocheckPrices) && isset($autocheckPrices[$defaultCurrency])
                        ? $autocheckPrices[$defaultCurrency]
                        : null);
                break;
            case 'auctions':
                $price = (is_array($auctionsPrices) && isset($auctionsPrices[$currency]))
                    ? $auctionsPrices[$currency]
                    : (is_array($auctionsPrices) && isset($auctionsPrices[$defaultCurrency])
                        ? $auctionsPrices[$defaultCurrency]
                        : null);
                break;
            case 'sticker':
                $price = (is_array($stickerPrices) && isset($stickerPrices[$currency]))
                    ? $stickerPrices[$currency]
                    : (is_array($stickerPrices) && isset($stickerPrices[$defaultCurrency])
                        ? $stickerPrices[$defaultCurrency]
                        : null);
                break;
            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid report type',
                ], 400);
        }

        if (!$price) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid report type or price not found',
            ], 500);
        }

        $vin = strtoupper($vin);
        $vin = str_replace([' ', '-', '.'], '', $vin);

        if (!preg_match('/^[0-9A-Z]{17}$/', $vin)) {
            return response()->json([
                'success' => false,
                'error' => 'Invalid VIN. Must be 17 characters.',
            ], 400);
        }

        // Получаем авторизованного пользователя, если он есть
        $user = auth()->user();

        // ПРОВЕРКА БАЛАНСА ДО СОЗДАНИЯ ЗАКАЗА - это критически важно!
        if ($paymentMethod == 'report_balance') {
            // Проверяем, что пользователь авторизован
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'error' => 'User must be authenticated to use report balance',
                ], 401);
            }

            // Проверяем баланс ДО создания заказа
            // Обновляем данные пользователя из БД для актуальной проверки
            $user->refresh();

            if ($user->report_balance < 1) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient report balance',
                ], 400);
            }

            // Вычитаем 1 отчет из баланса (1 отчет = 1 единица report_balance)
            $balanceDeducted = UserService::useReportBalance($user->id, 1);

            if (!$balanceDeducted) {
                return response()->json([
                    'success' => false,
                    'error' => 'Insufficient report balance',
                ], 400);
            }

            // Баланс успешно списан, заказ будет создан со статусом 'pending payment'
            // и переведен в 'paid' на странице thank-you page
        }

        // Создаем заказ только после успешной проверки баланса
        // Если оплата через report_balance, устанавливаем price = 0 (человек тратит отчеты, а не деньги)
        $orderPrice = ($paymentMethod == 'report_balance') ? 0 : $price;

        // Создаем заказ сначала со статусом 'pending payment',
        // чтобы потом обновить его на 'paid' и сработал OrderObserver
        $orderData = [
            'email' => $email,
            'vin' => $vin,
            'report_type' => $reportType,
            'currency' => $currency,
            'locale' => $currentLocale,
            'status' => 'pending payment', // Сначала создаем со статусом 'pending payment'
            'total_price' => $orderPrice,
            'payment_method' => $paymentMethod,
            'order_purpose' => 'report',
            'invoice_id' => null,
        ];

        // Если пользователь авторизован, создаем заказ через связь
        if ($user) {
            $order = $user->orders()->create($orderData);
        } else {
            $order = Order::create($orderData);
        }

        // Для report_balance заказ создается со статусом 'pending payment'
        // Статус будет обновлен на 'paid' на странице thank-you page,
        // чтобы OrderObserver подхватил изменение и обработал заказ

        // orderReference больше не нужен, используем только id

        // Теперь создаем ссылку на оплату с использованием id
        $paymentController = new PaymentController;
        $paymentUrl = null;

        switch ($paymentMethod) {
            case 'platon':
                $platonController = new PlatonController;
                $platonResponse = $platonController->createPaymentLinkForOrder(
                    $order,
                    $reportType,
                    $email,
                    $vin,
                    $currency,
                    $currentLocale
                );

                if (! ($platonResponse['success'] ?? false) || empty($platonResponse['payment_form'])) {
                    return response()->json([
                        'success' => false,
                        'error' => $platonResponse['error'] ?? 'Failed to create Platon payment form',
                    ], 400);
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
                $monobankResponse = $paymentController->createInvoiceLinkMonobank($reportType, $email, $vin, (string)$order->id, $currency, $currentLocale);

                // Проверяем на ошибки
                if (isset($monobankResponse['error'])) {
                    return response()->json([
                        'success' => false,
                        'error' => $monobankResponse['error'],
                    ], 400);
                }

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
                }
                break;

            case 'stripe':
                $stripeResponse = $paymentController->createIntent($request);
                if ($stripeResponse->getStatusCode() !== 200) {
                    $stripeData = json_decode($stripeResponse->getContent(), true);

                    return response()->json([
                        'success' => false,
                        'error' => $stripeData['error'] ?? 'Unknown error',
                    ], $stripeResponse->getStatusCode());
                }
                // Получаем данные из JsonResponse
                $stripeData = json_decode($stripeResponse->getContent(), true);
                $paymentUrl = $stripeData['url'] ?? null;
                break;

            case 'report_balance':
                // Для report_balance сразу редиректим на thank-you page без статуса,
                // чтобы запустился polling для проверки статуса и получения report_url
                // Убеждаемся, что order_key существует
                if (!$order->order_key) {
                    $order->generateOrderKey();
                    $order->refresh();
                }
                $orderKey = $order->order_key;
                $thankYouUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/thank-you');
                $paymentUrl = $thankYouUrl . '?id=' . $order->id . '&key=' . $orderKey;
                break;

            default:
                return response()->json([
                    'success' => false,
                    'error' => 'Invalid payment method',
                ], 400);
        }

//        Log::info('Purchase report: Order created.', [
//            'order_id' => $order->id,
//            'payment_method' => $paymentMethod,
//            'status' => $order->status,
//        ]);

        // Явно сохраняем сессию перед ответом
        $request->session()->save();

        if (isset($paymentForm)) {
            return response()->json([
                'success' => true,
                'payment_form' => $paymentForm,
            ]);
        }

        if ($paymentUrl) {
            return response()->json([
                'success' => true,
                'url' => $paymentUrl,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Failed to create payment order',
            ], 500);
        }
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending payment,paid,processing,completed,failed,refund,expired,fraud',
        ]);

        $order = Order::findOrFail($id);

        // Проверяем, что пользователь имеет доступ к этому заказу
        $user = auth()->user();

        // Админы могут изменять статус любого заказа
        if ($user->role !== 'admin' && $order->email !== $user->email) {
            return response()->json([
                'success' => false,
                'error' => 'Unauthorized',
            ], 403);
        }

        $oldStatus = $order->status;
        $order->status = $request->input('status');
        $order->save();

//        Log::info('Order status updated', [
//            'order_id' => $order->id,
//            'old_status' => $oldStatus,
//            'new_status' => $order->status,
//            'user' => $user->email,
//        ]);

        return response()->json([
            'success' => true,
            'message' => 'Статус заказа успешно обновлен',
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
            ],
        ]);
    }

    public function resendEmail(Request $request, $id)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $order = Order::with('user')->findOrFail($id);

        if (!$order->report_key) {
            return response()->json([
                'success' => false,
                'error' => 'У заказа нет report_key. Невозможно отправить email.',
            ], 400);
        }

        try {
            $orderLocale = $order->locale ?? LaravelLocalization::getDefaultLocale();
            $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
            if (!in_array($orderLocale, $supportedLocales, true)) {
                $orderLocale = LaravelLocalization::getDefaultLocale();
            }

            Mail::to($request->email)
                ->locale($orderLocale)
                ->send(new ReportMail($order));

//            Log::info('OrderController: Report email resent', [
//                'order_id' => $order->id,
//                'email' => $request->email,
//                'report_key' => $order->report_key,
//            ]);

            return response()->json([
                'success' => true,
                'message' => 'Email успешно отправлен на ' . $request->email,
            ]);
        } catch (\Exception $e) {
            Log::error('OrderController: Failed to resend report email', [
                'order_id' => $order->id,
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Ошибка при отправке email: ' . $e->getMessage(),
            ], 500);
        }
    }
}
