<?php
include("connection.php");
include_once("functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

$rez_categorii = mysqli_query($conexiune, "SELECT id, name FROM categories ORDER BY name ASC");

if (!$rez_categorii) {
    die("Eroare la încărcarea categoriilor.");
}

$categorii = mysqli_fetch_all($rez_categorii, MYSQLI_ASSOC);

$cautare = trim($_GET["q"] ?? "");
$filtru_categorie = (int)($_GET["categorie"] ?? 0);

$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        WHERE 1=1";

$tipuri = "";      
$parametri = [];   

if ($cautare != "") {
    $sql .= " AND (p.name LIKE ? OR p.manufacturer LIKE ?)";
    $like = "%" . $cautare . "%";
    $tipuri .= "ss";
    $parametri[] = $like;
    $parametri[] = $like;
}

if ($filtru_categorie > 0) {
    $sql .= " AND p.category_id = ?";
    $tipuri .= "i";
    $parametri[] = $filtru_categorie;
}

$sql .= " ORDER BY p.name ASC";

$stmt = mysqli_prepare($conexiune, $sql);

if (count($parametri) > 0) {
    mysqli_stmt_bind_param($stmt, $tipuri, ...$parametri);
}

mysqli_stmt_execute($stmt);
$rezultat = mysqli_stmt_get_result($stmt);
$products = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
$total = count($products);
?>

<!doctype html>
<html lang="ro">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista produselor</title>
    <link rel="stylesheet" href="stylesheet/style_produse.css" />
    <style>
      .filtre { display: flex; gap: 10px; flex-wrap: wrap; }
      .filtre input,
      .filtre select {
        padding: 10px 14px;
        border: 1px solid rgb(81, 241, 69);
        background-color: beige;
        color: rgb(1, 43, 4);
        font-family: "Courier New", Courier, monospace;
        font-size: 0.95rem;
      }
      .filtre input { flex: 1; min-width: 200px; }
      .actions form { margin: 0; }
      .stoc-status { display: block; font-size: 0.8rem; }
    </style>
  </head>

  <body>
    <main>
      <header>
        <div class="title">
          <h2>Produse</h2>
          <p>Vizualizare, adăugare, modificare și ștergere produse.</p>
        </div>
        <a class="btn btn-add" href="product-create.php">+ Adaugă produs</a>
      </header>

      <?php afiseazaMesaj(); ?>

      <form class="filtre" method="GET" action="products.php">
        <input type="text" name="q" placeholder="Caută produs..." value="<?= e($cautare) ?>">

        <select name="categorie">
          <option value="0">Toate categoriile</option>

          <?php foreach ($categorii as $cat): ?>
            <option value="<?= e($cat["id"]) ?>"
              <?= ($cat["id"] == $filtru_categorie) ? "selected" : "" ?>>
              <?= e($cat["name"]) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <button type="submit" class="btn btn-add">Caută</button>

        <?php if ($cautare != "" || $filtru_categorie > 0): ?>
          <a class="btn btn-view" href="products.php">Resetează</a>
        <?php endif; ?>
      </form>

      <p><?= $total ?> produse</p>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Imagine</th>
              <th>Produs</th>
              <th>Categorie</th>
              <th>Preț</th>
              <th>Stoc</th>
              <th>Status</th>
              <th>Acțiuni</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($products as $row): ?>
              <tr class="<?= $row["is_active"] ? "" : "inactive" ?>">

                <td class="col-img">
                  <div class="thumb">
                    <img src="../images/<?= e($row["image"]) ?>" alt="<?= e($row["name"]) ?>">
                  </div>
                </td>

                <td class="col-name"><?= e($row["name"]) ?></td>

                <td><?= e($row["category_name"] ?? "Fără categorie") ?></td>

                <td class="col-price"><?= e(formatPrice($row["price"])) ?></td>

                <td>
                  <?= e($row["stock"]) ?>
                  <span class="stoc-status"><?= e(getStockStatus($row["stock"])) ?></span>
                </td>

                <td>
                  <?php if ($row["is_active"]): ?>
                    <span class="badge badge-active">Activ</span>
                  <?php else: ?>
                    <span class="badge badge-inactive">Inactiv</span>
                  <?php endif; ?>
                </td>

                <td>
                  <div class="actions">
                    <a class="btn btn-view" href="product-view.php?id=<?= urlencode((string)$row["id"]) ?>">
                      Vizualizare
                    </a>

                    <a class="btn btn-edit" href="product-edit.php?id=<?= urlencode((string)$row["id"]) ?>">
                      ✎ Editare
                    </a>

                    <form
                      action="product-delete.php"
                      method="POST"
                      onsubmit="return confirm('Sigur dorești să ștergi acest produs?');"
                    >
                      <input type="hidden" name="id" value="<?= e($row["id"]) ?>">
                      <button type="submit" class="btn btn-delete">🗑 Ștergere</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if ($total == 0): ?>
              <tr>
                <td colspan="7">Nu au fost găsite produse.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>