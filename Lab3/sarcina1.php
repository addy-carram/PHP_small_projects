<?php
function calcul($ore, $tarif) {
    return $ore * $tarif;
}

function calcul_suplimentar($ore, $tarif) {
   
    return ($ore - 160) * $tarif * 0.5;
}

function calcul_vechime($salariu, $an) {
    if ($an > 5 && $an < 9) {
        return $salariu * 0.05;
    }
    return $salariu * 0.1;
}

function calcul_weekend() {
    return 300;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $nume = ($_POST["nume"]);
    $ore = $_POST["ore"];
    $tarif= $_POST["taxa"];
    $vechime = $_POST["vechime"];
    $a_lucrat = isset($_POST["lucrat"]);

    if ($tarif > 0 && $vechime >= 0 && $ore > 0) {
        $salariu = calcul($ore, $tarif);
         $salariu_initial = calcul($ore, $tarif);
        echo "Salariul pentru ".$nume."<br>";
        if($ore<160){
        echo "Ore normale: ".$ore."<br>";
        }
        else echo "Ore normale max:160<br>";
        
        echo "Plata ore normale: ".calcul($ore, $tarif)."<br>";
        if ($ore > 160) {
            $salariu += calcul_suplimentar($ore, $tarif);
            echo "Ore suplimentare: ".($ore-160)."<br>";
            echo "Plata ore suplimentare: ".calcul_suplimentar($ore, $tarif)."<br>";
        }
        if ($vechime > 5) {
        $salariu += calcul_vechime($salariu, $vechime);
        echo "Bonus vechime: ".calcul_vechime($salariu_initial,$vechime)."<br>";
        }
        if ($a_lucrat) {
            $salariu += calcul_weekend();
            echo "Bonus weekend: ".calcul_weekend()."<br>";
        }
        echo "Salariu final: ".$salariu."<br>";
    } else {
        echo "Introduceti valori pozitive";
    }
}
?>