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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | Vehicle Detection Backend (Flask API)
    |--------------------------------------------------------------------------
    | 'url' is the server-side URL (Laravel -> Flask, only works on same network)
    | 'client_url' is the browser-side URL (Browser -> Flask, via local IP or ngrok)
    | 'api_key' is shared between Flask and Laravel for authenticated requests
    */
    'detection_backend' => [
        'url' => env('DETECTION_BACKEND_URL', 'http://127.0.0.1:5000'),
        'client_url' => env('VITE_DETECTION_BACKEND_URL', 'http://127.0.0.1:5000'),
        'api_key' => env('DETECTION_BACKEND_API_KEY', 'change-me-in-production'),
    ],

];
