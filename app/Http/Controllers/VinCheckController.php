<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * STAGING: "VIN Check Services" — SEO funnel landing pages, one per record type that
 * actually appears in a Carfax / AutoCheck report. Each page: enter VIN -> we decode
 * it (NHTSA, for credibility) -> "records for this VIN are available in the full
 * report" -> buy CTA. One /vin-check/{slug} route renders from this registry; the
 * footer "VIN Check Services" column and the /vin-check hub read it too.
 */
class VinCheckController extends Controller
{
    /** slug => [label, title, metaTitle, metaDescription, source, shows, intro] */
    public const CHECKS = [
        'accident-history' => [
            'label' => 'Accident History', 'source' => 'Carfax & AutoCheck',
            'title' => 'Accident History Check by VIN',
            'metaTitle' => 'Accident History Check by VIN — Carfax & AutoCheck',
            'metaDescription' => 'Check a car\'s reported accident history by VIN. Records come from the full Carfax and AutoCheck report — from $3.00.',
            'shows' => 'reported accidents, damage severity and airbag deployment',
            'intro' => 'Find out whether a car has reported accidents before you buy. Enter the VIN and we\'ll confirm a report is available — the full Carfax/AutoCheck report shows the accident details.',
        ],
        'title-check' => [
            'label' => 'Title Check', 'source' => 'Carfax & AutoCheck',
            'title' => 'Title Check by VIN',
            'metaTitle' => 'Title Check by VIN — salvage, flood & rebuilt brands',
            'metaDescription' => 'Check a vehicle title by VIN for salvage, flood, rebuilt and lemon brands. Records from the full Carfax/AutoCheck report — from $3.00.',
            'shows' => 'title brands such as salvage, flood, rebuilt, junk and lemon',
            'intro' => 'A branded title can wipe out a car\'s value and safety. Enter the VIN to confirm a report is available — the full report reveals any title brands on record.',
        ],
        'odometer-check' => [
            'label' => 'Odometer Check', 'source' => 'Carfax & AutoCheck',
            'title' => 'Odometer Check by VIN',
            'metaTitle' => 'Odometer Check by VIN — mileage & rollback records',
            'metaDescription' => 'Check odometer readings and rollback flags by VIN. Records from the full Carfax/AutoCheck report — from $3.00.',
            'shows' => 'odometer readings over time and mileage-rollback flags',
            'intro' => 'Odometer fraud is common on used cars. Enter the VIN to confirm a report is available — the full report lists the mileage readings on record.',
        ],
        'owner-history' => [
            'label' => 'Owner History', 'source' => 'Carfax & AutoCheck',
            'title' => 'Owner History Check by VIN',
            'metaTitle' => 'Owner History Check by VIN — previous owners & use',
            'metaDescription' => 'Check how many previous owners a car had and how it was used, by VIN. Records from the full Carfax/AutoCheck report — from $3.00.',
            'shows' => 'the number of previous owners and how the car was used (personal, fleet, rental)',
            'intro' => 'Fewer owners and personal use usually mean a better-kept car. Enter the VIN to confirm a report is available — the full report shows the ownership timeline.',
        ],
        'service-history' => [
            'label' => 'Service History', 'source' => 'Carfax',
            'title' => 'Service History Check by VIN',
            'metaTitle' => 'Service & Maintenance History Check by VIN',
            'metaDescription' => 'Check a car\'s reported service and maintenance history by VIN. Records from the full Carfax report — from $3.00.',
            'shows' => 'service and maintenance records reported over the car\'s life',
            'intro' => 'A documented service history is a sign of a cared-for car. Enter the VIN to confirm a report is available — the full report lists the service records on file.',
        ],
        'auction-records' => [
            'label' => 'Auction Records', 'source' => 'AutoCheck',
            'title' => 'Auction Records Check by VIN',
            'metaTitle' => 'Auction Records Check by VIN — sales & announcements',
            'metaDescription' => 'Check dealer-auction sale records and condition announcements by VIN. Records from the full AutoCheck report — from $3.00.',
            'shows' => 'auction sale records and condition announcements',
            'intro' => 'Auction announcements often reveal problems a seller won\'t mention. Enter the VIN to confirm a report is available — the full report shows auction records.',
        ],
        'theft-check' => [
            'label' => 'Theft & Total Loss', 'source' => 'Carfax',
            'title' => 'Theft & Total Loss Check by VIN',
            'metaTitle' => 'Theft & Total Loss Check by VIN',
            'metaDescription' => 'Check for reported theft and total-loss records by VIN. Records from the full Carfax report — from $3.00.',
            'shows' => 'reported theft and insurance total-loss records',
            'intro' => 'A prior theft recovery or total loss can hide serious damage. Enter the VIN to confirm a report is available — the full report shows theft and total-loss records.',
        ],
    ];

    public function index(): View
    {
        return view('vin-checks.index', [
            'checks'          => self::CHECKS,
            'metaTitle'       => __('vincheck.hub.meta_title'),
            'metaDescription' => __('vincheck.hub.meta_desc'),
        ]);
    }

    public function show(string $check): View
    {
        // STAGING: локализованный роут /{locale}/... передаёт {locale} первым позиционным
        // параметром, поэтому берём нужный параметр по имени из роута (работает и для /en, и для локалей).
        $check = request()->route('check');
        abort_unless(isset(self::CHECKS[$check]), 404);
        $data = self::CHECKS[$check];

        return view('vin-checks.show', [
            'check'           => $data,
            'slug'            => $check,
            'metaTitle'       => __("vincheck.checks.$check.meta_title"),
            'metaDescription' => __("vincheck.checks.$check.meta_desc"),
        ]);
    }
}
