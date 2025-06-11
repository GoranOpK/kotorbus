<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        // Ako je API request, NEMA redirecta, vrati 401
        if ($request->expectsJson()) {
            return null;
        }

        // Za web rute, može redirect na login
        return route('login');
    }
}