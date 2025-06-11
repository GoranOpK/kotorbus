<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Podrazumijevani Cache Store
    |--------------------------------------------------------------------------
    |
    | Ovdje biraš koji cache driver će biti korišćen kao osnovni u aplikaciji.
    | Najčešće je to "file", "database" ili "redis". Možeš promijeniti u .env fajlu.
    |
    */
    'default' => env('CACHE_STORE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Cache Store-ovi
    |--------------------------------------------------------------------------
    |
    | Ovdje definišeš sve moguće cache "store-ove" (mjesta gdje se čuvaju podaci u kešu).
    | Svaki store ima svoj driver. Možeš imati više store-ova istog tipa, npr. više redis ili file keša.
    |
    | Podržani driveri: "array", "database", "file", "memcached",
    |                   "redis", "dynamodb", "octane", "null"
    |
    */
    'stores' => [

        // "array" - koristi RAM (brzo, ali nestaje kad se resetuje app)
        'array' => [
            'driver' => 'array',
            'serialize' => false, // treba li serijalizovati podatke
        ],

        // "database" - keš u bazi, u tabeli "cache"
        'database' => [
            'driver' => 'database',
            'connection' => env('DB_CACHE_CONNECTION'), // konekcija iz database.php
            'table' => env('DB_CACHE_TABLE', 'cache'), // ime tabele u bazi
            'lock_connection' => env('DB_CACHE_LOCK_CONNECTION'), // za atomic locks (nije obavezno)
            'lock_table' => env('DB_CACHE_LOCK_TABLE'),
        ],

        // "file" - keš na disku, u storage/framework/cache/data
        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'), // folder za keš fajlove
            'lock_path' => storage_path('framework/cache/data'), // folder za lock fajlove
        ],

        // "memcached" - koristi memcached server (za veće projekte, brzi cache u RAM-u)
        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Ovde možeš dodati dodatne opcije za memcached
                // Primjer: Memcached::OPT_CONNECT_TIMEOUT => 2000,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        // "redis" - koristi Redis server (brz RAM cache, dobar za velike aplikacije)
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_CACHE_CONNECTION', 'cache'), // konekcija iz redis.php
            'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
        ],

        // "dynamodb" - koristi AWS DynamoDB za cache (cloud)
        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
        ],

        // "octane" - koristi Laravel Octane, za ekstremno brze performanse (Swoole, RoadRunner)
        'octane' => [
            'driver' => 'octane',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Prefiks za Cache Ključeve
    |--------------------------------------------------------------------------
    |
    | Ako koristiš APC, database, memcached, redis ili dynamodb, može se desiti da više aplikacija dijeli isti cache.
    | Zato je preporučljivo koristiti prefiks da ne dođe do sudara među ključevima.
    |
    */
    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

];