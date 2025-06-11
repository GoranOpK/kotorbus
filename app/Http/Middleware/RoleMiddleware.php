<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next, $role): Response
    {
        // Provjerava da li autentifikovani korisnik ima odgovarajuću ulogu
        if ($request->user() && $request->user()->role === $role) {
            return $next($request); // Dozvoli prolaz ako korisnik ima odgovarajuću ulogu
        }

        // Vraća odgovor sa status kodom 403 (Zabranjeno) ako korisnik nema ulogu
        return response()->json(['message' => 'Access denied.'], 403);
    }
}