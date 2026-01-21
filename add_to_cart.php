<?php
session_start();
require 'connect.php';

// Përdor `id` nga session
$user_id = $_SESSION['id'] ?? 0;
$product_id = $_POST['product_id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if (!$user_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please log in to continue.'
    ]);
    exit;
}

if (!$product_id) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not available.'
    ]);
    exit;
}

// Kontrollo në DB nëse produkti ka size
$stmt = $conn->prepare("SELECT sizes FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Product not available.'
    ]);
    exit;
}

// Nëse produkti ka size → ridrejto përdoruesin për të zgjedhur
if (!empty($product['sizes'])) {
    echo json_encode([
        'status' => 'choose_size',
        'message' => 'Choose a size for your Item!',
        'redirect_url' => 'product_detail.php?id=' . $product_id
    ]);
    exit;
}

// Nëse nuk ka size → shto direkt në cart
$stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity, added_at) VALUES (?, ?, ?, NOW())");
$stmt->bind_param("iii", $user_id, $product_id, $quantity);
$stmt->execute();

echo json_encode([
    'status' => 'success',
    'message' => 'Product added to cart 🛒'
]);
