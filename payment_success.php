<?php
global $conn;
session_start();
require_once "includes/login/menu.php";
require_once "connect.php";

$user_id = $_SESSION['id'] ?? null;
//if(!$user_id){
//    header("Location: login.php");
//    exit;
//}

$sql = "SELECT c.product_id, c.quantity, p.amount
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cart_items = $result->fetch_all(MYSQLI_ASSOC);

if(empty($cart_items)){
    header("Location: cart.php");
    exit;
}

$total = 0;
foreach($cart_items as $item){
    $total += $item['amount'] * $item['quantity'];
}

$address = $_SESSION['checkout_address'] ?? [
    'line1' => '123 Main St',
    'line2' => '',
    'city' => 'Your City',
    'postal_code' => '00000',
    'country' => 'Your Country'
];


mysqli_begin_transaction($conn);

try {
    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, payment_status) VALUES (?, ?, ?)");
    $status = 'Paid';
    $stmt_order->bind_param("ids", $user_id, $total, $status);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach($cart_items as $item){
        $stmt_item->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['amount']);
        $stmt_item->execute();
    }

    $stmt_addr = $conn->prepare("INSERT INTO order_addresses (order_id, address_line1, address_line2, city, postal_code, country) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt_addr->bind_param("isssss", $order_id, $address['line1'], $address['line2'], $address['city'], $address['postal_code'], $address['country']);
    $stmt_addr->execute();

    mysqli_commit($conn);

    $stmt_clear = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear->bind_param("i", $user_id);
    $stmt_clear->execute();

} catch(Exception $e){
    mysqli_rollback($conn);
    die("Failed to save order: ".$e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Successful | Treggo</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">

    <!-- Font Awesome (optional but recommended) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <style>
        body {
            background-color: #f4f6f9;
        }
        .success-card {
            border-radius: 8px;
            border: 1px solid #e1e1e1;
        }
        .success-icon {
            font-size: 60px;
            color: #28a745;
        }
    </style>
</head>

<body>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-7">

            <div class="card success-card shadow-sm">
                <div class="card-body text-center p-5">

                    <i class="fas fa-check-circle success-icon mb-4"></i>

                    <h2 class="mb-3">Payment Successful</h2>

                    <p class="text-muted mb-4">
                        Thank you for your purchase!
                        Your order has been placed successfully.
                    </p>

                    <hr>

                    <p class="mb-1">
                        <strong>Status:</strong>
                        <span class="badge badge-success">Completed</span>
                    </p>

                    <p class="mb-4 text-muted">
                        This transaction was processed securely.
                    </p>

                    <div class="d-flex justify-content-center">
                        <a href="orders.php" class="btn btn-outline-primary mr-2">
                            <i class="fas fa-receipt"></i> View Orders
                        </a>

                        <a href="products.php" class="btn btn-primary">
                            <i class="fas fa-home"></i> Continue Shopping
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
