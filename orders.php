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
$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$orders = $result->fetch_all(MYSQLI_ASSOC);
?>

<body>

<div class="container mt-5">

    <div class="top-buttons d-flex justify-content-between align-items-center">
        <h2>My Orders</h2>
        <a href="products.php" class="btn btn-primary">
            <i class="fas fa-home"></i> Back to Menu
        </a>
    </div>

    <?php if($orders): ?>
        <?php foreach($orders as $order): ?>
            <div class="card order-card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Order #<?php echo $order['id']; ?></h5>
                    <p class="card-text">
                        <strong>Total:</strong> $<?php echo number_format($order['total_amount'], 2); ?><br>
                        <strong>Status:</strong> <?php echo htmlspecialchars($order['payment_status']); ?><br>
                        <strong>Placed on:</strong> <?php echo $order['created_at']; ?>
                    </p>
                    <a href="order_details.php?order_id=<?php echo $order['id']; ?>" class="btn btn-primary">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>You have not placed any orders yet. <a href="products.php">Shop now</a></p>
    <?php endif; ?>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="css/plugins/toastr/toastr.min.css">
<script src="js/plugins/toastr/toastr.min.js"></script>
<script src="js/inactivityLogout.js"></script>
</body>
</html>

