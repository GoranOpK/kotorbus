<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Ovdje definišete koji disk (storage driver) će Laravel koristiti kao
    | podrazumijevani za rad sa fajlovima. Najčešće je to "local", ali može
    | biti i npr. "s3" ako koristite Amazon S3 za pohranu.
    |
    */
    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Ovdje možete definisati koliko god želite diskova (storage drivera).
    | Svaki disk ima svoj driver i konfiguraciju. Isti driver može imati više
    | diskova (npr. više različitih "local" diskova). Najčešći su:
    | - local: lokalni disk na serveru
    | - public: lokalni disk čiji su fajlovi dostupni javno (npr. slike)
    | - s3: Amazon S3 cloud storage
    | - ftp/sftp: eksterni serveri
    |
    | 'throw' - ako je true, bacaće exception na grešku, inače ne.
    | 'report' - ako je true, prijavljuje error handleru.
    | 'serve' - ako je true, dozvoljava servisiranje fajlova (samo "local").
    |
    | Više info: https://laravel.com/docs/filesystem
    |
    */
    'disks' => [

        // Lokalni disk - fajlovi su privatni, nisu dostupni javno
        'local' => [
            'driver' => 'local', // tip storage-a
            'root' => storage_path('src/private'), // putanja na disku
            'serve' => true, // dozvoljeno "servisiranje" fajlova
            'throw' => false, // ne baca exception na grešku
            'report' => false, // ne prijavljuje error handleru
        ],

        // Javni disk - koristi se za slike i fajlove dostupne public (npr. upload)
        'public' => [
            'driver' => 'local',
            'root' => storage_path('src/public'),
            'url' => env('APP_URL').'/storage', // javni URL za pristup fajlovima
            'visibility' => 'public', // fajlovi su javni
            'throw' => false,
            'report' => false,
        ],

        // Amazon S3 disk - koristi se za cloud storage
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'), // AWS Access Key
            'secret' => env('AWS_SECRET_ACCESS_KEY'), // AWS Secret
            'region' => env('AWS_DEFAULT_REGION'), // region bucket-a
            'bucket' => env('AWS_BUCKET'), // ime bucket-a
            'url' => env('AWS_URL'), // custom url (nije obavezno)
            'endpoint' => env('AWS_ENDPOINT'), // custom endpoint (nije obavezno)
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Ovdje se definišu simbolički linkovi koji se kreiraju kada pokrenete
    | "php artisan storage:link". Tipično je jedan link:
    | - public/storage -> storage/src/public
    | Time sve što stavite u storage/src/public bude dostupno iz javnog foldera.
    |
    */
    'links' => [
        public_path('storage') => storage_path('src/public'),
    ],

];