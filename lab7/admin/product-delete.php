
<?php
include("connection.php");
include_once("functions.php");
$succes = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id=$_POST["id"];
    $sql="DELETE FROM `products` WHERE id='$id'";
    if (!mysqli_query($conexiune, $sql)) {
        die(mysqli_error($conexiune));
    } else {
        $succes=true;
    }
    
} else {
    $mesaj = "Nu au fost trimise date.";
}

mysqli_close($conexiune);
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmare - Management elevi</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f6fb;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 45px 35px;
            border-radius: 16px;
            box-shadow: 0 4px 25px rgba(0,0,0,0.06);
            text-align: center;
            max-width: 450px;
            width: 100%;
        }

        .icon {
            width: 75px;
            height: 75px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .icon.error {
            background: #fee2e2;
            color: #dc2626;
        }

        h2 {
            color: #1e293b;
            margin-bottom: 12px;
        }

        p {
            color: #64748b;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        .btn {
            display: inline-block;
            background: #2b9102;
            color: white;
            padding: 13px 25px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
        }
    </style>
</head>

<body>

<div class="card">

    <?php if ($succes): ?>
        <div class="icon">✓</div>
        <h2>Produs sters!</h2>
    <?php else: ?>
        <div class="icon error">!</div>
        <h2>Operațiune nereușită</h2>
    <?php endif; ?>


    <a href="products.php" class="btn">
        ← Înapoi la lista produselor
    </a>

</div>

</body>
</html>
