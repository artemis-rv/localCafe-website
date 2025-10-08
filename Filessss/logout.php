<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Properly destroy the cookie by setting it to expire in the past
// and using the same parameters as when it was created
setcookie("username", "", time() - 3600, "/");
setcookie("username", "", time() - 3600, "/", "", false, true); // Additional security parameters

// Also try to unset the cookie from the current request
if (isset($_COOKIE['username'])) {
    unset($_COOKIE['username']);
}

// Return logout status
echo "logout";
?>