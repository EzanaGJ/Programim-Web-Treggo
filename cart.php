<?php
session_start();
require_once "connect.php";
require_once "menu.php";

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['id'];

// Merr produktet nga shporta
$sql = "SELECT c.id as cart_id, p.id as product_id, p.name, p.amount, p.img, c.quantity
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
$total = 0;
while($row = $result->fetch_assoc()){
    $cart_items[] = $row;
    $total += $row['amount'] * $row['quantity'];
}
?>

<div id="page-wrapper" class="gray-bg">
    <div class="wrapper wrapper-content animated fadeInRight">
        <div>
            <div class="ibox">
                <div class="ibox-title">
                    <h5>Shopping Cart Summary</h5>
                </div>

                <div class="ibox-content d-flex justify-content-between align-items-center">
                    <div>
                        <span>Total</span>
                        <h2 class="font-bold mb-0" id="cart-total">$<?php echo number_format($total,2); ?></h2>
                    </div>

                    <div class="btn-group">
                        <a href="checkout.php" class="btn btn-primary btn-sm">
                            <i class="fa fa-shopping-cart"></i> Checkout
                        </a>
                        <a href="menu.php" class="btn btn-white btn-sm">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>

            <div class="ibox">
                <div class="ibox-title">
                    <h5>Items in your cart</h5>
                </div>

                <div class="ibox-content">
                    <?php if(count($cart_items) > 0): ?>
                        <?php foreach($cart_items as $item): ?>
                            <div class="ibox-content table-responsive">
                                <table class="table shoping-cart-table">
                                    <tbody>
                                    <tr>
                                        <td width="90">
                                            <div class="cart-product-imitation">
                                                <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width:70px;">
                                            </div>
                                        </td>
                                        <td class="desc">
                                            <h3><a href="#" class="text-navy"><?php echo htmlspecialchars($item['name']); ?></a></h3>
                                            <form class="remove-item-form d-inline">
                                                <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                                <button type="submit" class="text-muted remove-item btn btn-link p-0">
                                                    <i class="fa fa-trash"></i> Remove item
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Quantity input me data-amount -->
                                        <td width="100">
                                            <input type="number" min="1" class="form-control quantity-input"
                                                   data-cart-id="<?php echo $item['cart_id']; ?>"
                                                   data-amount="<?php echo $item['amount']; ?>"
                                                   value="<?php echo $item['quantity']; ?>">
                                        </td>

                                        <!-- Subtotal -->
                                        <td class="product-subtotal">
                                            $<?php echo number_format($item['amount'] * $item['quantity'],2); ?>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align:center;">Your cart is empty.</p>
                    <?php endif; ?>
                </div>

                <div class="ibox-content">
                    <button class="btn btn-primary float-right" onclick="window.location.href='checkout.php'">
                        <i class="fa fa-shopping-cart"></i> Checkout
                    </button>
                    <a href="menu.php" class="btn btn-white">
                        <i class="fa fa-arrow-left"></i> Continue shopping
                    </a>
                </div>
            </div>
        </div>

        <div class="footer text-center">
            <p class="m-t"><small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
        </div>
    </div>
</div>

<?php require_once "includes/no_login/footer.php"; ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function(){

        // ==================== UPDATE CART QUANTITY ====================
        $(".quantity-input").on("input change", function(){
            let input = $(this);
            let cart_id = input.data("cart-id");
            let new_qty = parseInt(input.val());
            if(isNaN(new_qty) || new_qty < 1){
                new_qty = 1;
                input.val(new_qty);
            }

            let amount = parseFloat(input.data("amount"));

            $.post('ajax.php', { action: 'update_cart', cart_id: cart_id, quantity: new_qty }, function(response){
                if(response.success){
                    // Përditëso subtotal për rreshtin
                    input.closest("tr").find(".product-subtotal").text('$'+(amount*new_qty).toFixed(2));

                    // Përditëso totalin
                    let total = 0;
                    $(".product-subtotal").each(function(){
                        total += parseFloat($(this).text().replace('$',''));
                    });
                    $("#cart-total").text('$'+total.toFixed(2));
                } else {
                    alert("Error updating cart.");
                }
            }, 'json');
        });

        // ==================== REMOVE ITEM ====================
        $(".remove-item-form").on("submit", function(e){
            e.preventDefault();
            let form = $(this);
            let cart_id = form.find("input[name='cart_id']").val();

            $.post('ajax.php', { action: 'remove_cart_item', cart_id: cart_id }, function(response){
                if(response.success){
                    // Heq tabelën e produktit
                    form.closest("table").parent().remove();

                    // Përditëso totalin
                    let total = 0;
                    $(".product-subtotal").each(function(){
                        total += parseFloat($(this).text().replace('$',''));
                    });
                    $("#cart-total").text('$'+total.toFixed(2));

                    // Nëse nuk ka më produkte
                    if($(".shoping-cart-table").length === 0){
                        $(".ibox-content").html('<p style="text-align:center;">Your cart is empty.</p>');
                    }
                } else {
                    alert("Error removing item.");
                }
            }, 'json');
        });

    });
</script>

