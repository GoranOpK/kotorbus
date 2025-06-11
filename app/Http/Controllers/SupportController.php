<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserSupportMail;

class SupportController extends Controller
{
    /**
     * Prikaz kontakt forme (opciono, za GET rutu)
     */
    public function showForm()
    {
        return view('support.contact');
    }

    /**
     * Slanje korisničkog upita na bus@kotor.me
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:255',
            'massage' => 'required|string|max:2000',
        ]);

        Mail::to('bus@kotor.me')->send(new UserSupportMail($validated));

        return back()->with('success','Your message has been recieved successfully! Our operators will contact you soon.');
    }
}