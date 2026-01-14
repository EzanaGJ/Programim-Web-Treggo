<?php
global $conn;
session_start();
require_once "connect.php";
require_once "menu.php";

// Get product ID from URL
$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    echo "Product not found";
    exit;
}

// Fetch product from database
$query_productdetail = "SELECT * FROM products WHERE id = ?";
$stmt = mysqli_prepare($conn, $query_productdetail);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) == 0) {
    echo "Product not found";
    exit;
}

$product = mysqli_fetch_assoc($result);
?>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="mb-3">
        <a href="products.php" class="btn btn-primary">
            <i class="fa fa-home"></i> Back to Main Menu
        </a>
    </div>

    <div class="ibox product-detail">
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-5">
                    <div class="product-images">
                        <div>
                            <div class="image-imitation">
                                <img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>" style="width:100%; height:300px; object-fit:cover;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <h2 class="font-bold m-b-xs product-detail-name"><?= $product['name'] ?></h2>
                    <div class="m-b-sm">
                        <small class="text-muted">
                            <?= $product['category'] ?> <i class="fa fa-angle-right"></i> <?= $product['subcategory'] ?>
                        </small>
                    </div>

                    <div class="m-t-md">
                        <h2 class="product-main-price">$<?= $product['amount'] ?></h2>
                    </div>
                    <hr>

                    <h4>Product description</h4>
                    <div class="text-muted"><?= $product['description'] ?></div>

                    <?php if (!empty($product['sizes'])): ?>
                        <hr>
                        <h4>Available Sizes</h4>
                        <div class="btn-group">
                            <?php
                            $sizes = explode(',', $product['sizes']);
                            foreach ($sizes as $size) {
                                echo '<button class="btn btn-outline-secondary btn-sm m-r-xs">' . trim($size) . '</button>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <hr>

                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-cart-plus"></i> Add to cart</button>
                        <button class="btn btn-xs btn-outline btn-danger"><i class="fa fa-heart"></i> Add to favorites</button>
                    </div>

                </div>
            </div>
        </div>
    </div>

</div>

<footer class="text-center">
    © 2025 Treggo | Designed by <strong>EMM'S</strong>
</footer>

<!--<script>-->
<!--    $(document).ready(function(){-->
<!--        $('.product-images').slick({-->
<!--            dots: true,-->
<!--            arrows: true-->
<!--        });-->
<!--    });-->
<!--</script>-->
