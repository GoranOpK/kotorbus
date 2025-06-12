<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * Lista tipova izuzetaka sa odgovarajućim nivoima logovanja.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * Lista tipova izuzetaka koji se ne prijavljuju.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * Lista input polja koja se nikada ne prikazuju u sesiji kod grešaka validacije.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Registruje callback funkcije za rukovanje izuzecima.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            // Ovdje možete dodati logiku za prijavljivanje grešaka
        });
    }

    /**
     * Override metode za neautentifikovane zahtjeve za API: vraća 401 u JSON-u, ne redirektuje na login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception)
    {
        // Ako je zahtjev za API ili očekuje JSON, vraća samo 401 bez redirekcije
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Za web dio aplikacije, redirektuje na login rutu (ako postoji)
        return redirect()->guest(route('login'));
    }
}