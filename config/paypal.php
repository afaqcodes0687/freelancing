<?php

return [
    // sandbox or live
    'mode' => env('PAYPAL_MODE', 'sandbox'),

    // Sandbox API credentials
    'sandbox' => [
        'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id' => env('PAYPAL_SANDBOX_APP_ID', ''),
    ],

    // Live API credentials
    'live' => [
        'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
    ],

    // Payment action. Can be 'Sale', 'Authorization', 'Order'
    'payment_action' => 'Sale',

    // Currency configuration
    // Note: PayPal supports specific currencies; use USD by default for sandbox.
    'currency' => env('PAYPAL_CURRENCY', 'USD'),
    'currency_code' => env('PAYPAL_CURRENCY', 'USD'),

    // Locale (optional)
    'locale' => env('PAYPAL_LOCALE', 'en_US'),

    // Validate SSL when creating API client
    'validate_ssl' => env('PAYPAL_VALIDATE_SSL', true),

    // Logging
    'log' => [
        'enabled' => env('PAYPAL_LOG_ENABLED', true),
        'file' => storage_path('logs/paypal.log'),
        'level' => env('PAYPAL_LOG_LEVEL', 'DEBUG'), // DEBUG, INFO, NOTICE, WARNING, ERROR, CRITICAL, ALERT, EMERGENCY
    ],
];
