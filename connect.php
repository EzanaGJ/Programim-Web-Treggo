<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "db_programim_web";
$port = 3306;

$conn = mysqli_connect($host, $user, $password, $database,$port);

if (!$conn) {
    echo "Error: Unable to connect to MySQL.";
    echo "Debugging errno: " . mysqli_connect_errno();
    echo "Error: " . mysqli_connect_error();
}


