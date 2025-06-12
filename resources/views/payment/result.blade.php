{{-- 
    Prikaz poruka o neuspehu plaćanja na osnovu kategorije odgovora iz procesora kartica.
    Očekuje se da su promenljive:
    - $respCdeCat : string (kategorija greške, npr. "1", "2", "3")
    - $retryTim   : int|string|null (koliko treba čekati do novog pokušaja, opcionalno)
    - $retryPrd   : string|null ("0" = minuta, "1" = sati, "2" = dana, opciono)
--}}

@if($respCdeCat === "1")
    <div class="alert alert-danger">
        You have entered incorrect information. Please check and try again.
    </div>
@elseif($respCdeCat === "2")
    <div class="alert alert-warning">
        Payment with this card is currently not possible.
        @if($retryTim && $retryPrd !== null)
            <br>
            You can try again in {{ $retryTim }}
            @if($retryPrd === "0") minutes
            @elseif($retryPrd === "1") hours
            @elseif($retryPrd === "2") days
            @endif
        @endif
    </div>
@elseif($respCdeCat === "3")
    <div class="alert alert-danger">
        Payment with this card is not possible because the card is blocked. Please use another card.
    </div>
@endif