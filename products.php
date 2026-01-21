<?php
global $conn;
session_start();

//if (!isset($_SESSION["id"]) || $_SESSION["role_id"] != 1) {
//    header("Location: login.php");
//    exit;
//}

require_once "connect.php";

require_once "menu.php";




$limit = 12; // Products per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$category = $_GET['category'] ?? null;
$subcategory = $_GET['subcategory'] ?? null;
$size = $_GET['size'] ?? null;


$where = " WHERE 1=1 ";
$params = [];
$types = "";

if ($category) {
    $where .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}
if ($subcategory) {
    $where .= " AND subcategory = ?";
    $params[] = $subcategory;
    $types .= "s";
}
if ($size) {
    $where .= " AND size = ?";
    $params[] = $size;
    $types .= "s";
}

$countQuery = "SELECT COUNT(*) as total FROM products $where";
$stmt = mysqli_prepare($conn, $countQuery);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$total = mysqli_fetch_assoc($result)['total'];
$totalPages = ceil($total / $limit);

// --- Fetch products for current page ---
$query = "SELECT * FROM products $where LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$products = [];
while ($row = mysqli_fetch_assoc($result)) {
    $products[] = $row;
}
?>

<div class="wrapper wrapper-content animated fadeInRight d-flex flex-column" style="min-height: 100vh;">

    <div class="row flex-grow-1" id="products-container">
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
                            <span class="product-price"><?= $product['amount'] ?>€</span>
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
                                <button class="btn btn-outline add-to-cart" data-id="<?= $product['id']; ?>">
                                    <i class="fa fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>

    <div class="text-center mt-auto py-4">
        <nav>
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Next</a>
                </li>
            </ul>
        </nav>
    </div>
</div

<!-- JS + Toastr -->
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
        $('.add-to-cart').on('click', function () {
            const productId = $(this).data('id');
            const button = $(this);

            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        button.removeClass('btn-outline').addClass('btn-success');
                        toastr.success('Success', 'Product added to cart 🛒');
                    }
                    else if (res.status === 'removed') {
                        button.addClass('btn-outline').removeClass('btn-success');
                        toastr.info('Notice', 'Product removed from cart');
                    }
                    else {
                        toastr.error('Error', res.message || 'Something went wrong');
                    }
                },
                error: function () {
                    toastr.error('System Error', 'Server not reachable');
                }
            });
        });


        // --- 4. Add to Favorites ---
        $('.add-to-favorites').on('click', function() {
            const productId = $(this).data('id');
            const button = $(this);

            $.ajax({
                url: 'add_to_favorites.php',
                method: 'POST',
                data: { product_id: productId },
                dataType: 'json',
                success: function(res) {
                    if (res.status === 'success') {
                        button.removeClass('btn-outline').addClass('btn-danger');
                        toastr.success('Success', 'Product added to favorites!');
                    } else if (res.status === 'removed') {
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


