<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class YearlyVehicleReservationReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Konstruktor - prosljeđuje podatke za izvještaj
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Priprema email sa godišnjim izvještajem o rezervacijama po tipu vozila u pdf-u
     */
    public function build()
    {
        // Generišemo PDF koristeći odgovarajući blade šablon iz resources/views/reports
        $pdf = Pdf::loadView('reports.yearly_vehicle_reservation_report_pdf', $this->data);

        return $this->subject('Godišnji izvještaj o rezervacijama po tipu vozila')
            ->text('emails.empty')
            ->attachData(
                $pdf->output(),
                'godisnji_izvjestaj_rezervacije_po_voznom_parku.pdf',
                ['mime' => 'application/pdf']
            );
    }
}