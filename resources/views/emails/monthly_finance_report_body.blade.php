<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mjesečni finansijski izvještaj</title>
</head>
<body>
    <h2>Mjesečni finansijski izvještaj</h2>
    <p>Period: {{ $month }}/{{ $year }}</p>
    <p>Ukupan prihod: <strong>{{ number_format($total, 2, ',', '.') }}€</strong></p>
    <p>U prilogu ovog emaila nalazi se PDF izvještaj.</p>
</body>
</html>