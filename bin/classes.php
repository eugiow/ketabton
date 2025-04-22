<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


class Auth {
    private $conn;


    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }



 // متد برای ورود کاربر
public function login($username, $password) {

    $username = filter_var($username, FILTER_SANITIZE_STRING);
    $password = filter_var($password, FILTER_SANITIZE_STRING);


    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // اگر کاربر یافت شد
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // بررسی رمز عبور
        if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['role'] = $user['role'];


                setcookie("user_id", $user['id'], time() + (7 * 24 * 60 * 60), "/"); 
                setcookie("username", $user['username'], time() + (7 * 24 * 60 * 60), "/");
                setcookie("fullname", $user['fullname'], time() + (7 * 24 * 60 * 60), "/");
                setcookie("role", $user['role'], time() + (7 * 24 * 60 * 60), "/");

            if ($user['role'] == 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header("Location: ../users/index.php");
            }
            exit();
        } else {
            $_SESSION['login_error'] = "رمز عبور اشتباه است.";

        }
    } else {
        $_SESSION['login_error'] = "نام کاربری یا ایمیل اشتباه است.";

    }
}


    // متد برای ثبت‌نام کاربر
    public function signup($fullname, $username, $email, $password, $confirm_password, $gender) {

        if ($password !== $confirm_password) {
            $_SESSION['signup_error'] = "رمز عبور و تأیید رمز عبور یکسان نیستند.";
            $_SESSION['signup_active'] = true;
            header("Location: login.php");
            exit();
        }
    

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['signup_error'] = "ایمیل وارد شده معتبر نیست.";
            $_SESSION['signup_active'] = true;
            header("Location: login.php");
            exit();
        }
    

        if (!preg_match("/^[a-zA-Z0-9]{5,}$/", $username)) {
            $_SESSION['signup_error'] = "نام کاربری باید حداقل 5 کاراکتر و فقط شامل حروف و اعداد باشد.";
            $_SESSION['signup_active'] = true;
            header("Location: login.php");
            exit();
        }
    
        // بررسی وجود ایمیل یا نام کاربری در دیتابیس
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $result = $stmt->get_result();
    

        if ($result->num_rows > 0) {
            $_SESSION['signup_error'] = "کاربر با این ایمیل یا نام کاربری قبلاً ثبت‌نام کرده است.";
            $_SESSION['signup_active'] = true;
            header("Location: login.php");
            exit();
        }
    
        // رمز عبور هش شده
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // ثبت اطلاعات کاربر جدید در دیتابیس
        $sql = "INSERT INTO users (fullname, username, email, password, gender) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssss", $fullname, $username, $email, $hashed_password, $gender);
    
        // اگر ثبت‌نام موفق بود
        if ($stmt->execute()) {
            $_SESSION['signup_success'] = "ثبت‌نام با موفقیت انجام شد!";
            
            // ارسال ایمیل تایید
            $this->sendVerificationEmail($email);
    
            // ذخیره اطلاعات در کوکی‌ها
            setcookie("username", $username, time() + (7 * 24 * 60 * 60), "/");
            setcookie("fullname", $fullname, time() + (7 * 24 * 60 * 60), "/");
            setcookie("role", 'user', time() + (7 * 24 * 60 * 60), "/");
            header("Location: login.php");
            exit();
        } else {
            // خطا در ثبت‌نام
            $_SESSION['signup_error'] = "خطا در ثبت‌نام: " . $stmt->error;
            $_SESSION['signup_active'] = true;
            header("Location: login.php");
            exit();
        }
    }
    

    // متد برای ارسال ایمیل تایید
    private function sendVerificationEmail($email) {
        $subject = "تایید ایمیل شما";
        $message = "لطفاً برای تایید ایمیل خود بر روی لینک زیر کلیک کنید: \n";
        $message .= "https://www.yoursite.com/verify-email.php?email=" . urlencode($email);
        $headers = "From: no-reply@yoursite.com";

        mail($email, $subject, $message, $headers);
    }

    // آزادسازی منابع
    public function __destruct() {
        $this->conn->close();
    }



    public function updateUser($userId, $fullname, $username, $email) {
        $sql = "UPDATE users SET fullname = ?, username = ?, email = ? WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sssi", $fullname, $username, $email, $userId);
    
        if ($stmt->execute()) {
            return "اطلاعات کاربر با موفقیت به‌روزرسانی شد.";
        } else {
            return "خطا در به‌روزرسانی کاربر: " . $stmt->error;
        }
    }
    
    
    public function makeAdmin($userId) {
        $sql = "UPDATE users SET role = 'admin' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            return "کاربر با موفقیت ادمین شد.";
        } else {
            return "خطا در ارتقاء نقش کاربر: " . $stmt->error;
        }
    }
    
    
    public function removeAdmin($userId) {
        $sql = "UPDATE users SET role = 'user' WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $userId);
        
        if ($stmt->execute()) {
            return "کاربر از حالت ادمینی خارج شد.";
        } else {
            return "خطا در حذف ادمینی: " . $stmt->error;
        }
    }
    
    

    public function deleteUser($userId) {
        $this->conn->begin_transaction();
        
        try {
            $sqlBooks = "DELETE FROM books WHERE user_id = ?";
            $stmtBooks = $this->conn->prepare($sqlBooks);
            $stmtBooks->bind_param("i", $userId);
            if (!$stmtBooks->execute()) {
                throw new Exception("خطا در حذف کتاب‌ها: " . $stmtBooks->error);
            }
    
            $sqlStories = "DELETE FROM stories WHERE user_id = ?";
            $stmtStories = $this->conn->prepare($sqlStories);
            $stmtStories->bind_param("i", $userId);
            if (!$stmtStories->execute()) {
                throw new Exception("خطا در حذف داستان‌ها: " . $stmtStories->error);
            }
    
            $sqlUser = "DELETE FROM users WHERE id = ?";
            $stmtUser = $this->conn->prepare($sqlUser);
            $stmtUser->bind_param("i", $userId);
            if (!$stmtUser->execute()) {
                throw new Exception("خطا در حذف کاربر: " . $stmtUser->error);
            }
    

            $this->conn->commit();
            return "کاربر و داده‌های مربوطه با موفقیت حذف شدند.";
        } catch (Exception $e) {

            $this->conn->rollback();
            return "خطا: " . $e->getMessage();
        }
    }
    



    public function requestSubscription($userId)
    {
        // بررسی وجود کاربر
        $sqlCheckUser = "SELECT id FROM users WHERE id = ?";
        $stmt = $this->conn->prepare($sqlCheckUser);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            return ['error' => "کاربر یافت نشد."];
        }
    
        // بررسی اینکه آیا کاربر کتابی دارد
        $sqlCheckBooks = "SELECT id FROM books WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlCheckBooks);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    

        $hasBooks = ($result->num_rows > 0);
        
        // بررسی اینکه آیا کاربر متن دارد
        $sqlCheckStories = "SELECT id FROM stories WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlCheckStories);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        $hasStories = ($result->num_rows > 0);
    
        if (!$hasBooks && !$hasStories) {
            return ['error' => "لیست کتاب‌ها و متن‌ها خالی است. شما نمی‌توانید درخواست اشتراک ارسال کنید."];
        }
    
        // بررسی وجود درخواست قبلی و حذف درخواست رد شده
        $sqlCheckRequest = "SELECT id, status FROM subscription_requests WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlCheckRequest);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $status = $row['status'];
    
            // اگر درخواست قبلی رد شده باشد، آن را حذف کن
            if ($status === 'rejected') {
                $sqlDeleteRequest = "DELETE FROM subscription_requests WHERE id = ?";
                $deleteStmt = $this->conn->prepare($sqlDeleteRequest);
                $deleteStmt->bind_param("i", $row['id']);
                $deleteStmt->execute();
            } else {
                if ($status === 'pending') {
                    return ['error' => "شما قبلاً یک درخواست معلق دارید."];
                }
            }
        }
    
        // ثبت درخواست جدید
        $sqlInsert = "INSERT INTO subscription_requests (user_id, status) VALUES (?, 'pending')";
        $stmt = $this->conn->prepare($sqlInsert);
        if (!$stmt) {
            return ['error' => "خطا در آماده‌سازی کوئری: " . $this->conn->error];
        }
    
        $stmt->bind_param("i", $userId);
    
        if ($stmt->execute()) {
            return ['success_s' => "درخواست شما با موفقیت ثبت شد."];
        } else {
            return ['error_s' => "خطا در ثبت درخواست: " . $stmt->error];
        }
    }
    
    
    public function cancelSubscriptionRequest($userId)
    {

        $sqlCheckRequest = "SELECT id FROM subscription_requests WHERE user_id = ?";
        $stmt = $this->conn->prepare($sqlCheckRequest);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            return ['error' => "درخواستی برای لغو وجود ندارد."];
        }
    

        $row = $result->fetch_assoc();
        $requestId = $row['id'];
        $sqlDeleteRequest = "DELETE FROM subscription_requests WHERE id = ?";
        $stmt = $this->conn->prepare($sqlDeleteRequest);
        $stmt->bind_param("i", $requestId);
    
        if ($stmt->execute()) {
            return ['success' => "درخواست شما با موفقیت لغو شد."];
        } else {
            return ['error' => "خطا در لغو درخواست: " . $stmt->error];
        }
    }
    





    
    public function deleteBook($bookId) {

    $sqlCheckBook = "SELECT id FROM books WHERE id = ?";
    $stmt = $this->conn->prepare($sqlCheckBook);
    $stmt->bind_param("i", $bookId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // اگر کتاب وجود داشت
    if ($result->num_rows > 0) {

        $sqlDeleteBook = "DELETE FROM books WHERE id = ?";
        $deleteStmt = $this->conn->prepare($sqlDeleteBook);
        $deleteStmt->bind_param("i", $bookId);
        
        if ($deleteStmt->execute()) {
            return "کتاب با موفقیت حذف شد.";
        } else {
            return "خطا در حذف کتاب: " . $deleteStmt->error;
        }
    } else {
        return "کتابی با این شناسه یافت نشد.";
    }
}



public function deleteStory($storyId) {

    $sql = "DELETE FROM stories WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $storyId);

    if ($stmt->execute()) {
        return "داستان با موفقیت حذف شد.";
    } else {
        return "خطا در حذف داستان: " . $stmt->error;
    }
}


// متد برای ویرایش داستان
public function editStory($storyId, $newTitle, $newStoryContent) {

    $sql = "UPDATE stories SET text_title = ?, story = ? WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("ssi", $newTitle, $newStoryContent, $storyId);

    if ($stmt->execute()) {
        return "داستان با موفقیت ویرایش شد.";
    } else {
        return "خطا در ویرایش داستان: " . $stmt->error;
    }
}


}

?>





