<?php
$zawodnicy = [
    ['nazwisko' => 'Anna Kowalska',    'okrazenia' => [312, 298, 305]],
    ['nazwisko' => 'Piotr Nowak',      'okrazenia' => [355, 430, 341]],
    ['nazwisko' => 'Marek Zieliński',  'okrazenia' => []],
    ['nazwisko' => 'Ewa Wiśniewska',   'okrazenia' => [402, 377, 395]],
];
?>
<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="utf-8">
<title>Wyniki zawodów</title>
<style>
    body { font-family: sans-serif; margin: 2rem; }
    table { border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 0.4rem 0.8rem; text-align: left; }
    caption { font-weight: bold; margin-bottom: 0.5rem; }
    tfoot td { font-weight: bold; }
    .elita        { background: #d4f5d4; }
    .zaawansowany { background: #fff3c4; }
    .amator       { background: #f5d4d4; }
    .brak         {text-align: center;}
</style>
</head>
<body>
<h1>Zawody biegowe: czasy okrążeń</h1>
<table>
    <caption>Wyniki zawodników</caption>
    <thead>
        <tr>
            <th scope="col">Zawodnik</th>
            <th scope="col">Okrążenia</th>
            <th scope="col">Najlepsze</th>
            <th scope="col">Średnie</th>
            <th scope="col">Kategoria</th>
            <th scope="col">Uwagi</th>
        </tr>
    </thead>
    <tbody>
        <?php
        function formatczas($sekundy, $separator = ":") {
            $minuty = floor($sekundy / 60);
            $sekundy = $sekundy % 60;
            return $minuty . $separator . str_pad($sekundy, 2, "0", STR_PAD_LEFT);
        }
            $zawodnicyLiczba = 0;
            $elita = 0;
            $najlepszeZawodow = null;
            $okrazeniaLiczba = 0;
 
            foreach ($zawodnicy as $value) {
                $zawodnicyLiczba++;
                echo "<tr>";
                echo "<td>" . $value['nazwisko'] . "</td>";
                if (count($value['okrazenia']) == 0) {
                    echo "<td colspan='5' class='brak'>brak ukończonych okrążeń</td>";
                    echo "</tr>";
                    continue;
                }
                $okrazeniaLiczba += count($value['okrazenia']);
                $najlepsze = min($value['okrazenia']);
 
                if ($najlepszeZawodow == null || $najlepsze < $najlepszeZawodow)
                    $najlepszeZawodow = $najlepsze;
 
                $czasy = [];
                foreach ($value['okrazenia'] as $czas) {
                    $czasy[] = formatczas($czas);
                }
                $srednia = round(array_sum($value['okrazenia']) / count($value['okrazenia']));
                $srednia = formatczas($srednia);
                if ($najlepsze < 300) {
                    $kategoria = "elita";
                    $elita++;
                } elseif ($najlepsze < 360) {
                    $kategoria = "zaawansowany";
                } else {
                    $kategoria = "amator";
                }
                $uwagi = [];
                if (max($value['okrazenia']) >= 420)
                    $uwagi[] = "słabe okrążenie";
 
                if (max($value['okrazenia']) <= $najlepsze + 15)
                    $uwagi[] = "równe tempo";
                echo "<td>" . implode(", ", $czasy) . "</td>";
                echo "<td>" . formatczas($najlepsze) . "</td>";
                echo "<td>" . $srednia . "</td>";
                echo "<td class='$kategoria'>" . $kategoria . "</td>";
                echo "<td>" . (empty($uwagi) ? "brak" : implode(", ", $uwagi)) . "</td>";
                echo "</tr>";
            }
        ?>
<tfoot>
        <?php
            echo "<tr>";
            echo "<td> Zawodnicy: " . $zawodnicyLiczba . "</td>";
            echo "<td> Ilość okrążeń: " . $okrazeniaLiczba . "</td>";
            echo "<td colspan='2'> Najlepsze okrążenie: " . formatCzas($najlepszeZawodow) . "</td>";
            echo "<td colspan='2'> Ilość Elit: " . $elita . "</td>";
            echo "</tr>";
         ?>
</tfoot>
</table>
</body>
</html>