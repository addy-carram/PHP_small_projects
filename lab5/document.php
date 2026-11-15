<?php
include 'functions.php';

$cat = $_GET["cat"] ?? "";
$file = $_GET["file"] ?? "";
$mesaj = "";
$tip = "";

$mesajeGet = [
    "redenumit" => "Fișierul a fost redenumit.",
];
if (isset($_GET["msg"]) && isset($mesajeGet[$_GET["msg"]])) {
    $mesaj = $mesajeGet[$_GET["msg"]];
    $tip = "succes";
}

$path = getFilePath($cat, $file);

if ($path === false) {
    pageStart("Document inexistent");
    showMessage("Fișierul nu există.", "eroare");
    echo '<p><a href="index.php">← Înapoi la panou</a></p>';
    pageEnd();
    exit;
}

$file = basename($path);
$dir = dirname($path);

//   redenumire  
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["redenumeste"])) {
    $nou = trim($_POST["nume_nou"] ?? "");

  
    if ($nou !== "" && getExtension($nou) === "") {
        $nou .= "." . getExtension($file);
    }

    if ($nou === "" || !isValidName($nou)) {
        $mesaj = 'Numele nu e valid (nu poate fi gol și nu poate conține / \ : * ? " < > |).';
        $tip = "eroare";
    } elseif (!isAllowedFile($nou)) {
        $mesaj = "Extensia nouă nu este permisă (txt, pdf, jpg, jpeg, png).";
        $tip = "eroare";
    } elseif (file_exists($dir . "/" . $nou)) {
        $mesaj = "Există deja un fișier cu numele „" . $nou . "”.";
        $tip = "eroare";
    } elseif (!rename($path, $dir . "/" . $nou)) {
        $mesaj = "Fișierul nu a putut fi redenumit.";
        $tip = "eroare";
    } else {
        writeLog("Fișier redenumit: " . $file . " -> " . $nou);
        header("Location: document.php?cat=" . urlencode($cat) . "&file=" . urlencode($nou) . "&msg=redenumit");
        exit;
    }
}

//   ștergere  
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["sterge"])) {
    if (unlink($path)) {
        writeLog("Fișier șters: " . $file);
        header("Location: index.php?cat=" . urlencode($cat) . "&msg=sters");
        exit;
    }
    $mesaj = "Fișierul nu a putut fi șters.";
    $tip = "eroare";
}

//   informații  
$extensie = getExtension($file);
$dimensiune = filesize($path);
$modificat = filemtime($path);

$continut = "";
$caractere = 0;
$cuvinte = 0;

if ($extensie === "txt") {
    $continut = file_get_contents($path);
    $caractere = mb_strlen($continut);
    $cuvinte = count(preg_split('/\s+/u', trim($continut), -1, PREG_SPLIT_NO_EMPTY));
}

$url = "documente/" . implode("/", array_map("rawurlencode", explode("/", $cat . "/" . $file)));

pageStart($file);
?>

<p><a href="index.php?cat=<?= urlencode($cat) ?>">← Înapoi la „<?= e($cat) ?>”</a></p>

<?php showMessage($mesaj, $tip); ?>

<section class="card">
  <h2><?= e($file) ?></h2>

  <dl class="info">
    <dt>Categorie</dt><dd><?= e($cat) ?></dd>
    <dt>Extensie</dt><dd><?= e(strtoupper($extensie)) ?></dd>
    <dt>Dimensiune</dt><dd><?= e(formatFileSize($dimensiune)) ?></dd>
    <dt>Ultima modificare</dt><dd><?= date("d.m.Y H:i", $modificat) ?></dd>
    <?php if ($extensie === "txt"): ?>
      <dt>Caractere</dt><dd><?= $caractere ?></dd>
      <dt>Cuvinte (aprox.)</dt><dd><?= $cuvinte ?></dd>
    <?php endif; ?>
  </dl>
</section>

<section class="card">
  <h2>Conținut</h2>
  <?php if ($extensie === "txt"): ?>
    <pre class="continut"><?= e($continut) ?></pre>
  <?php elseif ($extensie === "pdf"): ?>
    <p><a class="btn" href="<?= $url ?>" target="_blank">Deschide PDF-ul</a></p>
  <?php else: ?>
    <img class="previzualizare" src="<?= $url ?>" alt="<?= e($file) ?>">
  <?php endif; ?>
</section>

<section class="grid-2">
  <form method="POST" class="card" id="redenumire">
    <h2>Redenumire</h2>
    <label for="nume_nou">Numele nou</label>
    <input type="text" id="nume_nou" name="nume_nou" value="<?= e($file) ?>">
    <button type="submit" name="redenumeste" class="btn">Redenumește</button>
  </form>

  <form method="POST" class="card" id="stergere"
        onsubmit="return confirm('Sigur vrei să ștergi fișierul <?= e($file) ?>?');">
    <h2>Ștergere</h2>
    <p>Fișierul va fi șters definitiv.</p>
    <button type="submit" name="sterge" class="btn btn-pericol">Șterge fișierul</button>
  </form>
</section>

<?php pageEnd(); ?>