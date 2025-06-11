<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            VehicleTypesSeeder::class,
            ListOfTimeSlotsSeeder::class,
            AdminsSeeder::class,
            SystemConfigSeeder::class,
        ]);
    }
}