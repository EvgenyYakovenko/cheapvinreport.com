<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Определяем окружение
        $isProduction = app()->environment('production');
        $isLocal = app()->environment('local') ||
                   app()->environment('testing') ||
                   str_contains(request()->getHost(), 'localhost') ||
                   str_contains(request()->getHost(), '127.0.0.1');

        // Принудительно использовать HTTPS только в продакшене
        if ($isProduction && ! $isLocal) {
            URL::forceScheme('https');
            // same_site='none' требуется для cross-site запросов, secure=true обязателен для same_site='none'
            Config::set('session.secure', true);
            Config::set('session.same_site', 'none');

            // Удаляем файл hot, если он существует, чтобы Laravel использовал production-сборку Vite
            $hotFile = public_path('hot');
            if (file_exists($hotFile)) {
                @unlink($hotFile);
            }
        } else {
            // Для локальной разработки явно отключаем HTTPS
            URL::forceScheme('http');
            Config::set('session.secure', false);
            Config::set('session.same_site', 'lax');
        }

        ResetPassword::createUrlUsing(function ($notifiable, string $token) {
            $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
            $locale = app()->getLocale();
            $prefix = in_array($locale, $supportedLocales, true) ? '/'.$locale : '';

            return url($prefix.route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));
        });

        VerifyEmail::createUrlUsing(function ($notifiable) {
            $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
            $locale = app()->getLocale();
            $useLocale = in_array($locale, $supportedLocales, true) ? $locale : null;

            $routeName = $useLocale ? 'verification.verify.locale' : 'verification.verify';
            $params = [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ];
            if ($useLocale) {
                $params['locale'] = $useLocale;
            }

            return URL::temporarySignedRoute(
                $routeName,
                now()->addMinutes(config('auth.verification.expire', 60)),
                $params
            );
        });

        Order::observe(OrderObserver::class);
    }
}
