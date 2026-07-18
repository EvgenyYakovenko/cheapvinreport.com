<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use App\Services\MonobankService;

class SettingService
{
    public static function getSetting($key)
    {
        return Cache::remember('setting_'.$key, now()->addMinutes(5), function () use ($key) {
            $setting = Setting::where('key', $key)->first();

            if (! $setting || $setting->value === null) {
                return null;
            }

            $value = $setting->value;

            // Пытаемся декодировать JSON, если это JSON строка
            if (is_string($value)) {
                $decoded = json_decode($value, true);
                // Если декодирование успешно и результат - массив или объект, возвращаем декодированное значение
                if (json_last_error() === JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) {
                    return $decoded;
                }
            }

            // Возвращаем значение как есть (строка, число и т.д.)
            return $value;
        });
    }

    /**
     * Конвертация суммы из одной валюты в другую с использованием курсов Monobank.
     *
     * - Курс кешируется в таблице settings с ключом mono_currency_{fromCode}_{toCode}
     * - Если записи нет, берём курс из MonobankService::getCurrency и сохраняем.
     *
     * @param  float  $amount        Сумма для конвертации
     * @param  string $fromCurrency  Валюта источника (usd, eur, uah, kzt, pln)
     * @param  string $toCurrency    Валюта назначения (по умолчанию uah)
     * @return float Конвертированная сумма (если курс не получен — возвращаем исходную сумму)
     */
    public static function convert(float $amount, string $fromCurrency, string $toCurrency = 'uah'): float
    {
        $fromCurrency = strtolower($fromCurrency);
        $toCurrency = strtolower($toCurrency);

        // Если валюты совпадают — конвертация не нужна
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }

        // Маппинг буквенных кодов в числовые коды Monobank
        $currencyCodeMap = [
            'uah' => 980,
            'usd' => 840,
            'eur' => 978,
            'kzt' => 398,
            'pln' => 985,
        ];

        $fromCode = $currencyCodeMap[$fromCurrency] ?? null;
        $toCode = $currencyCodeMap[$toCurrency] ?? null;

        // Если не знаем коды валют — возвращаем исходную сумму
        if (! $fromCode || ! $toCode) {
            return $amount;
        }

        $key = 'mono_currency_'.$fromCode.'_'.$toCode;

        // Пытаемся взять курс из настроек/кеша
        $rate = self::getSetting($key);
        if ($rate === null) {
            // Если курса нет в настройках — берём из Monobank и сохраняем
            $rate = MonobankService::getCurrency($fromCode, $toCode);

            if ($rate === null) {
                // Если не смогли получить курс — ничего не ломаем, возвращаем исходную сумму
                return $amount;
            }

            $setting = Setting::where('key', $key)->first();
            if ($setting) {
                $setting->value = (string) $rate;
                $setting->save();
            } else {
                Setting::create([
                    'key' => $key,
                    'value' => (string) $rate,
                ]);
            }

            Cache::forget('setting_'.$key);
        } else {
            $rate = (float) $rate;
        }

        return $amount * $rate;
    }
}
