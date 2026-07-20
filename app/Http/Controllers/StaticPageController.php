<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * STAGING: simple static content pages (money-back, data-sources, and the legal
 * set: terms / privacy / refund / cookies). One method per page, each returns a
 * blade with its SEO meta. These replace the old DB-backed /page/{slug} entries so
 * the pages can never "disappear" when the database is reset.
 */
class StaticPageController extends Controller
{
    public function moneyBack(): View
    {
        return view('static.money-back', [
            'metaTitle'       => 'Money-Back Guarantee',
            'metaDescription' => 'Our money-back guarantee: if your report does not arrive within 24 hours, or it will not open or is empty, we refund you. No risk.',
        ]);
    }

    public function dataSources(): View
    {
        return view('static.data-sources', [
            'metaTitle'       => 'Data Sources',
            'metaDescription' => 'The official databases and records behind our VIN checks and vehicle-history reports: NHTSA vPIC, NHTSA recalls, NMVTIS, state title agencies, auction and salvage pools, NICB and more.',
        ]);
    }

    public function terms(): View
    {
        return view('static.terms', [
            'metaTitle'       => 'Terms & Conditions',
            'metaDescription' => 'The terms and conditions for using cheapvinreport.com and purchasing vehicle-history reports.',
        ]);
    }

    public function privacy(): View
    {
        return view('static.privacy', [
            'metaTitle'       => 'Privacy Policy',
            'metaDescription' => 'How cheapvinreport.com collects, uses and protects your personal data.',
        ]);
    }

    public function refund(): View
    {
        return view('static.refund', [
            'metaTitle'       => 'Refund Policy',
            'metaDescription' => 'When and how you can get a refund for a vehicle-history report purchased on cheapvinreport.com.',
        ]);
    }

    public function cookies(): View
    {
        return view('static.cookies', [
            'metaTitle'       => 'Cookie Policy',
            'metaDescription' => 'What cookies cheapvinreport.com uses, why we use them, and how you can control them.',
        ]);
    }
}
