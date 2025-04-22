<?php
include '../bin/db.php'; 
include '../bin/classes.php';

$auth = new Auth($conn); 


if (isset($_POST['update']) && isset($_GET['user_id'])) {
    $userId = intval($_GET['user_id']); 
    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);


    if (empty($fullname) || empty($username) || empty($email)) {
        echo "<p style='color:red;'>لطفاً تمامی فیلدها را پر کنید.</p>";
        exit;
    }

    // فراخوانی متد updateUser از کلاس Auth
    $result = $auth->updateUser($userId, $fullname, $username, $email);

    if (strpos($result, 'با موفقیت') !== false) {
        echo "<p style='color:green;'>$result</p>";
        header("Location: mguser.php?success=1");
        exit;
    } else {
        echo "<p style='color:red;'>$result</p>";
    }
} else {
    echo "<p style='color:red;'>اطلاعات ارسالی ناقص است.</p>";
}
?>
