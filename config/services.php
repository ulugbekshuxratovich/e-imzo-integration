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

    'eimzo' => [
        'server_url' => env('EIMZO_SERVER_URL', 'http://localhost:8080'),
        'frontend_url' => env('EIMZO_FRONTEND_URL', 'http://localhost'),
        'api_keys' => env('EIMZO_API_KEYS', 'localhost:YOUR_API_KEY'),
        'timeout' => env('EIMZO_TIMEOUT', 30),

        'mobile' => [
            'enabled' => env('EIMZO_MOBILE_ENABLED', false),
            'site_id' => env('EIMZO_MOBILE_SITE_ID', ''),
        ],
    ],

];
