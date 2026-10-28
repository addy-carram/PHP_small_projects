<?php
// array-ul cu produsele din catalog
$catalogProduse = [
    ["nume" => "Laptop", "pret" => 2500, "categorie" => "Electronice", "stoc" => 5],
    ["nume" => "Telefon", "pret" => 1800, "categorie" => "Electronice", "stoc" => 12],
    ["nume" => "Căști", "pret" => 450, "categorie" => "Accesorii", "stoc" => 0],
    ["nume" => "Tastatura", "pret" => 350, "categorie" => "Accesorii", "stoc" => 8],
    ["nume" => "Ceas", "pret" => 1200, "categorie" => "Electronice", "stoc" => 3],
    ["nume" => "Mouse", "pret" => 150, "categorie" => "Accesorii", "stoc" => 15],
    ["nume" => "Monitor", "pret" => 750, "categorie" => "Electronice", "stoc" => 0],
    ["nume" => "Scaun", "pret" => 600, "categorie" => "Mobilier", "stoc" => 4],
    ["nume" => "Masa", "pret" => 1400, "categorie" => "Mobilier", "stoc" => 2],
    ["nume" => "Lampă", "pret" => 120, "categorie" => "Mobilier", "stoc" => 20]
];

// luam categoriile pentru dropdown
$categoriiUnice = array_unique(array_column($catalogProduse, 'categorie'));

function filtreazaProduse($produse, $categorie = null, $pretMin = 0, $pretMax = PHP_INT_MAX, $doarInStoc = false) {
    $produseFiltrate = [];
    foreach ($produse as $produs) {
        if ($categorie && $categorie !== 'toate' && $produs['categorie'] !== $categorie) {
            continue;
        }
        if ($produs['pret'] < $pretMin || $produs['pret'] > $pretMax) {
            continue;
        }
        if ($doarInStoc && $produs['stoc'] <= 0) {
            continue;
        }
        $produseFiltrate[] = $produs;
    }
    return $produseFiltrate;
}

// suma recursiva
function calculeazaSumaRecursiv($produse, $index = 0) {
    if ($index >= count($produse)) {
        return 0;
    }
    return $produse[$index]['pret'] + calculeazaSumaRecursiv($produse, $index + 1);
}

// preluare date din GET
$catSelectata = isset($_GET['categorie']) ? $_GET['categorie'] : 'toate';
$pMin = isset($_GET['pret_min']) && $_GET['pret_min'] !== '' ? (float)$_GET['pret_min'] : 0;
$pMax = isset($_GET['pret_max']) && $_GET['pret_max'] !== '' ? (float)$_GET['pret_max'] : PHP_INT_MAX;
$inStoc = isset($_GET['in_stoc']);

// filtrare
$produseAfisate = filtreazaProduse($catalogProduse, $catSelectata, $pMin, $pMax, $inStoc);

// sortare crescator dupa pret cu functie anonima
usort($produseAfisate, function($a, $b) {
    return $a['pret'] <=> $b['pret'];
});

// calcul suma totala
$sumaTotala = calculeazaSumaRecursiv($produseAfisate);
?>
<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalog Produse</title>
</head>
<body>
    <div class="wrapper">
        <h1>Catalog de Produse</h1>
        
        <form method="GET" action="">
            <div>
                <label for="categorie">Categorie</label>
                <select name="categorie" id="categorie">
                    <option value="toate">Toate categoriile</option>
                    <?php foreach ($categoriiUnice as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php if($catSelectata === $cat) echo 'selected'; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label for="pret_min">Preț Minim (RON)</label>
                <input type="number" name="pret_min" id="pret_min" value="<?php echo isset($_GET['pret_min']) ? $_GET['pret_min'] : ''; ?>" placeholder="0">
            </div>

            <div>
                <label for="pret_max">Preț Maxim (RON)</label>
                <input type="number" name="pret_max" id="pret_max" value="<?php echo isset($_GET['pret_max']) && $_GET['pret_max'] != PHP_INT_MAX ? $_GET['pret_max'] : ''; ?>" placeholder="Fără limită">
            </div>

            <div>
                <input type="checkbox" name="in_stoc" id="in_stoc" <?php if($inStoc) echo 'checked'; ?>>
                <label for="in_stoc">Doar în stoc</label>
            </div>

            <button type="submit">Filtrează</button>
            <a href="sarcina1.php">Resetează</a>
        </form>

        <div class="info-statistici">
            Valoarea totală a produselor afișate în acest moment: <?php echo $sumaTotala; ?> RON
        </div>

        <div class="catalog-grid">
            <?php if (!empty($produseAfisate)): ?>
                <?php foreach ($produseAfisate as $produs): ?>
                    <div class="card">
                        <div class="card-body">
                            <div>
                                <div class="card-categorie"><?php echo htmlspecialchars($produs['categorie']); ?></div>
                                <h3 class="card-titlu"><?php echo htmlspecialchars($produs['nume']); ?></h3>
                            </div>
                            <div>
                                <div class="card-pret"><?php echo $produs['pret']; ?> RON</div>
                                <?php if ($produs['stoc'] > 0): ?>
                                    <span class="card-stoc stoc-disponibil">În stoc (<?php echo $produs['stoc']; ?> buc.)</span>
                                <?php else: ?>
                                    <span class="card-stoc stoc-epuizat">Stoc epuizat</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="nu-sunt-rezultate">Nu s-a găsit niciun produs care să corespundă filtrelor selectate.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
