<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Barryvdh\DomPDF\Facade\Pdf;

class DailyFinanceReportMail extends Mailable
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
     * Priprema email sa dnevnim finansijskim izvještajem u pdf-u
     */
    public function build()
    {
        // Generišemo PDF koristeći odgovarajući blade šablon iz resources/views/reports
        $pdf = Pdf::loadView('reports.daily_finance_report_pdf', $this->data);

        // Vraćamo mail sa subject-om i pdf-om u atačmentu
        return $this->subject('Dnevni finansijski izvještaj')
            ->text('emails.empty') // Poruka u tijelu maila (možeš promijeniti po potrebi)
            ->attachData(
                $pdf->output(), 
                'dnevni_finansijski_izvjestaj.pdf', 
                ['mime' => 'application/pdf']
            );
    }
}