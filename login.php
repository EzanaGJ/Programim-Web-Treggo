<?php
session_start();
require_once "includes/login/header.php";
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

            <button type="submit" id="loginButton" class="btn btn-primary btn-block">Login</button>

            <a href="forgotpassword.php" class="d-block mt-2"><small>Forgot password?</small></a>

            <p class="text-muted text-center mt-3"><small>Don't have an account?</small></p>
            <a class="btn btn-sm btn-white btn-block" href="register.php">Create an account</a>
        </form>

        <p id="countdown" class="text-danger mt-2"></p>

        <p class="m-t mt-4">
            <small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small>
        </p>
    </div>
</div>
<?php include "includes/no_login/footer.php"; ?>

<script>
    $(document).ready(function(){
        toastr.options = {
            closeButton: true,
            progressBar: true,
            positionClass: "toast-top-right",
            timeOut: 5000
        };

        function startCountdown(seconds){
            const btn = $("#loginButton");
            const countdown = $("#countdown");

            btn.prop("disabled", true);
            let remaining = seconds;

            const interval = setInterval(()=>{
                remaining--;
                let min = Math.floor(remaining/60);
                let sec = remaining % 60;
                countdown.text(`Blocked for: ${min}m ${sec}s`);
                if(remaining<=0){
                    clearInterval(interval);
                    btn.prop("disabled", false);
                    countdown.text('');
                }
            }, 1000);
        }

        $("#loginForm").submit(function(e){
            e.preventDefault();
            let email = $("#email").val().trim();
            let password = $("#password").val().trim();
            let remember = $("#rememberId").is(":checked");
            let error = false;

            if(!email.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)){
                toastr.error("Email invalid");
                error=true;
            }
            if(password===""){ toastr.error("Password can not be emty!"); error=true; }
            if(error) return;

            $.ajax({
                url: "ajax.php",
                type: "POST",
                dataType: "json",
                data: {action:"login", email, password, rememberId: remember},
                success:function(resp){
                    if(resp.status==200){
                        toastr.success(resp.message);
                        setTimeout(()=>{ window.location.href = resp.location; }, 500);
                    } else if(resp.status==403){
                        toastr.error(resp.message);
                        if(resp.remaining) startCountdown(resp.remaining);
                    } else {
                        toastr.error(resp.message);
                    }
                },
                error:function(xhr,status,error){
                    toastr.error("AJAX error: "+error);
                }
            });
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