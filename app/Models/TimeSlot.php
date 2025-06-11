<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model za tabelu 'list_of_time_slots' (lista vremenskih slotova)
class TimeSlot extends Model
{
    // Ime tabele u bazi (ako se razlikuje od standardnog imena)
    protected $table = 'list_of_time_slots';

    // Polja koja mogu biti masovno dodijeljena (mass assignment)
    protected $fillable = [
        'time_slot', // Vrijeme ili opis vremenskog slota 
    ];

    // Relacija: jedan vremenski slot može biti povezan sa više rezervacija kao slot za vraćanje vozila
    public function dropOffReservations()
    {
        // Svi rezervacije gdje je ovaj slot označen kao 'drop off'
        return $this->hasMany(Reservation::class, 'drop_off_time_slot_id');
    }

    // Relacija: jedan vremenski slot može biti povezan sa više rezervacija kao slot za preuzimanje vozila
    public function pickUpReservations()
    {
        // Svi rezervacije gdje je ovaj slot označen kao 'pick up'
        return $this->hasMany(Reservation::class, 'pick_up_time_slot_id');
    }
}