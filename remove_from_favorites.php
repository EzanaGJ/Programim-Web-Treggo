<?php
global $conn;
session_start();
require_once "connect.php";

// Clear any accidental output buffers
ob_clean();
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Session expired']);
    exit;
}

if (isset($_POST['product_id'])) {
    $user_id = $_SESSION['id'];
    $product_id = intval($_POST['product_id']);

    $query = "DELETE FROM favorites WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);

    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No product ID provided']);
}
exit;