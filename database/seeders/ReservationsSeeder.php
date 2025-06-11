<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReservationsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('reservations')->insert([
            [
                'user_name' => 'Test Korisnik',
                'vehicle_type_id' => 1, // zavisi od tvoje tabele vehicle_types
                'time_slot_id' => 1, // zavisi od tvoje tabele list_of_time_slots
                'reservation_date' => now()->toDateString(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}