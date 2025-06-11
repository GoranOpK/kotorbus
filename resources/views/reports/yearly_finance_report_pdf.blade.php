<tbody>
    @if(is_iterable($financeData))
        @foreach($financeData as $row)
        <tr>
            <td>{{ $row->mjesec }}</td>
            <td>{{ number_format($row->prihod ?? 0, 2, ',', '.') }} €</td>
        </tr>
        @endforeach
    @else
        <tr>
            <td>Ukupno</td>
            <td>{{ number_format($financeData ?? 0, 2, ',', '.') }} €</td>
        </tr>
    @endif
</tbody>