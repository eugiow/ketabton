<?php
session_start();
include '../../bin/db.php';  
include '../../bin/classes.php'; 

$userId = $_SESSION['user_id']; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_text'])) {
    $textTitle = $_POST['text_title'];
    $story = $_POST['story'];


    if (empty($textTitle) || empty($story)) {
        $_SESSION['error'] = "لطفاً تمام فیلدها را پر کنید.";
        header("Location: ../add.php");
        exit;
    }

    // افزودن متن به دیتابیس
    $sql = "INSERT INTO stories (text_title, story, user_id) VALUES (?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $textTitle, $story, $userId);  
        if ($stmt->execute()) {

            $_SESSION['success'] = "متن با موفقیت اضافه شد.";
            header("Location: ../add.php");
            exit;
        } else {

            $_SESSION['error'] = "خطا در ذخیره اطلاعات.";
            header("Location: ../add.php");
            exit;
        }
        $stmt->close();
    } else {

        $_SESSION['error'] = "خطا در آماده‌سازی درخواست.";
        header("Location: ../add.php");
        exit;
    }
}
?>
