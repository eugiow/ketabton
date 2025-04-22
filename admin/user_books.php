<?php
include '../bin/db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $user_id = intval($_POST['user_id']);

    // دریافت اطلاعات کاربر
    $user_sql = "SELECT fullname, username FROM users WHERE id = ?";
    $user_stmt = $conn->prepare($user_sql);
    $user_stmt->bind_param("i", $user_id);
    $user_stmt->execute();
    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user_data = $user_result->fetch_assoc();
        $fullname = $user_data['fullname'];
        $username = $user_data['username'];
    } else {
        echo "<p class='text-red-500'>کاربر یافت نشد.</p>";
        exit;
    }

    // دریافت کتاب‌های کاربر
    $books_sql = "SELECT id, book_title, author_name, release_date, book_image FROM books WHERE user_id = ?";
    $books_stmt = $conn->prepare($books_sql);
    $books_stmt->bind_param("i", $user_id);
    $books_stmt->execute();
    $books_result = $books_stmt->get_result();

    // دریافت متن‌های کاربر
    $stories_sql = "SELECT id, text_title, story, created_at FROM stories WHERE user_id = ?";
    $stories_stmt = $conn->prepare($stories_sql);
    $stories_stmt->bind_param("i", $user_id);
    $stories_stmt->execute();
    $stories_result = $stories_stmt->get_result();
} else {
    echo "<p class='text-red-500'>شناسه کاربر ارسال نشده است.</p>";
    exit;
}

// حذف کتاب
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_book_id']) && isset($_POST['user_id'])) {
    $delete_book_id = intval($_POST['delete_book_id']);
    $user_id = intval($_POST['user_id']); 

    // حذف کتاب از پایگاه داده
    $delete_sql = "DELETE FROM books WHERE id = ? AND user_id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("ii", $delete_book_id, $user_id);
    $delete_stmt->execute();


    header("Location: req.php");
    exit;
}

// حذف متن
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_story_id']) && isset($_POST['user_id'])) {
    $delete_story_id = intval($_POST['delete_story_id']);
    $user_id = intval($_POST['user_id']); 

    // حذف متن از پایگاه داده
    $delete_story_sql = "DELETE FROM stories WHERE id = ? AND user_id = ?";
    $delete_story_stmt = $conn->prepare($delete_story_sql);
    $delete_story_stmt->bind_param("ii", $delete_story_id, $user_id);
    $delete_story_stmt->execute();

    header("Location: req.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لیست کتاب‌ها و متن‌های کاربر</title>
    <link rel="stylesheet" href="../path/to/your/css/styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        .book-item, .story-item {
            background-color: #fafafa;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 5px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }
        .book-item h2, .story-item h3 {
            margin: 0 0 10px;
            color: #333;
        }
        .book-item p, .story-item p {
            margin: 5px 0;
            color: #666;
        }
        .book-item img {
            margin-top: 10px;
        }
        .btn-delete {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body dir="rtl">
    <section class="container">
        <h1>لیست کتاب‌ها و متن‌های کاربر:  (<?php echo htmlspecialchars($username); ?>)</h1>

        <h2>کتاب‌ها:</h2>
        <?php if ($books_result->num_rows > 0): ?>
            <ul>
                <?php while ($book = $books_result->fetch_assoc()): ?>
                    <li class="book-item">
                        <h2><?php echo htmlspecialchars($book['book_title']); ?></h2>
                        <p>نویسنده: <?php echo htmlspecialchars($book['author_name']); ?></p>
                        <p>تاریخ انتشار: <?php echo htmlspecialchars($book['release_date']); ?></p>

                        <!-- دکمه حذف کتاب -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_book_id" value="<?php echo htmlspecialchars($book['id']); ?>">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <button type="submit" class="btn-delete">حذف</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>هیچ کتابی برای این کاربر ثبت نشده است.</p>
        <?php endif; ?>

        <h2>متن‌ها:</h2>
        <?php if ($stories_result->num_rows > 0): ?>
            <ul>
                <?php while ($story = $stories_result->fetch_assoc()): ?>
                    <li class="story-item">
                        <h3><?php echo htmlspecialchars($story['text_title']); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($story['story'])); ?></p>
                        <p><small>تاریخ انتشار: <?php echo htmlspecialchars($story['created_at']); ?></small></p>

                        <!-- دکمه حذف متن -->
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="delete_story_id" value="<?php echo htmlspecialchars($story['id']); ?>">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user_id); ?>">
                            <button type="submit" class="btn-delete">حذف</button>
                        </form>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>هیچ متنی برای این کاربر ثبت نشده است.</p>
        <?php endif; ?>
    </section>
</body>
</html>
