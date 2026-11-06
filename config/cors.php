<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => ['*'],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

    'default_number_group_telegram_broadcast'=> env('DEFAULT_NUMBER_GROUP_TELEGRAM_BROADCAST',0),
    'default_number_event_limit'             => env('DEFAULT_NUMBER_EVENT_LIMIT', null),
    'default_event_limit_period_type'        => env('DEFAULT_EVENT_LIMIT_PERIOD_TYPE', null),

];
