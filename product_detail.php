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

<style>
    /* Grey styling for the price */
    .product-main-price {
        color: #676a6c;
        font-weight: 600;
    }

    /* Breadcrumb link styling */
    .category-link {
        color: #676a6c;
        text-decoration: none;
    }
    .category-link:hover {
        color: #1ab394;
        text-decoration: underline;
    }

    /* Grey styling for size selection */
    .selectable-size {
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid #e7eaec;
        color: #676a6c;
    }
    .selectable-size.active {
        background-color: #888888 !important;
        color: #ffffff !important;
        border-color: #777777 !important;
    }
</style>

<div class="wrapper wrapper-content animated fadeInRight">

    <div class="ibox product-detail">
        <div class="ibox-content">
            <div class="row">

                <div class="col-md-5">
                    <div class="product-images">
                        <div class="image-imitation">
                            <img src="<?= $product['img'] ?>" alt="<?= $product['name'] ?>" style="width:100%; height:auto; max-height:500px; object-fit:scale-down;">
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <h2 class="font-bold m-b-xs product-detail-name"><?= $product['name'] ?></h2>

                    <div class="m-b-sm">
                        <small class="text-muted">
                            <a href="products.php?category=<?= urlencode($product['category']) ?>" class="category-link">
                                <?= $product['category'] ?>
                            </a>
                            <i class="fa fa-angle-right"></i>
                            <a href="products.php?subcategory=<?= urlencode($product['subcategory']) ?>" class="category-link">
                                <?= $product['subcategory'] ?>
                            </a>
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
                                $s = trim($size);
                                echo '<button type="button" class="btn btn-white m-r-xs selectable-size" data-size="' . $s . '">' . $s . '</button>';
                            }
                            ?>
                        </div>
                    <?php endif; ?>
                    <hr>

                    <div class="btn-group">
                        <button class="btn btn-primary btn-sm"><i class="fa fa-cart-plus"></i> Add to cart</button>
                        <button class="btn btn-sm btn-outline btn-danger add-to-favorites" data-id="<?= $product['id'] ?>">
                            <i class="fa fa-heart"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function() {
        // Size selection logic
        $('.selectable-size').on('click', function() {
            $('.selectable-size').removeClass('active');
            $(this).addClass('active');
        });

        // Favorites logic
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
                        toastr.success('Added to favorites!');
                    } else if (res.status === 'removed') {
                        button.addClass('btn-outline').removeClass('btn-danger');
                        toastr.info('Removed from favorites.');
                    }
                }
            });
        });
    });
</script>