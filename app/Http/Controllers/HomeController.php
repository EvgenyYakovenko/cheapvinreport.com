<?php

namespace App\Http\Controllers;

use App\Models\DeletedUser;
use App\Models\Order;
use App\Models\Post;
use App\Models\Page;
use App\Services\OrderService;
use App\Services\SettingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class HomeController extends Controller
{
    public function index(): View
    {
        // Определяем валюту с учетом настройки multi_currency_enabled
        $currency = SettingController::getCurrency();
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';

        // Маппинг символов валют
        $currencySymbols = [
            'usd' => '$',
            'uah' => '₴',
            'pln' => 'zł',
            'kzt' => '₸',
        ];
        $currencySymbol = $currencySymbols[$currency] ?? '$';

        // Получаем цены отчетов из настроек (теперь это массивы по валютам)
        $carfaxPrices = SettingController::getSetting('carfax_price') ?? [];
        $autocheckPrices = SettingController::getSetting('autocheck_price') ?? [];
        $auctionsPrices = SettingController::getSetting('auctions_price') ?? [];
        $stickerPrices = SettingController::getSetting('sticker_price') ?? [];

        // Извлекаем цены для текущей валюты или дефолтной
        $carfaxPrice = (is_array($carfaxPrices) && isset($carfaxPrices[$currency]))
            ? $carfaxPrices[$currency]
            : (is_array($carfaxPrices) && isset($carfaxPrices[$defaultCurrency])
                ? $carfaxPrices[$defaultCurrency]
                : '10');
        $autocheckPrice = (is_array($autocheckPrices) && isset($autocheckPrices[$currency]))
            ? $autocheckPrices[$currency]
            : (is_array($autocheckPrices) && isset($autocheckPrices[$defaultCurrency])
                ? $autocheckPrices[$defaultCurrency]
                : '10');
        $auctionsPrice = (is_array($auctionsPrices) && isset($auctionsPrices[$currency]))
            ? $auctionsPrices[$currency]
            : (is_array($auctionsPrices) && isset($auctionsPrices[$defaultCurrency])
                ? $auctionsPrices[$defaultCurrency]
                : '10');
        $stickerPrice = (is_array($stickerPrices) && isset($stickerPrices[$currency]))
            ? $stickerPrices[$currency]
            : (is_array($stickerPrices) && isset($stickerPrices[$defaultCurrency])
                ? $stickerPrices[$defaultCurrency]
                : '10');

        // Получаем данные пользователя, если авторизован
        $user = Auth::user();
        $userReportBalance = $user ? $user->report_balance : null;

        // Получаем настройку для расчета цены пополнения баланса отчетов
        $topupReportBalancePrice = SettingController::getSetting('topup_report_balance_price');

        $conversionRateToUah = null;
        if ($currency !== 'uah') {
            $conversionRateToUah = SettingService::convert(1.0, $currency, 'uah');
        }

        return view('index', compact(
            'carfaxPrice',
            'autocheckPrice',
            'auctionsPrice',
            'stickerPrice',
            'userReportBalance',
            'user',
            'currencySymbol',
            'currency',
            'topupReportBalancePrice',
            'conversionRateToUah',
        ));
    }

    public function author(): View
    {
        $author = config('author', []);
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $supportedLocales = array_keys(config('laravellocalization.supportedLocales', []));

        $basePath = '/author';
        $hreflangUrls = [];

        foreach ($supportedLocales as $localeCode) {
            if ($localeCode === $defaultLocale) {
                $hreflangUrls[$localeCode] = url($basePath);
                continue;
            }

            $hreflangUrls[$localeCode] = LaravelLocalization::getLocalizedURL($localeCode, $basePath);
        }

        $xDefaultUrl = url($basePath);
        $metaTitle = ($author['name'] ?? 'Author') . ' - ' . config('app.name', 'CheapVINReport');
        $metaDescription = $author['bio'] ?? 'Author profile';
        $seoImage = $author['avatar'] ?? asset('images/hero-new.jpg');

        return view('author', compact(
            'author',
            'hreflangUrls',
            'xDefaultUrl',
            'metaTitle',
            'metaDescription',
            'seoImage'
        ));
    }

    public function thankYouPost(Request $request): RedirectResponse
    {
        // Сохраняем сессию перед обработкой
        $request->session()->save();

        $isStripe = $request->has('session_id');
        if ($isStripe) {
                $sessionId = $request->input('session_id');

                // Получаем данные из сессии Stripe через API
                try {
                    Stripe::setApiKey(config('services.stripe.secret'));
                    $session = Session::retrieve($sessionId);
                    $sessionArray = $session->toArray();
                    $metadata = $sessionArray['metadata'] ?? [];
                    $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';
                    $data = [
                        'session_id' => $sessionId,
                        'payment_status' => $session->payment_status ?? 'unpaid',
                        'email' => $metadata['email'] ?? $sessionArray['customer_details']['email'] ?? $sessionArray['customer_email'] ?? null,
                        'vin' => $metadata['vin'] ?? null,
                        'report_type' => $metadata['report_type'] ?? null,
                        'currency' => $session->currency ?? $defaultCurrency,
                        'locale' => $metadata['locale'] ?? app()->getLocale(),
                    ];

                    $result = OrderService::processStripeOrder($data);
                    if ($result->getStatusCode() !== 200) {
                        return redirect()->to(\App\Support\LocaleRoute::route('index'));
                    }
                    $stripeData = json_decode($result->getContent(), true);
                    if ($stripeData['success']) {
                        // Получаем заказ для определения локали
                        $orderId = $stripeData['order_id'] ?? $stripeData['orderReference'] ?? null;
                        $order = $orderId ? Order::find($orderId) : null;
                        if (!$order) {
                            return redirect()->to(\App\Support\LocaleRoute::route('index'));
                        }

                        $orderLocale = $order->locale ?? LaravelLocalization::getDefaultLocale();
                        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
                        if (! in_array($orderLocale, $supportedLocales, true)) {
                            $orderLocale = LaravelLocalization::getDefaultLocale();
                        }

                        // Убеждаемся, что order_key существует
                        if (!$order->order_key) {
                            $order->generateOrderKey();
                            $order->refresh();
                        }
                        $orderKey = $order->order_key;

                        $thankYouUrl = LaravelLocalization::getLocalizedURL($orderLocale, '/thank-you');
                        return redirect($thankYouUrl.'?id='.$orderId.'&key='.$orderKey);
                    } else {
                        $currentLocale = app()->getLocale();
                        $homeUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/');
                        return redirect($homeUrl);
                    }
                } catch (\Exception $e) {
                    Log::error('Stripe session retrieve error', ['error' => $e->getMessage(), 'session_id' => $sessionId]);
                    $currentLocale = app()->getLocale();
                    $homeUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/');
                    return redirect($homeUrl);
                }
        }

        // Если есть id, но не определили тип платежной системы
        $orderId = $request->input('id') ?? $request->input('orderReference') ?? $request->input('order_id');
        if ($orderId) {
            // Сохраняем сессию перед редиректом
            $request->session()->save();

            // Получаем заказ для определения локали
            $order = Order::find($orderId);
            if (!$order) {
                return redirect()->to(\App\Support\LocaleRoute::route('index'));
            }

            $orderLocale = $order->locale ?? LaravelLocalization::getDefaultLocale();
            $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
            if (! in_array($orderLocale, $supportedLocales, true)) {
                $orderLocale = LaravelLocalization::getDefaultLocale();
            }

            // Убеждаемся, что order_key существует
            if (!$order->order_key) {
                $order->generateOrderKey();
                $order->refresh();
            }
            $orderKey = $order->order_key;

            $thankYouUrl = LaravelLocalization::getLocalizedURL($orderLocale, '/thank-you');
            return redirect($thankYouUrl.'?id='.$orderId.'&key='.$orderKey);
        }

        // Сохраняем сессию перед редиректом
        $request->session()->save();

        $currentLocale = app()->getLocale();
        $homeUrl = LaravelLocalization::getLocalizedURL($currentLocale, '/');
        return redirect($homeUrl);
    }

    public function thankYou(Request $request)
    {
        // Сохраняем сессию для восстановления после внешнего редиректа
        $request->session()->save();

        $sessionId = $request->get('session_id');
        $orderId = $request->input('id') ?? $request->input('orderReference') ?? $request->input('order_id') ?? $request->input('order');

        // Если есть session_id от Stripe
        if ($sessionId) {
            // Получаем данные из сессии Stripe через API
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                $session = Session::retrieve($sessionId);
                $sessionArray = $session->toArray();
                $metadata = $sessionArray['metadata'] ?? [];

                $data = [
                    'session_id' => $sessionId,
                    'payment_status' => $session->payment_status ?? 'unpaid',
                    'email' => $metadata['email'] ?? $sessionArray['customer_details']['email'] ?? $sessionArray['customer_email'] ?? null,
                    'vin' => $metadata['vin'] ?? null,
                    'report_type' => $metadata['report_type'] ?? null,
                ];

                $result = OrderService::processStripeOrder($data);
                if ($result->getStatusCode() !== 200) {
                    Log::error('Stripe session retrieve error', ['error' => $result->getContent(), 'session_id' => $sessionId]);

                    return redirect()->to(\App\Support\LocaleRoute::route('index'));
                }
                $stripeData = json_decode($result->getContent(), true);
                if ($stripeData['success']) {
                    // Получаем заказ для определения локали
                    $stripeOrderId = $stripeData['order_id'] ?? null;
                    $order = $stripeOrderId ? Order::find($stripeOrderId) : null;
                    if (!$order) {
                        return redirect()->to(\App\Support\LocaleRoute::route('index'));
                    }

                    $orderLocale = $order->locale ?? LaravelLocalization::getDefaultLocale();
                    $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
                    if (! in_array($orderLocale, $supportedLocales, true)) {
                        $orderLocale = LaravelLocalization::getDefaultLocale();
                    }

                    // Убеждаемся, что order_key существует
                    if (!$order->order_key) {
                        $order->generateOrderKey();
                        $order->refresh();
                    }
                    $orderKey = $order->order_key;

                    $thankYouUrl = LaravelLocalization::getLocalizedURL($orderLocale, '/thank-you');
                    return redirect($thankYouUrl.'?id='.$stripeOrderId.'&key='.$orderKey);
                } else {
                    Log::error('Stripe process order error', ['error' => $stripeData['error'], 'session_id' => $sessionId]);

                    return redirect()->to(\App\Support\LocaleRoute::route('index'));
                }
            } catch (\Exception $e) {
                Log::error('Stripe process order error', ['error' => $e->getMessage(), 'session_id' => $sessionId]);

                return redirect()->to(\App\Support\LocaleRoute::route('index'));
            }
        }

        // Если есть id (для других способов оплаты)
        if ($orderId) {
            $orderKey = $request->input('key');
            $order = Order::find($orderId);

            // Проверяем order_key для безопасности - обязателен для всех заказов
            if (!$order) {
                Log::warning('Thank you page: Order not found', [
                    'order_id' => $orderId,
                ]);
                return redirect()->to(\App\Support\LocaleRoute::route('index'));
            }

            if (!$order->order_key) {
                // Если у заказа нет order_key, генерируем его
                $order->generateOrderKey();
                $order->refresh();
            }

            if (! $orderKey || $order->order_key !== $orderKey) {
                Log::warning('Thank you page: Invalid or missing order_key', [
                    'order_id' => $orderId,
                    'provided_key' => $orderKey,
                    'expected_key' => $order->order_key,
                ]);
                return redirect()->to(\App\Support\LocaleRoute::route('index'));
            }

            // Логика перевода заказа в 'paid' для report_balance перенесена в OrderController::checkOrderStatus()
            // чтобы страница thank-you отдавалась сразу, а генерация отчета происходила асинхронно через polling

            $paymentUrl = null;
            if ($order->payment_data) {
                $paymentData = is_array($order->payment_data) ? $order->payment_data : json_decode($order->payment_data, true);
                $paymentUrl = $paymentData['payment_url'] ?? null;
            }

            return view('thank-you', [
                'order_id' => $orderId,
                'order_key' => $orderKey,
                'paymentMethod' => $order->payment_method ?? 'other',
                'order' => $order,
                'paymentUrl' => $paymentUrl,
            ]);
        }

        // Если нет ни session_id, ни id - редирект на главную
        return redirect()->to(\App\Support\LocaleRoute::route('index'));
    }

    public function panel(Request $request)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->to(\App\Support\LocaleRoute::route('index'));
        }
        $vinSearch = trim((string) $request->query('vin', ''));
        $normalizedVinSearch = strtoupper(str_replace([' ', '-', '.'], '', $vinSearch));

        $ordersQuery = Order::select('id', 'vin', 'report_type', 'status', 'total_price', 'currency', 'created_at', 'report_key', 'order_purpose', 'report_to_add')
            ->where('user_id', $user->id);

        if ($normalizedVinSearch !== '') {
            $ordersQuery->where('vin', 'like', '%'.addcslashes($normalizedVinSearch, '%_\\').'%');
        }

        $orders = $ordersQuery
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Определяем валюту с учетом настройки multi_currency_enabled
        $currency = SettingController::getCurrency();
        $defaultCurrency = SettingController::getSetting('default_currency') ?? 'usd';

        // Получаем настройку для расчета цены отчетов
        $topupReportBalancePrice = SettingController::getSetting('topup_report_balance_price');

        return view('panel', compact('user', 'orders', 'topupReportBalancePrice', 'currency', 'vinSearch'));
    }

    /**
     * Обновить имя пользователя
     */
    public function updateName(Request $request): JsonResponse
    {
        $user = Auth::user();
        $cacheKey = 'user_update_name_' . $user->id;

        // Проверка на спам (раз в день - 24 часа)
        if (Cache::store('redis')->has($cacheKey)) {
            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.please_wait'),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Устанавливаем кеш на 24 часа (раз в день)
            Cache::store('redis')->put($cacheKey, true, now()->addDay());

            $user->name = $request->input('name');
            $user->save();

//            Log::info('User name updated', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => __('panel.settings.js.name_updated_success'),
                'name' => $user->name,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user name', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.error_updating_name'),
            ], 500);
        }
    }

    /**
     * Изменить пароль пользователя
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $user = Auth::user();
        $cacheKey = 'user_update_password_' . $user->id;

        // Проверка на спам (раз в день - 24 часа)
        if (Cache::store('redis')->has($cacheKey)) {
            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.please_wait'),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Устанавливаем кеш на 24 часа (раз в день)
            Cache::store('redis')->put($cacheKey, true, now()->addDay());

            $user->password = Hash::make($request->input('password'));
            $user->save();

//            Log::info('User password updated', ['user_id' => $user->id]);

            return response()->json([
                'success' => true,
                'message' => __('panel.settings.js.password_updated_success'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to update user password', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.error_updating_password'),
            ], 500);
        }
    }

    /**
     * Удалить аккаунт пользователя
     */
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = Auth::user();
        $cacheKey = 'user_delete_account_' . $user->id;

        // Проверка на спам (раз в день - 24 часа)
        if (Cache::store('redis')->has($cacheKey)) {
            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.please_wait'),
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'password' => ['required', 'current_password'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first(),
            ], 422);
        }

        try {
            // Устанавливаем кеш на 24 часа (раз в день)
            Cache::store('redis')->put($cacheKey, true, now()->addDay());

            $userId = $user->id;

            // Переносим данные пользователя в deleted_users перед удалением
            DeletedUser::create([
                'name' => $user->name,
                'email' => $user->email,
                'email_verified_at' => $user->email_verified_at,
                'role' => $user->role,
                'balance' => $user->balance,
                'report_balance' => $user->report_balance,
                'password' => $user->password,
                'remember_token' => $user->remember_token,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $user->delete();

//            Log::info('User account deleted', ['user_id' => $userId]);

            return response()->json([
                'success' => true,
                'message' => __('panel.settings.js.account_deleted_success'),
                'redirect' => \App\Support\LocaleRoute::route('index'),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete user account', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => __('panel.settings.js.error_deleting_account'),
            ], 500);
        }
    }

    /**
     * Обработка постов по /blog/{slug}.
     * Если для текущей локали нет перевода, редиректит на дефолтный язык.
     */
    public function post($slug): View|RedirectResponse
    {
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $urlLocale = request()->route('locale') ?: request()->segment(1);
        $hasLocalePrefix = $urlLocale && in_array($urlLocale, $supportedLocales, true);
        $currentLocale = $hasLocalePrefix ? $urlLocale : $defaultLocale;
        app()->setLocale($currentLocale);

        $slug = request()->route('slug') ?? $slug;
        $post = Post::with('categories')->where('slug', $slug)->where('status', 'published')->first();

        if (! $post) {
            abort(404);
        }

        // Обрабатываем переводы поста
        $translations = $post->translations ?? [];

        $hasDefaultContent = false;
        if (is_array($translations) && array_key_exists($defaultLocale, $translations)) {
            $defaultTranslation = $translations[$defaultLocale] ?? null;
            if ($defaultTranslation && is_array($defaultTranslation)) {
                $hasDefaultContent = (
                    (isset($defaultTranslation['title']) && trim($defaultTranslation['title']) !== '') ||
                    (isset($defaultTranslation['content']) && trim($defaultTranslation['content']) !== '')
                );
            }
        }

        if (! $hasDefaultContent) {
            $hasDefaultContent = (
                trim((string) $post->title) !== '' ||
                trim((string) $post->content) !== ''
            );
        }

        $hasCurrentLocaleContent = false;
        if ($currentLocale === $defaultLocale) {
            $hasCurrentLocaleContent = $hasDefaultContent;
        } else {
            $localeTranslation = $translations[$currentLocale] ?? null;
            if ($localeTranslation && is_array($localeTranslation)) {
                $hasCurrentLocaleContent = (
                    (isset($localeTranslation['title']) && trim($localeTranslation['title']) !== '') ||
                    (isset($localeTranslation['content']) && trim($localeTranslation['content']) !== '')
                );
            }
        }

        if (! $hasCurrentLocaleContent) {
            Log::warning('Post locale content missing', [
                'slug' => $slug,
                'route_locale' => request()->route('locale'),
                'segment_locale' => request()->segment(1),
                'current_locale' => $currentLocale,
                'default_locale' => $defaultLocale,
                'has_default_content' => $hasDefaultContent,
                'has_current_locale_content' => $hasCurrentLocaleContent,
                'translations_keys' => is_array($translations) ? array_keys($translations) : [],
                'translation_snapshot' => is_array($translations) ? ($translations[$currentLocale] ?? null) : null,
            ]);
            abort(404);
        }

        $displayTitle = $post->title;
        $displayContent = $post->content;
        $displayThumbnail = $post->thumbnail;
        $displayMetaTitle = $post->meta_title;
        $displayMetaDescription = $post->meta_description;
        $displayMetaKeywords = $post->meta_keywords;

        if (isset($translations[$currentLocale])) {
            $translation = $translations[$currentLocale];
            if (!empty($translation['title'])) {
                $displayTitle = $translation['title'];
            }
            if (!empty($translation['content'])) {
                $displayContent = $translation['content'];
            }
            if (!empty($translation['thumbnail'])) {
                $displayThumbnail = $translation['thumbnail'];
            }
            if (!empty($translation['meta_title'])) {
                $displayMetaTitle = $translation['meta_title'];
            }
            if (!empty($translation['meta_description'])) {
                $displayMetaDescription = $translation['meta_description'];
            }
            if (!empty($translation['meta_keywords'])) {
                $displayMetaKeywords = $translation['meta_keywords'];
            }
        }

        // Для недефолтной локали не подмешиваем meta_title из базовой записи,
        // если в переводе его нет: тогда в шаблоне используем локализованный title.
        if ($currentLocale !== $defaultLocale) {
            $localeTranslation = $translations[$currentLocale] ?? null;
            if (is_array($localeTranslation) && empty($localeTranslation['meta_title'])) {
                $displayMetaTitle = null;
            }
        }

        // Базовый путь без локали для канонического URL
        $basePath = '/blog/' . ltrim($slug, '/');

        // Формируем hreflang только по реально существующим переводам
        $hreflangUrls = [];
        $supportedLocales = config('laravellocalization.supportedLocales', []);

        foreach (array_keys($supportedLocales) as $localeCode) {
            // Дефолтная локаль — добавляем только если есть контент
            if ($localeCode === $defaultLocale) {
                if ($hasDefaultContent) {
                    $hreflangUrls[$localeCode] = url($basePath);
                }
                continue;
            }

            // Для не-дефолтных локалей проверяем наличие перевода
            $tr = $translations[$localeCode] ?? null;

            // Строгая проверка: должен быть хотя бы title или content (не пустые строки)
            $hasContent = false;
            if ($tr && is_array($tr)) {
                $hasContent = (
                    (isset($tr['title']) && trim($tr['title']) !== '') ||
                    (isset($tr['content']) && trim($tr['content']) !== '')
                );
            }

            // Добавляем в hreflang ТОЛЬКО если есть реальный контент
            if ($hasContent) {
                $hreflangUrls[$localeCode] = LaravelLocalization::getLocalizedURL(
                    $localeCode,
                    $basePath
                );
            }
        }

        // x-default указывает на дефолтную версию без префикса, если есть контент
        $xDefaultUrl = $hasDefaultContent ? url($basePath) : null;

        return view('post', compact(
            'post',
            'displayTitle',
            'displayContent',
            'displayThumbnail',
            'displayMetaTitle',
            'displayMetaDescription',
            'displayMetaKeywords',
            'hreflangUrls',
            'xDefaultUrl'
        ));
    }

    /**
     * Обработка страниц по /page/{slug}.
     * Если для текущей локали нет перевода, редиректит на дефолтный язык.
     */
    public function page($slug): View|RedirectResponse
    {
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $urlLocale = request()->route('locale') ?: request()->segment(1);
        $hasLocalePrefix = $urlLocale && in_array($urlLocale, $supportedLocales, true);
        $currentLocale = $hasLocalePrefix ? $urlLocale : $defaultLocale;
        app()->setLocale($currentLocale);

        $slug = request()->route('slug') ?? $slug;
        $page = Page::where('slug', $slug)->where('status', 'published')->first();

        if (! $page) {
            abort(404);
        }

        // Обрабатываем переводы страницы
        $translations = $page->translations ?? [];

        $hasDefaultContent = false;
        if (is_array($translations) && array_key_exists($defaultLocale, $translations)) {
            $defaultTranslation = $translations[$defaultLocale] ?? null;
            if ($defaultTranslation && is_array($defaultTranslation)) {
                $hasDefaultContent = (
                    (isset($defaultTranslation['title']) && trim($defaultTranslation['title']) !== '') ||
                    (isset($defaultTranslation['content']) && trim($defaultTranslation['content']) !== '')
                );
            }
        }

        if (! $hasDefaultContent) {
            $hasDefaultContent = (
                trim((string) $page->title) !== '' ||
                trim((string) $page->content) !== ''
            );
        }

        $hasCurrentLocaleContent = false;
        if ($currentLocale === $defaultLocale) {
            $hasCurrentLocaleContent = $hasDefaultContent;
        } else {
            $localeTranslation = $translations[$currentLocale] ?? null;
            if ($localeTranslation && is_array($localeTranslation)) {
                $hasCurrentLocaleContent = (
                    (isset($localeTranslation['title']) && trim($localeTranslation['title']) !== '') ||
                    (isset($localeTranslation['content']) && trim($localeTranslation['content']) !== '')
                );
            }
        }

        if (! $hasCurrentLocaleContent) {
            abort(404);
        }

        $displayTitle = $page->title;
        $displayContent = $page->content;
        $displayMetaTitle = $page->meta_title;
        $displayMetaDescription = $page->meta_description;
        $displayMetaKeywords = $page->meta_keywords;

        if (isset($translations[$currentLocale])) {
            $translation = $translations[$currentLocale];
            if (!empty($translation['title'])) {
                $displayTitle = $translation['title'];
            }
            if (!empty($translation['content'])) {
                $displayContent = $translation['content'];
            }
            if (!empty($translation['meta_title'])) {
                $displayMetaTitle = $translation['meta_title'];
            }
            if (!empty($translation['meta_description'])) {
                $displayMetaDescription = $translation['meta_description'];
            }
            if (!empty($translation['meta_keywords'])) {
                $displayMetaKeywords = $translation['meta_keywords'];
            }
        }

        // Для недефолтной локали не подмешиваем meta_title из базовой записи,
        // если в переводе его нет: тогда в шаблоне используем локализованный title.
        if ($currentLocale !== $defaultLocale) {
            $localeTranslation = $translations[$currentLocale] ?? null;
            if (is_array($localeTranslation) && empty($localeTranslation['meta_title'])) {
                $displayMetaTitle = null;
            }
        }

        // Базовый путь без локали для канонического URL
        $basePath = '/page/' . ltrim($slug, '/');

        // Формируем hreflang только по реально существующим переводам
        $hreflangUrls = [];
        $supportedLocales = config('laravellocalization.supportedLocales', []);

        foreach (array_keys($supportedLocales) as $localeCode) {
            // Дефолтная локаль — добавляем только если есть контент
            if ($localeCode === $defaultLocale) {
                if ($hasDefaultContent) {
                    $hreflangUrls[$localeCode] = url($basePath);
                }
                continue;
            }

            // Для не-дефолтных локалей проверяем наличие перевода
            $tr = $translations[$localeCode] ?? null;

            // Строгая проверка: должен быть хотя бы title или content (не пустые строки)
            $hasContent = false;
            if ($tr && is_array($tr)) {
                $hasContent = (
                    (isset($tr['title']) && trim($tr['title']) !== '') ||
                    (isset($tr['content']) && trim($tr['content']) !== '')
                );
            }

            // Добавляем в hreflang ТОЛЬКО если есть реальный контент
            if ($hasContent) {
                $hreflangUrls[$localeCode] = LaravelLocalization::getLocalizedURL(
                    $localeCode,
                    $basePath
                );
            }
        }

        // x-default указывает на дефолтную версию без префикса, если есть контент
        $xDefaultUrl = $hasDefaultContent ? url($basePath) : null;

        return view('page', compact(
            'page',
            'displayTitle',
            'displayContent',
            'displayMetaTitle',
            'displayMetaDescription',
            'displayMetaKeywords',
            'hreflangUrls',
            'xDefaultUrl'
        ));
    }
}
