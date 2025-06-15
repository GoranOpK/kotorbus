<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\MailController;
use App\Http\Controllers\Api\ReservedSlotsController;
use App\Http\Controllers\Api\ReadonlyAdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Ovdje se definiraju sve API rute aplikacije.
*/

// Testna ruta za izvještaj (privremeno, možeš kasnije zaštititi middleware-om po potrebi)
Route::get('admin/test-dnevni-finansijski', [AdminController::class, 'testDnevniFinansijski']);

// Javne API rute (bez autentifikacije)
Route::get('vehicle-types', [VehicleTypeController::class, 'index']);
Route::get('timeslots', [TimeSlotController::class, 'index']);
Route::get('timeslots/available', [TimeSlotController::class, 'availableSlots']);
// Route::get('timeslots/reserved-today', [TimeSlotController::class, 'reservedSlotsToday']);
// Nova ruta za readonly admin prikaz rezervisanih slotova po intervalu i slotu
Route::get('timeslots/reserved-today', [ReservedSlotsController::class, 'reservedToday']);
Route::post('reservations/reserve', [ReservationController::class, 'reserve'])->middleware('throttle:10,1');
Route::get('reservations/slots', [ReservationController::class, 'showSlots']);
Route::get('reservations/by-date', [ReservationController::class, 'byDate']);
Route::get('slots/{slot_id}/availability', [TimeSlotController::class, 'availability']);

// Rute za slanje email-ova
Route::post('send-payment-confirmation', [MailController::class, 'sendPaymentConfirmation'])->name('api.mail.payment-confirmation');
Route::post('send-reservation-confirmation', [MailController::class, 'sendReservationConfirmation'])->name('api.mail.reservation-confirmation');

// RESTful rute za ExampleController (ako treba javno, ostavi ovdje; ako samo za admin, prebaci dolje)
Route::apiResource('examples', ExampleController::class);

// Test rute (po potrebi ukloni iz produkcije)
Route::get('test', fn() => response()->json(['ok' => true]));
Route::get('testjson', fn() => response()->json(['ok' => true]));
Route::get('test-goran', fn() => ['test' => 'uspeh']);
Route::get('cors-test', fn() => response()->json(['ok' => true]));

// ----- ADMIN I AUTENTIFIKOVANI KORISNICI -----

// Autentifikacija admina (login, logout)
Route::post('admin/login', [AdminController::class, 'login']);
Route::post('admin/logout', [AdminController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/readonly-admin/login', [ReadonlyAdminController::class, 'login']);

// SVI PRIJAVLJENI KORISNICI (autentifikovani)
Route::middleware(['auth:sanctum'])->group(function () {
    // Obični korisnici i admini mogu vidjeti svoje rezervacije
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    // ...dodaj još rute za sve prijavljene korisnike ovdje ako treba
});

// SAMO ADMIN
Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    // Blokiranje slotova i dana
    Route::post('admin/block_slots', [AdminController::class, 'blockSlots']);
    Route::post('admin/block_day', [AdminController::class, 'blockDay']);
    Route::post('admin/update_slots', [AdminController::class, 'updateSlots']);

    // Upravljanje slotovima (sem index i show)
    Route::apiResource('timeslots', TimeSlotController::class)->except(['index', 'show']);

    // Upravljanje vrstama vozila (sem index i show)
    Route::apiResource('vehicle-types', VehicleTypeController::class)->except(['index', 'show']);

    // Upravljanje admin korisnicima (sem index)
    Route::apiResource('admins', AdminController::class)->except(['index']);
    Route::get('admins', [AdminController::class, 'index']); // svi admini

    // Upravljanje rezervacijama
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']);
    Route::patch('reservations/{id}/status', [ReservationController::class, 'updateStatus']);
    Route::post('admin/update_reservation/{reservation}', [AdminController::class, 'updateReservation']);
    Route::post('admin/reservation_free/{reservation}', [AdminController::class, 'freeReservation']);
    Route::get('admin/reservation/{reservation}', [AdminController::class, 'showReservation']);

    // Sistem konfiguracija
    Route::post('system-config', [SystemConfigController::class, 'store']);
});