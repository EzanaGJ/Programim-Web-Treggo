<?php
require_once "connect.php";

function getUsers($conn){
    $res = mysqli_query($conn, "SELECT id, name, surname, email, role, email_verified, created_at FROM users");
    return mysqli_fetch_all($res, MYSQLI_ASSOC);
}

function getUser($conn, $id){
    $id = intval($id);
    $res = mysqli_query($conn, "SELECT id, name, surname, email, role FROM users WHERE id='$id'");
    return mysqli_fetch_assoc($res);
}

function updateUser($conn, $id, $name, $surname, $email, $role){
    $id = mysqli_real_escape_string($conn, $id);
    $name = mysqli_real_escape_string($conn, $name);
    $surname = mysqli_real_escape_string($conn, $surname);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);
    return mysqli_query($conn, "UPDATE users SET name='$name', surname='$surname', email='$email', role='$role' WHERE id='$id'");
}

function deleteUser($conn, $id){
    $id = mysqli_real_escape_string($conn, $id);
    return mysqli_query($conn, "DELETE FROM users WHERE id='$id'");
}

function addUser($conn, $name, $surname, $email, $role){
    $name = mysqli_real_escape_string($conn, $name);
    $surname = mysqli_real_escape_string($conn, $surname);
    $email = mysqli_real_escape_string($conn, $email);
    $role = mysqli_real_escape_string($conn, $role);
    $password = password_hash('password123', PASSWORD_DEFAULT);
    return mysqli_query($conn, "INSERT INTO users (name,surname,email,role,password) VALUES ('$name','$surname','$email','$role','$password')");
}
