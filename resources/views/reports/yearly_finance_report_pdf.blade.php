<meta charset="UTF-8">
<tbody>
    @foreach($financeData as $row)
        <tr>
            <td>{{ $row['mjesec'] }}</td>
            <td>{{ number_format($row['prihod'] ?? 0, 2, ',', '.') }} &euro;</td>
        </tr>
    @endforeach
</tbody>