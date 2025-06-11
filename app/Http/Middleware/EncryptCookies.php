<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * The names of the cookies that should not be encrypted.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Dodajte kolačiće koje želite izuzeti iz enkripcije (ako je potrebno)
    ];

    /**
     * Decrypt a cookie and log the process.
     *
     * @param string $name
     * @param string $value
     * @return string|null
     */
    protected function decryptCookie($name, $value)
    {
        try {
            \Log::info("Decrypting cookie: {$name}");
            return parent::decryptCookie($name, $value);
        } catch (\Exception $e) {
            \Log::error("Failed to decrypt cookie: {$name}. Error: " . $e->getMessage());
            return null; // Vraća null ako kolačić ne može biti dekriptovan
        }
    }
}