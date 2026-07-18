<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (app()->environment(['local', 'testing'])) {
            User::query()->updateOrCreate(
                ['email' => config('app.seed_admin.email', 'admin@example.com')],
                [
                    'name' => config('app.seed_admin.name', 'Admin'),
                    'password' => Hash::make(config('app.seed_admin.password', 'password')),
                    'role' => 'admin',
                    'report_balance' => 0,
                ]
            );
        }

        $settings = [
            'default_currency' => 'usd',
            'carfax_price' => '{"usd": 10, "uah": 300, "pln": 40, "kzt": 400}',
            'autocheck_price' => '{"usd": 10, "uah": 300, "pln": 40, "kzt": 400}',
            'auctions_price' => '{"usd": 10, "uah": 300, "pln": 40, "kzt": 400}',
            'sticker_price' => '{"usd": 10, "uah": 300, "pln": 40, "kzt": 400}',
            'topup_report_balance_price' => '{"1-4": {"usd": 4, "uah": 120, "pln": 16, "kzt": 1600}, "5-24": {"usd": 3, "uah": 90, "pln": 12, "kzt": 1200}, "25-99": {"usd": 2.5, "uah": 75, "pln": 10, "kzt": 1000}, "100-999999": {"usd": 2, "uah": 60, "pln": 8, "kzt": 800}}',
            'currency_mapping' => '{"en": "usd", "uk": "uah", "pl": "pln", "ru": "usd", "kk": "kzt"}',
            'topup_report_locker' => '["5", "25", "100"]',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
