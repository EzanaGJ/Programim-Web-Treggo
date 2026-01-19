<?php
global $conn;
session_start();
require_once "connect.php";

$user_id = $_SESSION['id'];
$product_id = $_POST['product_id'];

// Check if already favorited
$check = mysqli_prepare($conn, "SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
mysqli_stmt_execute($check);
$res = mysqli_stmt_get_result($check);

if (mysqli_num_rows($res) > 0) {
    // Exists? DELETE IT
    $del = mysqli_prepare($conn, "DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
    mysqli_stmt_bind_param($del, "ii", $user_id, $product_id);
    mysqli_stmt_execute($del);
    echo json_encode(['status' => 'removed', 'message' => 'Removed!']);
} else {
    // New? INSERT IT
    $ins = mysqli_prepare($conn, "INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    mysqli_stmt_bind_param($ins, "ii", $user_id, $product_id);
    mysqli_stmt_execute($ins);
    echo json_encode(['status' => 'success', 'message' => 'Added!']);
}