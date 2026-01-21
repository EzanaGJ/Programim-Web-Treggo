<?php
session_start();
global $conn;
require_once "connect.php";
require_once "includes/login/resetpassword.php";

$msg = "";

if(isset($_POST['reset_password'])) {
    $code = trim($_POST['reset_code']);
    $password = trim($_POST['password']);
    $confirm = trim($_POST['confirm_password']);

    if($password !== $confirm){
        $msg = "Passwords do not match!";
    } else {
        // Kontrollo në DB reset code dhe expiry
        $stmt = $conn->prepare("SELECT * FROM users WHERE reset_code = ? AND reset_expires > NOW()");
        $stmt->bind_param("s", $code);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows > 0){
            $row = $result->fetch_assoc();

            $hashed = password_hash($password, PASSWORD_BCRYPT);

            // Update password dhe fshij reset code
            $stmt = $conn->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_expires = NULL WHERE id = ?");
            $stmt->bind_param("si", $hashed, $row['id']);
            $stmt->execute();

            $msg = "Password has been reset successfully! You can now login.";
        } else {
            $msg = "Invalid or expired reset code!";
        }
    }
}
?>



<div class="forgot-box animated fadeInDown">
    <h3>Reset Password</h3>
    <p>Enter the code from your email and your new password.</p>

    <form method="POST">
        <div class="form-group">
            <input type="text" name="reset_code" class="form-control" placeholder="Reset Code" required>
        </div>
        <div class="form-group">
            <input type="password" name="password" class="form-control" placeholder="New Password" required>
        </div>
        <div class="form-group">
            <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
        </div>
        <button type="submit" name="reset_password" class="btn btn-primary btn-block">Reset Password</button>
    </form>

    <!-- PHP message -->
    <?php if($msg) echo "<p class='small-msg'>$msg</p>"; ?>

    <p class="mt-3">
        <a href="login.php" style="color: #007bff; text-decoration: underline;">Back to Login</a>
    </p>
</div>

<div class="copyright">
    <p class="m-t"><small>©️ 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="css/plugins/toastr/toastr.min.css">
<script src="js/plugins/toastr/toastr.min.js"></script>
<script src="js/inactivityLogout.js"></script><?php
require_once "includes/login/footer.php";
?>

