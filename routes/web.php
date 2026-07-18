<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MonobankController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PlatonController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

require __DIR__ . '/auth.php';

$supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());

Route::middleware(['setLocaleFromUrl'])->group(function () use ($supportedLocales) {
    Route::get('/', [HomeController::class, 'index'])->middleware('cache.headers:max_age=3600;public')->name('index');
    Route::get('/author', [HomeController::class, 'author'])->middleware('cache.headers:max_age=3600;public')->name('author');
    Route::get('/blog', [BlogController::class, 'blog'])->middleware('cache.headers:max_age=3600;public')->name('blog');
    Route::get('/blog/{slug}', [HomeController::class, 'post'])->middleware('cache.headers:max_age=3600;public')->name('post');
    Route::get('/page/{slug}', [HomeController::class, 'page'])->middleware('cache.headers:max_age=3600;public')->name('page');
    Route::get('/thank-you', [HomeController::class, 'thankYou'])->name('thank-you')
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    Route::post('/thank-you', [HomeController::class, 'thankYouPost'])->name('thank-you.post')
        ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

    Route::prefix('{locale}')->whereIn('locale', $supportedLocales)->group(function () {
        Route::get('/', [HomeController::class, 'index'])->middleware('cache.headers:max_age=3600;public')->name('index.locale');
        Route::get('/author', [HomeController::class, 'author'])->middleware('cache.headers:max_age=3600;public')->name('author.locale');
        Route::get('/blog', [BlogController::class, 'blog'])->middleware('cache.headers:max_age=3600;public')->name('blog.locale');
        Route::get('/blog/{slug}', [HomeController::class, 'post'])->middleware('cache.headers:max_age=3600;public')->name('post.locale');
        Route::get('/page/{slug}', [HomeController::class, 'page'])->middleware('cache.headers:max_age=3600;public')->name('page.locale');
        Route::get('/thank-you', [HomeController::class, 'thankYou'])->name('thank-you.locale')
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
        Route::post('/thank-you', [HomeController::class, 'thankYouPost'])->name('thank-you.post.locale')
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    });
});

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localizationRedirect', 'localeViewPath'],
], function () {
    // Админские маршруты (должны быть ДО /{slug})
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard');
    Route::get('/dashboard/posts', [PostController::class, 'index'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.posts');
    Route::get('/dashboard/posts/create', [PostController::class, 'create'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.posts.create');
    Route::post('/dashboard/posts', [PostController::class, 'store'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.posts.store');
    Route::get('/dashboard/posts/{id}/edit', [PostController::class, 'edit'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.posts.edit');
    Route::put('/dashboard/posts/{id}', [PostController::class, 'update'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.posts.update');
    
    Route::get('/dashboard/pages', [PageController::class, 'index'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages');
    Route::get('/dashboard/pages/create', [PageController::class, 'create'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages.create');
    Route::post('/dashboard/pages', [PageController::class, 'store'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages.store');
    Route::get('/dashboard/pages/{id}/edit', [PageController::class, 'edit'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages.edit');
    Route::put('/dashboard/pages/{id}', [PageController::class, 'update'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages.update');
    Route::delete('/dashboard/pages/{id}', [PageController::class, 'destroy'])->middleware(['auth', 'verified', 'checkUserRole:admin'])->name('dashboard.pages.destroy');
    
    // Пользовательские маршруты (должны быть ДО /{slug})
    Route::middleware(['auth'])->group(function () {
        Route::get('/panel', [HomeController::class, 'panel'])->name('panel');
        Route::post('/panel/update-name', [HomeController::class, 'updateName'])->name('panel.update-name');
        Route::post('/panel/update-password', [HomeController::class, 'updatePassword'])->name('panel.update-password');
        Route::post('/panel/delete-account', [HomeController::class, 'deleteAccount'])->name('panel.delete-account');
    });
    
    // Админские маршруты (должны быть ДО /{slug})
    Route::middleware(['auth', 'checkUserRole:admin'])->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
        Route::post('/orders/{id}/resend-email', [OrderController::class, 'resendEmail'])->name('orders.resend-email');
        Route::post('/dashboard/orders/create', [AdminController::class, 'createOrder'])->name('dashboard.orders.create');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');
        
        // Управление пользователями
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::patch('/users/{id}', [UserController::class, 'update'])->name('users.update');
    });
    
    // API-маршруты (должны быть ДО /{slug}, чтобы не конфликтовать)
    Route::group(['prefix' => 'vin-report'], function () {
        Route::get('/{report_key}', [ReportController::class, 'viewReport'])->name('view-report');
        Route::post('/check-vin', [ReportController::class, 'checkVin'])->name('check-vin');
        Route::post('/purchase-report/{payment_method?}', [OrderController::class, 'purchaseReport'])->name('purchase-report');
    });

    Route::post('/order/check-status', [OrderController::class, 'checkOrderStatus'])->name('order.check-status');
    Route::post('/order/get-payment-url', [OrderController::class, 'getPaymentUrl'])->name('order.get-payment-url');

    Route::group(['prefix' => 'payment'], function () {
        // Route::post('/topup-balance', [BalanceController::class, 'topupBalance'])->name('topup.balance');
        Route::post('/topup-report-balance', [BalanceController::class, 'topupReportBalance'])->name('topup.report-balance');

        Route::post('/stripe/create-payment-intent', [PaymentController::class, 'createIntent'])->name('create.payment.intent');
        Route::post('/stripe/webhook', [PaymentController::class, 'stripeWebhook'])->name('stripe.webhook')
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        Route::post('/monobank/callback', [MonobankController::class, 'monobankCallback'])->name('monobank.callback')
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);

        Route::post('/platon/callback', [PlatonController::class, 'platonCallback'])->name('platon.callback')
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    });
    
});
