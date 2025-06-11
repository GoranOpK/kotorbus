<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Dnevni izvještaj o rezervacijama po tipu vozila' }}</title>
    <style>
        /* Stilovi za PDF prikaz */
        body { font-family: DejaVu Sans, Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; margin-top: 24px; }
        th, td { border: 1px solid #cccccc; padding: 8px 12px; text-align: left; }
        th { background: #eeeeee; }
    </style>
</head>
<body>
    <!-- Naslov izvještaja -->
    <h2>{{ $title ?? 'Dnevni izvještaj o rezervacijama po tipu vozila' }}</h2>
    <!-- Prikaz datuma izvještaja -->
    <p>Datum: {{ $date }}</p>
    <table>
        <thead>
            <tr>
                <th>Tip vozila</th>
                <th>Broj rezervacija</th>
            </tr>
        </thead>
        <tbody>
        <!-- Prikaz svih tipova vozila i broja rezervacija za svaki tip -->
        @foreach ($reservationsByType as $row)
            <tr>
                <td>
                    <!-- Prikaz imena tipa vozila, provjerava više mogućih polja u objektu -->
                    @if(isset($row->tip_vozila))
                        {{ $row->tip_vozila }}
                    @elseif(isset($row->vehicleType) && isset($row->vehicleType->name))
                        {{ $row->vehicleType->name }}
                    @else
                        Nepoznat tip
                    @endif
                </td>
                <td>
                    <!-- Prikaz broja rezervacija za taj tip vozila, provjerava više mogućih polja -->
                    @if(isset($row->broj_rezervacija))
                        {{ $row->broj_rezervacija }}
                    @elseif(isset($row->count))
                        {{ $row->count }}
                    @else
                        0
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</body>
</html>