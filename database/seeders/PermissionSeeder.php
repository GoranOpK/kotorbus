<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Pokreni seeder.
     */
    public function run(): void
    {
        // Kreiramo permisiju ako ne postoji
        Permission::firstOrCreate(['name' => 'view_reservations_readonly']);
    }
}