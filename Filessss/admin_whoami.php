<?php
header('Content-Type: application/json');
session_start();
$admin = $_SESSION['admin'] ?? ($_COOKIE['admin'] ?? '');
echo json_encode([ 'admin' => $admin ]);
?>


