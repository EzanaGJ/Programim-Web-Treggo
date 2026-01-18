<?php
session_start();
require_once "includes/login/resetpassword.php";
?>


<div class="forgot-box animated fadeInDown">
    <h3>Forgot Password</h3>
    <p>Enter your email to generate a new password.</p>

    <form id="forgotPasswordForm" class="m-t">
        <div class="form-group">
            <input type="email" id="forgotEmailId" class="form-control" placeholder="Email address" required>
            <span id="forgot_email_messageId" class="text-danger"></span>
        </div>
        <button type="button" class="btn btn-primary block full-width m-b" onclick="sendResetLink()">Send New Password</button>
    </form>

    <div id="newPasswordContainer">
        <label><strong>New Password</strong></label>
        <input type="text" id="new_password" class="form-control" readonly>
        <button id="backToLogin" class="btn btn-btn btn-primary block full-width m-t">Back to Login</button>
    </div>
</div>

<div class="copyright">
    <p class="m-t"><small>©️ 2025 Treggo | Designed by <strong>EMM'S</strong></small></p>
</div>

<?php include "includes/no_login/footer.php"; ?>



<script>
    toastr.options = {
        closeButton: true,
        progressBar: true,
        positionClass: "toast-top-right",
        timeOut: 10000
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

        $.ajax({
            url: "ajax.php",
            type: "POST",
            data: { action: "forgot_password", email: email },
            dataType: "json",
            success: function(resp) {
                if (resp.status === 200) {
                    $("#new_password").val(resp.new_password);
                    $("#newPasswordContainer").fadeIn();
                    toastr.success(resp.message);
                } else {
                    toastr.error(resp.message);
                }
            },
            error: function() {
                toastr.error("There was an error processing your request.");
            }
        });
    }

    $("#backToLogin").click(function(){
        window.location.href = "login.php";
    });
</script>

