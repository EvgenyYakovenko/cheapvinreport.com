<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Setting extends Model
{
    /**
     * Разрешаем массовое заполнение полей key и value,
     * чтобы можно было безопасно вызывать Setting::create([...]).
     */
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getPriceFromAmountOfReports($amount, $currency = 'usd')
    {
        $setting = self::where('key', 'topup_report_balance_price')->first();
        if (! $setting) {
            Log::error('Setting: Setting not found');

            return null;
        }
        $priceValue = $setting->value;
        $priceRanges = json_decode($priceValue, true);
        if (! $priceRanges || ! is_array($priceRanges)) {
            Log::error('Setting: Price ranges not found');

            return null;
        }

        // Новый формат: фиксированные пакеты по количеству (например: "5", "25", "100")
        if (isset($priceRanges[(string) $amount]) || isset($priceRanges[(int) $amount])) {
            $packageKey = isset($priceRanges[(string) $amount]) ? (string) $amount : (int) $amount;
            $pricesByCurrency = $priceRanges[$packageKey];
            if (is_array($pricesByCurrency)) {
                $totalPrice = $pricesByCurrency[$currency] ?? $pricesByCurrency['usd'] ?? null;
                if ($totalPrice !== null && is_numeric($totalPrice)) {
                    return (float) $totalPrice;
                }
            } elseif (is_numeric($pricesByCurrency)) {
                return (float) $pricesByCurrency;
            }
        }

        // Старый формат: диапазоны цен за отчет (price per report)
        foreach ($priceRanges as $range => $pricesByCurrency) {
            $rangeParts = explode('-', $range);
            if (count($rangeParts) !== 2) {
                continue;
            }

            $min = (int) $rangeParts[0];
            $max = (int) $rangeParts[1];

            if ($amount >= $min && $amount <= $max) {
                // Если pricesByCurrency - это массив с валютами
                if (is_array($pricesByCurrency)) {
                    $pricePerReport = $pricesByCurrency[$currency] ?? $pricesByCurrency['usd'] ?? null;
                    if ($pricePerReport !== null && is_numeric($pricePerReport)) {
                        $totalPrice = $amount * (float) $pricePerReport;
//                        Log::info('Setting: Price from amount of reports: '.$totalPrice.' for currency: '.$currency);

                        return $totalPrice;
                    }
                }
                // Если pricesByCurrency - это число (старый формат)
                elseif (is_numeric($pricesByCurrency)) {
                    $totalPrice = $amount * (float) $pricesByCurrency;
//                    Log::info('Setting: Price from amount of reports: '.$totalPrice);

                    return $totalPrice;
                }
            }
        }

        // Если не найден диапазон, используем последний доступный
        $ranges = array_keys($priceRanges);
        if (count($ranges) > 0) {
            $lastRange = end($ranges);
            $lastRangeParts = explode('-', $lastRange);
            if (count($lastRangeParts) === 2) {
                $lastMax = (int) $lastRangeParts[1];
                if ($amount > $lastMax) {
                    $lastPrices = end($priceRanges);
                    if (is_array($lastPrices)) {
                        $pricePerReport = $lastPrices[$currency] ?? $lastPrices['usd'] ?? null;
                        if ($pricePerReport !== null && is_numeric($pricePerReport)) {
                            $totalPrice = $amount * (float) $pricePerReport;
//                            Log::info('Setting: Price from amount of reports (last range): '.$totalPrice.' for currency: '.$currency);

                            return $totalPrice;
                        }
                    } elseif (is_numeric($lastPrices)) {
                        $totalPrice = $amount * (float) $lastPrices;
//                        Log::info('Setting: Price from amount of reports (last range): '.$totalPrice);

                        return $totalPrice;
                    }
                }
            }
        }

        return null;
    }
}
