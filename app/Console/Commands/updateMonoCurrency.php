<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\MonobankService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class updateMonoCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-mono-currency
                            {from=840 : Код валюты «из» (ISO 4217, напр. 840=USD)}
                            {to=980 : Код валюты «в» (ISO 4217, напр. 980=UAH)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить курс валютной пары из Monobank и сохранить в Setting для конвертации на сайте';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $from = (int) $this->argument('from');
        $to = (int) $this->argument('to');

        $rate = MonobankService::getCurrency($from, $to);

        if ($rate === null) {
            $this->error("Курс пары {$from} → {$to} не получен (API или пара недоступна).");
            return self::FAILURE;
        }

        $key = "mono_currency_{$from}_{$to}";

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

        $this->info("Курс {$from} → {$to} сохранён: {$rate} (ключ: {$key}).");
        return self::SUCCESS;
    }
}
