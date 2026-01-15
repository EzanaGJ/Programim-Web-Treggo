<?php
session_start();
require_once "connect.php";
require_once "functions.php";
require_once "includes/no_login/header.php";

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT id, email, password, role_id FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){
        $user = mysqli_fetch_assoc($result);
        if(password_verify($password, $user['password'])){
            $_SESSION['id'] = $user['id'];
            $_SESSION['role_id'] = $user['role_id'];

            if($user['role_id'] == 1){
                header("Location: users.php");
            } else {
                header("Location: menu.php");
            }
            exit;
        } else {
            echo "Password gabim!";
        }
    } else {
        echo "Email nuk ekziston!";
    }
}
?>
<body class="gray-bg">

<div class="middle-box text-center loginscreen p-5 white-bg shadow-lg animated fadeInDown">
    <div>
        <h1 class="logo-name">Treggo</h1>
        <h3>Welcome Back</h3>
        <p>Discover and buy products easily with Treggo</p>

        <!-- Forma e thjeshtë me POST -->
        <form method="POST" id="loginForm">
            <div class="form-group">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email" required>
                <span id="email_message" class="text-danger"></span>
            </div>

            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
                <span id="password_message" class="text-danger"></span>
            </div>

            <div class="form-group text-left">
                <label>
                    <input type="checkbox" id="rememberId" name="rememberId"> Remember me
                </label>
            </div>

            <button type="submit" name="login" class="btn btn-primary btn-block">Login</button>

            <a href="forgotpassword.php"><small>Forgot password?</small></a>

            <p class="text-muted text-center"><small>Don't have an account?</small></p>

            <a class="btn btn-sm btn-white btn-block" href="register.php">Create an account</a>
        </form>

        <p class="m-t">
            <small>© 2025 Treggo | Designed by <strong>EMM'S</strong></small>
        </p>
    </div>
</div>









    <?php include "includes/login/footer.php";

?>

<script>
    function login(){
        var email = $("#email").val();
        var password = $("#password").val();
        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var error = 0;

        // Validation of the E-Mail
        if (!email_regex.test(email)){
            $("#email").addClass("border-danger");
            $("#email_message").text("E-Mail format is not allowed");
            error++;
        } else {
            $("#email").removeClass("border-danger")
            $("#email_message").text("");
        }

        // Validation of the Password
        if (isEmpty(password)){
            $("#password").addClass("border-danger");
            $("#password_message").text("Password can not be empty");
        } else{
            $("#password").removeClass("border-danger")
            $("#password_message").text("");
        }


        var data = new FormData();
        data.append("action", "login");
        data.append("email", email);
        data.append("password", password);


        // send data on backed
        if (error == 0) {
            $.ajax({
                type: "POST",
                url: "ajax.php",
                async: false,
                cache: false,
                processData: false,
                data: data,
                contentType: false,
                success: function (response, status, call) {
                    console.log(response);
                    response = JSON.parse(response);
                    if (call.status == 200) {
                        window.location.href = response.location
                    } else {
                        toastr["warning"](response.message, "Warning");
                    }
                },
            })
        }
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

