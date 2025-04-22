<?php
session_start();
include '../bin/db.php';  
include '../bin/classes.php'; 

// // بررسی لاگین بودن
// if (!isset($_SESSION['user_id'])) {
//     $_SESSION['login_error'] = "لطفاً ابتدا وارد شوید.";
//     header("Location: ../login/login.php");
//     exit();
// }
// بررسی اینکه آیا سشن‌ها و کوکی‌ها موجود هستند
if (isset($_COOKIE['username']) && isset($_COOKIE['fullname']) && isset($_COOKIE['role'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['fullname'] = $_COOKIE['fullname'];
    $_SESSION['role'] = $_COOKIE['role'];
}
echo 'Username Cookie: ' . $_COOKIE['username'] . '<br>';
echo 'Fullname Cookie: ' . $_COOKIE['fullname'] . '<br>';
echo 'Role Cookie: ' . $_COOKIE['role'] . '<br>';



mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); 
try {

    $conn = new mysqli($servername, $username, $password, $dbname);
    

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // دریافت اطلاعات کاربر
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();


    if (!$user) {
        throw new Exception("User not found.");
    }


    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];  
    $password = $_POST['password'];
    $repeat_password = $_POST['repeat_password'];


    if (preg_match('/[\x{0600}-\x{06FF}]/u', $username)) {
        $_SESSION['db_error'] = "نام کاربری نمی‌تواند شامل حروف فارسی باشد.";
        header("Location: info.php");
        exit();
    }

    // بررسی تکراری بودن نام کاربری
    $sql = "SELECT id FROM users WHERE username = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $username, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['db_error'] = "این نام کاربری قبلاً استفاده شده است.";
        header("Location: info.php");
        exit();
    }

    // بررسی تکراری بودن ایمیل
    $sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['db_error'] = "این ایمیل قبلاً استفاده شده است.";
        header("Location: info.php");
        exit();
    }

    // بررسی و بروزرسانی رمز عبور
    if ($password && $password === $repeat_password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        $password_hash = null;
    }

    // بروزرسانی اطلاعات کاربر
    if ($password_hash) {
        // اگر رمز عبور وجود دارد
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, gender = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $fullname, $username, $email, $gender, $password_hash, $user_id);
    } else {
        // اگر رمز عبور وجود ندارد
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ?, gender = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $fullname, $username, $email, $gender, $user_id);
    }


    $stmt->execute();

    $_SESSION['db_success'] = "اطلاعات شما با موفقیت بروزرسانی شد.";

    header("Location: info.php");
    exit();
} catch (Exception $e) {
    // مدیریت خطاها
    $_SESSION['db_error'] = "خطا در اتصال به پایگاه داده: " . $e->getMessage();
    header("Location: info.php");
    exit();
}
?>
