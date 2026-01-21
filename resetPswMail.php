<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendEmailF($data){
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'treggonoreply@gmail.com';
        $mail->Password = 'hlkbsutvsufizrrk';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('treggonoreply@gmail.com', 'Treggo No Reply');
        $mail->addAddress($data['user_email']);
        $mail->isHTML(true);

        if(isset($data['type']) && $data['type'] === 'forgot_password'){
            $resetUrl = "http://localhost/Rregullim2/reset_password.php?token=".$data["token"];
            $mail->Subject = "Reset Your Password";
            $mail->Body = "
                <h2><strong>Password Reset</strong> </h2>
                <p>Your reset code: <b>{$data['code']}</b></p>
                <p>Click the link below:</p>
                <a href='{$resetUrl}'>Reset Password</a>
                <br><small>This code expires in 5 minutes.</small>
            ";
        }
        $mail->send();
        return true;
    } catch(Exception $e){
        return false;
    }
}
?>
