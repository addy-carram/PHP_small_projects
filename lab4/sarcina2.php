<?php
function suma( ...$n){
$a=0;
foreach($n as $number){
    $a=$a+$number;
};
return $a;
}

function produs( ...$n){
$a=1;
foreach($n as $number){
    $a=$a*$number;
};
return $a;
}
function media( ...$n){
$a=0;
$numere=0;
foreach($n as $number){
    $a=$a+$number;
    $numere++;
};
return ($a/$numere);
}
function minim( ...$n){
$a=0;
foreach($n as $number){
    $a=$number;
};
$min=$number;
foreach($n as $number){
    if($min>$number){
        $min=$number;
    }
};
return $min;
}
function maxim( ...$n){
$a=0;
foreach($n as $number){
    $a=$number;
};
$max=$number;
foreach($n as $number){
    if($max<$number){
        $max=$number;
    }
};
return $max;
}
function oper($operatie, ...$n){
    switch($operatie){
        case "suma" : return suma(...$n);
        case "produs" : return produs(...$n);
        case "minim" : return minim(...$n);
        case "maxim" : return maxim(...$n);
        case "media" : return media(...$n);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
   
    $numere = $_POST["numere"]; 
    $numere_separ=explode(",",$numere);
    $numere=$numere_separ;
    $operatii= $_POST["operatii"];
    echo "La ".$operatii." este ".oper($operatii, ...$numere);
}

