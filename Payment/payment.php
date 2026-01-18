<?php
global $stripe, $conn;
session_start();
require_once '../connect.php';
require_once '../stripe_initialization.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo json_encode(['status'=> 'error', 'message' => 'You must be logged in']);
    exit;
}

$user_id = $_SESSION['id'];

try {
    $total = 0;
    $stmt = $conn->prepare("
        SELECT p.amount, c.quantity
        FROM cart c
        JOIN products p ON p.id = c.product_id
        WHERE c.user_id = ?
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $total += $row['amount'] * $row['quantity'];
    }

    if ($total <= 0) {
        echo json_encode(['status'=>'error','message'=>'Your cart is empty']);
        exit;
    }

    $paymentIntent = $stripe->paymentIntents->create([
        'amount' => intval($total * 100), // cents
        'currency' => 'eur',
        'metadata' => [
            'user_id' => $user_id
        ],
    ]);


    $stmt = $conn->prepare("
        INSERT INTO payment_logs (user_id, payment_provider, amount, currency, status, transaction_id)
        VALUES (?, 'stripe', ?, 'eur', 'initiated', ?)
    ");
    $stmt->bind_param("ids", $user_id, $total, $paymentIntent->id);
    if (!$stmt->execute()) {
        echo json_encode(['status'=>'error','message'=>'Failed to log payment']);
        exit;
    }

    echo json_encode([
        'status' => 'success',
        'clientSecret' => $paymentIntent->client_secret
    ]);

} catch(Exception $e) {
    echo json_encode(['status'=>'error','message'=>$e->getMessage()]);
}
