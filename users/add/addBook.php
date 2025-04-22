<?php
session_start();
include '../../bin/db.php';
include '../../bin/classes.php'; 

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_book'])) {
    $bookTitle = $_POST['book_title'];
    $authorName = $_POST['author_name'];
    $releaseDate = $_POST['release_date'];
    $bookImage = $_FILES['book_image'];


    if (empty($bookTitle) || empty($authorName) || empty($releaseDate)) {
        $_SESSION['error'] = "لطفاً تمام فیلدها را پر کنید";
        header("Location: ../add.php");
        exit;
    }


    $stmt = $conn->prepare("INSERT INTO books (book_title, author_name, release_date, user_id) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("sssi", $bookTitle, $authorName, $releaseDate, $userId); 
        if ($stmt->execute()) {
            $bookId = $stmt->insert_id; // گرفتن ID کتاب برای استفاده در نام فایل
            $stmt->close();
        } else {
            $_SESSION['error'] = "خطا در ذخیره اطلاعات: " . $stmt->error;
            header("Location: ../add.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "خطا در آماده‌سازی درخواست: " . $conn->error;
        header("Location: ../add.php");
        exit;
    }

    // تنظیم پوشه آپلود
    $targetDir = __DIR__ . '/uploads/';
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true); 
    }


    $imageName = null; 
    if ($bookImage['error'] == 0) {
        $fileExtension = pathinfo($bookImage['name'], PATHINFO_EXTENSION);
        $imageName = $bookTitle . '_' . $bookId . '.' . $fileExtension; 
        $targetFilePath = $targetDir . $imageName;

        if (!move_uploaded_file($bookImage['tmp_name'], $targetFilePath)) {
            $_SESSION['error'] = "خطا در انتقال فایل: فایل آپلود نشد";
            header("Location: ../add.php");
            exit;
        }
    }

    // به‌روزرسانی اطلاعات در دیتابیس
    if ($imageName) {

        $stmt = $conn->prepare("UPDATE books SET book_image = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("si", $imageName, $bookId);
            if (!$stmt->execute()) {
                $_SESSION['error'] = "خطا در به‌روزرسانی تصویر: " . $stmt->error;
                header("Location: ../add.php");
                exit;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "خطا در آماده‌سازی درخواست برای به‌روزرسانی تصویر";
            header("Location: ../add.php");
            exit;
        }
    } else {
        $_SESSION['warning'] = "کتاب بدون تصویر ذخیره شد."; 
    }

    $_SESSION['success'] = "کتاب با موفقیت اضافه شد";
    header("Location: ../add.php");
    exit;
}
?>
