<?php
session_start();
require_once "includes/no_login/header.php"; // Header i faqes
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treggo | Forgot Password</title>

    <!-- Lidhjet me CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

    <style>
        body { background-color: #f3f3f4; }
        .forgot-box {
            background-color: #ffffff;
            border: 2px solid darkseagreen;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            max-width: 400px;
            margin: 80px auto 20px auto;
        }
        .forgot-box h3 { margin-bottom: 15px; }
        .copyright { text-align: center; margin-top: 20px; color: #888888; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>

<body class="gray-bg">

<div class="forgot-box text-center animated fadeInDown">
    <h3>Forgot password</h3>
    <p>Enter your email address and we'll send you instructions to reset your password.</p>

    <form class="m-t" id="forgotPasswordForm">
        <div class="form-group">
            <input type="email" id="forgotEmailId" class="form-control" placeholder="Email address" required>
            <span id="forgot_email_messageId" class="text-danger"></span>
        </div>

        <button type="button" class="btn btn-primary block full-width m-b" onclick="sendResetLink()">Send new password</button>
    </form>

    <a href="login.php"><small>Back to login</small></a>
</div>

<div class="copyright">
    <p class="m-t"><small>©️ 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    function sendResetLink() {
        const email = $("#forgotEmailId").val().trim();
        const email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

        if (!email_regex.test(email)) {
            $("#forgotEmailId").addClass("border-danger");
            $("#forgot_email_messageId").text("Please enter a valid email");
            return;
        } else {
            $("#forgotEmailId").removeClass("border-danger");
            $("#forgot_email_messageId").text("");
        }

        const data = new FormData();
        data.append("action", "forgot_password");
        data.append("email", email);

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.status === 200) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error("There was an error processing your request.");
            }
        });
    }
</script>

</body>
</html>

<?php
include "includes/no_login/footer.php";
?>
