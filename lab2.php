<?php
//prima sarcina
$a=1;
$b=24;
$cifra;
$suma=0;
$estepar=0;
$nuCinci=0;
$nrNumere=0;
$sumaFinal=0;
echo "Numerele sunt: ";
for($a=1; $a<=$b; $a++){
$copy=$a;
while($copy!=0){
    $cifra=$copy%10;
    if($cifra%2==0){
        $estepar++;
    }
    if($a%5!=0){
        $nuCinci++;
    }
    $suma=$suma+$cifra;
    $copy = intdiv($copy, 10);
    
}
if($a%$suma==0){
    if($estepar!=0 && $nuCinci!=0){
        echo "$a "; $nrNumere++; $sumaFinal=$sumaFinal+$a;
    }
}
$estepar=0;$nuCinci=0;$suma=0;
}
echo" <br> Nr lor $nrNumere";
echo"<br> suma lor $sumaFinal";


$a = 1;
$b = 30; 

$nrMax = 0;
$nrSuma = 0;
$nrNumbers = 0;
$prevPrime = 0;     
$firstPeer = 0;
$secondPeer = 0;
$maxDiferenta = 0;
$diferenta = 0;

echo "<br> Numerele prime: ";

for ($a; $a < $b; $a++) {

    $nrImpartire = 0;  

    for ($i = 1; $i <= $a; $i++) {
        if ($a % $i == 0) $nrImpartire++;
    }

    if ($nrImpartire == 2) {   
        echo "$a ";
        $nrSuma = $nrSuma + $a;
        $nrNumbers++;

        if ($nrMax < $a) {
            $nrMax = $a;
        }

        if ($prevPrime != 0) {
            $diferenta = $a - $prevPrime;
            if ($maxDiferenta < $diferenta) {
                $maxDiferenta = $diferenta;
                $firstPeer = $prevPrime;
                $secondPeer = $a;
            }
        }

        $prevPrime = $a;
    }
}

echo "<br>";
echo "Nr lor: $nrNumbers <br>";
echo "Max numar: $nrMax <br>";
echo "Suma nr prime: $nrSuma <br>";
echo "Diferenta maxima: $maxDiferenta <br>";
echo "Perechea de numere: $firstPeer si $secondPeer <br> <br>";


//a treia sarcina
$a = 1;
$b = 30;

$count = 0;       
$sumaTotala = 0;  

echo "Numerele Armstrong din intervalul [$a, $b] sunt:<br>";

for ($numar = $a; $numar <= $b; $numar++) {

    // 1. Aflam numarul de cifre al numarului curent
    $temp = $numar;
    $nrCifre = 0;
    while ($temp != 0) {
        $nrCifre++;
        $temp = (int)($temp / 10);
    }

    if ($numar == 0) {
        $nrCifre = 1;
    }

    $temp = $numar;
    $suma = 0;
    while ($temp != 0) {
        $cifra = $temp % 10;

        $putere = 1;
        for ($i = 1; $i <= $nrCifre; $i++) {
            $putere = $putere * $cifra;
        }

        $suma = $suma + $putere;
        $temp = (int)($temp / 10);
    }

    if ($suma == $numar) {
        echo $numar . "<br>";
        $count++;
        $sumaTotala = $sumaTotala + $numar;
    }
}

echo "Numarul de numere Armstrong gasite: $count<br>";
echo "Suma numerelor Armstrong: $sumaTotala<br>";

//a patra sarcina

$numere = [14, -7, 22, 14, 5, -3, 22, 18, 9, 22];
$n = 10;

$maxim = $numere[0];
$minim = $numere[0];
$sumaPare = 0;
$countPozitive = 0;
$countNegative = 0;
$countZero = 0;
$suma = 0;

for ($i = 0; $i < $n; $i++) {

   
    if ($numere[$i] > $maxim) {
        $maxim = $numere[$i];
    }

    if ($numere[$i] < $minim) {
        $minim = $numere[$i];
    }

    if ($numere[$i] % 2 == 0) {
        $sumaPare = $sumaPare + $numere[$i];
    }

    if ($numere[$i] > 0) {
        $countPozitive++;
    } elseif ($numere[$i] < 0) {
        $countNegative++;
    } else {
        $countZero++;
    }

    $suma = $suma + $numere[$i];
}


$media = $suma / $n;

$countMaiMari = 0;
for ($i = 0; $i < $n; $i++) {
    if ($numere[$i] > $media) {
        $countMaiMari++;
    }
}

$valoareFrecventa = $numere[0];
$frecventaMaxima = 0;

for ($i = 0; $i < $n; $i++) {
    $frecventaCurenta = 0;

    for ($j = 0; $j < $n; $j++) {
        if ($numere[$j] == $numere[$i]) {
            $frecventaCurenta++;
        }
    }

    if ($frecventaCurenta > $frecventaMaxima) {
        $frecventaMaxima = $frecventaCurenta;
        $valoareFrecventa = $numere[$i];
    }
}


$pozitii = [];
for ($i = 0; $i < $n; $i++) {
    if ($numere[$i] == $valoareFrecventa) {
        $pozitii[] = $i;
    }
}

echo "<br>Cel mai mare element: $maxim <br>";
echo "Cel mai mic element: $minim <br>";
echo "Suma numerelor pare: $sumaPare <br>";
echo "Numarul valorilor pozitive: $countPozitive <br>";
echo "Numarul valorilor negative: $countNegative <br>";
echo "Numarul valorilor egale cu zero: $countZero <br>";
echo "Media aritmetica: $media <br>";
echo "Elemente mai mari decat media: $countMaiMari <br>";
echo "Valoarea cea mai frecventa: $valoareFrecventa (apare de $frecventaMaxima ori) <br>";

echo "Pozitiile pe care apare: ";
for ($i = 0; $i < count($pozitii); $i++) {
    echo $pozitii[$i];
    if ($i < count($pozitii) - 1) {
        echo ", ";
    }
}
echo "<br>";

//ultima sarcina

$text = "PROGRAMARE PHP";
$k = 3;

$lungime = strlen($text);
$textCriptat = "";
$textDecriptat = "";

echo "Textul initial: $text <br>";

// ----- CRIPTARE -----
for ($i = 0; $i < $lungime; $i++) {

    $caracter = $text[$i];

    if ($caracter == " ") {
        // pastram spatiul asa cum este
        $textCriptat = $textCriptat . " ";
    } else {
        // transformam litera in codul ei ASCII
        $cod = ord($caracter);

        // pozitia literei in alfabet (0 pentru A, 1 pentru B, ... 25 pentru Z)
        $pozitie = $cod - ord('A');

        // deplasam cu k pozitii, cu trecere circulara (modulo 26)
        $pozitieNoua = ($pozitie + $k) % 26;

        // transformam inapoi in litera
        $literaNoua = chr($pozitieNoua + ord('A'));

        $textCriptat = $textCriptat . $literaNoua;
    }
}

echo "Textul criptat: $textCriptat <br>";

// ----- DECRIPTARE -----
$lungimeCriptat = strlen($textCriptat);

for ($i = 0; $i < $lungimeCriptat; $i++) {

    $caracter = $textCriptat[$i];

    if ($caracter == " ") {
        $textDecriptat = $textDecriptat . " ";
    } else {
        $cod = ord($caracter);
        $pozitie = $cod - ord('A');

        // pentru decriptare scadem k, si adaugam 26 ca sa evitam numere negative
        $pozitieNoua = ($pozitie - $k + 26) % 26;

        $literaNoua = chr($pozitieNoua + ord('A'));

        $textDecriptat = $textDecriptat . $literaNoua;
    }
}

echo "Textul decriptat: $textDecriptat <br> <br>";



$text = "PROGRAMARE PHP";
$k = 3;

$lungime = strlen($text);
$textCriptat = "";
$textDecriptat = "";

echo "Textul initial: $text <br>";

// ----- CRIPTARE -----
for ($i = 0; $i < $lungime; $i++) {

    $caracter = $text[$i];

    if ($caracter == " ") {
        // pastram spatiul asa cum este
        $textCriptat = $textCriptat . " ";
    } else {
        // transformam litera in codul ei ASCII
        $cod = ord($caracter);

        // pozitia literei in alfabet (0 pentru A, 1 pentru B, ... 25 pentru Z)
        $pozitie = $cod - ord('A');

        // deplasam cu k pozitii, cu trecere circulara (modulo 26)
        $pozitieNoua = ($pozitie + $k) % 26;

        // transformam inapoi in litera
        $literaNoua = chr($pozitieNoua + ord('A'));

        $textCriptat = $textCriptat . $literaNoua;
    }
}

echo "Textul criptat: $textCriptat <br>";

// ----- DECRIPTARE -----
$lungimeCriptat = strlen($textCriptat);

for ($i = 0; $i < $lungimeCriptat; $i++) {

    $caracter = $textCriptat[$i];

    if ($caracter == " ") {
        $textDecriptat = $textDecriptat . " ";
    } else {
        $cod = ord($caracter);
        $pozitie = $cod - ord('A');

        // pentru decriptare scadem k, si adaugam 26 ca sa evitam numere negative
        $pozitieNoua = ($pozitie - $k + 26) % 26;

        $literaNoua = chr($pozitieNoua + ord('A'));

        $textDecriptat = $textDecriptat . $literaNoua;
    }
}

echo "Textul decriptat: $textDecriptat <br>";






?>
