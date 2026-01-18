<?php
global $conn;
session_start();
require_once "connect.php";
require_once "menu.php";

if (!isset($_SESSION["id"]) || $_SESSION["role_id"] != 2) {
    header("Location: login.php");
    exit;
}

$category = $_GET['category'] ?? null;
$subcategory = $_GET['subcategory'] ?? null;

// Build query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

// Filter by category
if ($category) {
    $query .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

// Filter by subcategory
if ($subcategory) {
    $query .= " AND subcategory = ?";
    $params[] = $subcategory;
    $types .= "s";
}
$size = $_GET['size'] ?? null;

if ($size) {
    $query .= " AND size = ?";
    $params[] = $size;
    $types .= "s";
}
// Prepare and execute
$stmt = mysqli_prepare($conn, $query);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch products
$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>
<div class="mb-3 d-flex justify-content-between">
    <a href="products.php" class="btn btn-primary">
        <i class="fa fa-home"></i> Back to Main Menu
    </a>
<!--add to cart-->
    <a href="cart.php" class="btn btn-primary">
        <i class="fa fa-shopping-cart"></i> Cart
    </a>
</div>


<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row" id="products-container">
        <?php foreach ($products as $product) { ?>
            <div class="col-md-3">
                <div class="ibox">
<!--                    changed product--box -->
                    <a href="product_detail.php?id=<?= $product['id'] ?>" style="text-decoration:none; color:inherit;">
                    <div class="ibox-content product-box">
                        <div class="product-imitation">
                            <img src="<?= htmlspecialchars($product['img']) ?>"
                                 alt="<?= htmlspecialchars($product['name']) ?>">
                        </div>

                        <div class="product-desc">
                            <span class="product-price">$<?= $product['amount'] ?></span>
                            <small class="text-muted">
                                <?= $product['category'] ?> › <?= $product['subcategory'] ?>
                            </small>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="product-name"><?= $product['name'] ?></a>
                            <div class="small m-t-xs">
                                <?= $product['description'] ?>

                            </div>
                            <div class="m-t text-right">
<!--                                add to cart-->
                                <button class="btn btn-xs btn-outline btn-danger"><i class="fa fa-heart"></i>
                                    <button class="btn btn-xs btn-outline btn-warning add-to-cart"
                                            data-id="<?= $product['id'] ?>">
                                        Add to Cart
                                    </button>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <footer class="text-center">
        © 2025 Treggo | Designed by <strong>EMM'S</strong>
    </footer>
</div>

<!--Maria-login-->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {

        /*** 1️⃣ Inactivity Logout Timer ***/
        let timeoutDuration = 900000; // 15 minutes = 900000ms
        let logoutTimer;

        function startLogoutTimer() {
            clearTimeout(logoutTimer);
            logoutTimer = setTimeout(() => {
                alert("You have been logged out due to inactivity.");
                window.location.href = "login.php"; // redirect to login
            }, timeoutDuration);
        }

        function resetLogoutTimer() {
            startLogoutTimer();
        }

        // Reset timer on user activity
        $(document).on('mousemove keydown click scroll', resetLogoutTimer);

        // Start the timer on page load
        startLogoutTimer();


        /*** 2️⃣ Add to Cart AJAX ***/
        $(".add-to-cart").on("click", function () {
            let product_id = $(this).data("id");

            $.ajax({
                url: "ajax.php",
                type: "POST",
                dataType: "json",
                data: {
                    action: "add_to_cart",
                    product_id: product_id
                },
                success: function (res) {
                    if (res.status === "success") {
                        alert("Produkti u shtua në shportë ✅");
                    } else {
                        alert(res.message);
                    }
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    alert("AJAX error");
                }
            });
        });

    });
</script>
