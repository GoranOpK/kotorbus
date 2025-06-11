<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Podrazumijevani log kanal
    |--------------------------------------------------------------------------
    |
    | Ovdje biraš koji log kanal će Laravel koristiti za upisivanje logova.
    | Vrijednost mora odgovarati nekom od kanala iz "channels" ispod.
    | Najčešće se koristi "stack" koji grupiše više kanala.
    |
    */
    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Kanal za deprecated logove
    |--------------------------------------------------------------------------
    |
    | Ovdje biraš gdje će se logovati deprecated upozorenja iz PHP-a i
    | biblioteka. Može biti "null" (da se ignorišu) ili npr. "single".
    | "trace" uključuje stack trace u log poruku.
    |
    */
    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Kanali
    |--------------------------------------------------------------------------
    |
    | Ovdje podešavaš sve kanale za logovanje aplikacije.
    | Laravel koristi Monolog, pa imaš razne opcije: fajl, svakodnevne logove,
    | Slack, syslog, errorlog, custom kanale itd.
    |
    | Dostupni driveri: "single", "daily", "slack", "syslog",
    |                   "errorlog", "monolog", "custom", "stack"
    |
    */
    'channels' => [

        // Stack kanal - možeš kombinovati više kanala u jedan (najčešće koristiš ovo)
        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK', 'single')), // podrazumijevano koristi "single"
            'ignore_exceptions' => false,
        ],

        // Single - piše sve logove u jedan fajl
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'), // gdje se čuvaju logovi
            'level' => env('LOG_LEVEL', 'debug'), // nivo logovanja
            'replace_placeholders' => true,
        ],

        // Daily - piše logove po danima (fajl za svaki dan)
        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14), // koliko dana čuva logove
            'replace_placeholders' => true,
        ],

        // Slack - šalje kritične logove na Slack kanal (potreban webhook url)
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        // Papertrail - cloud syslog servis (koristi Monolog handler)
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // STDERR - loguje direktno na standard error (npr. za docker)
        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        // Syslog - šalje logove na syslog servis OS-a
        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        // Errorlog - koristi PHP-ov error_log
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        // Null - ništa se ne loguje (korisno kad ne želiš nikakav log)
        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        // Emergency - koristi se kad svi ostali kanali nisu dostupni
        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

    ],

];