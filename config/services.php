<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    'monobank' => [
        'token' => env('MONOBANK_TOKEN'),
    ],

    'monolith' => [
        'url' => env('API_MONOLITH_URL'),
        'key' => env('API_MONOLITH_KEY'),
    ],

    'merchant_secret_key' => env('MERCHANT_SECRET_KEY', 'flk3409refn54t54t*FNJRET'),

    'platon' => [
        'enabled' => env('PLATON_ENABLED', false),
        'sandbox' => env('PLATON_SANDBOX', true),
        'merchant_id' => env('PLATON_MERCHANT_ID'),
        'password' => env('PLATON_PASSWORD'),
        'api_url' => env('PLATON_API_URL', 'https://secure.platononline.com/payment/auth'),
        'status_url' => env('PLATON_STATUS_URL', 'https://secure.platononline.com/post-unq/'),
        'callback_url' => env('PLATON_CALLBACK_URL'),
        'result_url' => env('PLATON_RESULT_URL'),
        'currency' => env('PLATON_CURRENCY', 'UAH'),
        'language' => env('PLATON_LANGUAGE', 'en'),
    ],

];
