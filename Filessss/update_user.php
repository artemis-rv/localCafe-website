<?php
header("Content-Type: application/json");
ini_set('display_errors', 0);
error_reporting(0);

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "localcafee";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

// Get POST data safely
$id = intval($_POST['id'] ?? 0);
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$role = htmlspecialchars(trim($_POST['role'] ?? ''));

if ($id <= 0 || !$name || !$role) {
    echo json_encode(["success" => false, "message" => "Invalid input."]);
    exit;
}

// Prepare and execute update
$stmt = $conn->prepare("UPDATE registered_users SET name = ?, role = ? WHERE user_id = ?");
$stmt->bind_param("ssi", $name, $role, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User updated successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update user."]);
}

$stmt->close();
$conn->close();
?>
