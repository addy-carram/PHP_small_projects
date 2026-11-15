<?php
include("../db_connection.php");
mysqli_set_charset($conexiune, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nume    = mysqli_real_escape_string($conexiune, $_POST["first_last_name"]);
    $adresa  = mysqli_real_escape_string($conexiune, $_POST["addres"]);
    $telefon = mysqli_real_escape_string($conexiune, $_POST["phone_number"]);
    $cod     = (int)$_POST["cod_id"];
    $getter  = mysqli_real_escape_string($conexiune, str_replace("T", " ", $_POST["date_getter"]));
    $return  = mysqli_real_escape_string($conexiune, str_replace("T", " ", $_POST["date_return"]));

    $sql = "INSERT INTO readers (first_last_name, addres, phone_number, cod_id, date_getter, date_return)
            VALUES ('$nume', '$adresa', '$telefon', $cod, '$getter', '$return')";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: readers.php?msg=adaugat");
    exit;
} else {
    header("Location: readers.php");
    exit;
}