<?php
session_start();

include '../bin/db.php';
include '../bin/classes.php'; 

$auth = new Auth($conn);

// بررسی نوع درخواست
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $auth->login($username, $password);
    } elseif (isset($_POST['signup'])) {
        $fullname = $_POST['fullname'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $gender = $_POST['gender'];  

        $auth->signup($fullname, $username, $email, $password, $confirm_password, $gender); 
    }
}


$error_message = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$signup_error_message = isset($_SESSION['signup_error']) ? $_SESSION['signup_error'] : '';
$signup_success_message = isset($_SESSION['signup_success']) ? $_SESSION['signup_success'] : '';

unset($_SESSION['login_error'], $_SESSION['signup_error'], $_SESSION['signup_success']);
?>


<!DOCTYPE html>
<html lang="fa">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود</title>
    <link rel="stylesheet" href="styles.css" type="text/css">
    <link rel="stylesheet" href="../assets/css/output.css">
    <link rel="stylesheet" href="../assets/css/input.css" type="text/css">

</head>

<body class="font">

    <?php if ($error_message): ?>
        <script>
            alert("<?php echo $error_message; ?>");
        </script>
    <?php endif; ?>

    <?php if ($signup_error_message): ?>
        <script>
            alert("<?php echo $signup_error_message; ?>");
        </script>
    <?php endif; ?>

    <?php if ($signup_success_message): ?>
        <script>
            alert("<?php echo $signup_success_message; ?>");
        </script>
    <?php endif; ?>

    <div class="wrapper">
        <div class="container">
            <div class="blueBg">
                <div class="box signin">
                    <h2>قبلاً حساب کاربری دارید؟</h2>
                    <button class="signinBtn" onclick="toggleForm('signin')">ورود</button>
                </div>
                <div class="box signup">
                    <h2>حساب کاربری ندارید؟</h2>
                    <button class="signupBtn" onclick="toggleForm('signup')">ثبت نام</button>
                </div>
            </div>

            <div class="formBox <?php echo isset($_SESSION['signup_active']) && $_SESSION['signup_active'] ? 'active' : ''; ?>" dir="rtl">
                <div class="form signinForm">
                    <form action="" method="POST">
                        <h3>ورود به حساب</h3>
                        <input type="text" name="username" placeholder="نام کاربری یا ایمیل" class="<?php echo $error_message ? 'input-error' : ''; ?>" required>
                        <input type="password" name="password" placeholder="رمز عبور" class="<?php echo $error_message ? 'input-error' : ''; ?>" required>
                        <input type="submit" name="login" value="ورود">
                        <a href="email.php" class="forgot">رمز عبور را فراموش کرده‌اید؟</a>
                    </form>
                </div>
                
                <div class="form signupForm">
                    <form action="" method="POST">
                        <h3>ثبت نام</h3>
                        <input type="text" name="fullname" placeholder="نام کامل" required>
                        <input type="text" name="username" placeholder="نام کاربری" required>
                        <input type="email" name="email" placeholder="آدرس ایمیل" required>
                        <input type="password" name="password" placeholder="رمز عبور" required>
                        <input type="password" name="confirm_password" placeholder="تأیید رمز عبور" required>
                        <div class="flex justify-between">
                            <div>
                                <p class="text-sm m-0">قوانین و مقررات را می‌پذیرم</p>
                            </div>
                            <div>
                                <input class="w-10 h-10 rounded-full" type="checkbox" name="terms" required>
                            </div>
                        </div>


                    <div class="flex justify-between">
                        <div class="flex space-x-4 items-center justify-center">
                        <!-- گزینه مرد -->
                        <label class="flex items-center space-x-2 text-lg cursor-pointer">
                            <input type="radio" name="gender" value="male" required class="form-radio text-blue-600 focus:ring-2 focus:ring-blue-500">
                            <i class="bi bi-person-standing text-blue-600 text-2xl"></i>
                            <span class="ml-2 text-blue-600">مرد</span>
                        </label>

                        <!-- گزینه زن -->
                        <label class="flex items-center space-x-2 text-lg cursor-pointer">
                            <input type="radio" name="gender" value="female" required class="form-radio text-pink-600 focus:ring-2 focus:ring-pink-500">
                            <i class="bi bi-person-standing-dress text-red-400  text-2xl"></i>
                            <span class="ml-2 text-red-400">زن</span>
                        </label>
                    </div>
                            <div>
                            <input type="submit" name="signup" value="ثبت نام">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="script.js"></script>
</body>

</html>

