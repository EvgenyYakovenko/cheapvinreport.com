<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * STAGING: simple static content pages (money-back, and later about / are-we-legit /
 * disclaimer). One method per page, each returns a blade with its SEO meta.
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
}
