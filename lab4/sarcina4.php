<?php
function nr_caractere($text){
    return mb_strlen($text);
}

function extragere_cuvinte($text){
    return preg_split('/\W+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
}
function nr_cuvinte($text){
    return count(extragere_cuvinte($text) );
}

function nr_propozitii($text){
    $a = preg_split('/[.!?]+/', $text, -1, PREG_SPLIT_NO_EMPTY);
    $nr = 0;
    foreach ($a as $propozitie) {
        if (trim($propozitie) !== "") {
            $nr++;
        }
    }
    return $nr;
}

function lung_cuvant($text){
    $cuvinte = extragere_cuvinte($text);
    if (empty($cuvinte)) {
        return "";
    }
    $cuvantel = $cuvinte[0];
    foreach ($cuvinte as $cuvant) {
        if (mb_strlen($cuvant) > mb_strlen($cuvantel)) {
            $cuvantel = $cuvant;
        }
    }
    return $cuvantel;
}

function scurt_cuvant($text){
    $cuvinte = extragere_cuvinte($text);
    if (empty($cuvinte)) {
        return "";
    }
    $cuvantel = $cuvinte[0];
    foreach ($cuvinte as $cuvant) {
        if (mb_strlen($cuvant) < mb_strlen($cuvantel)) {
            $cuvantel = $cuvant;
        }
    }
    return $cuvantel;
}

function frecventa($cuvinte, $rezultat = []){
    if (empty($cuvinte)) {
        return $rezultat;
    }
    $cuvant = array_shift($cuvinte);
    $rezultat[$cuvant] = ($rezultat[$cuvant] ?? 0) + 1;
    return frecventa($cuvinte, $rezultat);
}
function cuvant_frecvent($text){
   
    $filtru = function($cuvant){
        return mb_strlen($cuvant) >= 3;
    };

    $cuvinte = extragere_cuvinte(mb_strtolower($text));
    $cuvinte = array_values(array_filter($cuvinte, $filtru));

    $frecvente = frecventa($cuvinte);
    arsort($frecvente);
    return array_slice($frecvente, 0, 5, true);
}

$text = "";
$rezultat = false;


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit"])){
 $text = $_POST["textul"];
 $rezultat = true;
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>My ex4</title>
    </head>
    <body>
        <form method="POST">
            <textarea name="textul" id="textul" placeholder="Introduceti textul"></textarea>
            <input type="submit" name="submit" value="Verifica">
        </form>

         <?php if ($rezultat): ?>
            <h3>Rezultate</h3>
            <p>Numar caractere: <?php echo nr_caractere($text); ?></p>
            <p>Numar cuvinte: <?php echo nr_cuvinte($text); ?></p>
            <p>Numar propozitii: <?php echo nr_propozitii($text); ?></p>
            <p>Cel mai lung cuvant: <?php echo htmlspecialchars(lung_cuvant($text)); ?></p>
            <p>Cel mai scurt cuvant: <?php echo htmlspecialchars(scurt_cuvant($text)); ?></p>

            <p>Top 5 cuvinte frecvente:</p>
            <ul>
                <?php foreach (cuvant_frecvent($text) as $cuvant => $nr): ?>
                    <li><?php echo htmlspecialchars($cuvant); ?> - <?php echo $nr; ?> ori</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </body>

</html>