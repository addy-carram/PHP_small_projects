<?php
function verificaParola($parola, $lungimeMinima = 8) {
    return [
        "lungime"=> strlen($parola) >= $lungimeMinima,
        "litera_mare"=> preg_match('/[A-Z]/', $parola) === 1,
        "litera_mica"=> preg_match('/[a-z]/', $parola) === 1,
        "cifra"=> preg_match('/[0-9]/', $parola) === 1,
        "special" => preg_match('/[^A-Za-z0-9]/', $parola) === 1,
        "fara_spatii"=> preg_match('/\s/', $parola) === 0
    ];
}

$rezultate = null;
$mesajScor = "";
$clasaCSS = "";
$parolaTrimisa = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["verifica"])) {
    $parolaTrimisa = $_POST["parola"]; 
    $rezultate = verificaParola($parolaTrimisa);
    $calculScor = function($criterii) use (&$mesajScor, &$clasaCSS) {
        $criteriiIndeplinite = count(array_filter($criterii));
        if ($criteriiIndeplinite <= 2) {
            $mesajScor = "Parolă slabă ($criteriiIndeplinite/6)";
            $clasaCSS = "slaba";
        } elseif ($criteriiIndeplinite <= 4) {
            $mesajScor = "Parolă medie ($criteriiIndeplinite/6)";
            $clasaCSS = "medie";
        } else {
            $mesajScor = "Parolă puternică ($criteriiIndeplinite/6)";
            $clasaCSS = "puternica";
        }
    };
    $calculScor($rezultate);
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificator Parolă</title>

</head>
<body>

<div class="card">
    <h2>Verificator de Parolă</h2>
    <form method="POST" action="">
        <div>
            <label for="parola">Introduceți parola:</label>
            <input type="text" name="parola" id="parola" value="<?php echo htmlspecialchars($parolaTrimisa); ?>" placeholder="Scrie parola aici...">
        </div>
        <button type="submit" name="verifica">Verifică siguranța</button>
    </form>

    <?php if ($rezultate !== null): ?>
        <div <?php  echo $clasaCSS; ?>>
            <?php echo $mesajScor; ?>
        </div>
        <ul >
            <li <?php echo $rezultate['lungime'] ? 'valid' : 'invalid'; ?>>Cel puțin 8 caractere</li>
            <li <?php echo $rezultate['litera_mare'] ? 'valid' : 'invalid'; ?>>Cel puțin o literă mare (A-Z)</li>
            <li <?php echo $rezultate['litera_mica'] ? 'valid' : 'invalid'; ?>>Cel puțin o literă mică (a-z)</li>
            <li <?php echo $rezultate['cifra'] ? 'valid' : 'invalid'; ?>>Cel puțin o cifră (0-9)</li>
            <li <?php echo $rezultate['special'] ? 'valid' : 'invalid'; ?>>Cel puțin un caracter special (ex: @, #, $)</li>
            <li <?php echo $rezultate['fara_spatii'] ? 'valid' : 'invalid'; ?>>Nu conține spații</li>
        </ul>
    <?php endif; ?>
</div>

</body>
</html>
