<?php
include("../db_connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $sql = "DELETE FROM books WHERE id_book = $id";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: show.php?msg=stearsa");
    exit;
} else {
    header("Location: show.php");
    exit;
}