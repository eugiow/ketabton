<?php
include '../../bin/db.php';


if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    // کوئری برای حذف کتاب
    $deleteSql = "DELETE FROM books WHERE id = ?";
    $stmt = $conn->prepare($deleteSql);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();

    $_SESSION['message'] = "کتاب با موفقیت حذف شد.";
    header("Location: ../list.php");
    exit;
} else {
    die("شناسه کتاب مشخص نشده است.");
}
