<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Mape politika za aplikaciju.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        // Ovdje možete mapirati modele na odgovarajuće politike.
    ];

    /**
     * Registruje sve servise za autentifikaciju / autorizaciju.
     */
    public function boot()
    {
        $this->registerPolicies(); // Registruje sve mape politika definisane u $policies.

        // Ovdje možete dodati dodatne "Gate" mehanizme za kontrolu pristupa.
    }
}