<?php 
include ('../db_connection.php');
include_once("../functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

$tabel = mysqli_query($conexiune, "
    SELECT * FROM books
");

if (!$tabel) {
    die("Eroare la încărcarea cartilor..");
}

$carti = mysqli_fetch_all($tabel, MYSQLI_ASSOC);

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="style.css" />
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
            <a class="nav-link active" aria-current="page" href="show.php">Carti</a>
            <a class="nav-link" href="../readers/readers.php">Cititori</a>
            <a class="nav-link" href="../raport/raport.php">Rapoarte</a>
          </nav>
        </div>
      </dialog>
      <dialog class="dialog" id="modalEdit">
  <form method="post" action="editeaza.php">
    <div class="dialog-header">
      <h1 class="dialog-title">Editeaza carte</h1>
      <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
    </div>

    <div class="dialog-body">
    <img id="edit-img" class="modal-book-img" alt="">
      <input type="hidden" name="id" id="edit-id">
      <div class="mb-3"><label class="form-label">Titlu</label>
        <input type="text" name="title" id="edit-title" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Autor</label>
        <input type="text" name="author" id="edit-author" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Editura</label>
        <input type="text" name="editura" id="edit-editura" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Pret</label>
        <input type="number" step="0.01" name="price" id="edit-price" class="form-control" required></div>
    </div>

    <div class="dialog-footer">
      <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
      <button type="submit" class="btn-solid theme-primary">Salveaza modificarile</button>
    </div>
  </form>
</dialog>

<dialog class="dialog" id="modalDelete">
  <form method="post" action="sterge.php">
    <div class="dialog-header">
      <h1 class="dialog-title">Sigur doriti sa stergeti aceasta carte?</h1>
      <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
    </div>

    <div class="dialog-body">
        <img id="del-img" class="modal-book-img" alt="">
      <input type="hidden" name="id" id="del-id">
      <ul class="list-unstyled mb-0">
        <li><strong>Titlu:</strong> <span id="del-title"></span></li>
        <li><strong>Autor:</strong> <span id="del-author"></span></li>
        <li><strong>Editura:</strong> <span id="del-editura"></span></li>
        <li><strong>Pret:</strong> <span id="del-price"></span></li>
      </ul>
    </div>

    <div class="dialog-footer">
      <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
      <button type="submit" class="btn-solid theme-danger">Da, sterge</button>
    </div>
  </form>
</dialog>

<dialog class="dialog" id="modalAdd">
  <form method="post" action="adauga.php" enctype="multipart/form-data">
    <div class="dialog-header">
      <h1 class="dialog-title">Adauga carte</h1>
      <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
    </div>

    <div class="dialog-body">
      <div class="mb-3"><label class="form-label">Titlu</label>
        <input type="text" name="title" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Autor</label>
        <input type="text" name="author" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Editura</label>
        <input type="text" name="editura" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Pret</label>
        <input type="number" step="0.01" name="price" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Coperta</label>
        <input type="file" name="photo" accept="image/*" class="form-control"></div>
    </div>

    <div class="dialog-footer">
      <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
      <button type="submit" class="btn-solid theme-primary">Salveaza</button>
    </div>
  </form>
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
        </header>

        <div class="container-fluid p-7">
            <?php
            $mesaje = [
                "adaugata" => "Cartea a fost adăugată.",
                "editata"  => "Cartea a fost editată.",
                "stearsa"  => "Cartea a fost ștearsă.",
            ];
            $msg = $_GET["msg"] ?? "";
            ?>

            <?php if (isset($mesaje[$msg])): ?>
            <div class="notificare notificare-<?= e($msg) ?>" id="notificare">
                <?= e($mesaje[$msg]) ?>
            </div>
            <?php endif; ?>
          <div class="container text-center mb-7">
            <div class="row align-items-baseline">
              <div class="col">
                <h1>Carti</h1>
                <p>Lista cu toate cartile din biblioteca.</p>
              </div>
              <div class="col">
                 <button type="button" class="btn-solid theme-primary book-btn-add"
                data-bs-toggle="dialog" data-bs-target="#modalAdd">Adauga carte</button>
              </div>
        
            </div>
          </div>
            <div class="container text-center mt-4">
            <div class="row g-4 ">
            <?php foreach ($carti as $cart): ?>
            <div class="col-3 col-md-3 col-lg-3">
                <div class="book-card d-flex flex-column h-100">

                    <img class="book-img" src="../images_book/<?= e($cart["photo"]) ?>" alt="<?= e($cart["title"]) ?>">

                    <h3 class="book-title"><?= e($cart["title"]) ?></h3>
                    <h5 class="book-author"><?= e($cart["author"]) ?></h5>
                    <p class="book-publisher"><?= e($cart["editura"]) ?></p>
                    <p><?= e($cart["price"]) ?></p>

                    <div class="book-actions">
                <button type="button" class="book-btn book-btn-delete" data-action="delete"
                data-bs-toggle="dialog" data-bs-target="#modalDelete"
                data-id="<?= (int)$cart["id_book"] ?>"
                data-photo="<?= e($cart["photo"]) ?>"
                data-title="<?= e($cart["title"]) ?>"
                data-author="<?= e($cart["author"]) ?>"
                data-editura="<?= e($cart["editura"]) ?>"
                data-price="<?= e($cart["price"]) ?>">Sterge</button>

            <button type="button" class="book-btn book-btn-edit" data-action="edit"
                data-bs-toggle="dialog" data-bs-target="#modalEdit"
                data-id="<?= (int)$cart["id_book"] ?>"
                data-photo="<?= e($cart["photo"]) ?>"
                data-title="<?= e($cart["title"]) ?>"
                data-author="<?= e($cart["author"]) ?>"
                data-editura="<?= e($cart["editura"]) ?>"
                data-price="<?= e($cart["price"]) ?>">Editeaza</button>
                </div>
                </div>
            </div>
            <?php endforeach; ?>
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
      crossorigin="anonymous">
    
         </script>
        <script>
        const $ = (id) => document.getElementById(id);

        document.addEventListener('click', (ev) => {
            const btn = ev.target.closest('[data-action]');
            if (!btn) return;
            const d = btn.dataset;

            if (d.action === 'edit') {
            $('edit-id').value      = d.id;
            $('edit-title').value   = d.title;
            $('edit-author').value  = d.author;
            $('edit-editura').value = d.editura;
            $('edit-price').value   = d.price;
            $('edit-img').src       = '../images_book/' + d.photo;
            }

            if (d.action === 'delete') {
            $('del-id').value            = d.id;
            $('del-title').textContent   = d.title;
            $('del-author').textContent  = d.author;
            $('del-editura').textContent = d.editura;
            $('del-price').textContent   = d.price;
            $('del-img').src             = '../images_book/' + d.photo;
            }
        });
        const notificare = document.getElementById('notificare');
        if (notificare) {
        setTimeout(() => {
            notificare.style.opacity = '0';
            setTimeout(() => notificare.remove(), 400);
        }, 3000);

        // scoate ?msg=... din adresă, ca la Refresh să nu apară din nou
        history.replaceState(null, '', location.pathname);
        }
        </script>
  </body>
</html>

