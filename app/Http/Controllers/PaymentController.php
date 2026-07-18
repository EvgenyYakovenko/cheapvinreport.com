<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class PaymentController extends Controller
{
    public function createInvoiceLinkMonobank($reportType, $email, $vin, $orderId, $currency, $locale = null)
    {
        $currentLocale = $locale ?? app()->getLocale();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        if (! in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getDefaultLocale();
        }
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
        if (! $currency) {
            $currency = SettingController::getCurrency($locale ? explode('-', $locale)[0] : null);
        }

        // Получаем цены
        $carfaxPrices = SettingController::getSetting('carfax_price') ?? [];
        $autocheckPrices = SettingController::getSetting('autocheck_price') ?? [];
        $auctionsPrices = SettingController::getSetting('auctions_price') ?? [];
        $stickerPrices = SettingController::getSetting('sticker_price') ?? [];

        $prices = [
            'carfax' => (is_array($carfaxPrices) && isset($carfaxPrices[$currency]))
                ? $carfaxPrices[$currency]
                : (is_array($carfaxPrices) && isset($carfaxPrices[$defaultCurrency])
                    ? $carfaxPrices[$defaultCurrency]
                    : null),
            'autocheck' => (is_array($autocheckPrices) && isset($autocheckPrices[$currency]))
                ? $autocheckPrices[$currency]
                : (is_array($autocheckPrices) && isset($autocheckPrices[$defaultCurrency])
                    ? $autocheckPrices[$defaultCurrency]
                    : null),
            'auctions' => (is_array($auctionsPrices) && isset($auctionsPrices[$currency]))
                ? $auctionsPrices[$currency]
                : (is_array($auctionsPrices) && isset($auctionsPrices[$defaultCurrency])
                    ? $auctionsPrices[$defaultCurrency]
                    : null),
            'sticker' => (is_array($stickerPrices) && isset($stickerPrices[$currency]))
                ? $stickerPrices[$currency]
                : (is_array($stickerPrices) && isset($stickerPrices[$defaultCurrency])
                    ? $stickerPrices[$defaultCurrency]
                    : null),
        ];

        $price = $prices[$reportType] ?? null;

        if (! $price) {
            return [
                'error' => 'Invalid report type or price not found',
                'success' => false,
            ];
        }

        $token = config('services.monobank.token');

        // Для Monobank всегда выставляем оплату в гривне (UAH),
        // конвертируя цену из валюты пользователя в UAH через Monobank-курс.
        $priceInUah = SettingService::convert((float) $price, $currency, 'uah');
        $amount = (int) round($priceInUah * 100);
        $ccy = 980; // Всегда гривна

        // Получаем заказ для получения order_key
        $order = Order::find($orderId);
        if (!$order) {
            return [
                'error' => 'Order not found',
                'success' => false,
            ];
        }

        // Убеждаемся, что order_key существует
        if (!$order->order_key) {
            $order->generateOrderKey();
            $order->refresh();
        }
        $orderKey = $order->order_key;

        // Формируем URL с локалью для thank-you.post
        $thankYouUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/thank-you');
        $redirectUrl = $thankYouUrl.'?id='.$orderId.'&key='.$orderKey;
        $webHookUrl = url('/payment/monobank/callback');

        // Формируем корзину для отчета
        $basketOrder = [
            [
                'name' => 'Vehicle History Report by VIN',
                'qty' => 1,
                'sum' => $amount,
                'total' => $amount,
                'icon' => null,
                'unit' => 'шт.',
                'code' => "1",
                'barcode' => null,
                'header' => null,
                'footer' => null,
                'tax' => [2],
                'uktzed' => null,
                'splitReceiverId' => null,
                'discounts' => [],
            ],
        ];

        // Формируем merchantPaymInfo
        $merchantPaymInfo = [
            'reference' => (string) $orderId,
            'customerEmails' => [$email],
            'basketOrder' => $basketOrder,
        ];

        $response = Http::withHeaders([
            'X-Token' => $token,
        ])->withoutVerifying()
            ->post('https://api.monobank.ua/api/merchant/invoice/create', [
                'amount' => $amount,
                'ccy' => $ccy,
                'merchantPaymInfo' => $merchantPaymInfo,
                'redirectUrl' => $redirectUrl,
                'webHookUrl' => $webHookUrl,
                //'code' => 'MI042352',
                'validity' => 86400,
            ]);

        return [
            'url' => $response->json('pageUrl'),
            'invoice_id' => $response->json('invoiceId'),
            'success' => $response->successful(),
            'error' => $response->json('errText'),
            'data' => $response->json(),
        ];
    }

    public function createInvoiceLinkMonobankForTopup($amount, $email, $orderId, $currency, $locale = null)
    {
        $topupReportLocker = SettingController::getSetting('topup_report_locker') ?? [];
        if($topupReportLocker && !in_array($amount, $topupReportLocker)) {
            return [
                'error' => 'Amount is not allowed',
                'success' => false,
            ];
        }
        $currentLocale = $locale ?? app()->getLocale();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        if (! in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getDefaultLocale();
        }
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
        if (! $currency) {
            $currency = SettingController::getCurrency($locale ? explode('-', $locale)[0] : null);
        }

        $token = config('services.monobank.token');
        $price = Setting::getPriceFromAmountOfReports($amount, $currency);
        if (! $price) {
            return [
                'error' => 'Price not found',
                'success' => false,
            ];
        }

        // Всегда платим в гривне, конвертируя цену из валюты пользователя в UAH
        $priceInUah = SettingService::convert((float) $price, $currency, 'uah');
        $amountInCents = (int) round($priceInUah * 100);
        $ccy = 980; // UAH

        // Получаем заказ для получения order_key
        $order = Order::find($orderId);
        if (!$order) {
            return [
                'error' => 'Order not found',
                'success' => false,
            ];
        }

        // Убеждаемся, что order_key существует
        if (!$order->order_key) {
            $order->generateOrderKey();
            $order->refresh();
        }
        $orderKey = $order->order_key;

        // Формируем URL с локалью для thank-you.post
        $thankYouUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/thank-you');
        $redirectUrl = $thankYouUrl.'?id='.$orderId.'&key='.$orderKey;
        $webHookUrl = url('/payment/monobank/callback');

        // Формируем корзину для пополнения баланса
        // qty всегда равен 1, sum = общая сумма за весь bundle
        // Monobank API требует, чтобы total = sum * qty
        $basketOrder = [
            [
                'name' => "Vehicle History Report by VIN – $amount reports package",
                'qty' => 1,
                'sum' => $amountInCents, // Общая сумма за весь bundle
                'total' => $amountInCents, // total = sum * qty = amountInCents * 1
                'icon' => null,
                'unit' => 'шт.',
                'code' => "2",
                'barcode' => null,
                'header' => null,
                'footer' => null,
                'tax' => [2],
                'uktzed' => null,
                'splitReceiverId' => null,
                'discounts' => [],
            ],
        ];

        // Формируем merchantPaymInfo
        $merchantPaymInfo = [
            'reference' => (string) $orderId,
            'customerEmails' => [$email],
            'basketOrder' => $basketOrder,
        ];

        // Используем amountInCents для amount, чтобы он совпадал с total в корзине
        // Monobank API требует, чтобы amount в запросе совпадал с суммой total всех товаров в корзине
        $response = Http::withHeaders([
            'X-Token' => $token,
        ])->withoutVerifying()
            ->post('https://api.monobank.ua/api/merchant/invoice/create', [
                'amount' => $amountInCents, // Должно совпадать с total в basketOrder
                'ccy' => $ccy,
                'merchantPaymInfo' => $merchantPaymInfo,
                'redirectUrl' => $redirectUrl,
                'webHookUrl' => $webHookUrl,
                'validity' => 86400, // 1 hour
            ]);

        return [
            'url' => $response->json('pageUrl'),
            'invoice_id' => $response->json('invoiceId'),
            'success' => $response->successful(),
            'error' => $response->json('errText'),
            'data' => $response->json(),
        ];
    }

    public function createIntent(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $reportType = $request->input('report_type');
        $email = $request->input('email');
        $vin = $request->input('vin');
        $language = $request->header('Accept-Language') ?? 'en';
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $localePattern = implode('|', array_map('preg_quote', $supportedLocales));

        // ПРИОРИТЕТ 1: Локаль из тела запроса (передается из JavaScript)
        $currentLocale = $request->input('locale');

        // ПРИОРИТЕТ 2: Локаль из Referer заголовка (из URL откуда пришел запрос)
        if (! $currentLocale || ! in_array($currentLocale, $supportedLocales, true)) {
            $referer = $request->header('Referer');
            if ($referer) {
                // Извлекаем локаль из URL типа /ru/ или /es/
                if ($localePattern !== '' && preg_match('#/('.$localePattern.')/#', $referer, $matches)) {
                    $currentLocale = $matches[1];
                }
            }
        }

        // ПРИОРИТЕТ 3: Локаль из сессии LaravelLocalization
        if (! $currentLocale || ! in_array($currentLocale, $supportedLocales, true)) {
            $sessionLocale = session('laravellocalization.locale') ?? session('locale');
            if ($sessionLocale && in_array($sessionLocale, $supportedLocales, true)) {
                $currentLocale = $sessionLocale;
            }
        }

        // ПРИОРИТЕТ 4: Локаль из LaravelLocalization или дефолтная
        if (! $currentLocale || ! in_array($currentLocale, $supportedLocales, true)) {
            $currentLocale = LaravelLocalization::getCurrentLocale() ?? app()->getLocale();
        }

        // Логируем для отладки
//        Log::info('PaymentController: Locale determined for Stripe', [
//            'currentLocale' => $currentLocale,
//            'requestLocale' => $request->input('locale'),
//            'referer' => $request->header('Referer'),
//            'sessionLocale' => session('laravellocalization.locale') ?? session('locale'),
//            'appLocale' => app()->getLocale(),
//            'laravelLocalizationLocale' => LaravelLocalization::getCurrentLocale(),
//            'acceptLanguage' => $language,
//        ]);
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
        $currency = SettingController::getCurrency($currentLocale);

        // Получаем цены (теперь это массивы по валютам)
        $carfaxPrices = SettingController::getSetting('carfax_price') ?? [];
        $autocheckPrices = SettingController::getSetting('autocheck_price') ?? [];
        $auctionsPrices = SettingController::getSetting('auctions_price') ?? [];
        $stickerPrices = SettingController::getSetting('sticker_price') ?? [];

        $prices = [
            'carfax' => (is_array($carfaxPrices) && isset($carfaxPrices[$currency]))
                ? $carfaxPrices[$currency]
                : (is_array($carfaxPrices) && isset($carfaxPrices[$defaultCurrency])
                    ? $carfaxPrices[$defaultCurrency]
                    : null),
            'autocheck' => (is_array($autocheckPrices) && isset($autocheckPrices[$currency]))
                ? $autocheckPrices[$currency]
                : (is_array($autocheckPrices) && isset($autocheckPrices[$defaultCurrency])
                    ? $autocheckPrices[$defaultCurrency]
                    : null),
            'auctions' => (is_array($auctionsPrices) && isset($auctionsPrices[$currency]))
                ? $auctionsPrices[$currency]
                : (is_array($auctionsPrices) && isset($auctionsPrices[$defaultCurrency])
                    ? $auctionsPrices[$defaultCurrency]
                    : null),
            'sticker' => (is_array($stickerPrices) && isset($stickerPrices[$currency]))
                ? $stickerPrices[$currency]
                : (is_array($stickerPrices) && isset($stickerPrices[$defaultCurrency])
                    ? $stickerPrices[$defaultCurrency]
                    : null),
        ];

        $price = $prices[$reportType] ?? null;

        if (! $price) {
            return response()->json([
                'error' => 'Invalid report type or price not found',
            ], 400);
        }

        // Конвертируем цену в центы (USD)
        $amount = (int) ($price * 100);

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => 'Report for VIN: '.$vin,
                        'description' => 'Report type: '.$reportType,
                    ],
                    'unit_amount' => $amount,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'customer_email' => $email,
            'success_url' => LaravelLocalization::getLocalizedURL($currentLocale, '/thank-you?session_id={CHECKOUT_SESSION_ID}'),
            'cancel_url' => LaravelLocalization::getLocalizedURL($currentLocale, '/'),
            'metadata' => [
                'vin' => $vin,
                'report_type' => $reportType,
                'email' => $email,
                'locale' => $currentLocale,
            ],
        ]);

        // Возвращаем URL сессии, на который JS перенаправит пользователя
        return response()->json(['url' => $session->url]);
    }

    public function stripeWebhook(Request $request)
    {
        Stripe::setApiKey(config('services.stripe.secret'));
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sig_header, $endpoint_secret);
        } catch (\Exception $e) {
            Log::error('Stripe webhook: Invalid signature', ['error' => $e->getMessage()]);

            return response('Invalid webhook signature', 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            // Достаем metadata (в Stripe SDK это объект, используем toArray для надежности)
            $sessionArray = $session->toArray();
            $metadata = $sessionArray['metadata'] ?? [];
            $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
            // Подготавливаем данные для processOrder
            $stripeData = [
                'session_id' => $session->id,
                'payment_status' => $session->payment_status ?? 'unpaid',
                'email' => $metadata['email'] ?? $sessionArray['customer_details']['email'] ?? $sessionArray['customer_email'] ?? null,
                'vin' => $metadata['vin'] ?? null,
                'report_type' => $metadata['report_type'] ?? null,
                'currency' => $session->currency ?? $defaultCurrency,
                'locale' => $metadata['locale'] ?? app()->getLocale(),
                'amount_total' => $session->amount_total ?? 0,
                'stripe_data' => $sessionArray,
            ];

            // Используем общий метод processOrder
            $result = OrderService::processStripeOrder($stripeData);

            // Обрабатываем ответ от processOrder
            // Если это JsonResponse с ошибкой, логируем и возвращаем простой ответ для Stripe
            if ($result->getStatusCode() !== 200) {
                $responseData = json_decode($result->getContent(), true);
                Log::error('OrderService: Stripe webhook: processStripeOrder failed', [
                    'status_code' => $result->getStatusCode(),
                    'error' => $responseData['error'] ?? 'Unknown error',
                    'session_id' => $session->id,
                ]);

                // Возвращаем 200 для Stripe, чтобы не было повторных попыток
                // Ошибка уже залогирована
                return response('Order processing failed (logged)', 200);
            }

//            Log::info('Stripe webhook: Order processed successfully', [
//                'session_id' => $session->id,
//                'payment_status' => $session->payment_status,
//            ]);

            return response('Order processed successfully', 200);
        }

        // Для других типов событий просто подтверждаем получение
//        Log::info('Stripe webhook: Unhandled event type', ['event_type' => $event->type]);

        return response('Event received', 200);
    }
}
