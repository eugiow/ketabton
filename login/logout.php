<?php
session_start();

if (isset($_SESSION['username']) || isset($_COOKIE['username'])) {
    session_unset();
    
    session_destroy();

    $cookies = ['username', 'fullname', 'role', 'user_id']; 
    foreach ($cookies as $cookieName) {
        setcookie($cookieName, "", time() - 3600, "/");
    }

    header("Location: ../index.php");
    exit();
} else {

    header("Location: login.php");
    exit();
}
?>
