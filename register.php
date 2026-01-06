<?php
require_once "includes/no_login/header.php";
?>
<div class="middle-box text-center loginscreen p-5 white-bg shadow-lg animated fadeInDown">
    <div>
        <div class="logo-wrapper">
<!-- I added logo - wrapper -->
            <h1 class="logo-name">Treggo</h1>   <!-- changed something in  logo-name -->
        </div>
        <div class="field-wrapper">
<!-- I added field wrapper-->
            <h3>Register to Treggo</h3>
            <p>Create account to start shopping.</p>
            <form class="m-t" id="registerForm">
                <div class="form-group">
                    <input id="nameId" name="name" type="text" class="form-control" placeholder="Name" required="">
                    <span id="name_messageId" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <input id="surnameId" name="surname" type="text" class="form-control" placeholder="Surname" required="">
                    <span id="surname_messageId" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <input id="emailId" name="email" type="email" class="form-control" placeholder="Email" required="">
                    <span id="email_messageId" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <input id="passwordId" name="password" type="password" class="form-control" placeholder="Password" required="">
                    <span id="password_messageId" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <input id="confirm_passwordId" name="confirm_password" type="password" class="form-control" placeholder="Confirm Password" required="">
                    <span id="confirm_password_messageId" class="text-danger"></span>
                </div>
                <div class="form-group">
                    <div class="checkbox i-checks">
                        <label>
                            <input id="termsId" name="terms" type="checkbox"><i></i> Agree the terms and policy
                        </label>
                    </div>
                    <span id="terms_messageId" class="text-danger"></span>
                </div>
                <button type="button" class="btn btn-primary block full-width m-b" onclick="register()">Register</button>

                <p class="text-muted text-center"><small>Already have an account?</small></p>

                <a class="btn btn-sm btn-white btn-block" href="login.php">Login</a>
            </form>
            <p class="m-t"><small>Treggo | Designed by EMM'S</small></p>
        </div>
    </div>
</div>
<?php
require_once "includes/no_login/footer.php";
?>

<script>
    toastr.options = {
        "closeButton": true,
        "debug": true,
        "progressBar": true,
        "preventDuplicates": true,
        "positionClass": "toast-top-right",
        "onclick": null,
        "showDuration": "400",
        "hideDuration": "1000",
        "timeOut": "7000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };

    function register() {
        var name = $("#nameId").val();
        var surname = $("#surnameId").val();
        var email = $("#emailId").val();
        var password = $("#passwordId").val();
        var confirm_password = $("#confirm_passwordId").val();

        var alpha_regex = /^[a-zA-Z]{3,40}$/;
        var email_regex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        var password_regex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/;

        var error = 0;

        if (!alpha_regex.test(name)) {
            $("#nameId").addClass("border-danger");
            $("#name_messageId").text("Name should be at least 3 letters.");
            error++;
        } else {
            $("#nameId").removeClass("border-danger");
            $("#name_messageId").text("");
        }

        if (!alpha_regex.test(surname)) {
            $("#surnameId").addClass("border-danger");
            $("#surname_messageId").text("Surname should be at least 3 letters.");
            error++;
        } else {
            $("#surnameId").removeClass("border-danger");
            $("#surname_messageId").text("");
        }

        if (!email_regex.test(email)) {
            $("#emailId").addClass("border-danger");
            $("#email_messageId").text("Please enter a valid email (e.g., name@example.com).");
            error++;
        } else {
            $("#emailId").removeClass("border-danger");
            $("#email_messageId").text("");
        }

        if (!password_regex.test(password)) {
            $("#passwordId").addClass("border-danger");
            $("#password_messageId").text("Use 8+ characters with letters, numbers, and symbols.");
            error++;
        } else {
            $("#passwordId").removeClass("border-danger");
            $("#password_messageId").text("");
        }

        if (confirm_password !== password) {
            $("#confirm_passwordId").addClass("border-danger");
            $("#confirm_password_messageId").text("Password does not match!");
            error++;
        } else {
            $("#confirm_passwordId").removeClass("border-danger");
            $("#confirm_password_messageId").text("");
        }

        if (!$("#termsId").is(":checked")) {
            $("#terms_messageId").text("You must agree to the terms and policy.");
            error++;
        } else {
            $("#terms_messageId").text("");
        }

        if (error === 0) {
            var data = new FormData();
            data.append("action", "register");
            data.append("name", name);
            data.append("surname", surname);
            data.append("email", email);
            data.append("password", password);
            data.append("confirm_password", confirm_password);

            $.ajax({
                type: "POST",
                url: "ajax.php",
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    if (response.status === 200) {
                        toastr["success"](response.message, "Success");
                        if(response.location) {
                            setTimeout(function() {
                                window.location.href = response.location;
                            }, 2000);
                        }
                    } else if(response.status === 201) {
                        toastr["warning"](response.message, "Warning");
                    } else if(response.status === 202) {
                        toastr["error"](response.message, "Error");
                    }
                }
            });

        }
    }
</script>


