<?php

return [
    'enabled'     => env('PAYDUNYA_ENABLED', false),
    'master_key'  => env('PAYDUNYA_MASTER_KEY'),
    'public_key' => env('PAYDUNYA_PUBLIC_KEY'),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY'),
    'token'       => env('PAYDUNYA_TOKEN'),
    'mode'        => env('PAYDUNYA_MODE', 'test'),
    'store'       => [
        'name'        => env('PAYDUNYA_STORE_NAME', 'Maffood'),
        'tagline'     => env('PAYDUNYA_STORE_TAGLINE', ''),
        'phone'       => env('APP_PHONE', ''),
        'postal_addr' => env('APP_ADDRESS', 'Dakar, Sénégal'),
        'website_url' => env('APP_URL'),
    ],
];