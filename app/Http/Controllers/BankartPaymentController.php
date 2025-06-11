<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BankartDebitService;

class BankartPaymentController extends Controller
{
    /**
     * Debit (charge) payment via Bankart.
     * Ovaj metod ne čuva transakcije u bazi i ne koristi korisničke naloge.
     */
    public function debit(Request $request)
    {
        // Validacija ulaznih podataka (prilagodi po potrebi)
        $validated = $request->validate([
            'amount'   => 'required|numeric|min:1',
            'currency' => 'required|string|size:3',
            // Dodaj obavezno i kartične podatke prema zahtevu Bankart API-ja
            // Na primer:
            // 'card_number' => 'required|string',
            // 'expiry'      => 'required|string',
            // 'cvv'         => 'required|string',
            // ... i ostala polja po dokumentaciji ...
        ]);

        // Pripremi payload za Bankart API
        $payload = $validated;
        // Dodaj ostala polja iz zahteva ako ih imaš na formi
        // $payload['card_number'] = $request->input('card_number');
        // $payload['expiry'] = $request->input('expiry');
        // $payload['cvv'] = $request->input('cvv');
        // ...

        // Pozovi BankartDebitService
        $bankart = new BankartDebitService();
        $result = $bankart->debit($payload);

        // Vrati rezultat korisniku (ne čuvaš ništa u bazi)
        return response()->json($result);
    }
}