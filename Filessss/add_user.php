<?php
header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "localcafee";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Database connection failed."]));
}

// Get POST data and sanitize
$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$role = htmlspecialchars(trim($_POST['role'] ?? 'user'));

if (!$name || !$email || !$phone) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

// Prepare and execute INSERT to prevent SQL injection
$stmt = $conn->prepare("INSERT INTO registered_users (name, email, phone, role, status) VALUES (?, ?, ?, ?, 'active')");
$stmt->bind_param("ssss", $name, $email, $phone, $role);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "User added successfully."]);
} else {
    // Check for duplicate email
    if ($conn->errno === 1062) {
        echo json_encode(["success" => false, "message" => "Email already exists."]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add user."]);
    }
}

$stmt->close();
$conn->close();
?>
