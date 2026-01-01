<?php
global $conn;
require_once "dbconnect.php";
if ($_POST["action"] == "register") {

    $name = mysqli_real_escape_string($conn, $_POST["name"]);
    $surname = mysqli_real_escape_string($conn, $_POST["surname"]);
    $email = mysqli_real_escape_string($conn, $_POST["email"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);
    $confirm_password = mysqli_real_escape_string($conn, $_POST["confirm_password"]);
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $alpha_regex = "/^[a-zA-Z]{3,40}$/";
    $password_regex = "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[\W_]).{8,}$/";
    $email_code = rand(10000, 99999);
    $email_token = password_hash($email_code, PASSWORD_BCRYPT);
    $valid_date = date('Y-m-d H:i:s', strtotime(' +5 minutes '));

    if (!preg_match($alpha_regex, $name)) {

        http_response_code(400);
        $response = array("message" => "Name should be at least 3 letters.");
        echo json_encode($response);
        exit;
    }
    if (!preg_match($alpha_regex, $surname)) {
        http_response_code(400);
        $response = array("message" => "Surname should be at least 3 letters.");
        echo json_encode($response);
        exit;
    }
    if (!preg_match($email_regex, $email)) {
        http_response_code(400);
        $response = array("message" => "Please enter a valid email (e.g., name@example.com).");
        echo json_encode($response);
        exit;
    }
    if (!preg_match($password_regex, $password)) {
        http_response_code(400);
        $response = array("message" => "Use 8+ characters with letters, numbers, and symbols.");
        echo json_encode($response);
        exit;
    }
    if ($password != $confirm_password) {
        http_response_code(201);
        $response = array("message" => "Password does not match");
        echo json_encode($response);
        exit;
    }
    $query_check = "SELECT id 
                    FROM users
                    WHERE email = '" . $email . "';";

    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        http_response_code(400);
        $response = array("message" => "There is an error on Database", "error" => mysqli_error($conn), "error_number" => mysqli_errno($conn));
        echo json_encode($response);
        exit;
    }

    if (mysqli_num_rows($result_check) > 0) {
        http_response_code(400);
        $response = array("message" => "A user with this E-Mail exists");
        echo json_encode($response);
        exit;

    }
}