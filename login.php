

<?php
session_start();
require_once "includes/login/header.php";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Treggo | Login</title>
    <!-- CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
    <style>
        body {
            background-color: #f3f3f4;
        }
        .login-box {
            max-width: 400px;
            margin: 80px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-box h3 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body class="gray-bg">
<div class="login-box animated fadeInDown text-center">
    <h3>Login to Treggo</h3>
    <form id="loginForm">
        <div class="form-group text-left">
            <input type="email" id="email" class="form-control" placeholder="Email" required>
            <span id="email_message" class="text-danger"></span>
        </div>
        <div class="form-group text-left">
            <input type="password" id="password" class="form-control" placeholder="Password" required>
            <span id="password_message" class="text-danger"></span>
        </div>
        <input type="checkbox" name="remember" value="1"> Remember Me
        <button type="submit" class="btn btn-primary btn-block">Login</button>
        <a href="forgotpassword.php" class="d-block mt-3"><small>Forgot password?</small></a>
        <a href="register.php" class="btn btn-sm btn-white btn-block mt-2">Create an account</a>
    </form>
    <p class="m-t text-muted"><small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
</div>
<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 5000
    };

    $("#loginForm").submit(function(e){
        e.preventDefault();

        var email = $("#email").val().trim();
        var password = $("#password").val().trim();
        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error = false;

        // Validation
        if(!email_regex.test(email)){
            $("#email").addClass("border-danger");
            $("#email_message").text("Invalid E-Mail format");
            error = true;
        } else {
            $("#email").removeClass("border-danger");
            $("#email_message").text("");
        }

        if(password === ""){
            $("#password").addClass("border-danger");
            $("#password_message").text("Password cannot be empty");
            error = true;
        } else {
            $("#password").removeClass("border-danger");
            $("#password_message").text("");
        }

        if(error) return;

        // AJAX login
        $.ajax({
            url: "ajax.php",
            type: "POST",
            dataType: "json",
            data: {
                action: "login",
                email: email,
                password: password
            },
            success: function(response){
                if(response.status == 200){
                    toastr.success(response.message);
                    setTimeout(function(){
                        window.location.href = response.location;
                    }, 500);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function(xhr, status, error){
                toastr.error("AJAX error: " + error);
            }
        });
    });
</script>
</body>
</html>