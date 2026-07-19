<?php

/**
 * STAGING: real customer reviews copied from our own Etsy shop (Cheapvinreport,
 * 226 sales, 5.0 stars, 35 reviews). Shown as testimonials on the homepage and the
 * /reviews page, always linked back to the Etsy source for verification.
 *
 * EN (dev handoff): we deliberately do NOT emit self-hosted Review/AggregateRating
 * schema for these (Google disallows self-serving review markup). Credibility comes
 * from the outbound link to the real Etsy reviews page. For rich-result stars, wire
 * a licensed third-party widget (Trustpilot / Google Business Profile) later.
 */
return [
    'etsy' => [
        'rating' => '5.0',
        'count'  => 32,
        'sales'  => 226,
        'url'    => 'https://www.etsy.com/shop/Cheapvinreport/reviews',
    ],

    // Reviews that include written feedback (rating-only reviews omitted here).
    'items' => [
        ['name' => 'Jeremy',     'date' => 'Dec 2025', 'text' => 'Excellent seller, great value, highly recommended.'],
        ['name' => 'Michael',    'date' => 'Oct 2025', 'text' => 'Quick and cheap! A great find!'],
        ['name' => 'Randy',      'date' => 'Oct 2025', 'text' => 'Quick delivery! Highly recommend! Great seller!'],
        ['name' => 'Bobby',      'date' => 'Oct 2025', 'text' => 'Quick and legit for what I needed.'],
        ['name' => 'Beloved',    'date' => 'Oct 2025', 'text' => 'Great value, will buy again.'],
        ['name' => 'Ed',         'date' => 'Oct 2025', 'text' => 'Quick and easy process. Download available quickly.'],
        ['name' => 'Marcos',     'date' => 'Dec 2025', 'text' => 'As described. Very quick delivery.'],
        ['name' => 'Jason',      'date' => 'Dec 2025', 'text' => 'Exactly as described in store.'],
        ['name' => 'Etsy buyer', 'date' => 'Dec 2025', 'text' => 'Awesome service, for a great price.'],
        ['name' => 'Etsy buyer', 'date' => 'Dec 2025', 'text' => 'It was fast and safe, I was pleased.'],
        ['name' => 'Alncojan',   'date' => 'Dec 2025', 'text' => 'Received fast delivery. Thank you.'],
        ['name' => 'Anita',      'date' => 'Nov 2025', 'text' => 'This was very quick and easy to use.'],
        ['name' => 'Evan',       'date' => 'Nov 2025', 'text' => 'Good price for the carfax.'],
        ['name' => 'Marcos',     'date' => 'Oct 2025', 'text' => 'As described, quick delivery, great price.'],
        ['name' => 'Jeremy',     'date' => 'Oct 2025', 'text' => 'Exactly as described. Submit your VIN and receive your results.'],
        ['name' => 'Luis',       'date' => 'Oct 2025', 'text' => 'Fast and easy, no complaints.'],
        ['name' => 'Ankit',      'date' => 'Oct 2025', 'text' => 'It was fast and good.'],
        ['name' => 'Jawid',      'date' => 'Oct 2025', 'text' => 'Very fast to deliver the documents.'],
        ['name' => 'Carlos',     'date' => 'Oct 2025', 'text' => 'Perfect, the information came quickly.'],
        ['name' => 'Jovani',     'date' => 'Oct 2025', 'text' => '100% legit.'],
        ['name' => 'Ryan',       'date' => 'Oct 2025', 'text' => 'Arrived in download format. As described.'],
        ['name' => 'Cristian',   'date' => 'Oct 2025', 'text' => 'Very good service and fast.'],
    ],
];
