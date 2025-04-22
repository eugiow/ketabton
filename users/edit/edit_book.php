<?php
session_start();
include '../../bin/db.php';  

if (isset($_GET['id'])) {
    $bookId = $_GET['id'];

    $sql = "SELECT * FROM books WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    $book = $result->fetch_assoc();

    if (!$book) {
        die("کتاب یافت نشد.");
    }
} else {
    die("شناسه کتاب مشخص نشده است.");
}

// بررسی ارسال فرم
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookTitle = $_POST['book_title'];
    $authorName = $_POST['author_name'];
    $releaseDate = $_POST['release_date'];


    $uploadDir = '../../users/add/uploads/';
    $imagePath = $book['book_image']; // مقدار پیش‌فرض، تصویر قبلی


    if (isset($_FILES['book_image']) && $_FILES['book_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['book_image']['tmp_name'];
        $fileName = $_FILES['book_image']['name'];
        $fileSize = $_FILES['book_image']['size'];
        $fileType = $_FILES['book_image']['type'];

        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedExtensions)) {
            if ($fileSize < 2 * 1024 * 1024) { 
                $newFileName = uniqid() . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $imagePath = $newFileName;
                } else {
                    die("خطا در ذخیره‌سازی فایل.");
                }
            } else {
                die("حجم فایل بیش از حد مجاز است.");
            }
        } else {
            die("فرمت فایل مجاز نیست.");
        }
    }

    // کوئری برای بروزرسانی اطلاعات کتاب
    $updateSql = "UPDATE books SET book_title = ?, author_name = ?, release_date = ?, book_image = ? WHERE id = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssssi", $bookTitle, $authorName, $releaseDate, $imagePath, $bookId);
    $stmt->execute();

    $_SESSION['messagee'] = "کتاب با موفقیت ویرایش شد.";
    header("Location: ../list.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ویرایش داستان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.tailwindcss.com"></script>

</head>
<body class="bg-gray-100 flex p-2 justify-center min-h-screen">
<form 
    method="POST" 
    enctype="multipart/form-data" 
    class="max-w-lg mx-auto p-6 bg-white border border-gray-300 rounded-lg shadow-lg"
    dir="rtl"
>

    <div class="mb-4">
        <label for="book_title" class="block text-sm font-medium text-gray-700 mb-2">عنوان کتاب:</label>
        <input 
            type="text" 
            name="book_title" 
            id="book_title" 
            value="<?= htmlspecialchars($book['book_title']); ?>" 
            placeholder="عنوان کتاب" 
            required 
            class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
    </div>


    <div class="mb-4">
        <label for="author_name" class="block text-sm font-medium text-gray-700 mb-2">نویسنده:</label>
        <input 
            type="text" 
            name="author_name" 
            id="author_name" 
            value="<?= htmlspecialchars($book['author_name']); ?>" 
            placeholder="نویسنده" 
            required 
            class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
    </div>


    <div class="mb-4">
        <label for="release_date" class="block text-sm font-medium text-gray-700 mb-2">تاریخ انتشار:</label>
        <input 
            type="date" 
            name="release_date" 
            id="release_date" 
            value="<?= htmlspecialchars($book['release_date']); ?>" 
            required 
            class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
    </div>


    <div class="mb-4">
        <label for="book_image" class="block text-sm font-medium text-gray-700 mb-2">تصویر کتاب:</label>
        <input 
            type="file" 
            name="book_image" 
            id="book_image" 
            class="block w-full px-4 py-2 text-gray-700 bg-gray-50 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
        >
    </div>

 
    <button 
        type="submit" 
        class="w-full px-4 py-2 text-white bg-blue-600 hover:bg-blue-700 font-semibold text-sm rounded-lg shadow-md transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
    >
        ویرایش کتاب
    </button>
    <a href="../list.php" class="btn btn-outline-danger w-full mt-2">انصراف</a>
</form>
</body>
</html>
