<?php
// Zawody z karami, blok 4. Treść zadania w zadanie.md.
// Funkcje, dane, tabela i formularz są gotowe. Formularz już wysyła dane POST-em,
// ale nikt ich jeszcze nie odbiera. Twoja robota to sekcja OBSŁUGA FORMULARZA niżej:
// sprawdź, co przyszło, i dopisz okrążenie tylko wtedy, gdy ma sens.

// ===== FUNKCJE — gotowe, nie ruszaj =====

// Sekundy -> "m:ss".
function formatujCzas($sekundy, $separator = ':') {
    return floor($sekundy / 60) . $separator . str_pad($sekundy % 60, 2, '0', STR_PAD_LEFT);
}

// Jedno okrążenie -> czas w sekundach razem z doliczonymi karami. Kara kosztuje 5 sekund.
function czasZKarami($okrazenie) {
    return $okrazenie['czas'] + $okrazenie['kary'] * 5;
}

// Najlepszy (najkrótszy) czas z karami. Pusta lista -> null, bo nie ma z czego wybierać.
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

// Średni czas z karami, zaokrąglony. Pusta lista -> null.
function srednia($okrazenia) {
    if (count($okrazenia) == 0) {
        return null;
    }
    $suma = 0;
    foreach ($okrazenia as $okrazenie) {
        $suma += czasZKarami($okrazenie);
    }
    return round($suma / count($okrazenia));
}

// Jedna liczba wchodzi, jeden string wychodzi. Ten sam string idzie do tekstu i do class.
function kategoria($najlepsze) {
    if ($najlepsze === null) {
        return 'brak';
    } elseif ($najlepsze <= 290) {
        return 'elita';
    } elseif ($najlepsze <= 310) {
        return 'zaawansowany';
    }
    return 'amator';
}

// ===== DANE — gotowe, nie ruszaj =====
// Numer zawodnika to jego indeks w tablicy: 0 to Barańska, 4 to Fijałek.
$zawodnicy = [
    ['nazwisko' => 'Ewa Barańska',   'okrazenia' => [['czas' => 280, 'kary' => 1], ['czas' => 283, 'kary' => 0], ['czas' => 279, 'kary' => 1]]],
    ['nazwisko' => 'Marek Cichoń',   'okrazenia' => [['czas' => 283, 'kary' => 1], ['czas' => 291, 'kary' => 0], ['czas' => 288, 'kary' => 0]]],
    ['nazwisko' => 'Zofia Grabiec',  'okrazenia' => [['czas' => 292, 'kary' => 0], ['czas' => 312, 'kary' => 3], ['czas' => 299, 'kary' => 1]]],
    ['nazwisko' => 'Ola Dudek',      'okrazenia' => [['czas' => 318, 'kary' => 0], ['czas' => 325, 'kary' => 2]]],
    ['nazwisko' => 'Piotr Fijałek',  'okrazenia' => []],
];

// Komunikat o błędzie. Pusty = nie ma czego pokazać. HTML niżej wyświetla go nad formularzem.
$blad = '';

// ===== OBSŁUGA FORMULARZA =====
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $zawodnik = $_POST['zawodnik'];
    $czas = $_POST['czas'];
    $kary = $_POST['kary'];

    if(!isset($zawodnik) && !isset($czas) && !isset($kary)) {
        $blad = "Brak pol formularza";
        die();
    }

    if(!ctype_digit($czas)) {
        $blad = "Czas nie jest liczba";
        die();
    }

    if($czas <= 0) {
        $blad = "Czas musi byc dodatni";
        die();
    }

    if(!ctype_digit($kary)) {
        $blad = "Kara nie jest liczba";
        die();
    }

    if($kary <= 0) {
        $blad = "Kara musi byc dodatnia";
        die();
    }

    $zawodnicy[$zawodnik]['okrazenia'][] = ['czas' => $czas, 'kary' => $kary];
    // zamiast die() lepiej else if , else na koncu.


}


// ===== POCHODNE — liczone po obsłudze formularza, nie przed nią =====
$wszystkieOkrazenia = [];
foreach ($zawodnicy as $zawodnik) {
    foreach ($zawodnik['okrazenia'] as $okrazenie) {
        $wszystkieOkrazenia[] = $okrazenie;
    }
}

$liczbaElita = 0;
foreach ($zawodnicy as $zawodnik) {
    if (kategoria(najlepsze($zawodnik['okrazenia'])) == 'elita') {
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
    form { margin-bottom: 1rem; }
    label { margin-right: 1rem; }
    .blad { background: #f5d4d4; padding: 0.5rem; display: inline-block; }
    .elita        { background: #d4f5d4; }
    .zaawansowany { background: #fff3c4; }
    .amator       { background: #f5d4d4; }
</style>
</head>
<body>
<h1>Zawody z karami: czasy okrążeń</h1>

<?php if ($blad !== ''): ?>
    <p class="blad"><?= $blad ?></p>
<?php endif; ?>

<form method="post">
    <label>Zawodnik:
        <select name="zawodnik">
            <?php foreach ($zawodnicy as $i => $zawodnik): ?>
                <option value="<?= $i ?>"><?= $zawodnik['nazwisko'] ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Czas w sekundach: <input type="number" name="czas" required></label>
    <label>Kary: <input type="number" name="kary" value="0" required></label>
    <button type="submit">Dodaj okrążenie</button>
</form>

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

            $sredniCzas = srednia($zawodnik['okrazenia']);
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
        <tr>
            <td colspan="5">
                Okrążeń łącznie: <?= count($wszystkieOkrazenia) ?>,
                najlepsze okrążenie zawodów: <?= formatujCzas(najlepsze($wszystkieOkrazenia)) ?>,
                średnie okrążenie zawodów: <?= formatujCzas(srednia($wszystkieOkrazenia)) ?>
            </td>
        </tr>
    </tfoot>
</table>
</body>
</html>
