<?php
include('../db_connection.php');
include_once("../functions.php");

mysqli_set_charset($conexiune, "utf8mb4");

$tabel = mysqli_query($conexiune, "
    SELECT readers.*, books.title, books.photo
    FROM readers
    JOIN books ON readers.cod_id = books.id_book
    ORDER BY readers.id_reader DESC
");

if (!$tabel) {
    die("Eroare la încărcarea cititorilor..");
}

$cititori = mysqli_fetch_all($tabel, MYSQLI_ASSOC);

// cărțile pentru lista din formulare
$rez_carti = mysqli_query($conexiune, "SELECT id_book, title FROM books ORDER BY title");
$lista_carti = mysqli_fetch_all($rez_carti, MYSQLI_ASSOC);

// notificări
$mesaje = [
    "adaugat" => ["Cititorul a fost adăugat.", "adaugata"],
    "editat"  => ["Cititorul a fost editat.",  "editata"],
    "sters"   => ["Cititorul a fost șters.",   "stearsa"],
];
$msg = $_GET["msg"] ?? "";
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Cititori</title>
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
      <!-- SIDEBAR DESKTOP -->
      <aside class="sidebar-desktop d-none d-lg-block border-end min-vh-100 p-3">
        <h5 class="mb-3">Dashboard</h5>
        <nav class="nav flex-column">
          <a class="nav-link" href="carti.php">Carti</a>
          <a class="nav-link active" aria-current="page" href="readers.php">Cititori</a>
          <a class="nav-link" href="#">Rapoarte</a>
        </nav>
      </aside>

      <!-- DRAWER MOBIL -->
      <dialog class="drawer drawer-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
        <div class="drawer-header">
          <h5 class="drawer-title" id="sidebarLabel">Dashboard</h5>
          <button type="button" class="btn-close" data-bs-dismiss="drawer" aria-label="Close"></button>
        </div>
        <div class="drawer-body">
          <nav class="nav flex-column">
            <a class="nav-link" href="../books/show.php">Carti</a>
            <a class="nav-link active" aria-current="page" href="readers.php">Cititori</a>
            <a class="nav-link" href="../raport/raport.php">Rapoarte</a>
          </nav>
        </div>
      </dialog>

      <!-- MODAL ADAUGA -->
      <dialog class="dialog" id="modalAdd">
        <form method="POST" action="readers_add.php">
          <div class="dialog-header">
            <h1 class="dialog-title">Adauga cititor</h1>
            <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
          </div>

          <div class="dialog-body">
            <div class="mb-3"><label class="form-label">Nume și prenume</label>
              <input type="text" name="first_last_name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Adresa</label>
              <textarea name="addres" class="form-control" rows="2" required></textarea></div>
            <div class="mb-3"><label class="form-label">Telefon</label>
              <input type="text" name="phone_number" class="form-control" maxlength="13" required></div>
            <div class="mb-3"><label class="form-label">Carte împrumutată</label>
              <select name="cod_id" class="form-control" required>
                <?php foreach ($lista_carti as $b): ?>
                  <option value="<?= (int)$b["id_book"] ?>"><?= e($b["title"]) ?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="mb-3"><label class="form-label">Data împrumut</label>
              <input type="datetime-local" name="date_getter" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Data returnare</label>
              <input type="datetime-local" name="date_return" class="form-control" required></div>
          </div>

          <div class="dialog-footer">
            <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
            <button type="submit" class="btn-solid theme-primary">Salveaza</button>
          </div>
        </form>
      </dialog>

      <!-- MODAL EDITEAZA -->
      <dialog class="dialog" id="modalEdit">
        <form method="POST" action="readers_edit.php">
          <div class="dialog-header">
            <h1 class="dialog-title">Editeaza cititor</h1>
            <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
          </div>

          <div class="dialog-body">
            <input type="hidden" name="id" id="edit-id">
            <div class="mb-3"><label class="form-label">Nume și prenume</label>
              <input type="text" name="first_last_name" id="edit-name" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Adresa</label>
              <textarea name="addres" id="edit-addres" class="form-control" rows="2" required></textarea></div>
            <div class="mb-3"><label class="form-label">Telefon</label>
              <input type="text" name="phone_number" id="edit-phone" class="form-control" maxlength="13" required></div>
            <div class="mb-3"><label class="form-label">Carte împrumutată</label>
              <select name="cod_id" id="edit-cod" class="form-control" required>
                <?php foreach ($lista_carti as $b): ?>
                  <option value="<?= (int)$b["id_book"] ?>"><?= e($b["title"]) ?></option>
                <?php endforeach; ?>
              </select></div>
            <div class="mb-3"><label class="form-label">Data împrumut</label>
              <input type="datetime-local" name="date_getter" id="edit-getter" class="form-control" required></div>
            <div class="mb-3"><label class="form-label">Data returnare</label>
              <input type="datetime-local" name="date_return" id="edit-return" class="form-control" required></div>
          </div>

          <div class="dialog-footer">
            <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
            <button type="submit" class="btn-solid theme-primary">Salveaza modificarile</button>
          </div>
        </form>
      </dialog>

      <!-- MODAL STERGE -->
      <dialog class="dialog" id="modalDelete">
        <form method="POST" action="readers_delete.php">
          <div class="dialog-header">
            <h1 class="dialog-title">Sigur doriti sa stergeti acest cititor?</h1>
            <button type="button" class="btn-close" data-bs-dismiss="dialog" aria-label="Close"></button>
          </div>

          <div class="dialog-body">
            <input type="hidden" name="id" id="del-id">
            <ul class="list-unstyled mb-0">
              <li><strong>Cititor:</strong> <span id="del-name"></span></li>
              <li><strong>Telefon:</strong> <span id="del-phone"></span></li>
              <li class="d-flex align-items-center gap-2 mt-2">
                <img id="del-img" class="reader-img" alt="">
                <span id="del-title"></span>
              </li>
            </ul>
          </div>

          <div class="dialog-footer">
            <button type="button" class="btn-solid theme-secondary" data-bs-dismiss="dialog">Anuleaza</button>
            <button type="submit" class="btn-solid theme-danger">Da, sterge</button>
          </div>
        </form>
      </dialog>

      <!-- MAIN -->
      <main class="flex-grow-1">
        <header class="d-flex align-items-center gap-3 border-bottom p-3">
          <button class="btn-solid theme-secondary d-lg-none" type="button"
            data-bs-toggle="drawer" data-bs-target="#sidebar" aria-controls="sidebar">☰</button>
          <h1 class="h5 m-0">Overview</h1>
          <div class="ms-auto">Avatar</div>
        </header>

        <?php if (isset($mesaje[$msg])): ?>
          <div class="notificare notificare-<?= e($mesaje[$msg][1]) ?>" id="notificare">
            <?= e($mesaje[$msg][0]) ?>
          </div>
        <?php endif; ?>

        <div class="container-fluid p-7">
          <div class="container text-center mb-7">
            <div class="row align-items-baseline">
              <div class="col">
                <h1>Cititori</h1>
                <p>Lista cu toti cititorii bibliotecii.</p>
              </div>
              <div class="col">
                <button type="button" class="btn-solid theme-primary book-btn-add"
                  data-bs-toggle="dialog" data-bs-target="#modalAdd">Adauga cititor</button>
              </div>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table align-middle">
              <thead>
                <tr>
                  <th>Cititor</th>
                  <th>Adresa</th>
                  <th>Telefon</th>
                  <th>Carte</th>
                  <th>Data împrumut</th>
                  <th>Data returnare</th>
                  <th></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cititori as $c): ?>
                <tr>
                  <td><?= e($c["first_last_name"]) ?></td>
                  <td><?= e($c["addres"]) ?></td>
                  <td><?= e($c["phone_number"]) ?></td>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <img class="reader-img" src="../images_book/<?= e($c["photo"]) ?>" alt="<?= e($c["title"]) ?>">
                      <span><?= e($c["title"]) ?></span>
                    </div>
                  </td>
                  <td><?= e(date("d.m.Y H:i", strtotime($c["date_getter"]))) ?></td>
                  <td><?= e(date("d.m.Y H:i", strtotime($c["date_return"]))) ?></td>
                  <td>
                    <div class="d-flex gap-2">
                      <button type="button" class="book-btn book-btn-edit" data-action="edit"
                        data-bs-toggle="dialog" data-bs-target="#modalEdit"
                        data-id="<?= (int)$c["id_reader"] ?>"
                        data-name="<?= e($c["first_last_name"]) ?>"
                        data-addres="<?= e($c["addres"]) ?>"
                        data-phone="<?= e($c["phone_number"]) ?>"
                        data-cod="<?= (int)$c["cod_id"] ?>"
                        data-getter="<?= e(date("Y-m-d\TH:i", strtotime($c["date_getter"]))) ?>"
                        data-return="<?= e(date("Y-m-d\TH:i", strtotime($c["date_return"]))) ?>">Editeaza</button>

                      <button type="button" class="book-btn book-btn-delete" data-action="delete"
                        data-bs-toggle="dialog" data-bs-target="#modalDelete"
                        data-id="<?= (int)$c["id_reader"] ?>"
                        data-name="<?= e($c["first_last_name"]) ?>"
                        data-phone="<?= e($c["phone_number"]) ?>"
                        data-title="<?= e($c["title"]) ?>"
                        data-photo="<?= e($c["photo"]) ?>">Sterge</button>
                    </div>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
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

    <script>
      const $ = (id) => document.getElementById(id);

      document.addEventListener('click', (ev) => {
        const btn = ev.target.closest('[data-action]');
        if (!btn) return;
        const d = btn.dataset;

        if (d.action === 'edit') {
          $('edit-id').value     = d.id;
          $('edit-name').value   = d.name;
          $('edit-addres').value = d.addres;
          $('edit-phone').value  = d.phone;
          $('edit-cod').value    = d.cod;
          $('edit-getter').value = d.getter;
          $('edit-return').value = d.return;
        }

        if (d.action === 'delete') {
          $('del-id').value          = d.id;
          $('del-name').textContent  = d.name;
          $('del-phone').textContent = d.phone;
          $('del-title').textContent = d.title;
          $('del-img').src           = '../images_book/' + d.photo;
        }
      });

      // notificarea dispare singură
      const notificare = $('notificare');
      if (notificare) {
        setTimeout(() => {
          notificare.style.opacity = '0';
          setTimeout(() => notificare.remove(), 400);
        }, 3000);
        history.replaceState(null, '', location.pathname);
      }
    </script>
  </body>
</html>