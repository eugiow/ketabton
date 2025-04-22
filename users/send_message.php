<?php
include '../bin/db.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$userId = $_SESSION['user_id'];  
$message = $_POST['message'] ?? ''; 

if (!empty($message)) {

    $sql = "INSERT INTO user_messages (user_id, message, status) VALUES (?, ?, 'pending')";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("is", $userId, $message);

        if ($stmt->execute()) {
            $_SESSION['message_success'] = "پیام شما با موفقیت ارسال شد.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
        } else {
            $_SESSION['message_error'] = "خطا در ارسال پیام. لطفاً دوباره تلاش کنید.";
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }

        $stmt->close();
    } else {
        $_SESSION['message_error'] = "خطا در ارسال پیام. لطفاً دوباره تلاش کنید.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
} else {
    $_SESSION['message_error'] = "پیام نمی‌تواند خالی باشد.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
}

exit();
?>
