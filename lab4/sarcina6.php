<?php

?>


<!DOCTYPE html>
<html>
    <head>
        <title>My ex4</title>
    </head>
    <body>
        <form method="POST">
            <textarea name="textul" id="textul" placeholder="Introduceti textul"></textarea>
            <input type="submit" name="submit" value="Verifica">
        </form>

         <?php if ($rezultat): ?>
            <h3>Rezultate</h3>
            <p>Numar caractere: <?php echo nr_caractere($text); ?></p>
            <p>Numar cuvinte: <?php echo nr_cuvinte($text); ?></p>
            <p>Numar propozitii: <?php echo nr_propozitii($text); ?></p>
            <p>Cel mai lung cuvant: <?php echo htmlspecialchars(lung_cuvant($text)); ?></p>
            <p>Cel mai scurt cuvant: <?php echo htmlspecialchars(scurt_cuvant($text)); ?></p>

            <p>Top 5 cuvinte frecvente:</p>
            <ul>
                <?php foreach (cuvant_frecvent($text) as $cuvant => $nr): ?>
                    <li><?php echo htmlspecialchars($cuvant); ?> - <?php echo $nr; ?> ori</li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </body>

</html>