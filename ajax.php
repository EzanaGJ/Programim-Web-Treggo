<?php
global $conn;
session_start();
require_once "connect.php";
require_once "functions.php";

header('Content-Type: application/json');

/* Funksion per log aktivitet */
function logAction($conn,$userId,$action){
    $stmt = $conn->prepare("INSERT INTO auth_logs (user_id, action, created_at) VALUES (?, ?, NOW())");
    $stmt->bind_param("is",$userId,$action);
    $stmt->execute();
}

/* Tentativat e dështuara në 30 min */
function failedAttempts($conn,$userId){
    $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE user_id=? AND success=0 AND attempt_time > (NOW() - INTERVAL 30 MINUTE)");
    $stmt->bind_param("i",$userId);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res['attempts'] ?? 0;
}

/* Reset tentativat */
function resetFailedAttempts($conn,$userId){
    $stmt = $conn->prepare("DELETE FROM login_attempts WHERE user_id=?");
    $stmt->bind_param("i",$userId);
    $stmt->execute();
}

/* ACTION vetëm një herë */
$action = $_POST['action'] ?? '';

if ($action === '') {
    echo json_encode(["status" => "error", "message" => "No action specified"]);
    exit;
}

/* ==================== LOGIN ==================== */
if ($action === "login") {

    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $remember = isset($_POST['rememberId']);

    if(!$email || !$password){
        echo json_encode(["status"=>201,"message"=>"Email and Password are required."]);
        exit;
    }

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? LIMIT 1");
    $stmt->bind_param("s",$email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows==0){
        echo json_encode(["status"=>201,"message"=>"No user was found with this email"]);
        exit;
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];

    //  MOS LEJO LOGIN PA VERIFIKIM EMAIL-I (vetem kjo u shtua)
    if (isset($user['email_verified']) && (int)$user['email_verified'] === 0) {
        echo json_encode([
            "status" => 403,
            "message" => "Please verify your email before logging in."
        ]);
        exit;
    }

    if($user['login_block_until'] && strtotime($user['login_block_until'])>time()){
        $remaining = strtotime($user['login_block_until'])-time();
        echo json_encode(["status"=>403,"message"=>"Your account has been temporarily locked due to multiple failed login attempts. Please try again later.","remaining"=>$remaining]);
        exit;
    }

    if(!password_verify($password,$user['password'])){

        $stmt = $conn->prepare("INSERT INTO login_attempts (user_id, attempt_time, success) VALUES (?, NOW(), 0)");
        $stmt->bind_param("i",$userId);
        $stmt->execute();

        $attempts = failedAttempts($conn,$userId);

        if($attempts>=7){
            $blockUntil = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $stmt2 = $conn->prepare("UPDATE users SET login_block_until=? WHERE id=?");
            $stmt2->bind_param("si",$blockUntil,$userId);
            $stmt2->execute();

            logAction($conn,$userId,"account_locked");
            echo json_encode(["status"=>403,"message"=>"Login blocked for 30 minutes","remaining"=>30*60]);
        } else {
            logAction($conn,$userId,"login_failed");
            echo json_encode(["status"=>201,"message"=>"Password gabim"]);
        }
        exit;
    }

    resetFailedAttempts($conn,$userId);
    $stmt = $conn->prepare("UPDATE users SET login_block_until=NULL WHERE id=?");
    $stmt->bind_param("i",$userId);
    $stmt->execute();

    logAction($conn,$userId,"login_success");

    $_SESSION["id"] = $userId;
    $_SESSION["email"] = $user['email'];
    $_SESSION["role_id"] = $user['role_id'];
    $_SESSION["last_activity"] = time();
    $_SESSION['name'] = $user['name'];
    $_SESSION['surname'] = $user['surname'];

    if($remember){
        $token = bin2hex(random_bytes(32));
        $expire = date('Y-m-d H:i:s', time()+604800);
        setcookie("remember_me",$token,time()+604800,"/");
        $stmt2 = $conn->prepare("UPDATE users SET remember_token=?, remember_expire=? WHERE id=?");
        $stmt2->bind_param("ssi",$token,$expire,$userId);
        $stmt2->execute();
    }

    $location = ($user['role_id']==1) ? "users.php" : "products.php";
    echo json_encode(["status"=>200,"message"=>"Logged in successfully","location"=>$location]);
    exit;
}

/* ==================== REGISTER ==================== */
else if ($action === "register") {

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

    if (!preg_match($alpha_regex, $name)) { echo json_encode(["status"=>201,"message"=>"Name should be at least 3 letters."]); exit; }
    if (!preg_match($alpha_regex, $surname)) { echo json_encode(["status"=>201,"message"=>"Surname should be at least 3 letters."]); exit; }
    if (!preg_match($email_regex, $email)) { echo json_encode(["status"=>201,"message"=>"Please enter a valid email (e.g., name@example.com)."]); exit; }
    if (!preg_match($password_regex, $password)) { echo json_encode(["status"=>201,"message"=>"Use 8+ characters with letters, numbers, and symbols."]); exit; }
    if ($password != $confirm_password) { echo json_encode(["status"=>202,"message"=>"Password does not match"]); exit; }

    $query_check = "SELECT id FROM users WHERE email = '".$email."'";
    $result_check = mysqli_query($conn, $query_check);

    if (!$result_check) {
        echo json_encode(["status"=>202,"message"=>"There is an error on Database","error"=>mysqli_error($conn),"error_number"=>mysqli_errno($conn)]);
        exit;
    }

    if (mysqli_num_rows($result_check) > 0) {
        echo json_encode(["status"=>201,"message"=>"A user with this E-Mail exists"]);
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $query_insert = "INSERT INTO users SET
                 name = '".$name."',
                 surname = '".$surname."',
                 email = '".$email."',
                 password = '".$hashed_password."',
                 email_token = '$email_token',
                 token_date = '$valid_date',
                 created_at = '".date("Y-m-d H:i:s")."' ";

    $result_insert = mysqli_query($conn, $query_insert);

    if (!$result_insert) {
        echo json_encode(["status"=>201,"message"=>"There is an error on Database","error"=>mysqli_error($conn),"error_number"=>mysqli_errno($conn)]);
        exit;
    }

    $user_id = mysqli_insert_id($conn);

    $data['code'] = $verification_code;
    $data['id'] = $user_id;
    $data['token'] = $email_token;
    $data['user_email'] = $email;

    $send_status = sendEmail($data);

    if ($send_status) {
        echo json_encode(["status"=>200,"message"=>"User registered successfully. Verification email sent!","location"=>"login.php"]);
    } else {
        echo json_encode(["status"=>201,"message"=>"User registered, but email could not be sent."]);
    }
    exit;
}

/* ==================== UPDATE CART QUANTITY ==================== */
else if ($action === "update_cart") {

    if (!isset($_SESSION['id'], $_POST['cart_id'], $_POST['quantity'])) {
        echo json_encode(['success' => false]);
        exit;
    }

    $user_id = $_SESSION['id'];
    $cart_id = intval($_POST['cart_id']);
    $quantity = max(1, intval($_POST['quantity']));

    $sql = "UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $quantity, $cart_id, $user_id);

    echo json_encode(['success' => $stmt->execute()]);
    exit;
}

/* ==================== REMOVE ITEM FROM CART ==================== */
else if ($action === "remove_cart_item") {

    if (!isset($_SESSION['id'], $_POST['cart_id'])) {
        echo json_encode(['success' => false]);
        exit;
    }

    $user_id = $_SESSION['id'];
    $cart_id = intval($_POST['cart_id']);

    $sql = "DELETE FROM cart WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $cart_id, $user_id);

    echo json_encode(['success' => $stmt->execute()]);
    exit;
}

/* ==================== DEFAULT ==================== */
echo json_encode(["status" => "error", "message" => "Invalid action"]);
exit;
