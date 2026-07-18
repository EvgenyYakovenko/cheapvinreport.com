<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class LocaleRoute
{
    public static function route(string $name, array $params = []): string
    {
        $currentLocale = app()->getLocale();
        $defaultLocale = LaravelLocalization::getDefaultLocale();

        if ($currentLocale !== $defaultLocale && Route::has($name.'.locale')) {
            if (! array_key_exists('locale', $params)) {
                $params['locale'] = $currentLocale;
            }

            return route($name.'.locale', $params);
        }

        return route($name, $params);
    }
}
