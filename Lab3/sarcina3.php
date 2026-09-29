<?php
function tarif_baza($zona) {
    switch ($zona) {
        case "Chisinau":return 50;
        case "Centru":return 80;
        case "Nord":return 100;
        case "Sud":return 110;
        default:  return 0;
    }
}

function cost_greutate($greutate) {
    if ($greutate > 5) {
        return ($greutate - 5) * 10;
    }
    return 0;
}

function cost_livrare($baza, $suplimentar, $tip, $valoare) {
    $livrare = $baza + $suplimentar;
    if ($tip == "express") {
        $livrare = $livrare * 1.5;   
    } elseif ($valoare >= 1500) {
        $livrare = 0;                
    }
    return $livrare;
}

function total_comanda($valoare, $livrare, $ambalare) {
    $total = $valoare + $livrare;
    if ($ambalare) {
        $total += 40;
    }
    return $total;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $nume= htmlspecialchars($_POST["nume2"]);
    $valoare= $_POST["valoarea"];
    $greutate= $_POST["greutatea"];
    $zona= $_POST["zona"];
    $tip= isset($_POST["livrare"]) ? $_POST["livrare"] : "standart";
    $ambalare= isset($_POST["ambalare"]);

    if ($valoare > 0 && $greutate > 0) {
        $baza= tarif_baza($zona);
        $suplimentar = cost_greutate($greutate);
        $livrare= cost_livrare($baza, $suplimentar, $tip, $valoare);
        $total = total_comanda($valoare, $livrare, $ambalare);

        echo "Client: ".$nume."<br>";
        echo "Valoare produse: ".$valoare." lei<br>";
        echo "Zona: ".$zona."<br>";
        echo "Tarif de baza: ".$baza." lei<br>";
        echo "Cost suplimentar greutate: ".$suplimentar." lei<br>";
        echo "Tip livrare: ".$tip."<br>";
        echo "Cost livrare: ".$livrare." lei<br>";
        echo "Ambalare cadou: ".($ambalare ? 40 : 0)." lei<br>";
        echo "Total comanda: ".$total." lei<br>";
    } else {
        echo "Introduceti valori pozitive";
    }
}
?>