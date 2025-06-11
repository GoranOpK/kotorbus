<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Podešavanja (Settings)
    |--------------------------------------------------------------------------
    |
    | Postavi podrazumijevane vrijednosti za dompdf. Ovdje možeš dodati sve
    | definicije koje se mogu postaviti u dompdf_config.inc.php. Možeš takođe
    | prebrisati cijeli config fajl prema potrebi.
    |
    */
    'show_warnings' => false,   // Da li prikazivati warning-e iz dompdf-a (false = ne prikazuj)

    'public_path' => null,      // Ako treba, ovdje možeš prebrisati public path

    /*
     * Dejavu Sans font nedostaje pojedine znakove (€ i £), isključi ako ih trebaš prikazati.
     */
    'convert_entities' => true, // Da li automatski konvertovati specijalne znakove

    'options' => [
        /**
         * Lokacija direktorijuma za fontove koje koristi dompdf.
         * Ovaj direktorijum mora postojati i biti upisiv od strane web servera.
         */
        'font_dir' => storage_path('fonts'), // preporučena lokacija za fontove

        /**
         * Lokacija direktorijuma za cache fontova.
         */
        'font_cache' => storage_path('fonts'),

        /**
         * Lokacija privremenog direktorijuma, mora biti upisiv.
         */
        'temp_dir' => sys_get_temp_dir(),

        /**
         * dompdf "chroot": ograničava pristup dompdf-a fajlovima van ovog direktorijuma.
         * Nikad ne postavljaj na '/', zbog bezbjednosti.
         */
        'chroot' => realpath(base_path()),

        /**
         * Lista dozvoljenih protokola za pristup resursima (slike, css...).
         */
        'allowed_protocols' => [
            'data://' => ['rules' => []],
            'file://' => ['rules' => []],
            'http://' => ['rules' => []],
            'https://' => ['rules' => []],
        ],

        /**
         * Validacija putanja za logove i privremene fajlove
         */
        'artifactPathValidation' => null,

        /**
         * Putanja do fajla za log output (ako želiš da loguješ)
         */
        'log_output_file' => null,

        /**
         * Da li omogućiti subsetting fontova (smanjuje veličinu PDF-a).
         */
        'enable_font_subsetting' => false,

        /**
         * Backend za renderovanje PDF-a. Najčešće ostavi 'CPDF'.
         */
        'pdf_backend' => 'CPDF',

        /**
         * Koji media type HTML-a se renderuje u PDF. Najčešće 'screen'.
         */
        'default_media_type' => 'screen',

        /**
         * Podrazumijevana veličina papira (A4, letter...).
         */
        'default_paper_size' => 'a4',

        /**
         * Orijentacija papira (portrait ili landscape).
         */
        'default_paper_orientation' => 'portrait',

        /**
         * Podrazumijevana font porodica, koristi ako drugi font nije dostupan.
         */
        'default_font' => 'serif',

        /**
         * DPI za slike i fontove.
         */
        'dpi' => 96,

        /**
         * Dozvoli PHP kod unutar PDF-a (nije preporučljivo zbog sigurnosti).
         */
        'enable_php' => false,

        /**
         * Dozvoli JavaScript u PDF-u (radi samo u PDF reader-u, ne browser JS!).
         */
        'enable_javascript' => true,

        /**
         * Dozvoli preuzimanje slika i CSS-a sa udaljenih servera.
         * Ako je true, može biti sigurnosni rizik.
         */
        'enable_remote' => false,

        /**
         * Lista dozvoljenih udaljenih hostova (ako koristiš enable_remote).
         * Ostavi null za sve.
         */
        'allowed_remote_hosts' => null,

        /**
         * Odnos visine fonta, podešava razmak između linija.
         */
        'font_height_ratio' => 1.1,

        /**
         * Da li koristiti HTML5 parser (uvijek true u dompdf 2.x)
         */
        'enable_html5_parser' => true,
    ],

];