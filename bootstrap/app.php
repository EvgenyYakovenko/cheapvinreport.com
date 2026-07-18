<?php

use App\Http\Middleware\CheckUserRole;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        api: __DIR__.'/../routes/api.php',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // STAGING: block search-engine indexing on any non-production env.
        // No-op when APP_ENV=production (see App\Http\Middleware\ForceNoIndex).
        // Safe to keep permanently after merging into the main repo.
        $middleware->append(\App\Http\Middleware\ForceNoIndex::class);

        $middleware->validateCsrfTokens(except: [
            '*/thank-you.post',
            '*/thank-you',
            '*/payment/stripe/webhook',
            '*/payment/monobank/callback',
            '*/payment/platon/callback',
            '*/payment/hutko/callback',
        ]);
        $middleware->alias([
            'localize' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRoutes::class,
            'localizationRedirect' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationRedirectFilter::class,
            'localeSessionRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleSessionRedirect::class,
            'localeCookieRedirect' => \Mcamara\LaravelLocalization\Middleware\LocaleCookieRedirect::class,
            'localeViewPath' => \Mcamara\LaravelLocalization\Middleware\LaravelLocalizationViewPath::class,
            'checkUserRole' => CheckUserRole::class,
            'setLocaleFromUrl' => \App\Http\Middleware\SetLocaleFromUrl::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
