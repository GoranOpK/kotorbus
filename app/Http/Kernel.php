<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * Globalni middleware-ovi aplikacije.
     *
     * Ovi middleware-ovi se izvršavaju za svaki zahtjev koji dolazi u aplikaciju.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        \Illuminate\Http\Middleware\HandleCors::class, // Upravljanje CORS pravilima za zahtjeve        
        \App\Http\Middleware\TrustProxies::class, // Povjerenje proxy serverima
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class, // Blokiranje zahtjeva tokom režima održavanja
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class, // Validacija veličine POST podataka
        \App\Http\Middleware\TrimStrings::class, // Trimovanje praznih razmaka u stringovima
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class, // Konvertovanje praznih stringova u null
    ];

    /**
     * Middleware grupe za rute.
     *
     * Definiše grupe middleware-a koje se mogu primijeniti na određene rute.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
    ];

    /**
     * Middleware-ovi za pojedinačne rute.
     *
     * Ovi middleware-ovi se mogu dodijeliti pojedinačnoj ruti ili grupi ruta.
     *
     * @var array<string, class-string|string>
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class, // Provjera da li je korisnik autentifikovan
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class, // Osnovna HTTP autentifikacija
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class, // Autentifikacija sesije
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class, // Postavljanje keš zaglavlja
        'can' => \Illuminate\Auth\Middleware\Authorize::class, // Provjera dozvola za akcije
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class, // Preusmjeravanje ako je korisnik već autentifikovan
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class, // Potvrda lozinke za osjetljive akcije
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class, // Validacija potpisanih URL-ova
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class, // Ograničenje broja zahtjeva
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Provjera da li je email verifikovan
        'prevent.readonly' => \App\Http\Middleware\PreventReadonlyAdmin::class,
        'custom.auth' => \App\Http\Middleware\AuthenticateCustom::class,
        // Prilagođeni middleware za provjeru administratorskog pristupa:
        'admin' => \App\Http\Middleware\AuthorizeAdmin::class, // Provjera da li je korisnik administrator

        // Prilagođeni middleware za provjeru uloga
        // 'admin_or_control' => \App\Http\Middleware\AuthorizeAdminOrControl::class, // ako ti zatreba
        // 'role' => \Spatie\Permission\Middlewares\RoleMiddleware::class,
        // 'permission' => \Spatie\Permission\Middlewares\PermissionMiddleware::class,
        // 'role_or_permission' => \Spatie\Permission\Middlewares\RoleOrPermissionMiddleware::class, // Dodato za provjeru uloga
    ];
}