<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * STAGING: Reviews page. Shows all written customer reviews (from config/reviews.php),
 * copied from our Etsy shop and linked back there for verification.
 */
class ReviewController extends Controller
{
    public function index(): View
    {
        $etsy = config('reviews.etsy', []);

        return view('reviews', [
            'metaTitle'       => 'Customer Reviews',
            'metaDescription' => 'Real customer reviews for cheapvinreport.com — '
                . ($etsy['rating'] ?? '5.0') . ' stars from ' . ($etsy['count'] ?? '35')
                . ' verified buyers on Etsy.',
        ]);
    }
}
