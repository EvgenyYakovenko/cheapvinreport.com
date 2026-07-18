<?php

namespace App\Helpers;

class CurrencyHelper
{
    /**
     * Получить символ валюты по коду
     */
    public static function getCurrencySymbol(string $currency): string
    {
        $currencySymbols = [
            'usd' => '$',
            'uah' => '₴',
            'pln' => 'zł',
            'kzt' => '₸',
        ];

        return $currencySymbols[strtolower($currency)] ?? '$';
    }
}
