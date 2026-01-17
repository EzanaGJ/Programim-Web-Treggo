<?php
global $conn;
require_once "includes/login/header.php";
require_once "menu.php";

$search = trim($_GET['top-search'] ?? '');

$query = "SELECT * FROM products
          WHERE name LIKE ?
             OR description LIKE ?
             OR category LIKE ?
             OR subcategory LIKE ?";

$stmt = $conn->prepare($query);

$like = "%$search%";
$stmt->bind_param("ssss", $like, $like, $like, $like);
$stmt->execute();
$result = $stmt->get_result();
require_once "includes/login/footer.php";

?>

<div class="container mt-4">
    <h4>Search results for: <strong><?= htmlspecialchars($search) ?></strong></h4>

    <?php if ($result->num_rows > 0): ?>
        <div class="row">
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-3 col-sm-6 mb-4">
                    <div class="card h-100">

                        <a href="product_detail.php?id=<?= $row['id'] ?>"
                           style="text-decoration:none; color:inherit;">

                            <img src="<?= htmlspecialchars($row['img']) ?>"
                                 class="card-img-top"
                                 alt="<?= htmlspecialchars($row['name']) ?>">

                            <div class="card-body text-left">
                                <h6><?= htmlspecialchars($row['name']) ?></h6>
                                <strong><?= number_format($row['amount'], 2) ?> €</strong>
                            </div>

                        </a>

                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="text-muted">No products found.</p>
    <?php endif; ?>
</div>

