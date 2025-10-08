<?php

if($_SERVER["REQUEST_METHOD"] === "POST"){
  $username = trim($_POST['username'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $password = trim($_POST['password'] ?? '');
  $confirm = trim($_POST['confirm_password'] ?? '');

  if(!$username || !$email || !$password || !$confirm){ die('All fields are required.'); }
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){ die('Invalid email'); }
  if($password !== $confirm){ die('Passwords do not match'); }

  $hashed = password_hash($password, PASSWORD_DEFAULT);

  $conn = mysqli_connect('localhost','root','','localcafee');
  if(mysqli_connect_error()) die('DB error: '.mysqli_connect_error());

  // Ensure schema columns exist: role, status
  mysqli_query($conn, "ALTER TABLE registered_users ADD COLUMN role VARCHAR(20) DEFAULT 'user'");
  mysqli_query($conn, "ALTER TABLE registered_users ADD COLUMN status VARCHAR(20) DEFAULT 'active'");

  $stmt = $conn->prepare("INSERT INTO registered_users (name,email,password,phone,role,status) VALUES (?,?,?,?, 'admin','active')");
  $stmt->bind_param('ssss', $username, $email, $hashed, $phone);
  $stmt->execute();
  $stmt->close();
  $conn->close();

  header('Location: admin_login.html');
  exit;
}

?>


