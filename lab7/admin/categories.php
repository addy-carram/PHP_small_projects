<?php
include("connection.php");
include_once("functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

$tabel = mysqli_query($conexiune, "
    SELECT c.id, c.name, c.description, COUNT(p.id) AS nr_produse
    FROM categories c
    LEFT JOIN products p ON p.category_id = c.id
    GROUP BY c.id, c.name, c.description
    ORDER BY c.name ASC
");

if (!$tabel) {
    die("Eroare la încărcarea categoriilor.");
}

$categorii = mysqli_fetch_all($tabel, MYSQLI_ASSOC);
?>

<!doctype html>
<html lang="ro">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Lista categoriilor</title>
    <link rel="stylesheet" href="stylesheet/style_categorii.css" />
  </head>

  <body>
    <main>
      <header>
        <div class="title">
          <h2>Categorii</h2>
          <p>Vizualizare, adăugare, modificare și ștergere categorii.</p>
        </div>
        <a class="btn btn-add" href="category-create.php">+ Adaugă categorie</a>
      </header>

      <?php afiseazaMesaj(); ?>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>ID</th>
              <th>Denumire</th>
              <th>Descriere</th>
              <th>Produse</th>
              <th>Acțiuni</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($categorii as $cat): ?>
              <tr>
                <td><?= e($cat["id"]) ?></td>
                <td class="col-name"><?= e($cat["name"]) ?></td>
                <td><?= ($cat["description"] != "") ? e($cat["description"]) : "—" ?></td>
                <td><span class="badge"><?= e($cat["nr_produse"]) ?></span></td>
                <td>
                  <div class="actions">
                    <a class="btn btn-edit" href="category-edit.php?id=<?= urlencode((string)$cat["id"]) ?>">
                      ✎ Editare
                    </a>

                    <form
                      action="category-delete.php"
                      method="POST"
                      onsubmit="return confirm('Sigur dorești să ștergi această categorie?');"
                    >
                      <input type="hidden" name="id" value="<?= e($cat["id"]) ?>">
                      <button type="submit" class="btn btn-delete">🗑 Ștergere</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>

            <?php if (count($categorii) == 0): ?>
              <tr>
                <td colspan="5">Nu există categorii.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </main>
  </body>
</html>