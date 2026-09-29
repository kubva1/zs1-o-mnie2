<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$db = mysqli_connect('db.peciak.xyz', 'zs1', 'jMXVKs5Ao7', 'zawody');
mysqli_set_charset($db, 'utf8mb4');
// ===== FUNKCJE =====
function formatujCzas($sekundy, $separator = ':') {
    return floor($sekundy / 60) . $separator . str_pad($sekundy % 60, 2, '0', STR_PAD_LEFT);
}
function czasZKarami($okrazenie) {
    return $okrazenie['czas'] + $okrazenie['kary'] * 5;
}
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
// ===== SELECT ZAWODNIKÓW =====
$zawodnicy = [];
$wynik = mysqli_query($db, "SELECT id, nazwa FROM zawodnicy ORDER BY id");
while ($wiersz = mysqli_fetch_assoc($wynik)) {
    $zawodnicy[(int)$wiersz['id']] = [
        'nazwisko' => $wiersz['nazwa'],
        'okrazenia' => []
    ];
}
$blad = '';
// ===== OBSŁUGA FORMULARZA =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['zawodnik'])) {
        $blad = "Brak pola zawodnik";
    } elseif (!ctype_digit($_POST['zawodnik'])) {
        $blad = "Zawodnik musi być liczbą całkowitą";
    } else {
        $nr = (int)$_POST['zawodnik'];
        if (!isset($zawodnicy[$nr])) {
            $blad = "Nie ma takiego zawodnika";
        } elseif (!isset($_POST['czas'])) {
            $blad = "Brak pola czas";
        } elseif (!ctype_digit($_POST['czas'])) {
            $blad = "Czas musi być liczbą całkowitą";
        } else {
            $czas = (int)$_POST['czas'];
            if ($czas < 60 || $czas > 900) {
                $blad = "Czas musi być od 60 do 900";
            } elseif (!isset($_POST['kary'])) {
                $blad = "Brak pola kary";
            } elseif (!ctype_digit($_POST['kary'])) {
                $blad = "Kary muszą być liczbą całkowitą";
            } else {
                $kary = (int)$_POST['kary'];
                if ($kary < 0 || $kary > 10) {
                    $blad = "Kary muszą być od 0 do 10";
                } else {
                    mysqli_query(
                        $db,
                        "INSERT INTO okrazenia (zawodnik_id, czas, kary)
                         VALUES ($nr, $czas, $kary)"
                    );
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                }
            }
        }
    }
}
// ===== SELECT OKRĄŻEŃ =====
$wynik = mysqli_query($db,"SELECT zawodnik_id, czas, kary FROM okrazenia ORDER BY id");
while ($wiersz = mysqli_fetch_assoc($wynik)) {

    $nr = (int)$wiersz['zawodnik_id'];

    if (isset($zawodnicy[$nr])) {
        $zawodnicy[$nr]['okrazenia'][] = [
            'czas' => (int)$wiersz['czas'],
            'kary' => (int)$wiersz['kary']
        ];
    }
}
// ===== POCHODNE =====
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
body {
    font-family: sans-serif;
    margin: 2rem;
}
table {
    border-collapse: collapse;
}
th,
td {
    border: 1px solid #999;
    padding: 0.4rem 0.8rem;
    text-align: left;
}
caption {
    font-weight: bold;
    margin-bottom: 0.5rem;
}
tfoot td {
    font-weight: bold;
}
form {
    margin-bottom: 1rem;
}
label {
    margin-right: 1rem;
}
.blad {
    background: #f5d4d4;
    padding: 0.5rem;
    display: inline-block;
}
.elita {
    background: #d4f5d4;
}
.zaawansowany {
    background: #fff3c4;
}
.amator {
    background: #f5d4d4;
}
</style>
</head>
<body>
<h1>Zawody z karami: czasy okrążeń</h1>
<?php if ($blad !== ''): ?>
    <p class="blad">
        <?= $blad ?>
    </p>
<?php endif; ?>
<form method="post">
    <label>Zawodnik:
        <select name="zawodnik">
            <?php foreach ($zawodnicy as $i => $zawodnik): ?>

                <option value="<?= $i ?>">
                    <?= $zawodnik['nazwisko'] ?>
                </option>

            <?php endforeach; ?>

        </select>
    </label>
    <label>
        Czas w sekundach:
        <input type="number" name="czas" required>
    </label>
    <label>
        Kary:
        <input type="number" name="kary" value="0" required>
    </label>
    <button type="submit">
        Dodaj okrążenie
    </button>
</form>
<p>
    Zawodników: <?= count($zawodnicy) ?>,
    w kategorii elita: <?= $liczbaElita ?>
</p>
<table>
    <caption>
        Wyniki zawodników (czasy razem z karami)
    </caption>
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
                echo '<tr>
                        <td>' . $zawodnik['nazwisko'] . '</td>
                        <td colspan="4">brak ukończonych okrążeń</td>
                      </tr>';
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
                <td>
                    <?= $zawodnik['nazwisko'] ?>
                </td>
                <td>
                    <?= implode(', ', $czasy) ?>
                </td>
                <td>
                    <?= formatujCzas($najlepszyCzas) ?>
                </td>
                <td>
                    <?= formatujCzas($sredniCzas) ?>
                </td>
                <td>
                    <?= $kat ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
    <tfoot>
        <tr>
            <td colspan="5">
                Okrążeń łącznie:
                <?= count($wszystkieOkrazenia) ?>,
                <?php if (count($wszystkieOkrazenia) > 0): ?>
                    najlepsze okrążenie zawodów:
                    <?= formatujCzas(najlepsze($wszystkieOkrazenia)) ?>,
                    średnie okrążenie zawodów:
                    <?= formatujCzas(srednia($wszystkieOkrazenia)) ?>
                <?php else: ?>
                    brak okrążeń
                <?php endif; ?>
            </td>
        </tr>
    </tfoot>
</table>
</body>
</html>