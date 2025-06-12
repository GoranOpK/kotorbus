<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Ovdje navodiš koji domeni/hostovi će primati stateful (cookie-based) API
    | autentifikaciju. Ovo su obično tvoji frontend domeni (lokalni i produkcijski)
    | koji pristupaju API-ju kao SPA (Single Page Application).
    | Na ove domene browser šalje cookies i očekuje session-autentifikaciju.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:8000,127.0.0.1,127.0.0.1:8000,::1',
        Sanctum::currentApplicationUrlWithPort(),
        // Sanctum::currentRequestHost(),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Ovdje navodiš koje Laravel "guardove" koristi Sanctum za provjeru autentifikacije.
    | Najčešće je to 'web', što znači da koristi standardnu autentikaciju korisnika
    | iz Laravel-a. Ako koristiš više guardova, možeš ih ovdje navesti.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Broj minuta nakon kojeg će izdat token biti istekao (ako je podešeno).
    | Ako je null, tokeni ne ističu automatski. Ovo se odnosi na personal access token-e,
    | ali ne i na sesije iz browsera (one traju koliko i session).
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Prefiks koji se dodaje na početak novih tokena radi bolje sigurnosti
    | (npr. radi lakšeg prepoznavanja tokena u kodu ili za potrebe skeniranja
    | na platformama poput GitHub-a). Obično može ostati prazan.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Ovdje možeš definisati koje middleware-ove Sanctum koristi tokom
    | obrade zahtjeva za SPA autentifikaciju.
    | - 'authenticate_session' provjerava korisničku sesiju,
    | - 'encrypt_cookies' šifrira cookies,
    | - 'validate_csrf_token' provjerava CSRF token.
    | Ove vrijednosti uglavnom ne trebaš mijenjati.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies' => Illuminate\Cookie\Middleware\EncryptCookies::class,
        'validate_csrf_token' => Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    ],

];