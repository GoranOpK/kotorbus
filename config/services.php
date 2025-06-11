<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Ovdje se nalaze podešavanja (credentials) za integraciju sa raznim
    | eksternim servisima kao što su Mailgun, Postmark, AWS SES, Slack itd.
    | Ova lokacija je standardna za Laravel, tako da paketi i tvoj kod
    | mogu lako pronaći sve neophodne podatke za povezivanje sa servisima.
    |
    */

    // Postmark servis za slanje e-pošte
    'postmark' => [
        'token' => env('POSTMARK_TOKEN'), // API token se povlači iz .env fajla
    ],

    // Amazon SES servis za slanje e-pošte
    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'), // AWS Access Key ID iz .env
        'secret' => env('AWS_SECRET_ACCESS_KEY'), // AWS Secret iz .env
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'), // Region, podrazumijevano 'us-east-1'
    ],

    // Resend servis za slanje e-pošte
    'resend' => [
        'key' => env('RESEND_KEY'), // API ključ iz .env
    ],

    // Slack notifikacije (npr. za obavještenja)
    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'), // Slack bot token iz .env
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'), // Slack kanal iz .env
        ],
    ],

    'payment_gateway' => [
        'sandbox_url' => env('PAYMENT_GATEWAY_SANDBOX_URL'),
        'production_url' => env('PAYMENT_GATEWAY_PRODUCTION_URL'),
        'api_key' => env('PAYMENT_GATEWAY_API_KEY'),
    ],
];