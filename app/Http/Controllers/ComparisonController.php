<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * STAGING: "Compare" cluster — cheapvinreport.com vs each competitor.
 * EN (dev handoff): one /compare/{slug} route renders from this registry, so a new
 * comparison = one entry here. Footer "Compare" column and /compare hub read it too.
 * Competitor prices/models are approximate list values and may change — keep updated.
 */
class ComparisonController extends Controller
{
    /** Feature rows shared by every comparison table: [label, our value]. */
    private const OUR = 'cheapvinreport.com';

    public const ITEMS = [
        'carfax' => [
            'name' => 'Carfax', 'label' => 'vs Carfax.com', 'price' => '$44.99', 'model' => 'Pay per report',
            'title' => 'cheapvinreport.com vs Carfax.com',
            'metaTitle' => 'cheapvinreport.com vs Carfax — same history, from $3.00',
            'metaDescription' => 'Carfax charges around $44.99 per report. cheapvinreport.com delivers the same USA vehicle history from $3.00. Full comparison.',
            'intro' => 'Carfax is the best-known vehicle-history brand in the US — and the most expensive. If you just need the accident, title and odometer history behind a VIN, you can get the same essentials for a fraction of the price.',
            'fair' => 'Carfax has the largest dealer service-record network and deep brand recognition, so a dealer or high-volume buyer who specifically wants the Carfax brand on file may still prefer it.',
        ],
        'autocheck' => [
            'name' => 'AutoCheck', 'label' => 'vs AutoCheck', 'price' => '$24.99', 'model' => 'Per report or bundles',
            'title' => 'cheapvinreport.com vs AutoCheck',
            'metaTitle' => 'cheapvinreport.com vs AutoCheck — cheaper VIN reports',
            'metaDescription' => 'AutoCheck (Experian) costs around $24.99. cheapvinreport.com delivers USA vehicle history from $3.00. See the full comparison.',
            'intro' => 'AutoCheck, by Experian, is popular at auctions for its numeric score and strong auction records. For everyday buyers who mainly need accidents, title and odometer, our reports cover the essentials for far less.',
            'fair' => 'If you buy at dealer auctions and rely on the AutoCheck Score to compare cars quickly, AutoCheck has a specific edge there.',
        ],
        'epicvin' => [
            'name' => 'EpicVIN', 'label' => 'vs EpicVIN', 'price' => '$19.99', 'model' => 'Per report or bundles',
            'title' => 'cheapvinreport.com vs EpicVIN',
            'metaTitle' => 'cheapvinreport.com vs EpicVIN — VIN reports from $3.00',
            'metaDescription' => 'EpicVIN runs about $19.99 per report. cheapvinreport.com delivers USA vehicle history from $3.00. Full side-by-side comparison.',
            'intro' => 'EpicVIN is a NMVTIS-based alternative to the big brands. It covers similar ground to us, but usually at a higher single-report price.',
            'fair' => 'EpicVIN bundles some dealer tools and its own extras that a reseller or dealer might find useful.',
        ],
        'bumper' => [
            'name' => 'Bumper', 'label' => 'vs Bumper', 'price' => '$19.99/mo', 'model' => 'Subscription',
            'title' => 'cheapvinreport.com vs Bumper',
            'metaTitle' => 'cheapvinreport.com vs Bumper — no subscription, from $3.00',
            'metaDescription' => 'Bumper works on a monthly subscription. cheapvinreport.com is pay-per-report from $3.00 with no subscription. Compare here.',
            'intro' => 'Bumper sells vehicle history through a monthly subscription with unlimited-style lookups. If you only need one or two reports, a subscription is poor value — pay per report instead.',
            'fair' => 'If you research dozens of cars every month, an unlimited subscription like Bumper can make sense for that volume.',
        ],
        'clearvin' => [
            'name' => 'ClearVIN', 'label' => 'vs ClearVIN', 'price' => '$14.99', 'model' => 'Per report or bundles',
            'title' => 'cheapvinreport.com vs ClearVIN',
            'metaTitle' => 'cheapvinreport.com vs ClearVIN — cheaper VIN history',
            'metaDescription' => 'ClearVIN runs around $14.99 per report. cheapvinreport.com delivers USA vehicle history from $3.00. Full comparison.',
            'intro' => 'ClearVIN is known for detailed salvage and auction data, including photos on some records. For the core history most buyers need, we deliver it for less.',
            'fair' => 'For salvage-auction buyers who want auction photos and detailed sale records, ClearVIN has a niche strength.',
        ],
        'carvertical' => [
            'name' => 'carVertical', 'label' => 'vs carVertical', 'price' => '$16.99', 'model' => 'Per report or packages',
            'title' => 'cheapvinreport.com vs carVertical',
            'metaTitle' => 'cheapvinreport.com vs carVertical — USA VIN reports from $3.00',
            'metaDescription' => 'carVertical focuses on European data. For US vehicles, cheapvinreport.com delivers history from $3.00. Compare here.',
            'intro' => 'carVertical is strongest for European vehicles. For US-market cars, our reports are focused on American records and cost less per report.',
            'fair' => 'If you are checking a European-registered car, carVertical\'s European coverage is its main advantage.',
        ],
        'carfaxcheaper' => [
            'name' => 'CarfaxCheaper', 'label' => 'vs CarfaxCheaper', 'price' => '$5.99', 'model' => 'Per report or subscription',
            'title' => 'cheapvinreport.com vs CarfaxCheaper',
            'metaTitle' => 'cheapvinreport.com vs CarfaxCheaper — from $3.00',
            'metaDescription' => 'CarfaxCheaper starts around $5.99. cheapvinreport.com delivers the same USA vehicle history from $3.00. Compare the two.',
            'intro' => 'CarfaxCheaper is another discount reseller. We cover the same core USA vehicle history — accidents, title, odometer and owners — at an even lower entry price.',
            'fair' => 'CarfaxCheaper offers membership and white-label options that suit resellers running at volume.',
        ],
        'cheapcarfax' => [
            'name' => 'CheapCarfax', 'label' => 'vs CheapCarfax', 'price' => '$9.99', 'model' => 'Per report',
            'title' => 'cheapvinreport.com vs CheapCarfax',
            'metaTitle' => 'cheapvinreport.com vs CheapCarfax — from $3.00',
            'metaDescription' => 'CheapCarfax runs around $9.99 per report. cheapvinreport.com delivers the same USA vehicle history from $3.00. Full comparison.',
            'intro' => 'CheapCarfax is a discount reseller with a similar product to ours. The main difference is price — we start lower.',
            'fair' => 'CheapCarfax offers brand-specific decoder pages and a browser extension some buyers like.',
        ],
        'carfaxdeals' => [
            'name' => 'CarfaxDeals', 'label' => 'vs CarfaxDeals', 'price' => '$9.99', 'model' => 'Per report',
            'title' => 'cheapvinreport.com vs CarfaxDeals',
            'metaTitle' => 'cheapvinreport.com vs CarfaxDeals — from $3.00',
            'metaDescription' => 'CarfaxDeals runs around $9.99 per report. cheapvinreport.com delivers the same USA vehicle history from $3.00. Compare here.',
            'intro' => 'CarfaxDeals is a discount reseller comparable to us. We deliver the same essential history at a lower starting price.',
            'fair' => 'CarfaxDeals may run promotions or bundles that appeal depending on how many reports you need.',
        ],
    ];

    /** Build the comparison table rows for a given competitor. */
    private function rows(array $item): array
    {
        return [
            ['Single report price', '$3.00', $item['price']],
            ['Accidents, title &amp; odometer', 'Included', 'Included'],
            ['Instant online delivery', 'Yes', 'Yes'],
            ['USA coverage', 'Yes', 'Yes'],
            ['Pricing model', 'Pay per report', $item['model']],
            ['Money-back guarantee', 'Yes', '—'],
        ];
    }

    public function index(): View
    {
        return view('comparisons.index', [
            'items'           => self::ITEMS,
            'metaTitle'       => __('compare.hub.meta_title'),
            'metaDescription' => __('compare.hub.meta_desc'),
        ]);
    }

    public function show(string $competitor): View
    {
        // STAGING: локализованный роут /{locale}/... передаёт {locale} первым позиционным
        // параметром, поэтому берём нужный параметр по имени из роута (работает и для /en, и для локалей).
        $competitor = request()->route('competitor');
        abort_unless(isset(self::ITEMS[$competitor]), 404);
        $item = self::ITEMS[$competitor];

        return view('comparisons.show', [
            'item'            => $item,
            'slug'            => $competitor,
            'our'             => self::OUR,
            'metaTitle'       => __("compare.items.$competitor.meta_title"),
            'metaDescription' => __("compare.items.$competitor.meta_desc"),
        ]);
    }
}
