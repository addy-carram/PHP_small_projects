<?php
function taxa_curs($curs){
switch ($curs) {
        case "PHP Web Development": return 1200;
        case "JavaScript":  return 1400;
        case "Web Design": return 1000;
        case "Python": return 1500;
        default: return 0;
    }
}
function calcul_reducere($taxa, $pachet) {
    if ($pachet == "elev") {
        return $taxa * 0.15;
    } elseif ($pachet == "student") {
        return $taxa * 0.10;
    }
    return 0; 
}
function calcul_servicii($material, $certificat, $acces) {
    $total = 0;
    if ($material) $total += 150;
    if ($certificat) $total += 100;
    if ($acces) $total += 200;
    return $total;
}

function suma_finala($taxa, $reducere, $servicii) {
    return $taxa - $reducere + $servicii;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $nume= htmlspecialchars($_POST["nume1"]);
    $varsta= $_POST["varsta"];
    $email= htmlspecialchars($_POST["email"]);
    $curs= $_POST["curs"];
    $pachet= isset($_POST["packet"]) ? $_POST["packet"] : "adult";
    $material= isset($_POST["material"]);
    $certificat = isset($_POST["certificat"]);
    $acces=isset($_POST["acces"]);
    $taxa= taxa_curs($curs);
    $reducere= calcul_reducere($taxa, $pachet);
    $servicii= calcul_servicii($material, $certificat, $acces);
    $final= suma_finala($taxa, $reducere, $servicii);

    echo "Nume: ".$nume."<br>";
    echo "Varsta: ".$varsta."<br>";
    echo "Email: ".$email."<br>";
    echo "Curs: ".$curs."<br>";
    echo "Taxa curs: ".$taxa." lei<br>";
    echo "Reducere: ".$reducere." lei<br>";
    echo "Servicii suplimentare: ".$servicii." lei<br>";
    echo "Suma finala: ".$final." lei<br>";
}
?>