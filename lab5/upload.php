<?php
include 'functions.php';

$mesaj = "";
$tip = "";
$categorii = getCategories(BASE_DIR);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $categorie = $_POST["categorie"] ?? "";

    if (!in_array($categorie, $categorii)) {
        $mesaj = "Selectează o categorie existentă.";
        $tip = "eroare";
    } elseif (!isset($_FILES["fisier"]) || $_FILES["fisier"]["error"] === UPLOAD_ERR_NO_FILE) {
        $mesaj = "Selectează un fișier.";
        $tip = "eroare";
    } elseif ($_FILES["fisier"]["error"] !== UPLOAD_ERR_OK) {
        $mesaj = "Fișierul nu a fost încărcat corect (poate depășește limita serverului).";
        $tip = "eroare";
    } else {
        $nume = basename($_FILES["fisier"]["name"]);

        if (!isValidName($nume)) {
            $mesaj = "Numele fișierului conține caractere nepermise.";
            $tip = "eroare";
        } elseif (!isAllowedFile($nume)) {
            $mesaj = "Sunt permise doar fișiere txt, pdf, jpg, jpeg și png.";
            $tip = "eroare";
        } elseif ($_FILES["fisier"]["size"] > 2 * 1024 * 1024) {
            $mesaj = "Fișierul depășește 2 MB.";
            $tip = "eroare";
        } elseif (file_exists(BASE_DIR . "/" . $categorie . "/" . $nume)) {
            $mesaj = "Există deja un fișier cu numele „" . $nume . "” în această categorie.";
            $tip = "eroare";
        } elseif (!move_uploaded_file($_FILES["fisier"]["tmp_name"], BASE_DIR . "/" . $categorie . "/" . $nume)) {
            $mesaj = "Fișierul nu a putut fi salvat.";
            $tip = "eroare";
                } else {
            writeLog("Fișier încărcat: " . $categorie . "/" . $nume);
            header("Location: index.php?cat=" . urlencode($categorie) . "&msg=incarcat");
            exit;
        }
    }
}

pageStart("Încarcă fișier");
?>

<?php showMessage($mesaj, $tip); ?>

<form method="POST" action="upload.php" enctype="multipart/form-data" class="card">
  <h2>Încarcă un fișier</h2>

  <label for="categorie">Categoria destinație</label>
  <select id="categorie" name="categorie">
    <?php foreach ($categorii as $c): ?>
      <option value="<?= e($c) ?>"><?= e($c) ?></option>
    <?php endforeach; ?>
  </select>

  <label for="fisier">Fișier (txt, pdf, jpg, jpeg, png — max. 2 MB)</label>
  <input type="file" id="fisier" name="fisier" accept=".txt,.pdf,.jpg,.jpeg,.png">

  <button type="submit" class="btn">Încarcă fișierul</button>
</form>

<?php pageEnd(); ?>