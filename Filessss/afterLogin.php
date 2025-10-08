<?php 

// Check if cookie exists and is not empty
if(isset($_COOKIE["username"]) && !empty($_COOKIE["username"]))
{
    $username = $_COOKIE["username"];
    echo $username;
}
// If no valid cookie, don't echo anything (this will prevent the "Welcome, !" message)

?>