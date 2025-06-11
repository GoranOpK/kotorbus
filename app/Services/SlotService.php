<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SlotService
{
    public function getSlotsForDate($date)
    {
        $tableName = date('Ymd', strtotime($date));
        return DB::table($tableName)->get();
    }

    public function reserveSlot($date, $slotId)
    {
        $tableName = date('Ymd', strtotime($date));
        return DB::table($tableName)
            ->where('time_slot_id', $slotId)
            ->update(['remaining' => DB::raw('remaining - 1')]);
    }
}