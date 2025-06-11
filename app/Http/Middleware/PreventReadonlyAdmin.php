<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventReadonlyAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Provjera da li je korisnik readonly admin
        if (auth()->check() && auth()->user()->hasRole('admin_readonly')) {
            return response()->json(['message' => 'Readonly admin ne može menjati podatke.'], 403);
        }
        return $next($request);
    }
}