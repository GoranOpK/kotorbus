<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;
use Illuminate\Support\Facades\DB;

// DODANO: Controller za readonly admin prikaz rezervisanih slotova
use App\Http\Controllers\AdminReadonlyController;

/*
|--------------------------------------------------------------------------
| WEB RUTE - Ovdje su rute za stranice, forme i admin panel (nije API!)
|--------------------------------------------------------------------------
| Ovdje ide sve što korisnik "vidi" kao HTML, forme, stranice, SPA, itd.
| API rute NE IDU ovdje, već u routes/api.php!
|--------------------------------------------------------------------------
*/

// ======= JAVNE KORISNIČKE RUTE =======

// Prikaz forme za plaćanje
Route::get('/placanje', [PaymentController::class, 'showForm'])->name('payment.form');

// Procesiranje plaćanja (submit forme)
Route::post('/procesiraj-placanje', function(Request $req) {
    $vehicleTypeId = $req->input('vehicle_type_id');
    $vehicleType = DB::table('vehicle_types')->find($vehicleTypeId);

    if (!$vehicleType) {
        return response()->json(['message' => 'Nepostojeći tip vozila!'], 422);
    }

    $price = $vehicleType->price;

    // Priprema payload-a za plaćanje (samo ono što banci treba)
    $payload = [
        'amount'      => $price,
        'card_number' => $req->input('card_number'),
        'exp_month'   => $req->input('exp_month'),
        'exp_year'    => $req->input('exp_year'),
        'cvv'         => $req->input('cvv'),
        'cardholder'  => $req->input('cardholder'),
        // vehicle_type_id NIJE potreban za banku, ali možeš ga sačuvati za tvoje potrebe
    ];

    // Ovdje šalji $payload prema servisu/plaćanju
    // (Ovdje samo vraćamo za test)
    return response()->json([
        'message' => 'OK',
        'payload' => $payload,
    ]);
});

// Callback za online plaćanje
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');

// Prikaz i slanje forme za podršku
Route::get('/podrska', [SupportController::class, 'showForm'])->name('support.form');
Route::post('/podrska', [SupportController::class, 'send'])->name('support.send');

// ================== ADMIN PANEL ==================

// Sve rute koje počinju sa "/admin"
Route::prefix('admin')->name('admin.')->group(function () {
    // Login forma za admina (nije zaštićeno)
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    // Login submit sa throttle zaštitom
    Route::post('login', [LoginController::class, 'login'])
        ->name('login.submit')
        ->middleware('throttle:5,1');

    // Sve ispod ovoga dostupno je SAMO ulogovanom adminu ili readonly adminu (middleware: auth:admin)
    Route::middleware(['auth:admin'])->group(function () {
        // Logout ruta
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        // Generisanje izvještaja
        Route::get('izvjestaj', [ReportController::class, 'generate'])->name('report');
        // Prikaz rezervisanih slotova readonly adminu
        Route::get('todays-reserved-slots', [AdminReadonlyController::class, 'todaysReservedSlots'])
            ->name('todays_reserved_slots');
        // Samo pravi admin (middleware: admin)
        Route::middleware('admin')->group(function () {
            // Dashboard prikaz
            Route::get('dashboard', function () {
                return view('admin.dashboard');
            })->name('dashboard');
            // Brisanje rezervacija
            Route::post('brisanje', [ReservationController::class, 'delete'])->name('brisanje');
        });
    });

    // TEST/DEV rute - dostupno samo u lokalnom okruženju
    if (app()->environment('local')) {
        Route::get('test-dnevni-finansijski', [ReportController::class, 'sendDailyFinance']);
        Route::get('test-dnevni-vozila', [ReportController::class, 'sendDailyVehicleReservations']);
        Route::get('test-mjesecni-finansijski', [ReportController::class, 'sendMonthlyFinance']);
        Route::get('test-mjesecni-vozila', [ReportController::class, 'sendMonthlyVehicleReservations']);
        Route::get('test-godisnji-finansijski', [ReportController::class, 'sendYearlyFinance']);
        Route::get('test-godisnji-vozila', [ReportController::class, 'sendYearlyVehicleReservations']);
        Route::get('test-payment', [PaymentController::class, 'test']);
    }

    // CATCH-ALL ZA ADMIN PANEL (npr. ako koristiš SPA admin panel)
    // Ovo mora biti na kraju admin grupe!
    Route::get('/{any}', function () {
        return response()->file(public_path('index.html'));
    })->where('any', '.*');
});

// Test CSRF stranica
Route::get('/test-csrf', function () {
    return view('test-csrf');
});

// Ovu rutu Laravel automatski dodaje pri instalaciji Sanctuma
Route::get('/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

// Welcome stranica (početna)
Route::get('/', function () {
    return view('welcome');
});

// POST catch-all za web.php, PRE GET catch-all
Route::post('/{any}', function () {
    abort(404, 'POST ruta ne postoji');
})->where('any', '^(?!api/).*');

// ======= EKSPPLICITNA RUTA ZA ADMINCP.HTML =======
// Ova ruta MORA biti iznad globalnog catch-all-a!
// Ako postoji public/admincp.html, biće vraćen taj fajl
Route::get('/admincp.html', function () {
    $path = public_path('admincp.html');
    if (!file_exists($path)) {
        abort(404, 'admincp.html nije pronađen!');
    }
    return response()->file($path);
});

// ======= GLOBAL GET catch-all (npr. za SPA podršku) =======
// Ovo MORA biti poslednje u fajlu!
Route::get('/{any}', function () {
    $path = base_path('index.html');
    if (!file_exists($path)) {
        abort(404, 'index.html not found');
    }
    return response()->file($path);
})->where('any', '^(?!api\/).*');