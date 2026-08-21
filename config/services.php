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

    'dashboard' => [
        'user' => env('DASHBOARD_USER'),
        'pass' => env('DASHBOARD_PASS'),
    ],

    // config/services.php
    'career_api' => [
        'base_url' => env('CAREER_API_BASE_URL', 'http://api-server.test/api'),
        'client_id' => env('CAREER_API_SERVICE_CLIENT_ID'),
        'client_secret' => env('CAREER_API_SERVICE_CLIENT_SECRET'),
        'application_token_header' => env(
            'CAREER_API_APPLICATION_TOKEN_HEADER',
            'X-Career-Application-Token'
        ),
        'timeout' => env('CAREER_API_TIMEOUT', 15),
        'connect_timeout' => env('CAREER_API_CONNECT_TIMEOUT', 5),
    ],

];
