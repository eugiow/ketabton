<?php
require_once '../bin/classes.php';
require_once '../bin/db.php'; 

$auth = new Auth($conn);


if (isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $userId = intval($_GET['user_id']); 


switch ($action) {
    case 'edit':

        //  اطلاعات کاربرز  
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            ?>

            <!DOCTYPE html>
            <html lang="fa">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>فرم ویرایش کاربر</title>
            </head>
            <body style="font-family: Arial, sans-serif; background-color: #f7f7f7; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0;">
                <div class="form-container" style="background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); width: 100%; max-width: 400px;">
                    <h2 style="text-align: center; color: #333; margin-bottom: 20px;">ویرایش اطلاعات کاربر</h2>
                    <form method="POST" action="update_user.php?user_id=<?= $userId ?>">
                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="fullname" style="display: block; font-size: 14px; color: #333; margin-bottom: 5px;">نام کامل</label>
                            <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required style="width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="username" style="display: block; font-size: 14px; color: #333; margin-bottom: 5px;">نام کاربری</label>
                            <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" required style="width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
                        </div>

                        <div class="form-group" style="margin-bottom: 15px;">
                            <label for="email" style="display: block; font-size: 14px; color: #333; margin-bottom: 5px;">ایمیل</label>
                            <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required style="width: 100%; padding: 10px; font-size: 14px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;" />
                        </div>

                        <button type="submit" name="update" class="form-submit" style="width: 100%; padding: 12px; background-color: #007bff; color: white; font-size: 16px; border: none; border-radius: 4px; cursor: pointer;">ویرایش اطلاعات</button>
                    </form>
                </div>
            </body>
            </html>

            <?php
        } else {
            echo "کاربر یافت نشد.";
        }
        break;

    case 'make_admin':
        echo $auth->makeAdmin($userId);
        break;

    case 'remove_admin':
        echo $auth->removeAdmin($userId);
        break;

    case 'delete':
        echo $auth->deleteUser($userId);
        break;

    case 'show_list':
        
        include '../bin/db.php';
        $auth = new Auth($conn); 


        if (!isset($_GET['user_id'])) {
            echo "پارامتر user_id ارسال نشده است.";
            exit;
        }
        $userId = $_GET['user_id'];


        if (isset($_GET['action']) && $_GET['action'] == 'delete_book' && isset($_GET['book_id'])) {
            $book_id = $_GET['book_id'];

            // بررسی معتبر بودن شناسه کتاب
            if (!is_numeric($book_id)) {
                echo 'شناسه کتاب معتبر نیست.';
                exit;
            }


            if ($auth->deleteBook($book_id)) {
                echo '<p>کتاب با موفقیت حذف شد.</p>';
                header('Location: list_books.php?user_id=' . $userId);
                exit;
            } else {
                echo '<p>خطا در حذف کتاب.</p>';
            }
        }


        if (isset($_GET['action']) && $_GET['action'] == 'delete_text' && isset($_GET['text_id'])) {
            $text_id = $_GET['text_id'];

            // بررسی معتبر بودن شناسه متن
            if (!is_numeric($text_id)) {
                echo 'شناسه متن معتبر نیست.';
                exit;
            }


            if ($auth->deleteText($text_id)) {
                echo '<p>متن با موفقیت حذف شد.</p>';
                header('Location: list_texts.php?user_id=' . $userId);
                exit;
            } else {
                echo '<p>خطا در حذف متن.</p>';
            }

        }

        // نمایش لیست کتاب‌ها و متن‌ها
        $sql_books = "SELECT id, book_title AS title, author_name AS author, release_date AS date, 'book' AS type FROM books WHERE user_id = ?";
        $sql_texts = "SELECT id, text_title AS title, story AS author, created_at AS date, 'stories' AS type FROM stories WHERE user_id = ?";

        // اجرای query برای کتاب‌ها و متن‌ها
        $stmt_books = $conn->prepare($sql_books);
        $stmt_books->bind_param("i", $userId);
        $stmt_books->execute();
        $result_books = $stmt_books->get_result();

        $stmt_texts = $conn->prepare($sql_texts);
        $stmt_texts->bind_param("i", $userId);
        $stmt_texts->execute();
        $result_texts = $stmt_texts->get_result();

        // ادغام نتایج دو query
        $combined_results = [];

        while ($book = $result_books->fetch_assoc()) {
            $combined_results[] = $book;
        }

        while ($text = $result_texts->fetch_assoc()) {
            $combined_results[] = $text;
        }


        if (count($combined_results) > 0) {
            echo '<div style="background-color: #296EDB; padding: 20px; border-radius: 8px; width: 100%; margin: 0 auto; max-width: 1000px;">';
            echo '<h2 style="text-align: center; font-size: 24px; font-weight: 600; color: #fff; margin-bottom: 20px;">لیست کتاب‌ها و متن‌ها</h2>';
            echo '<table style="width: 100%; border-collapse: collapse; margin-top: 10px;">';
            echo '<thead style="background-color: #f1f1f1;">';
            echo '<tr style="width: 100%;">';
            echo '<th style="padding: 12px; text-align: left;">عنوان</th>';
            echo '<th style="padding: 12px; text-align: left;">نویسنده</th>';
            echo '<th style="padding: 12px; text-align: left;">تاریخ</th>';
            echo '<th style="padding: 12px; text-align: left;">نوع</th>';

            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            foreach ($combined_results as $item) {
                echo '<tr style="background-color: #fff; border-top: 1px solid #ddd;">';
                echo '<td style="padding: 12px; text-align: left;">' . htmlspecialchars($item['title']) . '</td>';
                echo '<td style="padding: 12px; text-align: left;">' . htmlspecialchars($item['author']) . '</td>';
                echo '<td style="padding: 12px; text-align: left;">' . htmlspecialchars($item['date']) . '</td>';
                echo '<td style="padding: 12px; text-align: left; font-weight: 600;">' . ($item['type'] == 'book' ? 'کتاب' : 'متن') . '</td>';
                echo '<td style="padding: 12px; text-align: left;">';
                echo '</td>';
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div style="background-color: #296EDB; padding: 20px; border-radius: 8px;">';
            echo '<h2 style="text-align: center; color: #fff;">کتاب و متنی آپلود نشده</h2>';
            echo '</div>';
        }
        break;
        

    default:
        echo "عملیات نامعتبر!";
        break;
}
}
else {
    echo "پارامترهای لازم ارسال نشده‌اند.";
}
?>
