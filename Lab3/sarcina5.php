<?php
function pret_destinatie($destinatia) {
    switch ($destinatia) {
        case "Romania":  return 350;
        case "Bulgaria": return 400;
        case "Grecia":   return 550;
        case "Turcia":   return 600;
        default:         return 0;
    }
}

function cost_cazare($pret, $persoane, $nopti, $hotel) {
    $cazare = $pret * $persoane * $nopti;
    if ($hotel == "4 stars") {
        $cazare = $cazare * 1.20;
    } elseif ($hotel == "5 stars") {
        $cazare = $cazare * 1.40;
    }
    return $cazare;
}

function cost_masa($masa, $persoane, $nopti) {
    switch ($masa) {
        case "mic_dejun":$tarif = 100; break;
        case "demipensiune": $tarif = 200; break;
        case "all":$tarif = 350; break;
        default:$tarif = 0;
    }
    return $tarif * $persoane * $nopti;
}

function cost_servicii($aeroport, $asigurare, $excursie, $persoane) {
    $total = 0;
    if ($aeroport) $total += 500;
    if ($asigurare) $total += 150 * $persoane;
    if ($excursie) $total += 300 * $persoane;
    return $total;
}

function calcul_reducere($persoane, $nopti) {
    $procent = 0;
    if ($persoane >= 4) $procent += 0.05;
    if ($nopti >= 7)$procent += 0.05;
    return $procent;
}

function total_final($cazare, $masa, $servicii, $procent) {
    $subtotal = $cazare + $masa + $servicii;
    return $subtotal - $subtotal * $procent;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $nume= htmlspecialchars($_POST["nume"]);
    $destinatia = $_POST["destinatia"];
    $persoane = (int)$_POST["persoane"];
    $nopti= (int)$_POST["nopti"];
    $hotel = isset($_POST["hotel"]) ? $_POST["hotel"] : "3 stars";
    $masa= $_POST["masa"];
    $aeroport = isset($_POST["aeroport"]);
    $asigurare= isset($_POST["asigurare"]);
    $excursie= isset($_POST["excursie"]);

    if ($persoane > 0 && $nopti > 0) {
        $pret= pret_destinatie($destinatia);
        $cazare= cost_cazare($pret, $persoane, $nopti, $hotel);
        $cost_m = cost_masa($masa, $persoane, $nopti);
        $servicii= cost_servicii($aeroport, $asigurare, $excursie, $persoane);
        $procent= calcul_reducere($persoane, $nopti);
        $total = total_final($cazare, $cost_m, $servicii, $procent);

        echo "<pre>";
        echo "REZERVARE PACHET TURISTIC\n";
        echo "─────────────────────────────\n";
        echo "Client: ".$nume."<br>";
        echo "Destinatia: ".$destinatia."<br>";
        echo "Persoane: ".$persoane."<br>";
        echo "Nopti: ".$nopti."<br>";
        echo "Hotel: ".$hotel."<br>";
        echo "─────────────────────────────\n";
        echo "Cost cazare: ".$cazare." lei<br>";
        echo "Cost masa: ".$cost_m." lei<br>";
        echo "Servicii suplimentare: ".$servicii." lei<br>";
        echo "Reducere: ".($procent * 100)."%<br>";
        echo "─────────────────────────────\n";
        echo "Total final: ".$total." lei<br>";
    } else {
        echo "Introduceti valori pozitive";
    }
}
?>