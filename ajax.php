<?php
session_start();
require_once "connect.php";
require_once "functions.php";

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if(isset($_POST['action']) && $_POST['action'] == 'forgot_password'){
    global $conn;
    $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');

    if(empty($email)){
        echo json_encode(["status"=>201,"message"=>"Please enter your email"]);
        exit;
    }

    $query = "SELECT id FROM users WHERE email='$email'";
    $result = mysqli_query($conn, $query);

    if(!$result || mysqli_num_rows($result) != 1){
        echo json_encode(["status"=>201,"message"=>"No user found with that email"]);
        exit;
    }

    $user = mysqli_fetch_assoc($result);

    // Gjenero fjalëkalim të ri dhe ruaj si hash
    $new_password = substr(str_shuffle("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"),0,8);
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $update = "UPDATE users SET password='$hashed_password' WHERE id='".$user['id']."'";
    if(mysqli_query($conn,$update)){
        echo json_encode(["status"=>200,"message"=>"New password generated!","new_password"=>$new_password]);
    } else {
        echo json_encode(["status"=>201,"message"=>"Error updating password"]);
    }
    exit;
}

else if(isset($_POST['action']) && $_POST['action'] == 'login'){
    global $conn;

    $email = mysqli_real_escape_string($conn,$_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if(empty($email) || empty($password)){
        echo json_encode(["status"=>201,"message"=>"Email and Password are required"]);
        exit;
    }

    $query = "SELECT id,email,role_id,password FROM users WHERE email='$email' LIMIT 1";
    $result = mysqli_query($conn,$query);

    if(!$result || mysqli_num_rows($result)==0){
        echo json_encode(["status"=>201,"message"=>"No user found with that email"]);
        exit;
    }

    $user = mysqli_fetch_assoc($result);

    if(!password_verify($password,$user['password'])){
        echo json_encode(["status"=>201,"message"=>"Incorrect Password"]);
        exit;
    }

    // Session
    $_SESSION["id"] = $user['id'];
    $_SESSION["email"] = $user['email'];
    $_SESSION["role_id"] = $user['role_id'];

    // Redirect sipas role
    $location = ($user['role_id'] == 1) ? "users.php" : "products.php";

    echo json_encode(["status"=>200,"message"=>"Logged in successfully","location"=>$location]);
    exit;
}


else if ($_POST["action"] === "login") {

    // 1️⃣ Get data
    $email = trim($_POST["email"] ?? '');
    $password = trim($_POST["password"] ?? '');
    $email_regex = "/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";

    // 2️⃣ Validate input
    if (!preg_match($email_regex, $email)) {
        http_response_code(400);
        echo json_encode(["message" => "E-Mail format is not allowed"]);
        exit;
    }

    if (empty($password)) {
        http_response_code(400);
        echo json_encode(["message" => "Password cannot be empty"]);
        exit;
    }

    // 3️⃣ Check user in DB using prepared statement
    $stmt = $conn->prepare("SELECT id, email, role_id, password FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result_check = $stmt->get_result();

    if (!$result_check) {
        http_response_code(500);
        echo json_encode([
            "message" => "Database error",
            "error" => $conn->error
        ]);
        exit;
    }

    if ($result_check->num_rows === 0) {
        http_response_code(400);
        echo json_encode(["message" => "No user found with that E-Mail"]);
        exit;
    }

    $user = $result_check->fetch_assoc();
    $userId = $user["id"];

    // 4️⃣ Check failed login attempts
    $attempts = failedAttempts($conn, $userId);
    if ($attempts >= 7) {
        http_response_code(403);
        echo json_encode([
            "message" => "Login blocked for 30 minutes due to too many attempts"
        ]);
        exit;
    }

    // 5️⃣ Verify password
    if (!password_verify($password, $user["password"])) {
        logAction($conn, $userId, "login_failed", "Incorrect password");
        http_response_code(400);
        echo json_encode(["message" => "Incorrect password"]);
        exit;
    }

    // 6️⃣ Successful login
    resetFailedAttempts($conn, $userId);
    logAction($conn, $userId, "login_success");

    // 7️⃣ Set session
    $_SESSION["id"] = $user["id"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role_id"] = $user["role_id"];

    // 8️⃣ Determine redirect location
    $location = "menu.php"; // default for all roles (can adjust if you have multiple roles)

    http_response_code(200);
    echo json_encode([
        "message" => "User logged in successfully",
        "location" => $location
    ]);
    exit;
}


else if ($_POST["action"] == "register") {

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


// Merr veprimin nga JS
$action = $_POST['action'] ?? '';

if (!isset($action)) {
    echo json_encode([
        "status" => "error",
        "message" => "No action specified"
    ]);
    exit;
}

/* ==================== ADD TO CART ==================== */
if ($action === "add_to_cart") {

    if (!isset($_SESSION['id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Duhet të jesh i loguar"
        ]);
        exit;
    }

    if (!isset($_POST['product_id'])) {
        echo json_encode([
            "status" => "error",
            "message" => "Product ID missing"
        ]);
        exit;
    }

    $user_id = (int) $_SESSION['id'];
    $product_id = (int) $_POST['product_id'];

    // Kontrollo nëse produkti ekziston në cart
    $check = mysqli_prepare(
        $conn,
        "SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?"
    );
    mysqli_stmt_bind_param($check, "ii", $user_id, $product_id);
    mysqli_stmt_execute($check);
    $result = mysqli_stmt_get_result($check);

    if ($row = mysqli_fetch_assoc($result)) {
        // Rrit quantity
        $newQty = $row['quantity'] + 1;
        $upd = mysqli_prepare(
            $conn,
            "UPDATE cart SET quantity = ? WHERE id = ?"
        );
        mysqli_stmt_bind_param($upd, "ii", $newQty, $row['id']);
        mysqli_stmt_execute($upd);
    } else {
        // Shto produkt të ri
        $ins = mysqli_prepare(
            $conn,
            "INSERT INTO cart (user_id, product_id, quantity)
             VALUES (?, ?, 1)"
        );
        mysqli_stmt_bind_param($ins, "ii", $user_id, $product_id);
        mysqli_stmt_execute($ins);
    }

    echo json_encode([
        "status" => "success",
        "message" => "Product added to cart"
    ]);
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
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
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
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

/* ==================== DEFAULT ==================== */
echo json_encode([
    "status" => "error",
    "message" => "Invalid action"
]);
exit;
