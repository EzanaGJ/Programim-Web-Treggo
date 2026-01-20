<?php
global $conn;
session_start();
require_once "connect.php";
require_once "includes/login/menu.php";
//require "includes/login/auth.php";

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['id'];

$address = "";
$stmt = $conn->prepare("SELECT address FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->bind_result($address);
$stmt->fetch();
$stmt->close();

$total = 0;
$products = [];

$stmt = $conn->prepare("
    SELECT p.id, p.name, p.amount, c.quantity
    FROM cart c
    JOIN products p ON p.id = c.product_id
    WHERE c.user_id = ?
");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $products[] = $row;
    $total += $row['amount'] * $row['quantity'];
}
$stmt->close();

$cartEmpty = ($total <= 0);
?>

<script src="https://js.stripe.com/v3/"></script>

<div id="wrapper">

    <!-- Page Heading -->
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Checkout</h2>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="products.php">Menu</a></li>
                <li class="breadcrumb-item"><a href="cart.php">Cart</a></li>
                <li class="breadcrumb-item active"><strong>Checkout</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">

        <?php if ($cartEmpty): ?>
            <div class="text-center p-5">
                <h3>Your cart is empty</h3>
                <a href="products.php" class="btn btn-primary mt-3">Go to Shop</a>
            </div>
        <?php else: ?>

            <!-- TWO COLUMN CHECKOUT -->
            <div class="row">

                <!-- LEFT COLUMN -->
                <div class="col-lg-8">

                    <!-- SHIPPING ADDRESS -->
                    <div class="card shadow-sm p-3 mb-4">
                        <h5 class="mb-3">
                            <span class="badge badge-primary mr-2">1</span>
                            Shipping Address
                        </h5>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea id="address" class="form-control" rows="3"><?= htmlspecialchars($address ?? '') ?></textarea>
                            <small class="text-muted">
                                Loaded from your profile. You can edit it for this order.
                            </small>
                        </div>
                    </div>

                    <div class="card shadow-sm p-3 mb-4">
                        <h5 class="mb-4">
                            <span class="badge badge-primary mr-2">2</span>
                            Payment Method
                        </h5>

                        <!-- Payment Cards -->
                        <div class="d-flex flex-wrap justify-content-between mb-4">
                            <div class="payment-card card bg-light text-center p-3 border-primary" data-target="#stripe" style="cursor:pointer; flex:1; margin-right:10px;">
                                <i class="fas fa-credit-card fa-lg mb-2 text-primary"></i>
                                <h6>Credit Card</h6>
                                <small class="text-muted">Secure Stripe payment</small>
                            </div>

                            <div class="payment-card card bg-light text-center p-3 border-secondary" data-target="#paypal" style="cursor:pointer; flex:1;">
                                <i class="fab fa-paypal fa-lg mb-2 text-info"></i>
                                <h6>PayPal</h6>
                                <small class="text-muted">Coming soon</small>
                            </div>
                        </div>

                        <!-- Stripe -->
                        <div id="stripe" class="payment-pane">
                            <form id="payment-form">
                                <input type="hidden" id="amount" value="<?= number_format($total, 2, '.', '') ?>">
                                <div class="form-group">
                                    <label>Card Details</label>
                                    <div id="card-element" class="form-control p-2"></div>
                                </div>
                                <div id="card-errors" class="text-danger mb-3"></div>

                                <p class="text-muted small text-center">
                                    By placing your order, you agree to our terms and conditions.
                                </p>

                                <button class="btn btn-success btn-lg btn-block">
                                    Place Order & Pay €<?= number_format($total, 2) ?>
                                </button>
                            </form>
                        </div>

                        <!-- PayPal -->
                        <div id="paypal" class="payment-pane" style="display:none;">
                            <div class="text-center p-4">
                                <p>PayPal integration coming soon.</p>
                                <button class="btn btn-info btn-lg" disabled>
                                    <i class="fab fa-paypal"></i> Pay with PayPal
                                </button>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- RIGHT COLUMN: ORDER SUMMARY -->
                <div class="col-lg-4">
                    <div class="card shadow-sm p-3">
                        <h5 class="mb-3">Order Summary</h5>

                        <?php foreach ($products as $p): ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span><?= htmlspecialchars($p['name']) ?> × <?= $p['quantity'] ?></span>
                                <strong>€<?= number_format($p['amount'] * $p['quantity'], 2) ?></strong>
                            </div>
                        <?php endforeach; ?>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <strong>Total</strong>
                            <strong class="text-success">€<?= number_format($total, 2) ?></strong>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once "includes/no_login/footer.php"; ?>

<script>
    $(document).ready(function() {

        // --- 1. Inactivity Logout ---
        let timeoutDuration = 900000; // 15 minuta
        let logoutTimer;

        function startLogoutTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(() => {
                alert("You have been logged out due to inactivity.");
                window.location.href = "login.php";
            }, timeoutDuration);
        }

        $(document).on('mousemove keydown click scroll', startLogoutTimer);
        startLogoutTimer();

    const stripe = Stripe("pk_test_51SqvzNBphMflaAAwIGHwDNuP7XcKcDbQ2Ovohrso1iJ3p10H52m3UaEJR4xX3WBk3WUWGVM0bwIANK1ON4eTqbsZ00Q4akb2T0");
    const elements = stripe.elements();
    const card = elements.create("card", { hidePostalCode: true });
    card.mount("#card-element");

    const form = document.getElementById("payment-form");
    const errorDiv = document.getElementById("card-errors");

    form.addEventListener("submit", async (e) => {
        e.preventDefault();

        const amount = document.getElementById("amount").value;
        const address = document.getElementById("address").value;

        const response = await fetch("payment/payment.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "amount=" + amount + "&address=" + encodeURIComponent(address)
        });

        const data = await response.json();
        if (data.status !== "success") {
            errorDiv.textContent = data.message;
            return;
        }

        const result = await stripe.confirmCardPayment(data.clientSecret, {
            payment_method: { card: card }
        });

        if (result.error) {
            errorDiv.textContent = result.error.message;
        } else {
            window.location.href = "payment_success.php";
        }
    });

    // Payment card switch
    document.querySelectorAll(".payment-card").forEach(card => {
        card.addEventListener("click", () => {
            document.querySelectorAll(".payment-card").forEach(c => c.classList.remove("border-primary"));
            document.querySelectorAll(".payment-pane").forEach(p => p.style.display = "none");

            card.classList.add("border-primary");
            document.querySelector(card.dataset.target).style.display = "block";
        });
    });

    });
</script>
