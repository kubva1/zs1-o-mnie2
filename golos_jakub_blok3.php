<?php
// Zawody z karami. Twoje zadanie ma dwa etapy — treść w zadanie.md.
// ETAP 1: napisz ciała dwóch pustych funkcji niżej.
// ETAP 2: w HTML-u dwa fragmenty kodu są napisane po dwa razy (POWTÓRKA A i POWTÓRKA B).
//         Wytnij każdy z nich do funkcji: srednia() i kategoria().
//         Strona po Twojej zmianie ma wyglądać dokładnie tak samo jak przed nią.

// ===== FUNKCJE =====

// DANE OD PROWADZĄCEGO — wzorzec, nie ruszaj. Sekundy -> "m:ss".
function formatujCzas($sekundy, $separator = ':') {
    return floor($sekundy / 60) . $separator . str_pad($sekundy % 60, 2, '0', STR_PAD_LEFT);
}

// ETAP 1. Jedno okrążenie (rekord z kluczami 'czas' i 'kary') -> czas w sekundach
// razem z doliczonymi karami. Jedna kara kosztuje 5 sekund.
// czasZKarami(['czas' => 300, 'kary' => 2]) ma zwrócić 310
function czasZKarami($okrazenie) {
    return $okrazenie['czas'] + ($okrazenie['kary'] * 5);
}

// ETAP 1. Lista okrążeń -> najlepszy, czyli najkrótszy czas z karami.
// Dla pustej listy zwróć null — nie ma z czego wybierać.
// najlepsze([]) ma zwrócić null
function najlepsze($okrazenia) {
    if (count($okrazenia) == 0) {
        return null;
    }
    $najlepszy = null;
    foreach ($okrazenia as $okrazenie) {
        $czas = czasZKarami($okrazenie);
        if ($najlepszy === null || $czas < $najlepszy) {
            $najlepszy = $czas;
        }
    }
    return $najlepszy;
}

// ETAP 2: Funkcja licząca średni czas z listy okrążeń (POWTÓRKA A)
function srednia($okrazenia) {
    if (count($okrazenia) == 0) {
        return 0;
    }
    $suma = 0;
    foreach ($okrazenia as $okrazenie) {
        $suma += czasZKarami($okrazenie);
    }
    return round($suma / count($okrazenia));
}

// ETAP 2: Funkcja zwracająca nazwę kategorii na podstawie najlepszego czasu (POWTÓRKA B)
function kategoria($najlepszyCzas) {
    if ($najlepszyCzas === null) {
        return 'brak';
    } elseif ($najlepszyCzas <= 290) {
        return 'elita';
    } elseif ($najlepszyCzas <= 310) {
        return 'zaawansowany';
    } else {
        return 'amator';
    }
}


// ===== DANE — gotowe, nie ruszaj =====
$zawodnicy = [
    ['nazwisko' => 'Ewa Barańska',   'okrazenia' => [['czas' => 280, 'kary' => 1], ['czas' => 283, 'kary' => 0], ['czas' => 279, 'kary' => 1]]],
    ['nazwisko' => 'Marek Cichoń',   'okrazenia' => [['czas' => 283, 'kary' => 1], ['czas' => 291, 'kary' => 0], ['czas' => 288, 'kary' => 0]]],
    ['nazwisko' => 'Zofia Grabiec',  'okrazenia' => [['czas' => 292, 'kary' => 0], ['czas' => 312, 'kary' => 3], ['czas' => 299, 'kary' => 1]]],
    ['nazwisko' => 'Ola Dudek',      'okrazenia' => [['czas' => 318, 'kary' => 0], ['czas' => 325, 'kary' => 2]]],
    ['nazwisko' => 'Piotr Fijałek',  'okrazenia' => []],
];

// Wszystkie okrążenia zawodów w jednej liście.
$wszystkieOkrazenia = [];
foreach ($zawodnicy as $zawodnik) {
    foreach ($zawodnik['okrazenia'] as $okrazenie) {
        $wszystkieOkrazenia[] = $okrazenie;
    }
}

$liczbaElita = 0;
foreach ($zawodnicy as $zawodnik) {
    // POWTÓRKA B (1 z 2) — zastąpione wywołaniem funkcji kategoria()
    $n = najlepsze($zawodnik['okrazenia']);
    $kat = kategoria($n);
    if ($kat == 'elita') {
        $liczbaElita++;
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Zawody z karami</title>
<style>
    body { font-family: sans-serif; margin: 2rem; }
    table { border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 0.4rem 0.8rem; text-align: left; }
    caption { font-weight: bold; margin-bottom: 0.5rem; }
    tfoot td { font-weight: bold; }
    .elita        { background: #d4f5d4; }
    .zaawansowany { background: #fff3c4; }
    .amator       { background: #f5d4d4; }
</style>
</head>
<body>
<h1>Zawody z karami: czasy okrążeń</h1>
<p>Zawodników: <?= count($zawodnicy) ?>, w kategorii elita: <?= $liczbaElita ?></p>
<table>
    <caption>Wyniki zawodników (czasy razem z karami)</caption>
    <thead>
        <tr>
            <th scope="col">Zawodnik</th>
            <th scope="col">Okrążenia</th>
            <th scope="col">Najlepsze</th>
            <th scope="col">Średnie</th>
            <th scope="col">Kategoria</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($zawodnicy as $zawodnik): ?>
            <?php
            if (count($zawodnik['okrazenia']) == 0) {
                echo '<tr><td>' . $zawodnik['nazwisko'] . '</td><td colspan="4">brak ukończonych okrążeń</td></tr>';
                continue;
            }

            $najlepszyCzas = najlepsze($zawodnik['okrazenia']);

            $czasy = [];
            foreach ($zawodnik['okrazenia'] as $okrazenie) {
                $czasy[] = formatujCzas(czasZKarami($okrazenie));
            }

            // POWTÓRKA A (1 z 2) — zastąpione wywołaniem funkcji srednia()
            $sredniCzas = srednia($zawodnik['okrazenia']);

            // POWTÓRKA B (2 z 2) — zastąpione wywołaniem funkcji kategoria()
            $kat = kategoria($najlepszyCzas);
            ?>
            <tr class="<?= $kat ?>">
                <td><?= $zawodnik['nazwisko'] ?></td>
                <td><?= implode(', ', $czasy) ?></td>
                <td><?= formatujCzas($najlepszyCzas) ?></td>
                <td><?= formatujCzas($sredniCzas) ?></td>
                <td><?= $kat ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <?php
        // POWTÓRKA A (2 z 2) — zastąpione wywołaniem funkcji srednia()
        $sredniaZawodow = srednia($wszystkieOkrazenia);

        // Trzecie nieoznaczone powtórzenie — zastąpione gotową funkcją najlepsze()
        $najlepszeZawodow = najlepsze($wszystkieOkrazenia);
        ?>
        <tr>
            <td colspan="5">
                Okrążeń łącznie: <?= count($wszystkieOkrazenia) ?>,
                najlepsze okrążenie zawodów: <?= formatujCzas($najlepszeZawodow) ?>,
                średnie okrążenie zawodów: <?= formatujCzas($sredniaZawodow) ?>
            </td>
        </tr>
    </tfoot>
</table>
</body>
</html>
