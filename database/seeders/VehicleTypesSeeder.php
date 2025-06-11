<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VehicleTypesSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['description_vehicle' => '8+1 mjesta (manji kombi)', 'price' => 20.00],
            ['description_vehicle' => '9-23 mjesta (mini bus)', 'price' => 40.00],
            ['description_vehicle' => '23+ mjesta (veliki autobus)', 'price' => 50.00],
        ];

        foreach ($types as $type) {
            DB::table('vehicle_types')->updateOrInsert(
                ['description_vehicle' => $type['description_vehicle']],
                ['price' => $type['price']]
            );
        }
    }
}