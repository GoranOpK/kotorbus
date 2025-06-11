<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Podrazumijevana Queue Konekcija
    |--------------------------------------------------------------------------
    |
    | Ovdje biraš koji queue driver će Laravel koristiti kao osnovni.
    | Najčešće je to "database" (poslovi se čuvaju u bazi), ali možeš koristiti
    | i "redis", "beanstalkd", "sqs" ili "sync" (za development/test).
    |
    */
    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Konekcije
    |--------------------------------------------------------------------------
    |
    | Ovdje podešavaš sve dostupne konekcije (backende) za queue sistem.
    | Svaka konekcija ima svoj set opcija. Možeš imati više konekcija istog tipa.
    |
    | Driveri: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */
    'connections' => [

        // Sync driver - sve se izvršava odmah, bez reda (koristi se za dev/test)
        'sync' => [
            'driver' => 'sync',
        ],

        // Database driver - poslovi se čuvaju u bazi u tabeli "jobs"
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_QUEUE_CONNECTION'), // konekcija iz config/database.php
            'table' => env('DB_QUEUE_TABLE', 'jobs'), // ime tabele za queue poslove
            'queue' => env('DB_QUEUE', 'default'), // ime queue-a (možeš imati više)
            'retry_after' => (int) env('DB_QUEUE_RETRY_AFTER', 90), // koliko sekundi čeka prije retry
            'after_commit' => false, // da li da se posao izvrši tek nakon commit-a u bazi
        ],

        // Beanstalkd driver - popularni queue server (mora biti instaliran na serveru)
        'beanstalkd' => [
            'driver' => 'beanstalkd',
            'host' => env('BEANSTALKD_QUEUE_HOST', 'localhost'),
            'queue' => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after' => (int) env('BEANSTALKD_QUEUE_RETRY_AFTER', 90),
            'block_for' => 0, // koliko sekundi da čeka nove poslove (0 = ne čeka)
            'after_commit' => false,
        ],

        // Amazon SQS - cloud queue servis (potreban AWS nalog)
        'sqs' => [
            'driver' => 'sqs',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'prefix' => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/your-account-id'),
            'queue' => env('SQS_QUEUE', 'default'),
            'suffix' => env('SQS_SUFFIX'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => false,
        ],

        // Redis driver - koristi se za brze i velike queue sisteme
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'), // konekcija iz redis.php
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null, // koliko sekundi da čeka posao (null = ne blokira)
            'after_commit' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Batch poslovi (Job Batching)
    |--------------------------------------------------------------------------
    |
    | Ovdje podešavaš bazu i tabelu gdje se čuvaju informacije o batch jobovima.
    | Batch znači više poslova koji se izvršavaju zajedno.
    |
    */
    'batching' => [
        'database' => env('DB_CONNECTION', 'sqlite'), // konekcija iz database.php
        'table' => 'job_batches', // tabela za batch poslove
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs (Neuspjeli poslovi)
    |--------------------------------------------------------------------------
    |
    | Ovdje biraš kako i gdje će se čuvati logovi o neuspjelim queue poslovima.
    | Laravel podržava fajl, bazu (database-uuids), DynamoDB, ili da ignorišeš.
    |
    | Podržani driveri: "database-uuids", "dynamodb", "file", "null"
    |
    */
    'failed' => [
        'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'sqlite'), // konekcija iz database.php
        'table' => 'failed_jobs', // tabela za failed jobs
    ],

];