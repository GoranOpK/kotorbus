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
        // Koristi Sanctum guard eksplicitno za API autentifikaciju
        $admin = auth('sanctum')->user();

        if (!$admin || $admin->username === 'control') {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}