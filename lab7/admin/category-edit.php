<?php
include("connection.php");
include_once("functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
} else {
    $id = (int)($_GET["id"] ?? 0);
}

if ($id <= 0) {
    die("Categorie invalidă.");
}

$stmt_cat = mysqli_prepare($conexiune, "SELECT id, name, description FROM categories WHERE id = ?");
mysqli_stmt_bind_param($stmt_cat, "i", $id);
mysqli_stmt_execute($stmt_cat);
$rez_cat = mysqli_stmt_get_result($stmt_cat);
$categorie = mysqli_fetch_assoc($rez_cat);
mysqli_stmt_close($stmt_cat);

if (!$categorie) {
    die("Categoria nu există.");
}
$erori = [];
$nume = $categorie["name"];
$descriere = $categorie["description"] ?? "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nume = trim($_POST["name"] ?? "");
    $descriere = trim($_POST["description"] ?? "");

    
    if ($nume == "") {
        $erori["name"] = "Denumirea este obligatorie.";
    } elseif (mb_strlen($nume) < 2 || mb_strlen($nume) > 100) {
        $erori["name"] = "Denumirea trebuie să aibă între 2 și 100 de caractere.";
    } elseif (categoryNameExists($conexiune, $nume, $id)) {
        
        $erori["name"] = "Există deja o categorie cu această denumire.";
    }

    if (mb_strlen($descriere) > 255) {
        $erori["description"] = "Descrierea nu poate depăși 255 de caractere.";
    }

    if (count($erori) == 0) {

        $descriere_db = ($descriere == "") ? null : $descriere;

 $stmt = mysqli_prepare($conexiune, "UPDATE categories SET name = ?, description = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $nume, $descriere_db, $id);

        if (mysqli_stmt_execute($stmt)) {
            setMesaj("Categoria a fost actualizată.");
            header("Location: categories.php");
            exit;
        } else {
            $erori["general"] = "Categoria nu a putut fi modificată: " . mysqli_stmt_error($stmt);
        }
    }
}
?>

<!doctype html>
<html lang="ro">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editare categorie</title>
    <link rel="stylesheet" href="stylesheet/style_categorii.css" />
  </head>

  <body>
    <main class="mic">
      <header>
        <div class="title">
          <h2>Editare categorie</h2>
          <p>Modifică denumirea sau descrierea categoriei.</p>
        </div>
        <a class="btn btn-edit" href="categories.php">← Înapoi</a>
      </header>

      <?php if (count($erori) > 0): ?>
        <div class="rezumat">
          <strong>Corectează erorile:</strong>
          <ul>
            <?php foreach ($erori as $mesaj): ?>
              <li><?= e($mesaj) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="form-grid" method="POST" action="category-edit.php">

        <input type="hidden" name="id" value="<?= e($id) ?>">

        <div class="field span-6">
          <label for="name">Denumire</label>
          <input type="text" id="name" name="name" required minlength="2" maxlength="100"
                 value="<?= e($nume) ?>">
          <?php if (isset($erori["name"])): ?>
            <span class="eroare"><?= e($erori["name"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-6">
          <label for="description">Descriere (max. 255 caractere)</label>
          <textarea id="description" name="description" rows="4" maxlength="255"><?= e($descriere) ?></textarea>
          <?php if (isset($erori["description"])): ?>
            <span class="eroare"><?= e($erori["description"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="form-actions span-6">
          <button type="submit">Salvează modificările</button>
        </div>
      </form>
    </main>
  </body>
</html>