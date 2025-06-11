<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// Model za tabelu 'vehicle_types' (tipovi vozila)
class VehicleType extends Model
{
    // Ime tabele u bazi (nije obavezno ako se poklapa sa nazivom modela u množini)
    protected $table = 'vehicle_types';

    // Polja koja mogu biti masovno dodijeljena (mass assignment)
    protected $fillable = [
        'description_vehicle', // Opis tipa vozila 
        'price',               // Cijena vezana za ovaj tip vozila
    ];

    // Relacija: jedan tip vozila može imati više rezervacija
    public function reservations()
    {
        // Vraća sve rezervacije koje su vezane za ovaj tip vozila
        return $this->hasMany(Reservation::class, 'vehicle_type_id');
    }
}