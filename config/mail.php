<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | Ova opcija određuje koji mailer će biti korišten za slanje emailova.
    | Ako u .env fajlu nemaš podešen MAIL_MAILER, koristiće se 'log', što znači
    | da će svi emailovi biti upisani u laravel.log fajl, a neće biti zaista poslati.
    | Za produkciju, preporučuje se koristiti 'smtp' i podesiti podatke za SMTP server.
    */
    'default' => env('MAIL_MAILER', 'log'),

    /*
    |--------------------------------------------------------------------------
    | Konfiguracija mailera
    |--------------------------------------------------------------------------
    |
    | Ovdje podešavaš sve mailere koje tvoja aplikacija može koristiti.
    | Najčešće ćeš koristiti samo jedan, najčešće 'smtp'.
    | Svaki mailer može imati svoj transport (npr. smtp, sendmail, log, ...).
    | Ako koristiš neki od email servisa (Mailgun, SES, Postmark...), ovdje ih možeš dodati.
    |
    */
    'mailers' => [

        // SMTP mailer - koristi se za slanje preko SMTP servera (npr. Gmail, Mailgun, Zoho...)
        'smtp' => [
            'transport' => 'smtp',
            'scheme' => env('MAIL_SCHEME'), // Obično nije potrebno (možeš izostaviti ako ne koristiš SSL/TLS direktno)
            'url' => env('MAIL_URL'), // Obično nije potrebno, koristi host/port umjesto ovoga
            'host' => env('MAIL_HOST', '127.0.0.1'), // SMTP server (npr. smtp.gmail.com)
            'port' => env('MAIL_PORT', 2525), // Port (npr. 587 za TLS, 465 za SSL)
            'username' => env('MAIL_USERNAME'), // Korisničko ime za SMTP nalog
            'password' => env('MAIL_PASSWORD'), // Lozinka za SMTP nalog
            'timeout' => null, // Vrijeme trajanja konekcije
            'local_domain' => env('MAIL_EHLO_DOMAIN', parse_url(env('APP_URL', 'http://localhost'), PHP_URL_HOST)), // Ehlo domen (obično nije potrebno dirati)
        ],

        // Amazon SES
        'ses' => [
            'transport' => 'ses',
        ],

        // Postmark servis
        'postmark' => [
            'transport' => 'postmark',
            // 'message_stream_id' => env('POSTMARK_MESSAGE_STREAM_ID'),
            // 'client' => [
            //     'timeout' => 5,
            // ],
        ],

        // Resend servis
        'resend' => [
            'transport' => 'resend',
        ],

        // Sendmail na serveru (rijetko se koristi osim na VPS/dedicated serverima)
        'sendmail' => [
            'transport' => 'sendmail',
            'path' => env('MAIL_SENDMAIL_PATH', '/usr/sbin/sendmail -bs -i'),
        ],

        // Log mailer - emailovi se upisuju u log fajl (korisno za testiranje u developmentu)
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],

        // Array mailer - emailovi se spremaju u memoriju (najčešće za testove)
        'array' => [
            'transport' => 'array',
        ],

        // Failover - koristi više mailera po redu ako prethodni ne uspije
        'failover' => [
            'transport' => 'failover',
            'mailers' => [
                'smtp',
                'log',
            ],
            'retry_after' => 60,
        ],

        // Roundrobin - koristi više mailera naizmjenično (rijetko se koristi)
        'roundrobin' => [
            'transport' => 'roundrobin',
            'mailers' => [
                'ses',
                'postmark',
            ],
            'retry_after' => 60,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Globalna "From" adresa
    |--------------------------------------------------------------------------
    |
    | Ovdje podesiš adresu i ime sa kojih će se slati svi emailovi iz aplikacije.
    | Ovo se može prepisati za svaki email posebno, ali je preporuka postaviti ovdje.
    |
    */
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'), // Email adresa sa koje se šalje
        'name' => env('MAIL_FROM_NAME', 'Example'), // Prikazno ime pošiljaoca
    ],

];