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

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row" id="products-container">
        <?php foreach ($products as $product) { ?>
            <div class="col-md-3">
                <div class="ibox">
                    <div class="ibox-content product-box">
                        <a href="product_detail.php?id=<?= $product['id'] ?>">
                            <div class="product-imitation">
                                <img src="<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="width:100%">
                            </div>
                        </a>

                        <div class="product-desc">
                            <span class="product-price">$<?= $product['amount'] ?></span>
                            <small class="text-muted">
                                <?= htmlspecialchars($product['category']) ?> › <?= htmlspecialchars($product['subcategory']) ?>
                            </small>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="product-name"><?= htmlspecialchars($product['name']) ?></a>

                            <div class="small m-t-xs">
                                <?= htmlspecialchars($product['description']) ?>
                            </div>

                            <div class="m-t text-right">
                                <button class="btn btn-sm btn-outline btn-danger add-to-favorites" data-id="<?= $product['id'] ?>">
                                    <i class="fa fa-heart"></i>
                                </button>
                                <button class="btn btn-xs btn-outline btn-warning add-to-cart" data-id="<?= $product['id'] ?>">
                                    Add to Cart
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="css/plugins/toastr/toastr.min.css" rel="stylesheet">
<script src="js/jquery-3.1.1.min.js"></script>
<script src="js/plugins/toastr/toastr.min.js"></script>

<script>
    $(document).ready(function() {

        // --- 1. Inactivity Logout (Untouched) ---
        let timeoutDuration = 900000;
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

        // --- 2. Inspinia Toastr Configuration ---
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "progressBar": true, // The thin line at the bottom
            "positionClass": "toast-top-right",
            "onclick": null,
            "showDuration": "400",
            "hideDuration": "1000",
            "timeOut": "7000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn", // Inspinia's smooth entry
            "hideMethod": "fadeOut"
        };

        // --- 3. Add to Cart (Standard Alert - Untouched) ---
        $(".add-to-cart").on("click", function () {
            let product_id = $(this).data("id");
            $.ajax({
                url: "ajax.php",
                type: "POST",
                dataType: "json",
                data: { action: "add_to_cart", product_id: product_id },
                success: function (res) {
                    if (res.status === "success") {
                        alert("Produkti u shtua në shportë ✅");
                    } else {
                        alert(res.message);
                    }
                }
            });
        });

        // --- 4. Add to Favorites (Exact Inspinia Detail Logic) ---
        $('.add-to-favorites').on('click', function() {
            const productId = $(this).data('id');
            const button = $(this);

            $.ajax({
                url: 'add_to_favorites.php',
                method: 'POST',
                data: { product_id: productId },
                dataType: 'json', // Matches your product detail dataType
                success: function(res) {
                    if (res.status === 'success') {
                        // Change button to Solid Red
                        button.removeClass('btn-outline').addClass('btn-danger');
                        toastr.success('Success', 'Product added to favorites!');
                    } else if (res.status === 'removed') {
                        // Change button back to Outline
                        button.addClass('btn-outline').removeClass('btn-danger');
                        toastr.info('Notice', 'Removed from favorites.');
                    } else {
                        toastr.error('Error', res.message || 'Something went wrong.');
                    }
                },
                error: function() {
                    toastr.error('System Error', 'Could not reach the server.');
                }
            });
        });

    });
</script>