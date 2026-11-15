<?php
include("../db_connection.php");
mysqli_set_charset($conexiune, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $titlu   = mysqli_real_escape_string($conexiune, $_POST["title"]);
    $autor   = mysqli_real_escape_string($conexiune, $_POST["author"]);
    $editura = mysqli_real_escape_string($conexiune, $_POST["editura"]);
    $pret    = (float)str_replace(",", ".", $_POST["price"]);

    // poza: o mutăm în images_book/ cu un nume unic
    $extensie = pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION);
    $nume_imagine = time() . "_" . rand(1000, 9999) . "." . $extensie;
    move_uploaded_file($_FILES["photo"]["tmp_name"], __DIR__ . "/../images_book/" . $nume_imagine);

    $sql = "INSERT INTO books (title, author, editura, price, photo)
            VALUES ('$titlu', '$autor', '$editura', $pret, '$nume_imagine')";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: show.php?msg=adaugata");
    exit;
} else {
    header("Location: show.php");
    exit;
}