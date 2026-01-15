<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_programim_web";
$port = 3307;

$conn = mysqli_connect($host, $user, $password, $database,$port);

if (!$conn) {
    header('Content-Type: application/json');
    echo json_encode(["status" => 202, "message" => "Database connection failed"]);
    exit;
}
