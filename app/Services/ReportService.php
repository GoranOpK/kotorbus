<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\VehicleTypeReportMail;
use App\Mail\FinancialReportMail;

class ReportService
{
    // Adrese na koje šaljemo izvještaje
    protected $emails = [
        'vladimir.jokic@kotor.me',
        'prihodi@kotor.me',
        'mirjana.grbovic@kotor.me',
    ];

    // ===========================
    // PODACI ZA IZVJEŠTAJE
    // ===========================

    /**
     * Dnevni izvještaj: broj rezervacija po tipu vozila za određeni dan.
     *
     * @param  string $date  Datum (npr. '2025-05-31')
     * @return \Illuminate\Support\Collection
     */
    public function dailyVehicleReservationsByType($date)
    {
        return Reservation::whereDate('reservation_date', $date)
            ->join('vehicle_types', 'reservations.vehicle_type_id', '=', 'vehicle_types.id')
            ->select('vehicle_types.name as tip_vozila', DB::raw('COUNT(*) as broj_rezervacija'))
            ->groupBy('vehicle_types.name')
            ->get();
    }

    /**
     * Mjesečni izvještaj: broj rezervacija po tipu vozila za određeni mjesec i godinu.
     */
    public function monthlyVehicleReservationsByType($month, $year)
    {
        return Reservation::whereYear('reservation_date', $year)
            ->whereMonth('reservation_date', $month)
            ->join('vehicle_types', 'reservations.vehicle_type_id', '=', 'vehicle_types.id')
            ->select('vehicle_types.name as tip_vozila', DB::raw('COUNT(*) as broj_rezervacija'))
            ->groupBy('vehicle_types.name')
            ->get();
    }

    /**
     * Godišnji izvještaj: broj rezervacija po tipu vozila za određenu godinu.
     */
    public function yearlyVehicleReservationsByType($year)
    {
        return Reservation::whereYear('reservation_date', $year)
            ->join('vehicle_types', 'reservations.vehicle_type_id', '=', 'vehicle_types.id')
            ->select('vehicle_types.name as tip_vozila', DB::raw('COUNT(*) as broj_rezervacija'))
            ->groupBy('vehicle_types.name')
            ->get();
    }

    /**
     * Finansijski izvještaj - zbir prihoda po danu, mjesecu, godini.
     */
    public function dailyFinancialReport($date)
    {
        return Reservation::whereDate('reservation_date', $date)
            ->sum('total_price');
    }

    public function monthlyFinancialReport($month, $year)
    {
        return Reservation::whereYear('reservation_date', $year)
            ->whereMonth('reservation_date', $month)
            ->sum('total_price');
    }

    public function yearlyFinancialReport($year)
    {
        return Reservation::whereYear('reservation_date', $year)
            ->sum('total_price');
    }

    // ===========================
    // SLANJE IZVJEŠTAJA
    // ===========================

    /**
     * Šalje izvještaj o rezervacijama po tipu vozila za zadani period na više adresa.
     * $periodType: 'daily', 'monthly', 'yearly'
     * $params: ['date' => ..., 'month' => ..., 'year' => ...]
     */
    public function sendVehicleTypeReport($periodType, $params)
    {
        switch ($periodType) {
            case 'daily':
                $data = $this->dailyVehicleReservationsByType($params['date']);
                $periodLabel = 'Dnevni izvještaj';
                $view = 'reports.vehicle_type_daily';
                $periodString = $params['date'];
                break;
            case 'monthly':
                $data = $this->monthlyVehicleReservationsByType($params['month'], $params['year']);
                $periodLabel = 'Mjesečni izvještaj';
                $view = 'reports.vehicle_type_monthly';
                $periodString = "{$params['month']}.{$params['year']}";
                break;
            case 'yearly':
                $data = $this->yearlyVehicleReservationsByType($params['year']);
                $periodLabel = 'Godišnji izvještaj';
                $view = 'reports.vehicle_type_yearly';
                $periodString = (string)$params['year'];
                break;
            default:
                throw new \InvalidArgumentException('Nepoznat period');
        }

        $pdf = Pdf::loadView($view, [
            'podaci' => $data,
            'period' => $periodLabel,
            'periodString' => $periodString,
        ]);

        // Slanje na više adresa
        Mail::to($this->emails)
            ->send(new VehicleTypeReportMail($pdf, $periodLabel, $periodString));
    }

    /**
     * Šalje finansijski izvještaj za zadani period na više adresa.
     * $periodType: 'daily', 'monthly', 'yearly'
     * $params: ['date' => ..., 'month' => ..., 'year' => ...]
     */
    public function sendFinancialReport($periodType, $params)
    {
        switch ($periodType) {
            case 'daily':
                $total = $this->dailyFinancialReport($params['date']);
                $periodLabel = 'Dnevni finansijski izvještaj';
                $view = 'reports.financial_daily';
                $periodString = $params['date'];
                break;
            case 'monthly':
                $total = $this->monthlyFinancialReport($params['month'], $params['year']);
                $periodLabel = 'Mjesečni finansijski izvještaj';
                $view = 'reports.financial_monthly';
                $periodString = "{$params['month']}.{$params['year']}";
                break;
            case 'yearly':
                $total = $this->yearlyFinancialReport($params['year']);
                $periodLabel = 'Godišnji finansijski izvještaj';
                $view = 'reports.financial_yearly';
                $periodString = (string)$params['year'];
                break;
            default:
                throw new \InvalidArgumentException('Nepoznat period');
        }

        $pdf = Pdf::loadView($view, [
            'total' => $total,
            'period' => $periodLabel,
            'periodString' => $periodString,
        ]);

        Mail::to($this->emails)
            ->send(new FinancialReportMail($pdf, $periodLabel, $periodString, $total));
    }

    // ===========================
    // DOWNLOAD (opciono)
    // ===========================

    /**
     * Vrati PDF izvještaj za preuzimanje (za admin panel).
     */
    public function downloadVehicleTypeReport($periodType, $params)
    {
        switch ($periodType) {
            case 'daily':
                $data = $this->dailyVehicleReservationsByType($params['date']);
                $view = 'reports.vehicle_type_daily';
                $periodString = $params['date'];
                break;
            case 'monthly':
                $data = $this->monthlyVehicleReservationsByType($params['month'], $params['year']);
                $view = 'reports.vehicle_type_monthly';
                $periodString = "{$params['month']}.{$params['year']}";
                break;
            case 'yearly':
                $data = $this->yearlyVehicleReservationsByType($params['year']);
                $view = 'reports.vehicle_type_yearly';
                $periodString = (string)$params['year'];
                break;
            default:
                throw new \InvalidArgumentException('Nepoznat period');
        }

        $pdf = Pdf::loadView($view, [
            'podaci' => $data,
            'periodString' => $periodString,
        ]);

        return $pdf->download('izvjestaj_po_tipovima_vozila_'.$periodString.'.pdf');
    }

    public function downloadFinancialReport($periodType, $params)
    {
        switch ($periodType) {
            case 'daily':
                $total = $this->dailyFinancialReport($params['date']);
                $view = 'reports.financial_daily';
                $periodString = $params['date'];
                break;
            case 'monthly':
                $total = $this->monthlyFinancialReport($params['month'], $params['year']);
                $view = 'reports.financial_monthly';
                $periodString = "{$params['month']}.{$params['year']}";
                break;
            case 'yearly':
                $total = $this->yearlyFinancialReport($params['year']);
                $view = 'reports.financial_yearly';
                $periodString = (string)$params['year'];
                break;
            default:
                throw new \InvalidArgumentException('Nepoznat period');
        }

        $pdf = Pdf::loadView($view, [
            'total' => $total,
            'periodString' => $periodString,
        ]);

        return $pdf->download('finansijski_izvjestaj_'.$periodString.'.pdf');
    }
}