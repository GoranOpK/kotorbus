<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthorizeAdmin
{
    /**
     * Dozvoljava pristup samo korisnicima koji NISU readonly admin ('control').
     */
    public function handle(Request $request, Closure $next)
    {
        // Provjeri da li postoji autentifikovani korisnik i da NIJE 'control'
        if (!$request->user() || $request->user()->username === 'control') {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}