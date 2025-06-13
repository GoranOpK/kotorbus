<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TimeSlotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\VehicleTypeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SystemConfigController;
use App\Http\Controllers\ExampleController;
use App\Http\Controllers\MailController;

// Testna ruta za izvještaj
Route::get('admin/test-dnevni-finansijski', [AdminController::class, 'testDnevniFinansijski']);


// Javne API rute
Route::get('vehicle-types', [VehicleTypeController::class, 'index']);
Route::get('timeslots', [TimeSlotController::class, 'index']);
Route::get('timeslots/available', [TimeSlotController::class, 'availableSlots']);
Route::get('timeslots/reserved-today', [\App\Http\Controllers\TimeSlotController::class, 'reservedSlotsToday']);
Route::post('reservations/reserve', [ReservationController::class, 'reserve'])->middleware('throttle:10,1');
Route::get('reservations/slots', [ReservationController::class, 'showSlots']);
Route::get('reservations/by-date', [ReservationController::class, 'byDate']);

// Admin rute (zaštićene, potrebna autentifikacija)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('reservations', [ReservationController::class, 'index']);
    Route::get('reservations/{reservation}', [ReservationController::class, 'show']);
    Route::get('admins', [AdminController::class, 'index']);
    });

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::apiResource('timeslots', TimeSlotController::class)->except(['index', 'show']);
    Route::delete('reservations/{reservation}', [ReservationController::class, 'destroy']);
    Route::apiResource('vehicle-types', VehicleTypeController::class)->except(['index', 'show']);
    Route::apiResource('admins', AdminController::class)->except(['index']);
    Route::patch('reservations/{id}/status', [ReservationController::class, 'updateStatus']);
    Route::post('system-config', [SystemConfigController::class, 'store']);
});

// Autentifikacija admina
Route::post('admin/login', [AdminController::class, 'login']);
Route::post('admin/logout', [AdminController::class, 'logout'])->middleware('auth:sanctum');

// RESTful rute za ExampleController
Route::apiResource('examples', ExampleController::class);

// Rute za slanje email-ova
Route::post('send-payment-confirmation', [MailController::class, 'sendPaymentConfirmation'])->name('api.mail.payment-confirmation');
Route::post('send-reservation-confirmation', [MailController::class, 'sendReservationConfirmation'])->name('api.mail.reservation-confirmation');

// Test rute
Route::get('test', fn() => response()->json(['ok' => true]));
Route::get('testjson', fn() => response()->json(['ok' => true]));
Route::get('test-goran', fn() => ['test' => 'uspeh']);
Route::get('cors-test', fn() => response()->json(['ok' => true]));

// Dostupnost slota
Route::get('slots/{slot_id}/availability', [TimeSlotController::class, 'availability']);