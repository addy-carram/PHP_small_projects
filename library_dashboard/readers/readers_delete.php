<?php
include("../db_connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST["id"];
    $sql = "DELETE FROM readers WHERE id_reader = $id";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: readers.php?msg=sters");
    exit;
} else {
    header("Location: readers.php");
    exit;
}