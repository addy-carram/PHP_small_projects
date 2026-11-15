<?php
include 'functions.php';

$mesaj = "";
$tip = "";

$numeCategorie = "";
$titlu = "";
$autor = "";
$continut = "";
$catAleasa = "";

$mesajeGet = [
    "sters"    => "Fișierul a fost șters.",
    "incarcat" => "Fișierul a fost încărcat.",
];
if (isset($_GET["msg"]) && isset($mesajeGet[$_GET["msg"]])) {
    $mesaj = $mesajeGet[$_GET["msg"]];
    $tip = "succes";
}

//   creare categorie  
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["creeaza_categorie"])) {
    $numeCategorie = trim($_POST["nume_categorie"] ?? "");

    if ($numeCategorie === "") {
        $mesaj = "Numele categoriei nu poate fi gol.";
        $tip = "eroare";
    } elseif (!isValidName($numeCategorie)) {
        $mesaj = 'Numele nu poate conține caracterele / \ : * ? " < > |';
        $tip = "eroare";
    } elseif (is_dir(BASE_DIR . "/" . $numeCategorie)) {
        $mesaj = "Categoria „" . $numeCategorie . "” există deja.";
        $tip = "eroare";
    } elseif (!mkdir(BASE_DIR . "/" . $numeCategorie)) {
        $mesaj = "Categoria nu a putut fi creată.";
        $tip = "eroare";
    } else {
        writeLog("Categorie creată: " . $numeCategorie);
        $mesaj = "Categoria „" . $numeCategorie . "” a fost creată.";
        $tip = "succes";
        $numeCategorie = "";
    }
}

//   creare document 

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["salveaza_document"])) {
    $titlu = trim($_POST["titlu"] ?? "");
    $catAleasa = $_POST["categorie"] ?? "";
    $autor = trim($_POST["autor"] ?? "");
    $continut = trim($_POST["continut"] ?? "");

    $numeFisier = makeFileName($titlu) . ".txt";

    if ($titlu === "" || $autor === "" || $continut === "") {
        $mesaj = "Completează titlul, autorul și conținutul.";
        $tip = "eroare";
    } elseif (!isValidName($titlu)) {
        $mesaj = 'Titlul nu poate conține caracterele / \ : * ? " < > |';
        $tip = "eroare";
    } elseif (!in_array($catAleasa, getCategories(BASE_DIR))) {
        $mesaj = "Selectează o categorie existentă.";
        $tip = "eroare";
    } elseif (file_exists(BASE_DIR . "/" . $catAleasa . "/" . $numeFisier)) {
        $mesaj = "Există deja un document cu numele „" . $numeFisier . "” în această categorie.";
        $tip = "eroare";
    } else {
        $text = "Titlu: " . $titlu . "\n"
              . "Autor: " . $autor . "\n"
              . "Data: " . date("d.m.Y") . "\n"
              . "--------------------------------\n"
              . $continut . "\n";

        if (file_put_contents(BASE_DIR . "/" . $catAleasa . "/" . $numeFisier, $text) === false) {
            $mesaj = "Documentul nu a putut fi salvat.";
            $tip = "eroare";
        } else {
            writeLog("Fișier creat: " . $catAleasa . "/" . $numeFisier);
            $mesaj = "Documentul „" . $numeFisier . "” a fost salvat.";
            $tip = "succes";
            $titlu = $autor = $continut = "";
        }
    }
}
// dacă a fost o eroare, redeschidem fereastra în care s-a greșit
$deschide = "";
if ($tip === "eroare" && isset($_POST["creeaza_categorie"])) {
    $deschide = "modalCategorie";
}
if ($tip === "eroare" && isset($_POST["salveaza_document"])) {
    $deschide = "modalDocument";
}
//   date pentru pagină  
$categorii = getCategories(BASE_DIR);

$totalCategorii = count($categorii);
$totalFisiere = countFiles(BASE_DIR);
$dimensiuneTotala = formatFileSize(dirSize(BASE_DIR));

// căutare
$cautare = trim($_GET["cauta"] ?? "");
$rezultateCautare = [];
if ($cautare !== "") {
    $rezultateCautare = searchFiles(BASE_DIR, $cautare);
}

// explorator
$cat = $_GET["cat"] ?? "";
$sort = $_GET["sort"] ?? "nume";
$fisiere = [];

if ($cat !== "" && in_array($cat, $categorii)) {
    $dir = BASE_DIR . "/" . $cat;
    foreach (getFiles($dir) as $nume) {
        $fisiere[] = [
            "name" => $nume,
            "size" => filesize($dir . "/" . $nume),
            "time" => filemtime($dir . "/" . $nume),
        ];
    }

    usort($fisiere, function ($a, $b) use ($sort) {
        if ($sort === "dimensiune") return $b["size"] <=> $a["size"];
        if ($sort === "data")       return $b["time"] <=> $a["time"];
        return strcasecmp($a["name"], $b["name"]);
    });
}
?>
<?php pageStart("Manager de documente", true); ?>

<div class="layout">

  <!-- COLOANA CU DOSARE -->
  <aside class="foldere">
    <h2>Dosare</h2>
    <ul>
      <?php foreach ($categorii as $c): ?>
        <li>
          <a class="dosar <?= $c === $cat ? "activ" : "" ?>" href="index.php?cat=<?= urlencode($c) ?>">
            <span>📂 <?= e($c) ?></span>
            <span class="nr"><?= countFiles(BASE_DIR . "/" . $c) ?></span>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php if (count($categorii) === 0): ?>
      <p class="gol">Nu există dosare.</p>
    <?php endif; ?>
  </aside>

  <!-- CONȚINUT -->
  <div>

    <section class="statistici">
      <div class="stat"><span>Categorii</span><strong><?= $totalCategorii ?></strong></div>
      <div class="stat"><span>Fișiere</span><strong><?= $totalFisiere ?></strong></div>
      <div class="stat"><span>Dimensiune totală</span><strong><?= e($dimensiuneTotala) ?></strong></div>
    </section>

    <?php if ($deschide === "") showMessage($mesaj, $tip); ?>

        <!-- REZULTATE CĂUTARE -->
    <?php if ($cautare !== ""): ?>
    <section class="card">
      <h2>Rezultate pentru „<?= e($cautare) ?>”</h2>

      <?php if (count($rezultateCautare) === 0): ?>
        <p class="gol">Nu s-a găsit niciun document.</p>
      <?php else: ?>
        <table>
          <thead><tr><th>Fișier</th><th>Categorie</th><th>Dimensiune</th><th></th></tr></thead>
          <tbody>
            <?php foreach ($rezultateCautare as $r): ?>
            <tr>
              <td><?= e($r["name"]) ?></td>
              <td><?= e($r["category"]) ?></td>
              <td><?= e(formatFileSize($r["size"])) ?></td>
              <td><a href="document.php?cat=<?= urlencode($r["category"]) ?>&file=<?= urlencode($r["name"]) ?>">Vizualizare</a></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
    <?php endif; ?>

    <!-- EXPLORATOR -->
    <?php if ($cat !== "" && in_array($cat, $categorii)): ?>
    <section class="card">
      <div class="rand-intre">
        <h2>Fișiere în „<?= e($cat) ?>”</h2>
        <div class="sortare">
          Sortare:
          <a href="index.php?cat=<?= urlencode($cat) ?>&sort=nume">Nume</a> ·
          <a href="index.php?cat=<?= urlencode($cat) ?>&sort=dimensiune">Dimensiune</a> ·
          <a href="index.php?cat=<?= urlencode($cat) ?>&sort=data">Data</a>
        </div>
      </div>

      <?php if (count($fisiere) === 0): ?>
        <p class="gol">Categoria nu conține fișiere.</p>
      <?php else: ?>
        <table>
          <thead>
            <tr><th>Fișier</th><th>Tip</th><th>Dimensiune</th><th>Modificat</th><th>Acțiuni</th></tr>
          </thead>
          <tbody>
            <?php foreach ($fisiere as $f): ?>
            <?php $link = "document.php?cat=" . urlencode($cat) . "&file=" . urlencode($f["name"]); ?>
            <tr>
              <td><?= e($f["name"]) ?></td>
              <td><?= e(strtoupper(getExtension($f["name"]))) ?></td>
              <td><?= e(formatFileSize($f["size"])) ?></td>
              <td><?= date("d.m.Y H:i", $f["time"]) ?></td>
              <td class="actiuni">
                <a href="<?= $link ?>">Vizualizare</a> |
                <a href="<?= $link ?>#redenumire">Redenumire</a> |
                <a href="<?= $link ?>#stergere" class="pericol">Ștergere</a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>
    </section>
    <?php else: ?>
      <p class="gol">Alege un dosar din coloana din stânga ca să vezi fișierele.</p>
    <?php endif; ?>

  </div>
</div>

<!-- MODAL:  -->
<dialog class="modal" id="modalCategorie">
  <form method="POST" action="index.php">
    <div class="modal-header">
      <h2>Categorie nouă</h2>
      <button type="button" class="inchide" data-inchide aria-label="Închide">✕</button>
    </div>
    <div class="modal-body">
      <?php if ($deschide === "modalCategorie") showMessage($mesaj, $tip); ?>

      <label for="nume_categorie">Numele categoriei</label>
      <input type="text" id="nume_categorie" name="nume_categorie" value="<?= e($numeCategorie) ?>">

      <div class="modal-footer">
        <button type="button" class="btn btn-secundar" data-inchide>Anulează</button>
        <button type="submit" name="creeaza_categorie" class="btn">Creează categorie</button>
      </div>
    </div>
  </form>
</dialog>

<!-- MODAL: DOCUMENT -->
<dialog class="modal" id="modalDocument">
  <form method="POST" action="index.php">
    <div class="modal-header">
      <h2>Document nou</h2>
      <button type="button" class="inchide" data-inchide aria-label="Închide">✕</button>
    </div>
    <div class="modal-body">
      <?php if ($deschide === "modalDocument") showMessage($mesaj, $tip); ?>

      <label for="titlu">Titlul documentului</label>
      <input type="text" id="titlu" name="titlu" value="<?= e($titlu) ?>">

      <label for="categorie">Categoria</label>
      <select id="categorie" name="categorie">
        <?php foreach ($categorii as $c): ?>
          <option value="<?= e($c) ?>" <?= $c === ($catAleasa !== "" ? $catAleasa : $cat) ? "selected" : "" ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="autor">Autorul</label>
      <input type="text" id="autor" name="autor" value="<?= e($autor) ?>">

      <label for="continut">Conținutul</label>
      <textarea id="continut" name="continut" rows="5"><?= e($continut) ?></textarea>

      <div class="modal-footer">
        <button type="button" class="btn btn-secundar" data-inchide>Anulează</button>
        <button type="submit" name="salveaza_document" class="btn">Salvează documentul</button>
      </div>
    </div>
  </form>
</dialog>

<!-- MODAL: ÎNCĂRCARE FIȘIER -->
<dialog class="modal" id="modalFisier">
  <form method="POST" action="upload.php" enctype="multipart/form-data">
    <div class="modal-header">
      <h2>Încarcă un fișier</h2>
      <button type="button" class="inchide" data-inchide aria-label="Închide">✕</button>
    </div>
    <div class="modal-body">
      <label for="categorie_upload">Categoria destinație</label>
      <select id="categorie_upload" name="categorie">
        <?php foreach ($categorii as $c): ?>
          <option value="<?= e($c) ?>" <?= $c === $cat ? "selected" : "" ?>><?= e($c) ?></option>
        <?php endforeach; ?>
      </select>

      <label for="fisier">Fișier (txt, pdf, jpg, jpeg, png — max. 2 MB)</label>
      <input type="file" id="fisier" name="fisier" accept=".txt,.pdf,.jpg,.jpeg,.png">

      <div class="modal-footer">
        <button type="button" class="btn btn-secundar" data-inchide>Anulează</button>
        <button type="submit" class="btn">Încarcă fișierul</button>
      </div>
    </div>
  </form>
</dialog>

<script>
  // deschide ferestrele
  document.querySelectorAll('[data-deschide]').forEach((buton) => {
    buton.addEventListener('click', () => {
      document.getElementById(buton.dataset.deschide).showModal();
    });
  });

  // butoanele „Anulează” și ✕
  document.querySelectorAll('[data-inchide]').forEach((buton) => {
    buton.addEventListener('click', () => buton.closest('dialog').close());
  });

  // click pe fundalul întunecat închide fereastra
  document.querySelectorAll('dialog.modal').forEach((fereastra) => {
    fereastra.addEventListener('click', (ev) => {
      if (ev.target === fereastra) fereastra.close();
    });
  });
</script>

<?php if ($deschide !== ""): ?>
<script>
  document.getElementById('<?= e($deschide) ?>').showModal();
</script>
<?php endif; ?>

<?php pageEnd(); ?>