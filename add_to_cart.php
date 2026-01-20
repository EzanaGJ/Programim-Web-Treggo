<?php
global $conn;
session_start();
require_once "connect.php";
header('Content-Type: application/json');

$user_id = $_SESSION['id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;

if (!$user_id || !$product_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'User not logged in or invalid product'
    ]);
    exit;
}

// Check if product already in cart
$check = $conn->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
$check->bind_param("ii", $user_id, $product_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {
    // REMOVE
    $del = $conn->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();

    echo json_encode([
        'status' => 'removed',
        'message' => 'Product removed from cart'
    ]);
} else {
    // ADD
    $ins = $conn->prepare(
        "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)"
    );
    $ins->bind_param("ii", $user_id, $product_id);
    $ins->execute();

    echo json_encode([
        'status' => 'success',
        'message' => 'Product added to cart'
    ]);
}
