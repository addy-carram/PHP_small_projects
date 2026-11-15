<?php
include("../db_connection.php");
mysqli_set_charset($conexiune, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id      = (int)$_POST["id"];
    $titlu   = mysqli_real_escape_string($conexiune, $_POST["title"]);
    $autor   = mysqli_real_escape_string($conexiune, $_POST["author"]);
    $editura = mysqli_real_escape_string($conexiune, $_POST["editura"]);
    $pret    = (float)str_replace(",", ".", $_POST["price"]);

    $sql = "UPDATE books
            SET title = '$titlu', author = '$autor', editura = '$editura', price = $pret
            WHERE id_book = $id";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: show.php?msg=editata");
    exit;
} else {
    header("Location: show.php");
    exit;
}