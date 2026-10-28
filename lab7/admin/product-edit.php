<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("connection.php");

mysqli_set_charset($conexiune, "utf8mb4");

function e($valoare) {
    return htmlspecialchars((string)$valoare, ENT_QUOTES, 'UTF-8');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
} else {
    $id = (int)($_GET["id"] ?? 0);
}

if ($id <= 0) {
    die("Produs invalid.");
}

$stmt_produs = mysqli_prepare($conexiune, "SELECT * FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt_produs, "i", $id);
mysqli_stmt_execute($stmt_produs);
$rez_produs = mysqli_stmt_get_result($stmt_produs);
$produs = mysqli_fetch_assoc($rez_produs);
mysqli_stmt_close($stmt_produs);

if (!$produs) {
    die("Produsul nu există.");
}

$rezultat = mysqli_query($conexiune, "SELECT id, name FROM categories ORDER BY name ASC");

if (!$rezultat) {
    die("Eroare la încărcarea categoriilor.");
}

$categorii = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);

$erori = [];
$nume = $produs["name"];
$category_id = $produs["category_id"];
$id_cat = (int)$produs["category_id"];
$descriere_scurta = $produs["short_description"];
$descriere = $produs["description"];
$pret = $produs["price"];
$stoc = $produs["stock"];
$producator = $produs["manufacturer"];
$tara = $produs["country"] ?? "";
$activ = (int)$produs["is_active"];
$imagine_veche = $produs["image"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nume = trim($_POST["name"] ?? "");
    $category_id = $_POST["category_id"] ?? "";
    $descriere_scurta = trim($_POST["short_description"] ?? "");
    $descriere = trim($_POST["description"] ?? "");
    $pret = trim($_POST["price"] ?? "");
    $stoc = trim($_POST["stock"] ?? "");
    $producator = trim($_POST["manufacturer"] ?? "");
    $tara = trim($_POST["country"] ?? "");

    $activ = isset($_POST["is_active"]) ? 1 : 0;

  
    if ($nume === "") {
        $erori["name"] = "Denumirea este obligatorie.";
    } elseif (mb_strlen($nume) < 3 || mb_strlen($nume) > 150) {
        $erori["name"] = "Denumirea trebuie să aibă între 3 și 150 de caractere.";
    }

    if ($category_id === "") {
        $erori["category_id"] = "Selectează o categorie.";
    } else {
        $id_cat = (int)$category_id;

        $verificare = mysqli_prepare($conexiune, "SELECT id FROM categories WHERE id = ?");
        mysqli_stmt_bind_param($verificare, "i", $id_cat);
        mysqli_stmt_execute($verificare);
        mysqli_stmt_store_result($verificare);

        if (mysqli_stmt_num_rows($verificare) == 0) {
            $erori["category_id"] = "Categoria selectată nu există.";
        }
        mysqli_stmt_close($verificare);
    }

    if (mb_strlen($descriere_scurta) > 255) {
        $erori["short_description"] = "Descrierea scurtă nu poate depăși 255 de caractere.";
    }

    if ($descriere === "") {
        $erori["description"] = "Descrierea completă este obligatorie.";
    }

    $pret = str_replace(",", ".", $pret);
    if (!is_numeric($pret) || (float)$pret <= 0) {
        $erori["price"] = "Prețul trebuie să fie un număr mai mare decât 0.";
    }

    if (!ctype_digit((string)$stoc)) {
        $erori["stock"] = "Stocul trebuie să fie un număr întreg, fără minus.";
    }

    if ($producator === "") {
        $erori["manufacturer"] = "Producătorul este obligatoriu.";
    }

    $folder = __DIR__ . "/../images/";
    $extensie = "";
    $imagine_noua = false;

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] != UPLOAD_ERR_NO_FILE) {
        $imagine_noua = true;

        if ($_FILES["image"]["error"] != UPLOAD_ERR_OK) {
            $erori["image"] = "Fișierul nu a fost încărcat corect.";
        } elseif ($_FILES["image"]["size"] > 2 * 1024 * 1024) {
            $erori["image"] = "Imaginea nu poate depăși 2 MB.";
        } else {
            $extensie = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
            $permise = ["jpg", "jpeg", "png", "webp"];

            if (!in_array($extensie, $permise)) {
                $erori["image"] = "Sunt permise doar fișiere JPG, JPEG, PNG și WEBP.";
            } elseif (!is_dir($folder)) {
                $erori["image"] = "Folderul images/ nu există.";
            }
        }
    }

    if (count($erori) === 0) {
        $nume_imagine = $imagine_veche;

        if ($imagine_noua) {
            $nume_imagine = time() . "_" . rand(1000, 9999) . "." . $extensie;

            if (!move_uploaded_file($_FILES["image"]["tmp_name"], $folder . $nume_imagine)) {
                $erori["image"] = "Imaginea nu a putut fi salvată în folderul images/.";
            }
        }

        if (count($erori) === 0) {
            $sql = "UPDATE products
                    SET category_id = ?, name = ?, short_description = ?, description = ?,
                        price = ?, stock = ?, manufacturer = ?, country = ?, image = ?, is_active = ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conexiune, $sql);

            if (!$stmt) {
                $erori["general"] = "Eroare SQL: " . mysqli_error($conexiune);
            } else {
                $stoc_int = (int)$stoc;
                $pret_nr = (float)$pret;

                mysqli_stmt_bind_param(
                    $stmt,
                    "isssdisssii",
                    $id_cat,
                    $nume,
                    $descriere_scurta,
                    $descriere,
                    $pret_nr,
                    $stoc_int,
                    $producator,
                    $tara,
                    $nume_imagine,
                    $activ,
                    $id
                );

                if (mysqli_stmt_execute($stmt)) {
                    if ($imagine_noua && $imagine_veche != "" && file_exists($folder . $imagine_veche)) {
                        unlink($folder . $imagine_veche);
                    }

                    header("Location: products.php?editat=1");
                    exit;
                } else {
                    $erori["general"] = "Produsul nu a putut fi modificat: " . mysqli_stmt_error($stmt);

                    if ($imagine_noua && file_exists($folder . $nume_imagine)) {
                        unlink($folder . $nume_imagine);
                    }
                }
            }
        }
    }
}
?>

<!doctype html>
<html lang="ro">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Editare produs</title>
    <link rel="stylesheet" href="stylesheet/style_product_create.css" />
    <style>
      .eroare { color: red; font-size: 14px; }
      .rezumat { background: #fdecea; border: 1px solid red; padding: 10px; margin-bottom: 15px; }
      .camp { margin-bottom: 12px; display: flex; flex-direction: column; }
      .imagine-curenta { margin-bottom: 8px; }
    </style>
  </head>

  <body>
    <main>
      <header>
        <div class="title">
          <h2>Editare produs</h2>
        </div>
        <a style=" color:#fff;" class="btn" href="products.php">← Înapoi la listă</a>
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

      <form method="POST" action="product-edit.php" enctype="multipart/form-data">

        <input type="hidden" name="id" value="<?= e($id) ?>">

        <div class="camp">
          <label for="name">Denumire *</label>
          <input type="text" id="name" name="name" required minlength="3" maxlength="150"
                 value="<?= e($nume) ?>">
          <?php if (isset($erori["name"])): ?>
            <span class="eroare"><?= e($erori["name"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-3">
          <label for="category_id">Categorie *</label>
          <select name="category_id" id="category_id" required>
            <option value="">-- Selectează categoria --</option>
            <?php foreach ($categorii as $cat): ?>
              <option value="<?= e($cat['id']) ?>"
                <?= ($cat['id'] == $category_id) ? 'selected' : '' ?>>
                <?= e($cat['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($erori["category_id"])): ?>
            <span class="eroare"><?= e($erori["category_id"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-3">
          <label for="short_description">Descriere scurtă (max. 255 caractere)</label>
          <input type="text" id="short_description" name="short_description" maxlength="255"
                 value="<?= e($descriere_scurta) ?>">
          <?php if (isset($erori["short_description"])): ?>
            <span class="eroare"><?= e($erori["short_description"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-6">
          <label for="description">Descriere completă *</label>
          <textarea id="description" name="description" rows="5" required><?= e($descriere) ?></textarea>
          <?php if (isset($erori["description"])): ?>
            <span class="eroare"><?= e($erori["description"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-6">
          <label for="price">Preț (MDL) *</label>
          <input type="number" id="price" name="price" required min="0.01" step="0.01"
                 value="<?= e($pret) ?>">
          <?php if (isset($erori["price"])): ?>
            <span class="eroare"><?= e($erori["price"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-2">
          <label for="stock">Stoc *</label>
          <input type="number" id="stock" name="stock" required min="0" step="1"
                 value="<?= e($stoc) ?>">
          <?php if (isset($erori["stock"])): ?>
            <span class="eroare"><?= e($erori["stock"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-2">
          <label for="manufacturer">Producător *</label>
          <input type="text" id="manufacturer" name="manufacturer" required maxlength="100"
                 value="<?= e($producator) ?>">
          <?php if (isset($erori["manufacturer"])): ?>
            <span class="eroare"><?= e($erori["manufacturer"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field span-2 optional">
          <label for="country">Țara de origine</label>
          <input type="text" id="country" name="country" maxlength="100" value="<?= e($tara) ?>">
        </div>

        <div class="field span-3">
          <label for="image">Imagine (alege una nouă doar dacă vrei să o schimbi)</label>

          <div class="imagine-curenta">
            <img src="../images/<?= e($imagine_veche) ?>" alt="<?= e($nume) ?>" width="100">
          </div>

          <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp">
          <?php if (isset($erori["image"])): ?>
            <span class="eroare"><?= e($erori["image"]) ?></span>
          <?php endif; ?>
        </div>

        <div class="field checkbox span-3 optional">
          <label>
            <input type="checkbox" name="is_active" value="1" <?= $activ == 1 ? 'checked' : '' ?>>
            Produs activ
          </label>
        </div>
            <div class="field span-3">
                 <button type="submit" class="btn btn-add">Salvează modificările</button>
      
            </div>
       </form>
    </main>
  </body>
</html>