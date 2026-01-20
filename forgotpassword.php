<?php
global $conn;
require 'connect.php';
require 'resetPswMail.php';
require_once "includes/login/forgotpassword.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = trim($_POST['email']);

    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        $token = bin2hex(random_bytes(16));
        $code = rand(100000, 999999);
        $expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

        // ruaj token + code + expiry
        $update = $conn->prepare("
            UPDATE users SET
                reset_token=?,
                reset_code=?,
                reset_expires=?
            WHERE id=?
        ");
        $update->bind_param("sssi", $token, $code, $expires, $user['id']);
        $update->execute();

        // dërgo email
        $data = [
                'type' => 'forgot_password',
                'user_email' => $email,
                'code' => $code,
                'token' => $token
        ];

        if(sendEmailF($data)){
            $msg = "Reset code sent to your email!";
        } else {
            $msg = "Failed to send email.";
        }

    } else {
        $msg = "Email not found!";
    }
}
?>

<div class="forgot-box animated fadeInDown">
    <h3>Forgot Password</h3>
    <p>Enter your email to generate a reset code.</p>

    <form method="POST" id="forgotPasswordForm">
        <div class="form-group">
            <input type="email" name="email" class="form-control" placeholder="Email address" required>
        </div>
        <button type="submit" class="btn btn-primary block full-width m-b">Send Reset Code</button>
    </form>

    <!-- PHP message displayed in smaller font -->
    <?php if(isset($msg)) echo "<p class='small-msg'>$msg</p>"; ?>

    <div id="newPasswordContainer">
        <label><strong>New Password</strong></label>
        <input type="text" id="new_password" class="form-control" readonly>
        <a href="login.php" class="btn btn-secondary block full-width mt-3">Back to Login</a>
    </div>
</div>

<div class="copyright">
    <p class="m-t"><small>©️ 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
</div>

<?php
require_once "includes/login/footer.php";
?>
