<?php
global $conn;
require_once "../connect.php";
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['role_id']) || $_SESSION['role_id'] != 1) {
    echo json_encode(["status"=>403,"message"=>"Unauthorized"]);
    exit;
}

$action = $_POST['action'] ?? '';

//fill data
if ($action === "fillModalData") {
    $id = (int)$_POST['id'];

    $q = "SELECT id,name,surname,email,role_id FROM users WHERE id=$id";
    $r = mysqli_query($conn,$q);

    if ($row = mysqli_fetch_assoc($r)) {
        echo json_encode(["status"=>200,"data"=>$row]);
    } else {
        echo json_encode(["status"=>404,"message"=>"User not found"]);
    }
    exit;
}

//update user
if ($action === "update_user_data") {
    $id = (int)$_POST['id'];
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $surname = mysqli_real_escape_string($conn,$_POST['surname']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $role_id = (int)$_POST['role_id'];

    $q = "UPDATE users 
          SET name='$name', surname='$surname', email='$email', role_id=$role_id
          WHERE id=$id";

    if (mysqli_query($conn,$q)) {
        echo json_encode(["status"=>200,"message"=>"User updated successfully"]);
    } else {
        echo json_encode(["status"=>500,"message"=>mysqli_error($conn)]);
    }
    exit;
}

//delete user
if ($action === "delete_user") {
    $id = (int)$_POST['id'];

    mysqli_query($conn,"DELETE FROM users WHERE id=$id");
    echo json_encode(["status"=>200,"message"=>"User deleted"]);
    exit;
}

//add user
//add user
if ($action === "add_user") {
    $name = mysqli_real_escape_string($conn,$_POST['name']);
    $surname = mysqli_real_escape_string($conn,$_POST['surname']);
    $email = mysqli_real_escape_string($conn,$_POST['email']);
    $role_id = (int)$_POST['role_id'];

    // Check if email already exists
    $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");

    if (mysqli_num_rows($check) > 0) {
        echo json_encode([
            "status" => 409,
            "message" => "This email is already registered. Please use a different email."
        ]);
        exit;
    }

    // Hash the default password
    $default_password = '12345678';
    $hashed_password = password_hash($default_password, PASSWORD_DEFAULT);

    $q = "INSERT INTO users (name, surname, email, role_id, password)
          VALUES ('$name','$surname','$email',$role_id,'$hashed_password')";

    if (mysqli_query($conn,$q)) {
        echo json_encode([
            "status"=>200,
            "message"=>"User added successfully"
        ]);
    } else {
        echo json_encode([
            "status"=>500,
            "message"=>"Something went wrong. Please try again."
        ]);
    }
    exit;
}
