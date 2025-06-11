<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// Mailable klasa za korisnički upit (kontakt/podrška)
class UserSupportMail extends Mailable
{
    use Queueable, SerializesModels;

    // Javni property u koji smještamo podatke iz forme (ime, email, poruka)
    public $podaci;

    // Konstruktor - prima podatke koje će koristiti šablon e-maila
    public function __construct($podaci)
    {
        $this->podaci = $podaci;
    }

    // Definiše subject i blade prikaz za email, te prosleđuje podatke šablonu
    public function build()
    {
        return $this->subject('Korisnički upit sa sajta') // Naslov e-maila
            ->view('emails.user_support')                // Blade šablon koji se koristi
            ->with('podaci', $this->podaci);             // Podaci dostupni u šablonu
    }
}