<?php

return [
    /*
    |--------------------------------------------------------------------------
    | hCaptcha Site Key
    |--------------------------------------------------------------------------
    |
    | Your public site key from hCaptcha dashboard.
    | This key is safe to expose in frontend code.
    |
    */

    'site_key' => env('HCAPTCHA_SITE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | hCaptcha Secret Key
    |--------------------------------------------------------------------------
    |
    | Your secret key from hCaptcha dashboard.
    | This key should NEVER be exposed in frontend code.
    |
    */

    'secret_key' => env('HCAPTCHA_SECRET_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | hCaptcha Verify URL
    |--------------------------------------------------------------------------
    |
    | The URL endpoint for verifying hCaptcha tokens.
    |
    */

    'verify_url' => env('HCAPTCHA_VERIFY_URL', 'https://api.hcaptcha.com/siteverify'),

    'test_mode' => env('HCAPTCHA_TEST_MODE', false),

    'trust_seconds' => env('HCAPTCHA_TRUST_SECONDS', 600),

    'trust_max_checks' => env('HCAPTCHA_TRUST_MAX_CHECKS', 10),
];

