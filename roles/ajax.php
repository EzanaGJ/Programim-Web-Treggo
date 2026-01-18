<?php
session_start();
global $conn;
require_once "../connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION['role_id']) || intval($_SESSION['role_id']) !== 1) {
    echo json_encode([
        "status" => 403,
        "message" => "Unauthorized access"
    ]);
    exit;
}

$action = $_POST['action'] ?? '';

/* ADD ROLE */
if ($action === "add_role") {

    $role_name = trim($_POST['role_name'] ?? '');

    if (strlen($role_name) < 3) {
        echo json_encode([
            "status" => 400,
            "message" => "Role name must be at least 3 characters"
        ]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO roles (role_name) VALUES (?)");
    $stmt->bind_param("s", $role_name);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 200,
            "message" => "Role added successfully",
            "data" => ["role_id" => $stmt->insert_id]
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "Failed to add role"
        ]);
    }

    $stmt->close();
    exit;
}

/* UPDATE ROLE */
if ($action === "update_role") {

    $role_id = intval($_POST['role_id'] ?? 0);
    $role_name = trim($_POST['role_name'] ?? '');

    if ($role_id <= 0 || strlen($role_name) < 3) {
        echo json_encode([
            "status" => 400,
            "message" => "Invalid role data"
        ]);
        exit;
    }

    $stmt = $conn->prepare("UPDATE roles SET role_name = ? WHERE role_id = ?");
    $stmt->bind_param("si", $role_name, $role_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 200,
            "message" => "Role updated successfully"
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "Failed to update role"
        ]);
    }

    $stmt->close();
    exit;
}

/* DELETE ROLE */
if ($action === "delete_role") {

    $role_id = intval($_POST['role_id'] ?? 0);

    if ($role_id <= 0) {
        echo json_encode([
            "status" => 400,
            "message" => "Invalid role ID"
        ]);
        exit;
    }

    /* Optional: prevent deleting Admin role */
    if ($role_id === 1) {
        echo json_encode([
            "status" => 403,
            "message" => "Admin role cannot be deleted"
        ]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM roles WHERE role_id = ?");
    $stmt->bind_param("i", $role_id);

    if ($stmt->execute()) {
        echo json_encode([
            "status" => 200,
            "message" => "Role deleted successfully"
        ]);
    } else {
        echo json_encode([
            "status" => 500,
            "message" => "Failed to delete role"
        ]);
    }

    $stmt->close();
    exit;
}

/* INVALID ACTION */
echo json_encode([
    "status" => 400,
    "message" => "Invalid action"
]);
exit;
