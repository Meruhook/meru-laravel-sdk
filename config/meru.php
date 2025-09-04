<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Meru API Configuration
    |--------------------------------------------------------------------------
    */

    'base_url' => env('MERU_BASE_URL', 'https://api.meruhook.com'),

    'api_token' => env('MERU_API_TOKEN'),

    'timeout' => env('MERU_TIMEOUT', 30),

    'retry' => [
        'times' => env('MERU_RETRY_TIMES', 3),
        'delay' => env('MERU_RETRY_DELAY', 100), // milliseconds
    ],

    'webhook' => [
        'signature_header' => 'X-Meru-Signature',
        'secret' => env('MERU_WEBHOOK_SECRET'),
        'tolerance' => env('MERU_WEBHOOK_TOLERANCE', 300), // seconds
    ],

    'debug' => env('MERU_DEBUG', false),
];
