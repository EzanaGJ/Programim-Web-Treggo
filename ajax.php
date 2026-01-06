<?php
global $conn;
require_once "dbconnect.php";
require_once "functions.php";

if ($_POST["action"] == "register") {

    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $alpha_regex = "/^[a-zA-Z]{3,40}$/";
    $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    $verification_code = rand(10000, 99999);
    $email_token = password_hash($verification_code, PASSWORD_BCRYPT);
    $valid_date = date('Y-m-d H:i:s', strtotime(' +5 minutes '));


    if (!preg_match($alpha_regex, $name)) {
        $response = array("status" => 201, "message" => "Name should be at least 3 letters.");
        echo json_encode($response);
        exit;
    }

    if (!preg_match($alpha_regex, $surname)) {
        $response = array("status" => 201, "message" => "Surname should be at least 3 letters.");
        echo json_encode($response);
        exit;
    }
    if (!preg_match($email_regex, $email)) {
        $response = array("status" => 201, "message" => "Please enter a valid email (e.g., name@example.com).");
        echo json_encode($response);
        exit;
    }
    if (!preg_match($password_regex, $password)) {
        $response = array("status" => 201, "message" => "Use 8+ characters with letters, numbers, and symbols.");
        echo json_encode($response);
        exit;
    }
    if ($password != $confirm_password) {
        $response = array("status" => 202, "message" => "Password does not match");
        echo json_encode($response);
        exit;
    }
    $query_check = "SELECT id 
                    FROM users
                    WHERE email = '" . $email . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        $response = array("status" => 202, "message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($result_check) > 0) {
        $response = array("status" => 201, "message" => "A user with this E-Mail exists");
        echo json_encode($response);
        exit;

    }
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    $query_insert = "INSERT INTO users SET
                 name = '" . $name . "',
                 surname = '" . $surname . "',
                 email = '" . $email . "',
                 password = '" . $hashed_password . "',
                 email_token = '$email_token',
                 token_date = '$valid_date',
                 created_at = '" . date("Y-m-d H:i:s") . "' ";

    $result_insert = mysqli_query($conn, $query_insert);

    if (!$result_insert) {
        $response = array(
            "status" => 201,
            "message" => "There is an error on Database",
            "error" => mysqli_error($conn),
            "error_number" => mysqli_errno($conn)
        );
        echo json_encode($response);
        exit;
    }

    $user_id = mysqli_insert_id($conn);
    $data['code'] = $verification_code;
    $data['id'] = $user_id;
    $data['token'] = $email_token;
    $data['user_email'] = $email;

    $send_status = sendEmail($data);
    if ($send_status) {
        $response = array("status" => 200,
            "message" => "User registered successfully. Verification email sent!",
            "location" => "login.php");
    }

    echo json_encode($response);
    exit;


}