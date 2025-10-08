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

$id = intval($_POST['id'] ?? 0);
if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid ID."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM registered_users WHERE user_id = ?");
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User deleted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to delete user."]);
}

$stmt->close();
$conn->close();
?>
