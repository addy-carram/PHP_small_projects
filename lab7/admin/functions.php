<?php

define("IMAGES_DIR", __DIR__ . "/../images/");


function e($valoare) {
    return htmlspecialchars((string)$valoare, ENT_QUOTES, 'UTF-8');
}

function setMesaj($text, $tip = "succes") {
    $_SESSION["mesaj"] = ["text" => $text, "tip" => $tip];
}

function afiseazaMesaj() {
    if (!isset($_SESSION["mesaj"])) {
        return;
    }

    $text = $_SESSION["mesaj"]["text"];
    $tip = $_SESSION["mesaj"]["tip"];
    unset($_SESSION["mesaj"]);

    if ($tip == "succes") {
        $stil = "background:#dcfce7; color:#14532d; border:1px solid #16a34a;";
        $text = "✓ " . $text;
    } else {
        $stil = "background:#fee2e2; color:#7f1d1d; border:1px solid #dc2626;";
    }

    echo '<div style="' . $stil . ' padding:12px 16px; font-weight:bold;">' . e($text) . '</div>';
}


function formatPrice($price) {
    return number_format((float)$price, 2, ",", " ") . " MDL";
}

function getStockStatus($stock) {
    if ($stock <= 0) {
        return "Stoc epuizat";
    }
    if ($stock <= 5) {
        return "Stoc limitat";
    }
    return "În stoc";
}

function validateImage($file) {
    if (!isset($file) || $file["error"] == UPLOAD_ERR_NO_FILE) {
        return "Selectează o imagine.";
    }

    if ($file["error"] != UPLOAD_ERR_OK) {
        return "Fișierul nu a fost încărcat corect.";
    }

    if ($file["size"] > 2 * 1024 * 1024) {
        return "Imaginea nu poate depăși 2 MB.";
    }

    $extensie = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $permise = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($extensie, $permise)) {
        return "Sunt permise doar fișiere JPG, JPEG, PNG și WEBP.";
    }

    if (!is_dir(IMAGES_DIR)) {
        return "Folderul images/ nu există.";
    }

    return "";
}
function generateImageName($file) {
    $extensie = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    return time() . "_" . rand(1000, 9999) . "." . $extensie;
}

function deleteImage($filename) {
    if ($filename == "") {
        return false;
    }

    $cale = IMAGES_DIR . basename($filename);

    if (file_exists($cale)) {
        return unlink($cale);
    }
    return false;
}


function countProductsInCategory($conexiune, $id_categorie) {
    $stmt = mysqli_prepare($conexiune, "SELECT COUNT(*) FROM products WHERE category_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id_categorie);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $numar);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    return $numar;
}

function categoryNameExists($conexiune, $nume, $id_exclus = 0) {
    $stmt = mysqli_prepare($conexiune, "SELECT id FROM categories WHERE name = ? AND id != ?");
    mysqli_stmt_bind_param($stmt, "si", $nume, $id_exclus);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    $exista = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    return $exista;
}