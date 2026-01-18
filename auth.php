<?php
session_start();

// Koha maksimale pa aktivitet (opsionale, mund ta heqësh ose ndryshosh)
$timeout_duration = 900; // 15 minuta

// Nëse nuk është loguar përdoruesi, ridrejtim në login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

// Opsionale: timeout pas 15 minutash pa aktivitet
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit();
}

// Përditëso kohën e aktivitetit
$_SESSION['last_activity'] = time();
?>

