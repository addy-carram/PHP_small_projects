<?php
function clean($value) {
    if (!is_scalar($value)) return '';
    return preg_replace('/\s+/u', ' ', trim((string)$value));
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function isValidName($name) {
    $len = mb_strlen($name);
    return $len >= 2 && $len <= 40 && !preg_match('/^\d+$/', $name);
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidPhone($phone) {
    return preg_match('/^\+373\d{8}$/', $phone) === 1;
}

function calculateAge($birthDate) {
    return (new DateTime($birthDate))->diff(new DateTime('today'))->y;
}

function old($field, $default = '') {
    $v = $_POST[$field] ?? $default;
    return is_array($v) ? $default : $v;
}

function showError($field, $errors) {
    return isset($errors[$field]) ? '<span class="error-msg">⚠ ' . e($errors[$field]) . '</span>' : '';
}

function hasError($field, $errors) {
    return isset($errors[$field]) ? 'has-error' : '';
}

function calculatePrice($course, $age, $level, $promoPercent = 0) {
    global $prices;
    $initial = $prices[$course] ?? 0;

    $percent = 0;
    if ($age < 18) {
        $percent += 15;
    } elseif ($age <= 25) {
        $percent += 10;
    }
    if ($level === 'Avansat') {
        $percent += 5;
    }
    $percent = min($percent + $promoPercent, 100);

    $discount = $initial * $percent / 100;
    return [
        'initial'  => $initial,
        'percent'  => $percent,
        'discount' => $discount,
        'final'    => max(0, $initial - $discount),
    ];
}

function getProfileStatus($age, $experience, $skillsCount) {
    $experience = (int)$experience;
    if ($experience >= 3 && $skillsCount >= 3) {
        return 'Profil experimentat';
    } elseif ($experience >= 1) {
        return 'Profil în dezvoltare';
    } else {
        return 'Profil junior';
    }
}