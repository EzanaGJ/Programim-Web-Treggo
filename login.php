
<?php
require_once "includes/no_login/header.php";
session_start();
?>
<nav id="mainMenu" style="display: none;">
    <?php
    if(isset($_SESSION['id'])) {
        include "includes/login/menu.php";
    }
    ?>
</nav>
<div class="middle-box text-center loginscreen p-5 white-bg shadow-lg animated fadeInDown">
<body class="gray-bg">

    <div>

        <h1 class="logo-name">Treggo</h1>

        <h3>Welcome Back</h3>
        <p>Discover and buy products easily with Treggo </p>

        <form id="loginForm">

            <div class="form-group">
                <input type="email"
                       id="loginEmailId"
                       class="form-control"
                       placeholder="Email">
                <span id="login_email_messageId" class="text-danger"></span>
            </div>

            <div class="form-group">
                <input type="password"
                       id="loginPasswordId"
                       class="form-control"
                       placeholder="Password">
                <span id="login_password_messageId" class="text-danger"></span>
            </div>

            <div class="form-group text-left">
                <label>
                    <input type="checkbox" id="rememberId"> Remember me
                </label>
            </div>

            <button type="button"
                    class="btn btn-primary block full-width m-b"
                    onclick="login()">
                Login
            </button>

            <a href="forgotpassword.php">
                <small>Forgot password?</small>
            </a>

            <p class="text-muted text-center">
                <small>Don't have an account?</small>
            </p>

            <a class="btn btn-sm btn-white btn-block" href="register.php">
                Create an account
            </a>

        </form>

        <p class="m-t">
            <small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small>
        </p>

    </div>
</div>

<?php
include "includes/login/footer.php";
?>


<script>
    toastr.options = {
        "closeButton": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "timeOut": "5000"
    };

    // validimi FRONTEND
    function login() {

        var email = $("#loginEmailId").val();
        var password = $("#loginPasswordId").val();
        var remember = $("#rememberId").is(":checked") ? 1 : 0;

        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error = 0;

        // EMAIL validation
        if (!email_regex.test(email)) {
            $("#loginEmailId").addClass("border-danger");
            $("#login_email_messageId").text("Please enter a valid email");
            error++;
        } else {
            $("#loginEmailId").removeClass("border-danger");
            $("#login_email_messageId").text("");
        }

        // PASSWORD validation
        if (password.length < 6) {
            $("#loginPasswordId").addClass("border-danger");
            $("#login_password_messageId").text("Password must be at least 6 characters");
            error++;
        } else {
            $("#loginPasswordId").removeClass("border-danger");
            $("#login_password_messageId").text("");
        }

        // Nëse ka gabime, ndalohet submit
        if (error > 0) {
            return false;
        }

        // AJAX request
        var data = new FormData();
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);
        data.append("remember", remember);

        $.ajax({
            type: "POST",
            url: "ajax.php",
            data: data,
            dataType: "json",
            processData: false,
            contentType: false,
            success: function (response, textStatus, xhr) {
                // përdorim statusin e HTTP për të kontrolluar
                if (xhr.status === 200) {
                    toastr.success(response.message);
                    setTimeout(() => {
                        window.location.href = response.location;
                    }, 1500);
                } else {
                    toastr.error(response.message || "Something went wrong");
                }
            },
            error: function(xhr) {
                // për raste kur AJAX dështon (server error)
                var msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Server error";
                toastr.error(msg);
            }
            }
        });
    }

</script>



    <style>
        body, html {
            height: 100%;
            background-color: #f3f3f4;
        }
        .middle-box {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }
        .logo-name {
            font-size: 80px;
            font-weight: bold;
            color: rgba(26,179,148,0.3);
        }
        form {
            width: 400px;
            margin: auto;
        }
    </style>
