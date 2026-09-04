<?php
$a=1;
$b=2;
$c=1;
$delta=$b*$b-4*$a*$c;
// sarcina1 calcularea necunoscutei aunei ecuatii de gr2
if($delta<0){
    echo" Nu exista solutii reale";
}
else if($delta==0){
    $x=-$b/(2*$a);
    echo "solutia este:$x";
}
else{
    $x1=-$b-sqrt($delta)/(2*$a);
    $x2=-$b+sqrt($delta)/(2*$a);
    echo "solutiile sunt:$x1 si $x2";
}
echo"<br></br>";

$a=0;
$b=3;
// sarcina2 verificare daca doua numere sunt consecutive
if(($a-$b==1 && $b-$a==-1) or($b-$a==1 && $a-$b==-1)){
    echo "Adevarat";
}
else{
    echo "Fals";
}

echo"<br></br>";

$a=3; $b=16; $c=4.2;
// sarcina 3 interschimarea valorilor in mod circular
$mijloc=$c;
$c=$b;
$b=$a;
$a=$mijloc;
echo "a=$a , b=$b , c=$c";
