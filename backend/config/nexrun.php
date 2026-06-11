<?php

return [

    /*
    |--------------------------------------------------------------------------
    | NexRun Application Configuration
    |--------------------------------------------------------------------------
    */

    'name' => env('APP_NAME', 'NexRun'),
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),

    /*
    |--------------------------------------------------------------------------
    | Order Statuses
    |--------------------------------------------------------------------------
    */

    'order_statuses' => [
        'pending',
        'paid',
        'processing',
        'packed',
        'shipped',
        'delivered',
        'returned',
        'refunded',
        'cancelled',
    ],

    'order_transitions' => [
        'pending'    => ['paid', 'cancelled'],
        'paid'       => ['processing', 'cancelled', 'refunded'],
        'processing' => ['packed', 'cancelled'],
        'packed'     => ['shipped'],
        'shipped'    => ['delivered'],
        'delivered'  => ['returned'],
        'returned'   => ['refunded'],
        'refunded'   => [],
        'cancelled'  => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Statuses
    |--------------------------------------------------------------------------
    */

    'payment_statuses' => [
        'pending',
        'processing',
        'completed',
        'failed',
        'refunded',
        'partially_refunded',
    ],

    /*
    |--------------------------------------------------------------------------
    | Loyalty Tiers
    |--------------------------------------------------------------------------
    */

    'loyalty' => [
        'points_per_currency' => 1, // 1 point per 1 BDT spent
        'tiers' => [
            'bronze'   => ['min_spent' => 0,      'min_orders' => 0,  'discount_pct' => 0],
            'silver'   => ['min_spent' => 10000,   'min_orders' => 5,  'discount_pct' => 2],
            'gold'     => ['min_spent' => 50000,   'min_orders' => 20, 'discount_pct' => 5],
            'platinum' => ['min_spent' => 150000,  'min_orders' => 50, 'discount_pct' => 10],
        ],
        'redemption_rate' => 100, // 100 points = 1 BDT discount
    ],

    /*
    |--------------------------------------------------------------------------
    | Referral Rewards
    |--------------------------------------------------------------------------
    */

    'referral' => [
        'referrer_points'  => 500,
        'referred_points'  => 200,
    ],

    /*
    |--------------------------------------------------------------------------
    | Inventory Alerts
    |--------------------------------------------------------------------------
    */

    'inventory' => [
        'low_stock_threshold' => 10,
        'reservation_ttl'     => 30, // minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Cart Settings
    |--------------------------------------------------------------------------
    */

    'cart' => [
        'guest_cart_ttl' => 7, // days
    ],

    /*
    |--------------------------------------------------------------------------
    | Wishlist Settings
    |--------------------------------------------------------------------------
    */

    'wishlist' => [
        'max_items' => 500,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Settings
    |--------------------------------------------------------------------------
    */

    'review' => [
        'requires_moderation' => true,
        'min_rating' => 1,
        'max_rating' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Return Policy
    |--------------------------------------------------------------------------
    */

    'returns' => [
        'return_period_days' => 7,
    ],

    /*
    |--------------------------------------------------------------------------
    | Auth Settings
    |--------------------------------------------------------------------------
    */

    'auth' => [
        'max_login_attempts'  => 5,
        'lockout_duration'    => 15, // minutes
        'otp_expiry'          => 5,  // minutes
        'otp_length'          => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    */

    'sms' => [
        'provider'  => env('SMS_PROVIDER', 'log'),
        'api_key'   => env('SMS_API_KEY'),
        'sender_id' => env('SMS_SENDER_ID', 'NexRun'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Payment Gateway Configuration
    |--------------------------------------------------------------------------
    */

    'payments' => [
        'sslcommerz' => [
            'store_id'       => env('SSLCOMMERZ_STORE_ID'),
            'store_password' => env('SSLCOMMERZ_STORE_PASSWORD'),
            'sandbox'        => env('SSLCOMMERZ_SANDBOX', true),
        ],
        'bkash' => [
            'username'   => env('BKASH_USERNAME'),
            'password'   => env('BKASH_PASSWORD'),
            'app_key'    => env('BKASH_APP_KEY'),
            'app_secret' => env('BKASH_APP_SECRET'),
            'sandbox'    => env('BKASH_SANDBOX', true),
        ],
        'nagad' => [
            'merchant_id'  => env('NAGAD_MERCHANT_ID'),
            'merchant_key' => env('NAGAD_MERCHANT_KEY'),
            'sandbox'      => env('NAGAD_SANDBOX', true),
        ],
        'rocket' => [
            'api_key' => env('ROCKET_API_KEY'),
            'sandbox' => env('ROCKET_SANDBOX', true),
        ],
        'stripe' => [
            'key'            => env('STRIPE_KEY'),
            'secret'         => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret'    => env('PAYPAL_SECRET'),
            'sandbox'   => env('PAYPAL_SANDBOX', true),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Settings
    |--------------------------------------------------------------------------
    */

    'support' => [
        'priorities' => ['low', 'medium', 'high', 'critical'],
        'statuses'   => ['open', 'in_progress', 'resolved', 'closed'],
    ],
];
