<?php
include '../bin/db.php';  

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id'])) {

        $_SESSION['user_id'] = $_COOKIE['user_id'];
        $_SESSION['username'] = $_COOKIE['username'];
        $_SESSION['fullname'] = $_COOKIE['fullname'];
        $_SESSION['role'] = $_COOKIE['role'];
    } else {

        $_SESSION['login_error'] = "لطفاً ابتدا وارد شوید.";
        header("Location: ../login/login.php");
        exit();
    }
}


// تابع برای اجرای کوئری و بازگرداندن مقدار
function fetchSingleValue($conn, $sql, $userId, $column) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL Error: " . $conn->error);
    }
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    return $data ? $data[$column] : null; 
}
// کوئری‌ها
$booksSql = "SELECT COUNT(*) AS book_count FROM books WHERE user_id = ?";
$storiesSql = "SELECT COUNT(*) AS story_count FROM stories WHERE user_id = ?";
$userSql = "SELECT created_at FROM users WHERE id = ?";
$emailsql = "SELECT email FROM users WHERE id = ?";

// دریافت اطلاعات از دیتابیس
$bookCount = fetchSingleValue($conn, $booksSql, $userId, 'book_count') ?? 0;
$storyCount = fetchSingleValue($conn, $storiesSql, $userId, 'story_count') ?? 0;
$joinDate = fetchSingleValue($conn, $userSql, $userId, 'created_at') ?? "Unknown";
$email = fetchSingleValue($conn, $emailsql, $userId, 'email') ?? "Unknown";

// محاسبه تعداد روز عضویت
$daysSinceJoin = "Unknown"; 

if ($joinDate !== "Unknown") {
    // تبدیل تاریخ عضویت به شیء DateTime
    $joinDateObject = new DateTime($joinDate);
    $currentDate = new DateTime(); 
    $interval = $joinDateObject->diff($currentDate); 


    $daysSinceJoin = $interval->days + 1; 
}


include '../views/pheader.php';
?>


<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">
                    <!-- شمارنده‌ها -->
                     <div class="px-2 pt-2">
                     <section class="w-full flex flex-row justify-between items-center px-6 py-2 bg-[#1E2734] rounded-3xl" aria-label="شمارنده‌ها">
                            <!-- شمارنده کتاب‌ها -->
                            <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="کتاب‌ها">
                                <div class="p-2.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-300 shadow-lg">
                                    <i class="bi bi-book text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
                                </div>
                                <p class="mt-2 text-sm font-bold"><?php echo $bookCount; ?></p>
                            </div>
                            <!-- جداکننده -->
                            <div class="w-px h-12 bg-gray-700 mx-2" aria-hidden="true"></div>
                            <!-- شمارنده نقل قول‌ها -->
                            <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="نقل قول‌ها">
                                <div class="p-2.5 rounded-full bg-gradient-to-r from-green-500 to-green-300 shadow-lg">
                                    <i class="bi bi-blockquote-right text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
                                </div>
                                <p class="mt-2 text-sm font-bold"><?php echo $storyCount; ?></p>
                            </div>
                            <!-- جداکننده -->
                            <div class="w-px h-12 bg-gray-700 mx-2" aria-hidden="true"></div>
                            <!-- شمارنده تاریخ عضویت -->
                            <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="تاریخ عضویت">
                                <div class="p-2.5  rounded-full bg-gradient-to-r from-purple-500 to-purple-300 shadow-lg">
                                    <i class="bi bi-calendar2-week text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
                                </div>
                                <p class="mt-2 text-sm font-bold"><?php echo date('Y/m/d', strtotime($joinDate)); ?></p>
                            </div>
                        </section>
                     </div>

                    <!-- پیام‌ها -->
                    <section class="flex-col-reverse justify-between h-full my-4 px-4 font" dir="rtl">
                        <div class="w-full">
                            <article class="alert bg-gradient-to-r from-green-400 to-green-600 text-white px-4 py-2 rounded-lg shadow-md">
                                <header class="flex justify-between items-center mb-2">
                                    <p class="font-bold">مدیر</p>
                                    <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="بستن"></button>
                                </header>
                                <p class="text-sm">کاربر <?php echo $_SESSION['fullname']; ?> عزیز به سایت کتابتون خوش آمدید.</p>
                            </article>
                            <article class="alert bg-gradient-to-r from-blue-400 to-blue-600 text-white px-4 py-2 rounded-lg shadow-md">
                                <header class="flex justify-between items-center mb-2">
                                    <p class="font-bold">مدیر</p>
                                    <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="بستن"></button>
                                </header>
                                <p class="text-sm">
                                    کاربر <?php echo $_SESSION['fullname']; ?> عزیز اطلاعات خود را تکمیل کنید.
                                    <a class="underline text-green-300" href="info.html">تکمیل کردن</a>
                                </p>
                            </article>
                        </div>
                                <div class="flex-col  flex  sm:flex-row space-y-2 sm:space-y-0 justify-between">
                                    <!-- بخش ایمیل کاربر -->
                                    <section class="flex items-center bg-gray-800 text-white px-6 py-3 rounded-lg shadow-xl hover:shadow-2xl transition-shadow duration-300">
                                        <p class="font-bold ml-2 mb-0 ">ایمیل شما:</p>
                                        <p class="text-sm mb-0 opacity-80"><?php echo $email; ?></p>
                                    </section>
                                    
                                    <!-- بخش روزهای عضویت کاربر -->
                                    <section class="flex items-center bg-gray-700 text-white px-6 py-3 rounded-lg shadow-xl hover:shadow-2xl transition-shadow duration-300">
                                        <p class="font-bold ml-2 mb-0 ">عضویت شما:</p>
                                        <p class="text-sm mb-0 opacity-80"><?php echo htmlspecialchars($daysSinceJoin); ?> روز</p>
                                    </section>
                                    <div class="hidden sm:block ">
                                        <a href="../login/logout.php" class="btn btn-danger px-6 py-3 rounded-lg shadow-xl hover:shadow-2xl">خروج</a>
                                    </div>


                                </div>

                    </section>
                </main>
            </div>
        </div>
    </section>


    <?php
    include '../views/mesg.php';
    include '../views/pfooter.php';
    ?>