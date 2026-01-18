<?php
session_start();
require_once "functions.php";
require_once "includes/no_login/header.php";
?>
<body class="gray-bg">

<div class="middle-box text-center loginscreen p-5 white-bg shadow-lg animated fadeInDown">
    <div>
        <h1 class="logo-name">Treggo</h1>
        <h3>Welcome Back</h3>
        <p>Discover and buy products easily with Treggo</p>

        <!-- Login Form -->
        <form method="POST" id="loginForm">
            <div class="form-group text-left">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                <span id="email_message" class="text-danger"></span>
            </div>

            <div class="form-group text-left">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <span id="password_message" class="text-danger"></span>
            </div>

            <div class="form-group text-left">
                <label><input type="checkbox" id="rememberId" name="rememberId"> Remember me</label>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Login</button>

            <a href="forgotpassword.php" class="d-block mt-2"><small>Forgot password?</small></a>

            <p class="text-muted text-center mt-3"><small>Don't have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="register.php">Create an account</a>
        </form>

        <p class="m-t mt-4">
            <small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small>
        </p>
    </div>
</div>

<?php include "includes/login/footer.php"; ?>

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

<style>
    body, html {
        height: 100%;
        background-color: #f3f3f4;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
    }

    .middle-box {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 100vh;
        text-align: center;
    }

    .white-bg {
        background: #fff;
        border-radius: 10px;
    }

    .logo-name {
        font-size: 80px;
        font-weight: bold;
        color: rgba(26,179,148,0.3);
        margin-bottom: 10px;
    }

    form {
        width: 100%;
        max-width: 400px;
        margin: 20px auto 0 auto;
    }

    input.form-control {
        border-radius: 5px;
        padding: 10px;
    }

    .btn-block {
        margin-top: 15px;
    }

    .text-left label {
        font-weight: normal;
    }

    .text-danger {
        font-size: 0.85em;
    }
</style>
