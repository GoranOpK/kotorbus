<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Migracija za dodavanje kolone "updated_at" u tabelu admins
return new class extends Migration
{
    /**
     * Pokretanje migracije.
     * Ovdje se dodaje nova kolona updated_at koja može biti prazna (nullable),
     * kako bi Laravel mogao automatski da vodi evidenciju o izmjenama svakog reda.
     */
    public function up(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Dodajemo timestamp kolonu "updated_at" odmah nakon "created_at"
            $table->timestamp('updated_at')->nullable()->after('created_at');
        });
    }

    /**
     * Povratak migracije (rollback).
     * Briše se kolona updated_at iz tabele admins, ako se migracija vraća nazad.
     */
    public function down(): void
    {
        Schema::table('admins', function (Blueprint $table) {
            // Brišemo kolonu "updated_at"
            $table->dropColumn('updated_at');
        });
    }
};