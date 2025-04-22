<?php
include '../bin/classes.php';
include '../bin/db.php';  


$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_COOKIE['username']) && isset($_COOKIE['fullname']) && isset($_COOKIE['role'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['fullname'] = $_COOKIE['fullname'];
    $_SESSION['role'] = $_COOKIE['role'];
}


if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id'])) {

        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['fullname'] = $_COOKIE['fullname'];
        $_SESSION['role'] = $_COOKIE['role'];
    } else {

        $_SESSION['login_error'] = "لطفاً ابتدا وارد شوید.";
        header("Location: ../login/login.php");
        exit();
    }
}




?>

<html lang="en">
    <head>
        <meta charset="UTF-8" dir="rtl">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>داشبورد</title>
        
        <!-- my css -->

        <link rel="stylesheet" href="../assets/css/output.css">
        <link rel="stylesheet" href="../assets/css/input.css">
        <link rel="stylesheet" href="../assets/css/splide.min.css">
        <!-- js tw -->
        <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
        <!-- bootstrap -->
        <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        <script src="../assets/js/bootstrap.min.js" crossorigin="anonymous"></script>
        <script src="../assets/js/bootstrap.bundle.min.js" ></script>
        <!-- js -->
        <script src="../assets/js/splide.min.js"></script>
        <script src="../assets/js/splide-extension-auto-scroll.min.js"></script>

    



        <style>
            .menu-right-b{
            background: url("../assets/images/img-site/bpanl.svg");
            background-repeat: no-repeat;
            background-size: cover;
            }
            .bgg-body{
            background-color: #0d141d;
            }

        </style>
    </head>

       