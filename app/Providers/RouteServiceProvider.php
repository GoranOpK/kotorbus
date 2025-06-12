<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Staza prema korijenu kontrolera za rute.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Definiše "home" rutu za vašu aplikaciju.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Učitajte rute za aplikaciju.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Konfiguriše ograničenja brzine za aplikaciju.
     */
    protected function configureRateLimiting(): void
    {
        \Illuminate\Support\Facades\RateLimiter::for('api', function (\Illuminate\Cache\RateLimiting\Limit $limit) {
            return $limit->perMinute(60);
        });
    }
}