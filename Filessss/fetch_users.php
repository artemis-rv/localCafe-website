<?php
// fetch_users.php

header("Content-Type: application/json");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "localcafee";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die(json_encode(["error" => "Database connection failed."]));
}

// Prepare and execute query securely
$stmt = $conn->prepare("SELECT user_id AS id, name, email, phone, role, 'active' AS status FROM registered_users");
$stmt->execute();
$result = $stmt->get_result();

$users = [];
while ($row = $result->fetch_assoc()) {
  $users[] = $row;
}

$stmt->close();
$conn->close();

// Return data as JSON
echo json_encode($users);
?>
