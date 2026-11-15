<?php
function e($valoare): string {
    return htmlspecialchars((string)$valoare, ENT_QUOTES, 'UTF-8');
}



?>