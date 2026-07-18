<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocaleFromUrl
{
    public function handle(Request $request, Closure $next)
    {
        $supportedLocales = array_keys(LaravelLocalization::getSupportedLocales());
        $defaultLocale = LaravelLocalization::getDefaultLocale();
        $urlLocale = $request->segment(1);

        $locale = in_array($urlLocale, $supportedLocales, true) ? $urlLocale : $defaultLocale;
        app()->setLocale($locale);

        return $next($request);
    }
}
