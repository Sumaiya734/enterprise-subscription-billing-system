<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for various payment gateways used in Bangladesh
    |
    */

    'default_gateway' => env('PAYMENT_DEFAULT_GATEWAY', 'bkash'),

    /*
    |--------------------------------------------------------------------------
    | bKash Configuration
    |--------------------------------------------------------------------------
    */
    'bkash' => [
        'base_url' => env('BKASH_BASE_URL', 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'),
        'app_key' => env('BKASH_APP_KEY', ''),
        'app_secret' => env('BKASH_APP_SECRET', ''),
        'username' => env('BKASH_USERNAME', ''),
        'password' => env('BKASH_PASSWORD', ''),
        'sandbox' => env('BKASH_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Nagad Configuration
    |--------------------------------------------------------------------------
    */
    'nagad' => [
        'base_url' => env('NAGAD_BASE_URL', 'http://sandbox.mynagad.com:10080'),
        'merchant_id' => env('NAGAD_MERCHANT_ID', ''),
        'merchant_number' => env('NAGAD_MERCHANT_NUMBER', ''),
        'public_key' => env('NAGAD_PUBLIC_KEY', ''),
        'private_key' => env('NAGAD_PRIVATE_KEY', ''),
        'sandbox' => env('NAGAD_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | SSLCommerz Configuration (for card payments)
    |--------------------------------------------------------------------------
    */
    'sslcommerz' => [
        'base_url' => env('SSLCOMMERZ_BASE_URL', 'https://sandbox.sslcommerz.com'),
        'store_id' => env('SSLCOMMERZ_STORE_ID', ''),
        'store_password' => env('SSLCOMMERZ_STORE_PASSWORD', ''),
        'sandbox' => env('SSLCOMMERZ_SANDBOX', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rocket Configuration
    |--------------------------------------------------------------------------
    */
    'rocket' => [
        'merchant_number' => env('ROCKET_MERCHANT_NUMBER', '01XXXXXXXXX'),
        'instructions_enabled' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Bank Transfer Configuration
    |--------------------------------------------------------------------------
    */
    'bank_transfer' => [
        'bank_name' => env('BANK_NAME', 'Dutch Bangla Bank Limited'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Your Company Name'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
        'routing_number' => env('BANK_ROUTING_NUMBER', '090270001'),
        'swift_code' => env('BANK_SWIFT_CODE', 'DBBLBDDHXXX'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Methods
    |--------------------------------------------------------------------------
    */
    'methods' => [
        'bkash' => [
            'name' => 'bKash',
            'icon' => '📱',
            'description' => 'Pay with bKash mobile banking',
            'enabled' => env('BKASH_ENABLED', true),
            'fee' => 0, // Percentage fee
        ],
        'nagad' => [
            'name' => 'Nagad',
            'icon' => '💳',
            'description' => 'Pay with Nagad mobile banking',
            'enabled' => env('NAGAD_ENABLED', true),
            'fee' => 0,
        ],
        'rocket' => [
            'name' => 'Rocket',
            'icon' => '🚀',
            'description' => 'Pay with Dutch Bangla Rocket',
            'enabled' => env('ROCKET_ENABLED', true),
            'fee' => 0,
        ],
        'card' => [
            'name' => 'Credit/Debit Card',
            'icon' => '💳',
            'description' => 'Pay with Visa, MasterCard, or local cards',
            'enabled' => env('CARD_ENABLED', true),
            'fee' => 2.5, // 2.5% processing fee
        ],
        'bank_transfer' => [
            'name' => 'Bank Transfer',
            'icon' => '🏦',
            'description' => 'Direct bank transfer',
            'enabled' => env('BANK_TRANSFER_ENABLED', true),
            'fee' => 0,
        ],
        'cash' => [
            'name' => 'Cash Payment',
            'icon' => '💵',
            'description' => 'Pay in cash at our office',
            'enabled' => env('CASH_ENABLED', true),
            'fee' => 0,
        ],
    ],
];