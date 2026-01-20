<?php
global $conn;
session_start();
require_once "connect.php";
header('Content-Type: application/json');

$user_id = $_SESSION['id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;
$size = $_POST['size'] ?? null;

if (!$user_id || !$product_id || !$size) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing user, product or size'
    ]);
    exit;
}

// kontrollo produkt + size
$check = $conn->prepare(
    "SELECT id FROM cart WHERE user_id = ? AND product_id = ? AND size = ?"
);
$check->bind_param("iis", $user_id, $product_id, $size);
$check->execute();
$res = $check->get_result();

if ($res->num_rows > 0) {

    // toggle remove
    $del = $conn->prepare(
        "DELETE FROM cart WHERE user_id = ? AND product_id = ? AND size = ?"
    );
    $del->bind_param("iis", $user_id, $product_id, $size);
    $del->execute();

    echo json_encode([
        'status' => 'removed',
        'message' => 'Product removed from cart'
    ]);
    exit;
}

// insert me size
$ins = $conn->prepare(
    "INSERT INTO cart (user_id, product_id, size, quantity)
     VALUES (?, ?, ?, 1)"
);
$ins->bind_param("iis", $user_id, $product_id, $size);
$ins->execute();

echo json_encode([
    'status' => 'success',
    'message' => 'Product added to cart'
]);
