<?php
define('BASE_DIR', __DIR__ . '/documente');

// ---- funcții oferite ----
function e($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function writeLog($message) {
    $date = date('d.m.Y H:i:s');
    $line = "[$date] $message" . PHP_EOL;

    file_put_contents(
        'activity.log',
        $line,
        FILE_APPEND
    );
}

// ---- funcții obligatorii ----
function getCategories($baseDir) {
    $categorii = [];
    if (!is_dir($baseDir)) {
        return $categorii;
    }
    foreach (scandir($baseDir) as $item) {
        if ($item === '.' || $item === '..') continue;
        if (is_dir($baseDir . '/' . $item)) {
            $categorii[] = $item;
        }
    }
    return $categorii;
}

function getFiles($directory) {
    $fisiere = [];
    if (!is_dir($directory)) {
        return $fisiere;
    }
    foreach (scandir($directory) as $item) {
        if (is_file($directory . '/' . $item)) {
            $fisiere[] = $item;
        }
    }
    return $fisiere;
}

function formatFileSize($bytes) {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return round($bytes / (1024 * 1024), 1) . ' MB';
}

// numără fișierele
function countFiles($directory) {
    $total = 0;
    if (!is_dir($directory)) {
        return $total;
    }
    foreach (scandir($directory) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $directory . '/' . $item;
        if (is_dir($path)) {
            $total += countFiles($path);
        } else {
            $total++;
        }
    }
    return $total;
}

function getExtension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

function isAllowedFile($filename) {
    return in_array(getExtension($filename), ['txt', 'pdf', 'jpg', 'jpeg', 'png']);
}

// caută recursiv
function searchFiles($directory, $search) {
    $rezultate = [];
    if (!is_dir($directory) || $search === '') {
        return $rezultate;
    }
    foreach (scandir($directory) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $directory . '/' . $item;

        if (is_dir($path)) {
            $rezultate = array_merge($rezultate, searchFiles($path, $search));
        } elseif (stripos($item, $search) !== false) {
            $rezultate[] = [
                'name'     => $item,
                'category' => substr($directory, strlen(BASE_DIR) + 1),
                'size'     => filesize($path),
            ];
        }
    }
    return $rezultate;
}


// dimensiunea totală
function dirSize($directory) {
    $total = 0;
    if (!is_dir($directory)) {
        return $total;
    }
    foreach (scandir($directory) as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $directory . '/' . $item;
        $total += is_dir($path) ? dirSize($path) : filesize($path);
    }
    return $total;
}

// nume valid
function isValidName($name) {
    $name = trim($name);
    if ($name === '' || $name === '.' || $name === '..') {
        return false;
    }
    return !preg_match('/[\/\\\\:*?"<>|]/', $name);
}

// numele fișierului 
function makeFileName($title) {
    $nume = preg_replace('/[\/\\\\:*?"<>|]/', '', trim($title));
    $nume = preg_replace('/\s+/', '_', $nume);
    return $nume;
}

// calea fisieruliu
function getFilePath($category, $file) {
    $path = realpath(BASE_DIR . '/' . $category . '/' . basename($file));
    $base = realpath(BASE_DIR);

    if ($path === false || strpos($path, $base . DIRECTORY_SEPARATOR) !== 0 || !is_file($path)) {
        return false;
    }
    return $path;
}

// mesaj de succes
function showMessage($mesaj, $tip) {
    if ($mesaj !== '') {
        echo '<div class="mesaj mesaj-' . e($tip) . '">' . e($mesaj) . '</div>';
    }
}

function pageStart($titlu, $butoane = false) {
    echo '<!doctype html>
<html lang="ro">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>' . e($titlu) . '</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header class="topbar">
    <h1>📁 Manager de documente</h1>
    <div class="topbar-actiuni">';

        if ($butoane) {
        echo '<form method="GET" action="index.php" class="cautare-header">
                <input type="text" name="cauta" placeholder="Caută documente..." value="' . e($_GET["cauta"] ?? "") . '">
                <button type="submit" class="btn">Caută</button>
              </form>
              <button type="button" class="btn btn-secundar" data-deschide="modalCategorie">+ Categorie</button>
              <button type="button" class="btn" data-deschide="modalDocument">+ Creează document</button>
              <button type="button" class="btn" data-deschide="modalFisier">+ Încarcă fișier</button>';
    } else {
        echo '<a class="btn" href="index.php">← Panou principal</a>';
    }

    echo '</div>
  </header>
  <main class="container">';
}
function pageEnd() {
    echo '</main></body></html>';
}