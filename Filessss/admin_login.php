<?php
session_start();
header("Content-Type: text/html");

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "localcafee";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data safely
$user = trim($_POST['username'] ?? '');
$pass = trim($_POST['password'] ?? '');

if (!$user || !$pass) {
    die("Username and password are required.");
}

// Check if admin exists
$stmt = $conn->prepare("SELECT user_id, name, role, password FROM registered_users WHERE name=? AND role='admin'");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    // Assume password is stored hashed with password_hash()
    if (password_verify($pass, $row['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_name'] = $row['name'];
        $_SESSION['admin_id'] = $row['user_id'];
        header("Location: admin_dashboard.php"); // redirect to admin dashboard
        exit;
    }
}

$stmt->close();
$conn->close();
echo "Invalid username or password.";
?>
