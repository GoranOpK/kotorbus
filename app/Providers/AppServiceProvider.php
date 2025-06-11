<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Registruje sve aplikacione servise.
     */
    public function register(): void
    {
        //
    }

    /**
     * Pokreće sve aplikacione servise.
     */
    public function boot(): void
    {
        // Forsiraj HTTPS na produkciji
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Sluša sve SQL upite koji se izvršavaju kroz aplikaciju
        DB::listen(function ($query) {
            // Provjera da li je upit spor, npr. traje duže od 100ms
            if ($query->time > 100) {
                // Logovanje sporih upita u fajlove logova
                Log::warning('Spor upit detektovan:', [
                    'sql' => $query->sql,          // SQL upit
                    'bindings' => $query->bindings, // Parametri upita
                    'time' => $query->time          // Vrijeme izvršenja u milisekundama
                ]);
            }
        });
    }
}