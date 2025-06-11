<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminsSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        DB::table('admins')->updateOrInsert(
            ['username' => 'admin'],
            [
                'password_hash' => Hash::make('kxgWLtrs5b4kn0BK9l7o'),
                'email' => 'bus@kotor.me',
                'created_at' => now(),
            ]
        );

        // Readonly admin "control"
        DB::table('admins')->updateOrInsert(
            ['username' => 'control'],
            [
                'password_hash' => Hash::make('z761ZSiAQ8ax6qN1sRBV'),
                'email' => 'controlbus@kotor.me',
                'created_at' => now(),
            ]
        );
    }
}