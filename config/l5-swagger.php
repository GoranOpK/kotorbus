<?php

return [
    // Podrazumijevani set konfiguracija
    'default' => 'default',

    // Konfiguracija za dokumentaciju
    'documentations' => [
        'default' => [
            'api' => [
                // Naslov koji će se prikazivati u Swagger korisničkom interfejsu
                'title' => 'L5 Swagger UI',
            ],

            'routes' => [
                /*
                 * Ruta za pristup korisničkom interfejsu dokumentacije API-ja
                 */
                'api' => 'api/documentation',
            ],
            'paths' => [
                /*
                 * Postavka da li koristiti apsolutne putanje za resurse u korisničkom interfejsu
                 */
                'use_absolute_path' => env('L5_SWAGGER_USE_ABSOLUTE_PATH', true),

                /*
                 * Putanja gdje se nalaze Swagger UI resursi (CSS, JS, itd.)
                 */
                'swagger_ui_assets_path' => env('L5_SWAGGER_UI_ASSETS_PATH', 'vendor/swagger-api/swagger-ui/dist/'),

                /*
                 * Naziv JSON fajla sa generisanom dokumentacijom
                 */
                'docs_json' => 'api-docs.json',

                /*
                 * Naziv YAML fajla sa generisanom dokumentacijom
                 */
                'docs_yaml' => 'api-docs.yaml',

                /*
                 * Format dokumentacije koji će se koristiti (json ili yaml)
                 */
                'format_to_use_for_docs' => env('L5_FORMAT_TO_USE_FOR_DOCS', 'json'),

                /*
                 * Putanje ka direktorijumima gdje se nalaze Swagger anotacije
                 */
                'annotations' => [
                    base_path('app'),
                ],
            ],

            /*
             * Ispravno: dodaj ovo kako bi izbjegao "Undefined array key 'additional_config_url'"
             */
            'additional_config_url' => null,
        ],
    ],

    // Podrazumijevane vrijednosti
    'defaults' => [
        'routes' => [
            /*
             * Ruta za pristup parsiranim Swagger anotacijama
             */
            'docs' => 'docs',

            /*
             * Ruta za povratni poziv OAuth2 autentifikacije
             */
            'oauth2_callback' => 'api/oauth2-callback',

            /*
             * Middleware za zaštitu pristupa Swagger dokumentaciji
             */
            'middleware' => [
                'api' => [],
                'asset' => [],
                'docs' => [],
                'oauth2_callback' => [],
            ],

            /*
             * Opcije za grupisanje ruta
             */
            'group_options' => [],
        ],

        'paths' => [
            /*
             * Putanja gdje će se čuvati parsirane anotacije
             */
            'docs' => storage_path('api-docs'),

            /*
             * Putanja ka direktorijumu za eksport pogleda
             */
            'views' => base_path('resources/views/vendor/l5-swagger'),

            /*
             * Osnovna putanja API-ja
             */
            'base' => env('L5_SWAGGER_BASE_PATH', null),

            /*
             * Direktorijumi koje treba isključiti iz skeniranja
             */
            'excludes' => [],
        ],

        'scanOptions' => [
            /*
             * Procesori koji se koriste za Swagger anotacije
             */
            'default_processors_configuration' => [],
            'analyser' => null,
            'analysis' => null,

            /*
             * Obrasci fajlova koje treba skenirati
             */
            'pattern' => null,

            /*
             * Direktorijumi koji se isključuju iz skeniranja
             */
            'exclude' => [],

            /*
             * Verzija OpenAPI specifikacije (3.0.0 ili 3.1.0)
             */
            'open_api_spec_version' => env('L5_SWAGGER_OPEN_API_SPEC_VERSION', \L5Swagger\Generator::OPEN_API_DEFAULT_SPEC_VERSION),
        ],

        /*
         * Definicije sigurnosti (autentifikacija i autorizacija)
         */
        'securityDefinitions' => [
            'securitySchemes' => [],
            'security' => [],
        ],

        /*
         * Generisanje dokumentacije na svakom zahtjevu (samo za razvojni mod)
         */
        'generate_always' => env('L5_SWAGGER_GENERATE_ALWAYS', false),

        /*
         * Generisanje YAML kopije dokumentacije
         */
        'generate_yaml_copy' => env('L5_SWAGGER_GENERATE_YAML_COPY', false),

        /*
         * Povjerenje za proxy IP adrese
         */
        'proxy' => false,

        /*
         * Sortiranje operacija u Swagger UI
         */
        'operations_sort' => env('L5_SWAGGER_OPERATIONS_SORT', null),

        /*
         * URL za validaciju Swagger UI
         */
        'validator_url' => null,

        /*
         * DODAJ OVO: izbjegava grešku na Laravel 10/11/12 i L5 Swagger 9+
         */
        'additional_config_url' => null,

        /*
         * Postavke za Swagger UI korisnički interfejs
         */
        'ui' => [
            'display' => [
                'dark_mode' => env('L5_SWAGGER_UI_DARK_MODE', false),
                'doc_expansion' => env('L5_SWAGGER_UI_DOC_EXPANSION', 'none'),
                'filter' => env('L5_SWAGGER_UI_FILTERS', true),
            ],

            'authorization' => [
                /*
                 * Postavka za čuvanje autorizacionih podataka
                 */
                'persist_authorization' => env('L5_SWAGGER_UI_PERSIST_AUTHORIZATION', false),

                'oauth2' => [
                    /*
                     * Postavka za PKCE u Authorization Code Grant flow
                     */
                    'use_pkce_with_authorization_code_grant' => false,
                ],
            ],
        ],

        /*
         * Konstantne vrijednosti koje se mogu koristiti u anotacijama
         */
        'constants' => [
            'L5_SWAGGER_CONST_HOST' => env('L5_SWAGGER_CONST_HOST', 'http://my-default-host.com'),
        ],
    ],
];