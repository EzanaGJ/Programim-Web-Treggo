<?php
global $conn;
session_start();
require_once "connect.php";
require_once "includes/login/menu.php";

$user_id = $_SESSION['id'] ?? null;
if(!$user_id){
    header("Location: login.php");
    exit;
}

$order_id = $_GET['order_id'] ?? null;
if(!$order_id){
    header("Location: orders.php");
    exit;
}

// Verify that this order belongs to the logged-in user
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if(!$order){
    die("Order not found.");
}


$stmt_items = $conn->prepare("
    SELECT oi.quantity, oi.price, p.name, p.img
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

$stmt_addr = $conn->prepare("SELECT * FROM order_addresses WHERE order_id = ?");
$stmt_addr->bind_param("i", $order_id);
$stmt_addr->execute();
$address = $stmt_addr->get_result()->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?php echo $order_id; ?> Details | Treggo</title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; }
        .order-item { border-bottom: 1px solid #e1e1e1; padding: 10px 0; }
        .order-item:last-child { border-bottom: none; }
        .product-img { width: 70px; }
    </style>

</head>

<body>

<div class="container mt-5">
    <h2 class="mb-4">Order #<?php echo $order_id; ?> Details</h2>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Order Summary</h5>
            <p class="card-text">
                <strong>Total:</strong> $<?php echo number_format($order['total_amount'],2); ?><br>
                <strong>Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?><br>
                <strong>Placed on:</strong> <?php echo $order['created_at']; ?>
            </p>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Items</h5>
            <?php if($items): ?>
                <?php foreach($items as $item): ?>
                    <div class="d-flex align-items-center order-item">
                        <img src="<?php echo htmlspecialchars($item['img']); ?>" class="product-img mr-3" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div>
                            <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                            Quantity: <?php echo $item['quantity']; ?> | Price: $<?php echo number_format($item['price'],2); ?><br>
                            Subtotal: $<?php echo number_format($item['quantity'] * $item['price'],2); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No items found for this order.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Shipping Address</h5>
            <p class="card-text">
                <?php echo htmlspecialchars($address['address_line1']); ?><br>
                <?php if(!empty($address['address_line2'])) echo htmlspecialchars($address['address_line2'])."<br>"; ?>
                <?php echo htmlspecialchars($address['city']).", ".htmlspecialchars($address['postal_code']); ?><br>
                <?php echo htmlspecialchars($address['country']); ?>
            </p>
        </div>
    </div>

    <a href="orders.php" class="btn btn-outline-primary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>


</body>
</html>
