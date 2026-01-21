<?php
if (!isset($_SESSION['id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.php");
    exit;
}