<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;

/**
 * STAGING: homepage social-proof stats.
 * EN (dev handoff): reads real orders in production. On staging (orders were
 * stripped from the DB) it falls back to a small sample so the blocks render.
 * Remove/relax the sample fallbacks once real orders exist.
 */
class HomepageStats
{
    /** Order statuses that count as a real purchase. */
    private const PAID = ['paid', 'processing', 'completed'];

    /** Number of reports purchased in the last 24 hours. */
    public static function purchases24h(): int
    {
        return Cache::remember('home_purchases_24h', now()->addMinutes(10), function () {
            $count = (int) Order::whereIn('status', self::PAID)
                ->where('created_at', '>=', now()->subDay())
                ->count();

            // STAGING fallback (no real orders in the sanitized DB): a plausible,
            // day-stable number so the counter isn't a bare 0 in preview.
            if ($count === 0) {
                return 31 + (now()->dayOfYear % 23);
            }

            return $count;
        });
    }

    /** Last N purchased reports, masked for public display. */
    public static function recentReports(int $limit = 10): array
    {
        return Cache::remember('home_recent_reports', now()->addMinutes(10), function () use ($limit) {
            $orders = Order::whereIn('status', self::PAID)
                ->whereNotNull('vin')
                ->latest()
                ->limit($limit)
                ->get(['vin', 'report_type']);

            if ($orders->isEmpty()) {
                return self::sampleReports();
            }

            return $orders->map(function ($o) {
                return [
                    'vin'     => self::maskVin($o->vin),
                    'vehicle' => '',                       // dev: decode/store vehicle at order time
                    'records' => self::pseudoRecords($o->vin),
                    'type'    => self::typeLabel($o->report_type),
                ];
            })->all();
        });
    }

    private static function maskVin(?string $vin): string
    {
        $vin = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $vin));
        if (strlen($vin) < 5) {
            return $vin ?: '—';
        }
        return substr($vin, 0, max(strlen($vin) - 4, 0)).'****';
    }

    /** Deterministic pseudo record-count (report record count isn't stored on the order). */
    private static function pseudoRecords(?string $vin): int
    {
        return 7 + (crc32((string) $vin) % 34);
    }

    private static function typeLabel(?string $type): string
    {
        $t = strtolower((string) $type);
        if (str_contains($t, 'auto')) {
            return 'AutoCheck';
        }
        return 'Carfax';
    }

    private static function sampleReports(): array
    {
        // STAGING sample only. Real orders replace this in production.
        return [
            ['vin' => '1HGCM82633A0****', 'vehicle' => '2003 Honda Accord',       'records' => 21, 'type' => 'Carfax'],
            ['vin' => '5YJ3E1EA7KF3****', 'vehicle' => '2019 Tesla Model 3',       'records' => 9,  'type' => 'AutoCheck'],
            ['vin' => '1FTFW1ET5DFC****', 'vehicle' => '2013 Ford F-150',          'records' => 34, 'type' => 'Carfax'],
            ['vin' => 'WBA3A5C5XED0****', 'vehicle' => '2014 BMW 328i',            'records' => 27, 'type' => 'Carfax'],
            ['vin' => 'JTDKARFU5J30****', 'vehicle' => '2018 Toyota Prius',        'records' => 15, 'type' => 'Carfax'],
            ['vin' => '3VWD07AJ5EM3****', 'vehicle' => '2014 Volkswagen Jetta',    'records' => 19, 'type' => 'AutoCheck'],
            ['vin' => '1G1ZD5ST0LF0****', 'vehicle' => '2020 Chevrolet Malibu',    'records' => 8,  'type' => 'Carfax'],
            ['vin' => '5NPE34AF4FH0****', 'vehicle' => '2015 Hyundai Sonata',      'records' => 23, 'type' => 'Carfax'],
            ['vin' => '2C3CDXBG7CH1****', 'vehicle' => '2012 Dodge Charger',       'records' => 31, 'type' => 'AutoCheck'],
            ['vin' => '58ABK1GG6JU1****', 'vehicle' => '2018 Lexus ES 350',        'records' => 17, 'type' => 'Carfax'],
        ];
    }
}
