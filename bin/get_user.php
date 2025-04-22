<?php
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['username']) && isset($_COOKIE['fullname']) && isset($_COOKIE['role'])) {
        // جستجوی اطلاعات کاربر در دیتابیس با استفاده از کوکی‌ها
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $_COOKIE['username']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];
        } else {
            $_SESSION['login_error'] = "اطلاعات نامعتبر است. لطفاً وارد شوید.";
            header("Location: ../login/login.php");
            exit();
        }
    } else {
        // اگر سشن و کوکی موجود نباشند، به صفحه ورود هدایت شود
        $_SESSION['login_error'] = "لطفاً ابتدا وارد شوید.";
        header("Location: ../login/login.php");
        exit();
    }
}



$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    $_SESSION['login_error'] = "کاربر یافت نشد.";
    header("Location: ../login/login.php");
    exit();
}
?>
