<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservedSlotsController extends Controller
{
    public function reservedToday(Request $request)
    {
        $date = $request->input('date', now()->toDateString());
        $now = now()->format('H:i');

        // Svi slotovi sa parsiranim vremenom
        $slots = DB::table('list_of_time_slots')
            ->select('id', 'time_slot')
            ->orderBy('id')
            ->get();

        // Parsiraj slotove u niz sa start_time i end_time
        $slotsParsed = $slots->map(function($slot) {
            [$start, $end] = explode(' - ', $slot->time_slot);
            return [
                'id'        => $slot->id,
                'time_slot' => $slot->time_slot,
                'start_time'=> $start,
                'end_time'  => $end,
            ];
        });

        // Pronađi naredna 3 slot-a koji počinju od trenutnog vremena
        $upcomingSlots = $slotsParsed
            ->filter(function($slot) use ($now) {
                return $slot['start_time'] >= $now;
            })
            ->sortBy('start_time')
            ->take(3)
            ->values();

        // Tipovi vozila
        $vehicleTypes = DB::table('vehicle_types')->pluck('description_vehicle', 'id')->toArray();

        // Sve rezervacije za danas (prikazuje i 'pending' i 'paid', promeni po potrebi)
        $reservations = collect(DB::table('reservations')
            ->where('reservation_date', $date)
            //->where('status', 'paid')
            ->get());

        $intervals = [];
        foreach ($upcomingSlots as $slot) {
            $slotReservations = $reservations->filter(function($res) use ($slot) {
                return $res->pick_up_time_slot_id == $slot['id'];
            });

            if ($slotReservations->count() > 0) { // prikazi samo slotove sa rezervacijama
                $intervals[] = [
                    'interval' => $slot['time_slot'],
                    'reservations' => $slotReservations->map(function($res) use ($vehicleTypes) {
                        return [
                            'vehicle_type' => $vehicleTypes[$res->vehicle_type_id] ?? 'Nepoznat tip',
                            'license_plate' => $res->license_plate,
                        ];
                    })->values()
                ];
            }
        }

        return response()->json([
            'server_time' => now()->format('Y-m-d H:i:s'),
            'data' => $intervals,
        ]);
    }
}