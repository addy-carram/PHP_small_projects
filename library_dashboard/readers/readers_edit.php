<?php
include("../db_connection.php");
mysqli_set_charset($conexiune, "utf8mb4");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id      = (int)$_POST["id"];
    $nume    = mysqli_real_escape_string($conexiune, $_POST["first_last_name"]);
    $adresa  = mysqli_real_escape_string($conexiune, $_POST["addres"]);
    $telefon = mysqli_real_escape_string($conexiune, $_POST["phone_number"]);
    $cod     = (int)$_POST["cod_id"];
    $getter  = mysqli_real_escape_string($conexiune, str_replace("T", " ", $_POST["date_getter"]));
    $return  = mysqli_real_escape_string($conexiune, str_replace("T", " ", $_POST["date_return"]));

    $sql = "UPDATE readers
            SET first_last_name = '$nume', addres = '$adresa', phone_number = '$telefon',
                cod_id = $cod, date_getter = '$getter', date_return = '$return'
            WHERE id_reader = $id";

    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    }

    mysqli_close($conexiune);
    header("Location: readers.php?msg=editat");
    exit;
} else {
    header("Location: readers.php");
    exit;
}