<?php
if (!isset($_SESSION["id"]) || $_SESSION["role_id"] != 1) {
    header("Location: login.php");
    exit;
}