{{-- 
    Prikaz poruka o neuspehu plaćanja na osnovu kategorije odgovora iz procesora kartica.
    Očekuje se da su promenljive:
    - $respCdeCat : string (kategorija greške, npr. "1", "2", "3")
    - $retryTim   : int|string|null (koliko treba čekati do novog pokušaja, opcionalno)
    - $retryPrd   : string|null ("0" = minuta, "1" = sati, "2" = dana, opciono)
--}}

@if($respCdeCat === "1")
    <div class="alert alert-danger">
        Uneli ste netačne podatke. Proverite i pokušajte ponovo.
    </div>
@elseif($respCdeCat === "2")
    <div class="alert alert-warning">
        Plaćanje ovom karticom trenutno nije moguće.
        @if($retryTim && $retryPrd !== null)
            <br>
            Možete pokušati ponovo za {{ $retryTim }}
            @if($retryPrd === "0") minuta
            @elseif($retryPrd === "1") sati
            @elseif($retryPrd === "2") dana
            @endif
        @endif
    </div>
@elseif($respCdeCat === "3")
    <div class="alert alert-danger">
        Plaćanje ovom karticom nije moguće jer je kartica blokirana. Koristite drugu karticu.
    </div>
@endif