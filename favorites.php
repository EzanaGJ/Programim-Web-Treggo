<?php
global $conn;
session_start();
require_once "connect.php";
require_once "menu.php";

if (!isset($_SESSION['id'])) { header("Location: login.php"); exit; }

$user_id = $_SESSION['id'];
$query = "SELECT p.id, p.name, p.description, p.category, p.subcategory, p.amount, p.img 
          FROM favorites f JOIN products p ON p.id = f.product_id WHERE f.user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        body { background-color: #f3f3f4; font-family: "open sans", sans-serif; }
        .fav-header { color: #ed5565; font-weight: 700; margin-bottom: 10px; font-size: 14px; }
        .ibox { background: #fff; border: 1px solid #e7eaec; }
        .product-item { padding: 15px 20px; border-bottom: 1px solid #f1f1f1; display: flex; align-items: center; }
        .product-img { width: 60px; height: 60px; object-fit: cover; margin-right: 15px; border: 1px solid #e7eaec; }
        .product-title { color: #1ab394; font-weight: 600; text-decoration: none; display: block; }
        .label-gray { background: #676a6c; color: #fff; font-size: 10px; padding: 2px 8px; border-radius: 4px; margin-right: 5px; }
        .btn-cart { background: #1ab394; color: #fff; border: none; padding: 5px 12px; border-radius: 4px; }
        .btn-remove { background: transparent; color: #676a6c; border: 1px solid #ccc; padding: 5px 12px; border-radius: 4px; margin-left: 5px; }
    </style>
</head>
<body>
<div class="wrapper wrapper-content">
    <div class="fav-header">Favorites List</div>
    <div class="ibox">
        <div class="ibox-content" id="favorites-container">
            <?php if (mysqli_num_rows($result) === 0): ?>
                <div class="p-4 text-muted" id="empty-msg">No items in your favorites.</div>
            <?php endif; ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <div class="product-item">
                    <img src="<?= htmlspecialchars($row['img']); ?>" class="product-img">
                    <div style="flex-grow: 1;">
                        <a href="product_detail.php?id=<?= $row['id'] ?>" class="product-title"><?= htmlspecialchars($row['name']); ?></a>
                        <small class="text-muted"><?= htmlspecialchars($row['description']); ?></small>
                        <div class="m-t-xs">
                            <span class="label-gray"><?= htmlspecialchars($row['category']); ?></span>
                            <span class="label-gray"><?= htmlspecialchars($row['subcategory']); ?></span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <span style="font-weight:700; margin-right:15px;"><?= number_format($row['amount'], 0); ?> €</span>
                        <button class="btn-cart addToCartBtn" data-id="<?= $row['id']; ?>"><i class="fa fa-shopping-cart"></i></button>
                        <button class="btn-remove removeFavoriteBtn" data-id="<?= $row['id']; ?>"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<script>
    $(document).ready(function () {
        toastr.options = { "progressBar": true, "positionClass": "toast-top-right", "timeOut": "3000" };

        $(document).on("click", ".removeFavoriteBtn", function (e) {
            e.preventDefault();
            const btn = $(this);
            const productId = btn.data("id");

            $.ajax({
                type: "POST",
                url: "remove_from_favorites.php",
                data: { product_id: productId },
                dataType: "json",
                success: function (res) {
                    if (res.status === "success") {
                        btn.closest(".product-item").fadeOut(300, function() {
                            $(this).remove();
                            if ($(".product-item").length === 0) { location.reload(); }
                        });
                        toastr.success('Item removed');
                    } else {
                        toastr.error(res.message);
                    }
                },
                error: function() {
                    toastr.error('Error: Check if remove_from_favorites.php exists');
                }
            });
        });
    });
</script>
</body>
</html>