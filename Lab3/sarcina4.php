<?php
function scor($teorie, $practica, $interviu) {
    return $teorie * 0.3 + $practica * 0.5 + $interviu * 0.2;
}

function experienta($ani) {
    if ($ani >= 5) {
        return 0.5;
    } elseif ($ani >= 3) {
        return 0.3;
    }
    return 0;
}

function finall($ponderat, $bonus_exp, $certificat) {
    $scor = $ponderat + $bonus_exp;
    if ($certificat) {
        $scor += 0.2;
    }
    if ($scor > 10) {
        $scor = 10;
    }
    return $scor;
}

function rezultat($practica, $scor) {
    if ($practica < 5) {
        return "Respins";
    }
    if ($scor >= 6) {
        return "Admis";
    }
    return "Respins";
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $nume= htmlspecialchars($_POST["nume4"]);
    $teorie = $_POST["teoretica"];
    $practica= $_POST["practica"];
    $interviu= $_POST["interviu"];
    $experienta= $_POST["experienta"];
    $certificat= isset($_POST["certificat"]);

    if ($teorie >= 1 && $teorie <= 10 &&
        $practica >= 1 && $practica <= 10 &&
        $interviu >= 1 && $interviu <= 10 &&
        $experienta >= 0) {

        $ponderat  = scor($teorie, $practica, $interviu);
        $bonus_exp = experienta($experienta);
        $final     = finall($ponderat, $bonus_exp, $certificat);
        $rezultat  = rezultat($practica, $final);

        echo "Candidat: ".$nume."<br>";
        echo "Scor ponderat: ".$ponderat."<br>";
        echo "Bonus experienta: ".$bonus_exp."<br>";
        echo "Bonus certificat: ".($certificat ? 0.2 : 0)."<br>";
        echo "Scor final: ".$final."<br>";
        echo "Rezultat: ".$rezultat."<br>";
    } else {
        echo "Notele trebuie sa fie intre 1 si 10, iar experienta nu poate fi negativa";
    }
}
?>