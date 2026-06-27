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
    |*/

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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
    ],


    'onesignal' => [
        'enabled' => env('ONESIGNAL_ENABLED', false),
        'app_id' => env('ONESIGNAL_APP_ID'),
        'rest_api_key' => env('ONESIGNAL_REST_API_KEY'),
        'api_url' => env('ONESIGNAL_API_URL', 'https://api.onesignal.com/notifications'),
    ],



    'bank_transfer' => [
        'bank_name' => env('PAYMENT_BANK_NAME', 'BCA'),
        'account_number' => env('PAYMENT_BANK_ACCOUNT_NUMBER', '1234567890'),
        'account_name' => env('PAYMENT_BANK_ACCOUNT_NAME', 'Kostify Residence'),
        'notes' => env('PAYMENT_BANK_NOTES', 'Transfer sesuai nominal tagihan, lalu upload bukti pembayaran.'),
    ],

    'admin_payment' => [
        'email' => env('ADMIN_PAYMENT_NOTIFY_EMAIL'),
    ],

    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

];
