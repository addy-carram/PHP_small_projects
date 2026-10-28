<?php

$meniu = [
    "Produse" => [
        "Hardware" => [
            "Laptopuri",
            "Telefoane",
            "Tablete"
        ],
        "Software" => [
            "Sisteme de operare",
            "Antivirus"
        ]
    ],
    "Servicii" => [
        "Reparații" => [
            "Laptopuri",
            "Telefoane"
        ],
        "Consultanță"
    ],
    "Contact"=>[
        "Nr Telefon"=>[
            "098678956",
            "067875645",
            "021345432"
        ]
    ]
];
function genereazaMeniu($meniu) {
$html = "<ul>";
    foreach ($meniu as $cheie => $valoare) {
        if (is_array($valoare)) {
            $html .= "<li>";
            $html .= "<a href='#'>" .$cheie." <span class='sageata'>-</span></a>";
            $html .= genereazaMeniu($valoare);
            $html .= "</li>";
        } else {
            $textElement = is_numeric($cheie) ? $valoare : $cheie;
            
            $html .= "<li><a href='#'>" . $textElement. "</a></li>";
        }
    }
    $html .= "</ul>";
    return $html;
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generator Recursiv</title>
</head>
<body>
<h2>Meniu de Navigare Generat Dinamic (Recursiv)</h2>
<nav>
    <?php 
    echo genereazaMeniu($meniu); 
    ?>
</nav>
</body>
</html>
