<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class YearlyFinanceReportMail extends Mailable
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
     * Priprema email sa godišnjim finansijskim izvještajem u pdf-u
     */
    public function build()
    {
        // Generišemo PDF koristeći odgovarajući blade šablon iz resources/views/reports
        $pdf = Pdf::loadView('reports.yearly_finance_report_pdf', $this->data);

        return $this->subject('Godišnji finansijski izvještaj')
            ->text('emails.empty')
            ->attachData(
                $pdf->output(),
                'godisnji_finansijski_izvjestaj.pdf',
                ['mime' => 'application/pdf']
            );
    }
}