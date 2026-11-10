<?php
include("db.php");
include_once("functions.php");

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_set_charset($conexiune, "utf8mb4");

$cities = [
    'Chisinau' => 'Chișinău',
    'Balti' => 'Bălți',
    'Cahul' => 'Cahul',
    'Orhei' => 'Orhei',
    'Ungheni' => 'Ungheni'
];

$courses = [
    'php' => 'PHP Web Development',
    'frontend' => 'Frontend Development',
    'testing' => 'Software Testing',
    'database' => 'Baze de date'
];

$skills = [
    'html' => 'HTML',
    'css' => 'CSS',
    'javascript' => 'JavaScript',
    'php' => 'PHP',
    'sql' => 'SQL',
    'git' => 'Git'
];

$prices = [
    'php'      => 2500,
    'frontend' => 2200,
    'testing'  => 1800,
    'database' => 2000
];

$promoCodes = [
    'PHP10'   => 10,
    'STUDENT' => 15,
    'WEB2026' => 20
];

$niveluri_permise = ["Începător", "Intermediar", "Avansat"];

$erori = [];
$competente = [];
$succes = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {

    $nume          = clean($_POST["nume"] ?? "");
    $prenume       = clean($_POST["prenume"] ?? "");
    $email         = clean($_POST["email"] ?? "");
    $telefon       = clean($_POST["telefon"] ?? "");
    $data_nasterii = clean($_POST["data_nasterii"] ?? "");
    $oras          = clean($_POST["oras"] ?? "");
    $adresa        = clean($_POST["adresa"] ?? "");
    $curs          = clean($_POST["curs"] ?? "");
    $nivel         = clean($_POST["nivel"] ?? "");
    $experienta    = clean($_POST["experienta"] ?? "");
    $website       = clean($_POST["website"] ?? "");
    $cod           = strtoupper(clean($_POST["cod_promo"] ?? ""));
    $motivatie     = trim($_POST["motivatie"] ?? "");
    $observatii    = trim($_POST["observatii"] ?? "");
    $acceptare     = isset($_POST["acceptare"]) ? 1 : 0;

    $competente = $_POST["competente"] ?? [];
    if (!is_array($competente)) $competente = [];
    $competente = array_values(array_unique(array_filter($competente, 'is_string')));

    // Nume, prenume 
    if ($nume === "") {
        $erori["nume"] = "Numele este obligatoriu.";
    } elseif (!isValidName($nume)) {
        $erori["nume"] = "Numele trebuie să aibă 2–40 de caractere și să nu fie doar cifre.";
    }

    if ($prenume === "") {
        $erori["prenume"] = "Prenumele este obligatoriu.";
    } elseif (!isValidName($prenume)) {
        $erori["prenume"] = "Prenumele trebuie să aibă 2–40 de caractere și să nu fie doar cifre.";
    }

    // E-mail
    if ($email === "") {
        $erori["email"] = "E-mailul este obligatoriu.";
    } elseif (!isValidEmail($email)) {
        $erori["email"] = "Introdu o adresă de e-mail validă (ex: nume@exemplu.com).";
    }

    // Telefon 
    if ($telefon === "") {
        $erori["telefon"] = "Telefonul este obligatoriu.";
    } elseif (!isValidPhone($telefon)) {
        $erori["telefon"] = "Format invalid. Exemplu: +37369123456";
    }

    //   Data nașterii 
    if ($data_nasterii === "") {
        $erori["data_nasterii"] = "Data nașterii este obligatorie.";
    } else {
        $d = DateTime::createFromFormat('Y-m-d', $data_nasterii);
        $dataValida = $d && $d->format('Y-m-d') === $data_nasterii;

        if (!$dataValida) {
            $erori["data_nasterii"] = "Data nașterii nu este validă.";
        } elseif ($d > new DateTime('today')) {
            $erori["data_nasterii"] = "Data nașterii nu poate fi în viitor.";
        } elseif (calculateAge($data_nasterii) < 16) {
            $erori["data_nasterii"] = "Trebuie să ai minimum 16 ani.";
        }
    }

    //   Oraș, curs  
    if ($oras === "") {
        $erori["oras"] = "Orașul este obligatoriu.";
    } elseif (!array_key_exists($oras, $cities)) {
        $erori["oras"] = "Orașul selectat nu este valid.";
    }

    if ($curs === "") {
        $erori["curs"] = "Cursul este obligatoriu.";
    } elseif (!array_key_exists($curs, $courses)) {
        $erori["curs"] = "Cursul selectat nu este valid.";
    }

    //   Website (opțional)  
    if ($website !== "" && !filter_var($website, FILTER_VALIDATE_URL)) {
        $erori["website"] = "Website invalid. Exemplu: https://exemplu.md";
    }

    //   Competențe  
    $nr_competente = count($competente);
    if ($nr_competente < 2) {
        $erori["competente"] = "Selectați minimum 2 competențe.";
    } elseif ($nr_competente > 4) {
        $erori["competente"] = "Selectați maximum 4 competențe.";
    } else {
        foreach ($competente as $c) {
            if (!array_key_exists($c, $skills)) {
                $erori["competente"] = "Au fost trimise competențe invalide.";
                break;
            }
        }
    }

    //   Nivel  
    if ($nivel === "") {
        $erori["nivel"] = "Nivelul este obligatoriu.";
    } elseif (!in_array($nivel, $niveluri_permise, true)) {
        $erori["nivel"] = "Nivelul selectat nu este valid.";
    }

    //   Experiență + condiție nivel  
    $optiuni_exp = ["options" => ["min_range" => 0, "max_range" => 50]];

    if ($experienta !== "" && filter_var($experienta, FILTER_VALIDATE_INT, $optiuni_exp) === false) {
        $erori["experienta"] = "Experiența trebuie să fie un număr întreg între 0 și 50.";
    } elseif ($nivel === "Avansat") {
        if ($experienta === "") {
            $erori["experienta"] = "Pentru nivelul Avansat experiența este obligatorie.";
        } elseif ((int)$experienta < 2) {
            $erori["experienta"] = "Pentru nivelul Avansat sunt necesari minimum 2 ani de experiență.";
        }
    } elseif ($nivel === "Intermediar") {
        if ($experienta !== "" && (int)$experienta < 1) {
            $erori["experienta"] = "Pentru nivelul Intermediar sunt necesari minimum 1 an de experiență.";
        }
    } else {
        
    }

    //   Motivație  
    $lungime = mb_strlen($motivatie);
    $nr_cuvinte = count(preg_split('/\s+/u', $motivatie, -1, PREG_SPLIT_NO_EMPTY));

    if ($motivatie === "") {
        $erori["motivatie"] = "Motivația este obligatorie.";
    } elseif ($lungime < 50 || $lungime > 500) {
        $erori["motivatie"] = "Motivația trebuie să aibă între 50 și 500 de caractere (acum: $lungime).";
    } elseif ($nr_cuvinte < 8) {
        $erori["motivatie"] = "Motivația trebuie să conțină minimum 8 cuvinte (acum: $nr_cuvinte).";
    }

    //   Regulament, cod promoțional  
    if (!$acceptare) {
        $erori["acceptare"] = "Trebuie să accepți regulamentul.";
    }

    if ($cod !== "" && !array_key_exists($cod, $promoCodes)) {
        $erori["cod_promo"] = "Cod promoțional invalid.";
    }

    //   Calcule + salvare  
    if (empty($erori)) {
        $varsta = calculateAge($data_nasterii);
        $promo  = $cod !== "" ? $promoCodes[$cod] : 0;
        $pret   = calculatePrice($curs, $varsta, $nivel, $promo);
        $profil = getProfileStatus($varsta, $experienta, count($competente));

        $oras_label      = $cities[$oras];
        $curs_label      = $courses[$curs];
        $skills_labels   = array_map(fn($k) => $skills[$k], $competente);
        $competente_text = implode(", ", $skills_labels);

        $adresa_db  = $adresa === "" ? null : $adresa;
        $exp_db     = $experienta === "" ? null : (int)$experienta;
        $website_db = $website === "" ? null : $website;
        $obs_db     = $observatii === "" ? null : $observatii;
        $cod_db     = $cod === "" ? null : $cod;

        $pret_initial = $pret['initial'];
        $reducere     = $pret['discount'];
        $pret_final   = $pret['final'];

        try {
           $stmt = mysqli_prepare($conexiune,
                "INSERT INTO inscrieri
                (nume, prenume, email, telefon, data_nasterii, oras, adresa, curs, nivel, competente,
                experienta, website, motivatie, observatii, acceptarea, promotional,
                pret_initial, reducere, pret_final, tip_profil)
                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            mysqli_stmt_bind_param($stmt, "ssssssssssisssisddds",
                $nume, $prenume, $email, $telefon, $data_nasterii, $oras_label, $adresa_db,
                $curs_label, $nivel, $competente_text, $exp_db, $website_db, $motivatie,
                $obs_db, $acceptare, $cod_db, $pret_initial, $reducere, $pret_final, $profil);

            mysqli_stmt_execute($stmt);
            $succes = true;
        } catch (mysqli_sql_exception $ex) {
            $erori["db"] = "Salvarea a eșuat. Încearcă din nou mai târziu.";
        }
    }
}
?>

<!doctype html>
<html lang="ro">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Înregistrare curs</title>
    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <main>
      <header>
        <h2>Înregistrare la curs</h2>
        <p>Câmpurile marcate cu * sunt obligatorii.</p>
      </header>

<?php if ($succes): ?>

      <div class="success">
        <h2>Înregistrare realizată cu succes!</h2>
        <p>Participant: <?= e($nume . " " . $prenume) ?></p>
        <p>Vârsta: <?= (int)$varsta ?> ani</p>
        <p>Curs: <?= e($curs_label) ?></p>
        <p>Nivel: <?= e($nivel) ?></p>
        <p>Competențe: <?= e($competente_text) ?></p>
        <p>Preț inițial: <?= e($pret['initial']) ?> MDL</p>
        <p>Reducere: <?= e($pret['percent']) ?>% (<?= e($pret['discount']) ?> MDL)</p>
        <p>Preț final: <?= e($pret['final']) ?> MDL</p>
        <p><?= e($profil) ?></p>
      </div>

<?php else: ?>

  <?php if (!empty($erori)): ?>
      <div class="error-summary">
        <p>Formularul conține <?= count($erori) ?> <?= count($erori) === 1 ? 'eroare' : 'erori' ?>:</p>
        <ul>
          <?php foreach ($erori as $eroare): ?>
            <li><?= e($eroare) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
  <?php endif; ?>

      <form action="register.php" method="post">

        <!-- 1. Date personale -->
        <fieldset class="section">
          <legend>1. Date personale</legend>

          <div class="field span-3 <?= hasError('nume', $erori) ?>">
            <label for="nume">Nume</label>
            <input type="text" id="nume" name="nume" placeholder="Nume"
                   value="<?= e(old('nume')) ?>" required minlength="2" maxlength="40" />
            <?= showError('nume', $erori) ?>
          </div>

          <div class="field span-3 <?= hasError('prenume', $erori) ?>">
            <label for="prenume">Prenume</label>
            <input type="text" id="prenume" name="prenume" placeholder="Prenume"
                   value="<?= e(old('prenume')) ?>" required minlength="2" maxlength="40" />
            <?= showError('prenume', $erori) ?>
          </div>

          <div class="field span-3 <?= hasError('data_nasterii', $erori) ?>">
            <label for="data_nasterii">Data nașterii</label>
            <input type="date" id="data_nasterii" name="data_nasterii"
                   max="<?= date('Y-m-d') ?>"
                   value="<?= e(old('data_nasterii')) ?>" required />
            <?= showError('data_nasterii', $erori) ?>
          </div>
        </fieldset>

        <!-- 2. Date de contact -->
        <fieldset class="section">
          <legend>2. Date de contact</legend>

          <div class="field span-3 <?= hasError('email', $erori) ?>">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="E-mail"
                   value="<?= e(old('email')) ?>" required />
            <?= showError('email', $erori) ?>
          </div>

          <div class="field span-3 <?= hasError('telefon', $erori) ?>">
            <label for="telefon">Telefon</label>
            <input type="tel" id="telefon" name="telefon" placeholder="+37369123456"
                   value="<?= e(old('telefon')) ?>" required pattern="\+373\d{8}"
                   title="Format: +373XXXXXXXX" />
            <?= showError('telefon', $erori) ?>
          </div>

          <div class="field span-3 <?= hasError('oras', $erori) ?>">
            <label for="oras">Oraș</label>
            <select id="oras" name="oras" required>
              <option value="">Alege orașul</option>
              <?php foreach ($cities as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= old('oras') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <?= showError('oras', $erori) ?>
          </div>

          <div class="field span-3 optional">
            <label for="adresa">Adresa</label>
            <input type="text" id="adresa" name="adresa" placeholder="Adresa"
                   value="<?= e(old('adresa')) ?>" />
          </div>
        </fieldset>

        <!-- 3. Informații despre curs -->
        <fieldset class="section">
          <legend>3. Informații despre curs</legend>

          <div class="field span-3 <?= hasError('curs', $erori) ?>">
            <label for="curs">Curs</label>
            <select id="curs" name="curs" required>
              <option value="">Alege cursul</option>
              <?php foreach ($courses as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= old('curs') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <?= showError('curs', $erori) ?>
          </div>

          <div class="field span-3 <?= hasError('nivel', $erori) ?>">
            <label>Nivel</label>
            <div class="options">
              <?php foreach ($niveluri_permise as $n): ?>
                <label><input type="radio" name="nivel" value="<?= e($n) ?>" required
                  <?= old('nivel') === $n ? 'checked' : '' ?>> <?= e($n) ?></label>
              <?php endforeach; ?>
            </div>
            <?= showError('nivel', $erori) ?>
          </div>
        </fieldset>

        <!-- 4. Experiență și competențe -->
        <fieldset class="section">
          <legend>4. Experiență și competențe</legend>

          <div class="field req span-6 <?= hasError('competente', $erori) ?>">
            <label>Competențe (2–4)</label>
            <div class="options">
              <?php foreach ($skills as $key => $label): ?>
                <label><input type="checkbox" name="competente[]" value="<?= e($key) ?>"
                  <?= in_array($key, $competente, true) ? 'checked' : '' ?>> <?= e($label) ?></label>
              <?php endforeach; ?>
            </div>
            <?= showError('competente', $erori) ?>
          </div>

          <div class="field span-3 optional <?= hasError('experienta', $erori) ?>">
            <label for="experienta">Experiență (ani)</label>
            <input type="number" id="experienta" name="experienta" placeholder="0"
                   step="1" min="0" max="50" value="<?= e(old('experienta')) ?>" />
            <?= showError('experienta', $erori) ?>
          </div>

          <div class="field span-3 optional <?= hasError('website', $erori) ?>">
            <label for="website">Website personal</label>
            <input type="url" id="website" name="website" placeholder="https://exemplu.md"
                   value="<?= e(old('website')) ?>" />
            <?= showError('website', $erori) ?>
          </div>
        </fieldset>

        <!-- 5. Informații suplimentare -->
        <fieldset class="section">
          <legend>5. Informații suplimentare</legend>

          <div class="field span-6 <?= hasError('motivatie', $erori) ?>">
            <label for="motivatie">Motivație</label>
            <textarea id="motivatie" name="motivatie" rows="4" required
                      minlength="50" maxlength="500"><?= e(old('motivatie')) ?></textarea>
            <?= showError('motivatie', $erori) ?>
          </div>

          <div class="field span-6 optional">
            <label for="observatii">Observații</label>
            <textarea id="observatii" name="observatii" rows="3"><?= e(old('observatii')) ?></textarea>
          </div>

          <div class="field span-3 optional <?= hasError('cod_promo', $erori) ?>">
            <label for="cod_promo">Cod promoțional</label>
            <input type="text" id="cod_promo" name="cod_promo" placeholder="Cod promoțional"
                   value="<?= e(old('cod_promo')) ?>" />
            <?= showError('cod_promo', $erori) ?>
          </div>
        </fieldset>

        <!-- 6. Confirmare -->
        <fieldset class="section">
          <legend>6. Confirmare</legend>

          <div class="field span-3 <?= hasError('acceptare', $erori) ?>">
            <div class="options">
              <label><input type="checkbox" name="acceptare" value="1" required
                <?= old('acceptare') ? 'checked' : '' ?>> Accept regulamentul *</label>
            </div>
            <?= showError('acceptare', $erori) ?>
          </div>

          <div class="actions span-3">
            <button type="submit" name="submit">Trimite înscrierea</button>
          </div>
        </fieldset>

      </form>

<?php endif; ?>
    </main>
  </body>
</html>