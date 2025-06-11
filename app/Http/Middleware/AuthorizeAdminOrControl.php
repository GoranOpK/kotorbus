<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizeAdminOrControl
{
    /**
     * Dozvoljava pristup korisnicima koji su admin ili readonly admin ('control').
     */
    public function handle(Request $request, Closure $next)
    {
        // Provjeri da li postoji korisnik i da li mu je username 'admin' ili 'control'
        if (!$request->user() || !in_array($request->user()->username, ['admin', 'control'])) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}