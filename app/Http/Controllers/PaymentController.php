<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function redirectToHpp(Request $request)
    {
        // Validacija ulaza
        $validated = $request->validate([
            // amount više nije deo forme, već ga dobijaš iz baze po tipu vozila
            'vehicle_type_id' => 'required|exists:vehicle_types,id',
            'email'  => 'required|email',
        ]);

        // Dohvati cijenu iz baze na osnovu vehicle_type_id
        $vehicleType = \DB::table('vehicle_types')->find($validated['vehicle_type_id']);
        if (!$vehicleType) {
            return back()->with('error', 'Nepostojeći tip vozila!');
        }
        $amount = $vehicleType->price;

        // Priprema payload-a (popuni prema svom ugovoru i dokumentaciji)
        $payload = [
            'merchantTransactionId' => uniqid('HPP-'),
            'amount' => number_format($amount, 2, '.', ''), // koristi pravu cijenu iz baze
            'currency' => 'EUR',
            'successUrl' => route('payment.success'),
            'cancelUrl'  => route('payment.cancel'),
            'errorUrl'   => route('payment.error'),
            'callbackUrl'=> route('payment.callback'),
            'customer'   => [
                'email' => $validated['email'],
            ],
            'language'   => 'sr',
        ];

        // Potpisivanje — prema Bankart dokumentaciji (implementiraj po zahtevu)
        $sharedSecret = config('services.bankart.shared_secret');
        $signature = hash_hmac('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE), $sharedSecret);

        // Poziv prema Bankart API za generisanje HPP linka
        $response = Http::withHeaders([
                'X-Signature' => $signature,
                'Content-Type' => 'application/json',
            ])
            ->post(config('services.bankart.api_url'), $payload);

        $data = $response->json();
        if (isset($data['redirectUrl'])) {
            return redirect()->away($data['redirectUrl']); // pravi HPP redirect
        }

        return back()->with('error', $data['message'] ?? 'Greška pri inicijalizaciji plaćanja.');
    }

    public function callback(Request $request)
    {
        $payload = $request->getContent();
        $headers = $request->headers;
        $sharedSecret = config('services.bankart.shared_secret');
        $signature = $headers->get('x-signature') ?? $headers->get('X-Signature');

        // Validacija potpisa
        $expectedSignature = hash_hmac('sha256', $payload, $sharedSecret);

        if (!hash_equals($expectedSignature, $signature)) {
            \Log::warning('Bankart: Invalid callback signature!');
            abort(403, 'Invalid signature');
        }

        // Procesuiraj status
        $data = json_decode($payload, true);

        // Primer: upiši status u bazu, šalji mail, itd.
        // $data['status'], $data['merchantTransactionId'], $data['amount'], itd.

        return response()->json(['status' => 'ok']);
    }

    /**
     * Test metoda za simulaciju plaćanja.
     * Poziva redirectToHpp sa test podacima.
     */
    public function test(Request $request)
    {
        // Test podaci
        $testRequest = new Request([
            'vehicle_type_id' => 1, // test tip vozila
            'email' => 'test@example.com',
        ]);

        // Možete koristiti direktno redirectToHpp ili simulirati kroz rutu
        // Ovde koristimo redirectToHpp kao internu metodu
        return $this->redirectToHpp($testRequest);
    }
}