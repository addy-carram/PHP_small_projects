<?php
include('../db_connection.php');
include_once("../functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

$raport = $_GET["raport"] ?? "";
$rezultate = [];
$mesaj = "";

// 1. Cărți cu prețul mai mare decât suma introdusă
if ($raport === "pret") {
    $suma = str_replace(",", ".", trim($_GET["suma"] ?? ""));

    if (!is_numeric($suma)) {
        $mesaj = "Introduceți o sumă.";
    } else {
        $suma = (float)$suma;
        $stmt = mysqli_prepare($conexiune,
            "SELECT title, author, editura, price,photo FROM books WHERE price > ? ORDER BY price DESC");
        mysqli_stmt_bind_param($stmt, "d", $suma);
        mysqli_stmt_execute($stmt);
        $rezultate = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
    }
}

// 2. Cititori care nu au returnat cartea până la data curentă
if ($raport === "restante") {
    $tabel = mysqli_query($conexiune, "
        SELECT readers.first_last_name, readers.phone_number, readers.date_return, books.title, books.photo
        FROM readers
        JOIN books ON readers.cod_id = books.id_book
        WHERE readers.date_return >NOW()
        ORDER BY readers.date_return ASC
    ");
    $rezultate = mysqli_fetch_all($tabel, MYSQLI_ASSOC);
}

// 3. Edituri cu numărul de cărți publicate
if ($raport === "edituri") {
    $tabel = mysqli_query($conexiune, "
        SELECT editura, COUNT(*) AS total
        FROM books
        GROUP BY editura
        ORDER BY total DESC
    ");
    $rezultate = mysqli_fetch_all($tabel, MYSQLI_ASSOC);
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Geist+Mono:wght@100..900&family=Geist:wght@100..900&display=swap"
      rel="stylesheet" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/css/bootstrap.min.css"
      rel="stylesheet"
      integrity="sha384-B/GM4XqrwHnWXNOWMbloTmrYXZg10cakYGmpfsR/bbzQ6JAJI4ihuyADKLnBgrCe"
      crossorigin="anonymous" />

    <style>
      :root {
        --bs-body-font-family: "Geist", system-ui, sans-serif;
        --sidebar-width: 260px;
      }
      .sidebar-desktop {
        width: var(--sidebar-width);
        flex-shrink: 0;
      }
    </style>
  </head>

  <body>
    <div class="d-flex">
      <!-- SIDEBAR DESKTOP (vizibil de la lg în sus) -->
      <aside
        class="sidebar-desktop d-none d-lg-block border-end min-vh-100 p-3">
        <h5 class="mb-3">Dashboard</h5>
        <nav class="nav flex-column">
          <a class="nav-link active" aria-current="page" href="#">Carti</a>
          <a class="nav-link" href="#">Cititori</a>
          <a class="nav-link" href="#">Rapoarte</a>
        </nav>
      </aside>

      <!-- DRAWER MOBIL (se deschide cu ☰) -->
      <dialog
        class="drawer drawer-start"
        tabindex="-1"
        id="sidebar"
        aria-labelledby="sidebarLabel">
        <div class="drawer-header">
          <h5 class="drawer-title" id="sidebarLabel">Dashboard</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="drawer"
            aria-label="Close"></button>
        </div>
        <div class="drawer-body">
          <nav class="nav flex-column">
            <a class="nav-link active" aria-current="page" href="../books/show.php">Carti</a>
            <a class="nav-link" href="../readers/readers.php">Cititori</a>
            <a class="nav-link" href="raport.php">Rapoarte</a>
          </nav>
        </div>
      </dialog>

      <!-- MAIN -->
      <main class="flex-grow-1">
        <header class="d-flex align-items-center gap-3 border-bottom p-3">
          <button
            class="btn-solid theme-secondary d-lg-none"
            type="button"
            data-bs-toggle="drawer"
            data-bs-target="#sidebar"
            aria-controls="sidebar">
            ☰
          </button>
          <h1 class="h5 m-0">Overview</h1>
          <div class="ms-auto">Avatar</div>
        </header>

        <div class="container-fluid p-4">
          <div class="container text-center">
            <div class="row align-items-center">
              <div class="col">
                <h1>Rapoarte</h1>
                <p>Uitati ce fel de selecctari puteti face</p>
              </div><div class="col">
  <h3>Introduceti suma si vom cauta cartile care depasesc aceasta suma</h3>
  <form method="GET" action="raport.php" class="d-flex gap-2 justify-content-center">
    <input type="hidden" name="raport" value="pret">
    <input type="number" name="suma" step="0.01" min="0" class="form-control" style="max-width:120px"
           value="<?= e($_GET["suma"] ?? "") ?>" required>
    <button type="submit" class="btn btn-primary">Caut</button>
  </form>
</div>

<div class="col">
  <h3>cititorii care inca nu au returnat cartile</h3>
  <form method="GET" action="raport.php">
    <input type="hidden" name="raport" value="restante">
    <button type="submit" class="btn btn-secondary">Verific</button>
  </form>
</div>

<div class="col">
  <h3>afisarea editurilor cu cartile publicate</h3>
  <form method="GET" action="raport.php">
    <input type="hidden" name="raport" value="edituri">
    <button type="submit" class="btn btn-secondary">Afiseaza</button>
  </form>
</div>
              </div>
            </div>
          </div>
          <div class="container text-center">
            <div class="row align-items-center">
              <div class="container mt-5">

  <?php if ($mesaj !== ""): ?>
    <p><?= e($mesaj) ?></p>

  <?php elseif ($raport !== "" && count($rezultate) === 0): ?>
    <p>Nu există rezultate pentru această selecție.</p>

  <?php elseif ($raport === "pret"): ?>
    <h4>Cărți cu prețul mai mare de <?= e($_GET["suma"]) ?></h4>
<div class="container text-center mt-4">
            <div class="row g-4 ">
            <?php foreach ($rezultate as $cart): ?>
            <div class="col-3 col-md-3 col-lg-3">
                <div class="book-card d-flex flex-column h-100">
                    <img class="book-img" src="../images_book/<?= e($cart["photo"]) ?>" alt="<?= e($cart["title"]) ?>">
                    <h3 class="book-title"><?= e($cart["title"]) ?></h3>
                    <h5 class="book-author"><?= e($cart["author"]) ?></h5>
                    <p class="book-publisher"><?= e($cart["editura"]) ?></p>
                    <p><?= e($cart["price"]) ?></p>

                    <div class="book-actions">
                </div>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            </div>

  <?php elseif ($raport === "restante"): ?>
    <h4>Cititori care nu au returnat cartea la termen</h4>
    <table class="table align-middle">
      <thead><tr><th>Cititor</th><th>Telefon</th><th>Carte</th><th>Termen returnare</th></tr></thead>
      <tbody>
        <?php foreach ($rezultate as $r): ?>
          <tr>
            <td><?= e($r["first_last_name"]) ?></td>
            <td><?= e($r["phone_number"]) ?></td>
            <td><img class="book-img" style="width: 50px; height: auto;" src="../images_book/<?= e($r["photo"]) ?>" alt="<?= e($r["title"]) ?>">
                   <?= e($r["title"]) ?></td>
            <td><?= e(date("d.m.Y H:i", strtotime($r["date_return"]))) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

  <?php elseif ($raport === "edituri"): ?>
    <h4>Edituri și numărul de cărți publicate</h4>
    <table class="table align-middle">
      <thead><tr><th>Editura</th><th>Număr de cărți</th></tr></thead>
      <tbody>
        <?php foreach ($rezultate as $r): ?>
          <tr>
            <td><?= e($r["editura"]) ?></td>
            <td><?= (int)$r["total"] ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

</div>
            </div>
          </div>
        </div>
      </main>
    </div>

    <script type="importmap">
      {
        "imports": {
          "@floating-ui/dom": "https://cdn.jsdelivr.net/npm/@floating-ui/dom@1.7.6/+esm",
          "vanilla-calendar-pro": "https://cdn.jsdelivr.net/npm/vanilla-calendar-pro@3.4.0/index.mjs"
        }
      }
    </script>
    <script
      type="module"
      src="https://cdn.jsdelivr.net/npm/bootstrap@6.0.0-alpha.1/dist/js/bootstrap.min.js"
      integrity="sha384-BJf/nQgxZJU68GxBse97Vdlc8Rh+z9PzzXiS3/BjV+1nQPR7IKn+iJnD3DJvCMaU"
      crossorigin="anonymous"></script>
  </body>
</html>
