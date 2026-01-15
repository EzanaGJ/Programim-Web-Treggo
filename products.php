<?php
global $conn;
session_start();
require_once "connect.php";
require_once "menu.php";

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
<div class="mb-3">
    <a href="products.php" class="btn btn-primary">
        <i class="fa fa-home"></i> Back to Main Menu
    </a>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row" id="products-container">
        <?php foreach ($products as $product) { ?>
            <div class="col-md-3">
                <div class="ibox">
<!--                    changed product-box-->
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
                                <button class="btn btn-xs btn-outline btn-danger"><i class="fa fa-heart"></i>
                                <button class="btn btn-xs btn-outline btn-warning">Add to Cart</button>
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
