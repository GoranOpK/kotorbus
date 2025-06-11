<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Pokreće migraciju.
     *
     * Ova funkcija kreira tabelu `sessions`, ali samo ako ona ne postoji.
     * Tabela se koristi za skladištenje podataka o sesijama korisnika.
     */
    public function up(): void
    {
        if (!Schema::hasTable('sessions')) { // Provjera da li tabela već postoji
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary(); // Primarni ključ
                $table->foreignId('user_id')->nullable()->index(); // ID korisnika, može biti null
                $table->string('ip_address', 45)->nullable(); // IP adresa korisnika, može biti null
                $table->text('user_agent')->nullable(); // Informacije o korisnikovom pretraživaču, može biti null
                $table->longText('payload'); // Skladištenje podataka sesije
                $table->integer('last_activity')->index(); // Vrijeme posljednje aktivnosti, indeksirano za bolje performanse
            });
        }
    }

    /**
     * Poništava migraciju.
     *
     * Ova funkcija briše tabelu `sessions` ako ona postoji.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions'); // Briše tabelu ako postoji
    }
};