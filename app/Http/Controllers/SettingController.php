<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\SettingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->keyBy('key');

        // Получаем все активные языки из конфига
        $supportedLocales = config('laravellocalization.supportedLocales', []);
        $activeLocales = array_filter($supportedLocales, function ($locale) {
            return ! empty($locale);
        });

        return view('settings.index', compact('settings', 'activeLocales'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'sometimes|array',
            'price' => 'sometimes|array',
            'currency_mapping' => 'sometimes|array',
        ]);

        // Обработка цен по валютам
        if ($request->has('price')) {
            $priceData = $request->input('price');
            foreach ($priceData as $reportType => $currencies) {
                $key = $reportType.'_price';
                $value = json_encode($currencies);

                $setting = Setting::where('key', $key)->first();
                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                } else {
                    Setting::create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
                // Очищаем кеш для этого ключа
                Cache::forget('setting_'.$key);
            }
        }

        // Обработка currency_mapping
        if ($request->has('currency_mapping')) {
            $currencyMapping = json_encode($request->input('currency_mapping'));
            $setting = Setting::where('key', 'currency_mapping')->first();
            if ($setting) {
                $setting->value = $currencyMapping;
                $setting->save();
            } else {
                Setting::create([
                    'key' => 'currency_mapping',
                    'value' => $currencyMapping,
                ]);
            }
            // Очищаем кеш для currency_mapping
            Cache::forget('setting_currency_mapping');
        }

        // Обработка остальных настроек
        if ($request->has('settings')) {
            foreach ($request->input('settings') as $key => $value) {
                $setting = Setting::where('key', $key)->first();

                // Обработка topup_report_balance_price
                if ($key === 'topup_report_balance_price') {
                    if (is_array($value)) {
                        $value = json_encode($value);
                    } elseif (is_string($value)) {
                        $decoded = json_decode($value, true);
                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $value = $value;
                        }
                    }
                }

                if ($setting) {
                    $setting->value = $value;
                    $setting->save();
                } else {
                    Setting::create([
                        'key' => $key,
                        'value' => $value,
                    ]);
                }
                // Очищаем кеш для этого ключа
                Cache::forget('setting_'.$key);
            }
        }

//        Log::info('Settings updated', [
//            'updated_keys' => array_merge(
//                array_keys($request->input('settings', [])),
//                array_keys($request->input('price', [])),
//                ['currency_mapping']
//            ),
//        ]);

        return redirect()->route('settings.index')->with('success', 'Настройки успешно обновлены!');
    }

    public static function getSetting($key)
    {
        return SettingService::getSetting($key) ?? null;
    }

    /**
     * Определяет валюту с учетом настройки мультивалютности из конфига
     * Если мультивалютность отключена, всегда возвращает default_currency
     *
     * @param string|null $language Язык для определения валюты (если не указан, берется из app()->getLocale())
     * @return string Код валюты (usd, uah, pln, kzt)
     */
    public static function getCurrency(?string $language = null): string
    {
        // Проверяем, включена ли мультивалютность из конфига
        $multiCurrencyEnabled = config('multicurrency.enabled', true);

        // Если мультивалютность отключена, всегда используем дефолтную валюту
        if (!$multiCurrencyEnabled) {
            return self::getSetting('default_currency') ?? 'usd';
        }

        // Если мультивалютность включена, используем стандартную логику
        if ($language === null) {
            $language = app()->getLocale();
        }
        // Убираем региональные коды (uk-UA -> uk)
        $language = explode('-', $language)[0];

        $currencyMapping = self::getSetting('currency_mapping') ?? [];
        $currency = is_array($currencyMapping) ? ($currencyMapping[$language] ?? null) : null;
        $defaultCurrency = self::getSetting('default_currency') ?? 'usd';

        return $currency ?? $defaultCurrency;
    }
}
