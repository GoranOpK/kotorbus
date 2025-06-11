<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimeSlotController extends Controller
{
    /**
     * Vraća sve vremenske slotove (za /api/timeslots)
     */
    public function index()
    {
        return TimeSlot::all();
    }

    /**
     * Vraća sve slobodne vremenske slotove za dati datum (za /api/timeslots/available?date=YYYY-MM-DD)
     */
    public function availableSlots(Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['error' => 'Date parameter is required.'], 400);
        }

        // Validacija datuma
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
        }

        $table = date('Ymd', strtotime($date));
        $exists = DB::selectOne(
            "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
            [$table]
        );
        if (!$exists || $exists->cnt == 0) {
            $allSlots = TimeSlot::all();
            return response()->json($allSlots); // Vrati plain listu slotova
        }

        $rows = DB::select("SELECT time_slot_id FROM `$table` WHERE available = 1");
        $slotIds = array_map(fn($r) => $r->time_slot_id, $rows);
        $slots = TimeSlot::whereIn('id', $slotIds)->get();

        return response()->json($slots); // Vrati plain listu slotova
    }

    /**
     * Prikazuje pojedinačni vremenski slot na osnovu ID-a (nije potreban JS-u, ali ostaje radi REST standarda)
     */
    public function show($id)
    {
        return TimeSlot::findOrFail($id);
    }

    /**
     * Kreira novi vremenski slot. (za admina)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'time_slot' => 'required|string|max:255',
        ]);
        return TimeSlot::create($validated);
    }

    /**
     * Ažurira postojeći vremenski slot. (za admina)
     */
    public function update(Request $request, $id)
    {
        $slot = TimeSlot::findOrFail($id);

        $validated = $request->validate([
            'time_slot' => 'sometimes|required|string|max:255',
        ]);

        $slot->update($validated);
        return $slot;
    }

    /**
     * Briše vremenski slot. (za admina)
     */
    public function destroy($id)
    {
        $slot = TimeSlot::findOrFail($id);
        $slot->delete();
        return response()->json(['message' => 'Deleted']);
    }

    /**
     * Proverava dostupnost pojedinačnog vremenskog slota za određeni dan.
     */
    public function availability($slot_id, Request $request)
    {
        $date = $request->query('date');
        if (!$date) {
            return response()->json(['error' => 'Date is required'], 400);
        }

        $table = date('Ymd', strtotime($date));
        $exists = DB::selectOne(
            "SELECT COUNT(*) as cnt FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ?",
            [$table]
        );
        if (!$exists || $exists->cnt == 0) {
            // Ako tabela ne postoji, slot je slobodan (nije rezervisan)
            return response()->json(['available' => true]);
        }

        $row = DB::selectOne("SELECT available FROM `$table` WHERE time_slot_id = ?", [$slot_id]);
        if (!$row) {
            // Ako reda nema, slot je slobodan
            return response()->json(['available' => true]);
        }

        return response()->json(['available' => (bool)$row->available]);
    }
}