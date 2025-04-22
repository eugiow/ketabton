<?php
include '../bin/db.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$isAdmin = ($_SESSION['role'] ?? '') === 'admin';


if (!isset($_SESSION['user_id'])) {
    if (isset($_COOKIE['user_id']) && isset($_COOKIE['role'])) {

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


$bookCountQuery = "SELECT COUNT(*) AS total_books FROM books";
$bookCountResult = $conn->query($bookCountQuery);
$bookCount = $bookCountResult->fetch_assoc()['total_books'] ?? 0;

// خواندن تعداد متن‌ها
$storyCountQuery = "SELECT COUNT(*) AS total_stories FROM stories";
$storyCountResult = $conn->query($storyCountQuery);
$storyCount = $storyCountResult->fetch_assoc()['total_stories'] ?? 0;

// خواندن تعداد کاربران
$userCountQuery = "SELECT COUNT(*) AS total_users FROM users";
$userCountResult = $conn->query($userCountQuery);
$userCount = $userCountResult->fetch_assoc()['total_users'] ?? 0;


// خواندن تعداد درخواست‌ها با وضعیت 'pending' یا همه درخواست‌ها
$sql = "SELECT COUNT(*) AS request_count FROM subscription_requests WHERE status = 'pending'"; 
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $requestCount = $row['request_count'];
} else {
    $requestCount = 0; 
}

// شمارش پیام‌ها
$messageCountQuery = "SELECT COUNT(*) AS unread_messages FROM user_messages WHERE status = 'pending'";
$messageCountResult = $conn->query($messageCountQuery);
$messageCount = $messageCountResult->fetch_assoc()['unread_messages'] ?? 0;


include '../views/pheader.php';
?>


<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">
                     <!-- شمارنده‌ها -->
<div class="px-2 py-2">
    <section class="w-full flex flex-row justify-between items-center px-6 py-2 bg-[#1E2734] rounded-3xl" aria-label="شمارنده‌ها">
        <!-- شمارنده کتاب‌ها -->
        <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="کتاب‌ها">
            <div class="p-2.5 rounded-full bg-gradient-to-r from-blue-500 to-blue-300 shadow-lg">
            <i class="bi bi-book text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
            </div>
            <p class="mt-2 text-sm font-bold"><?= htmlspecialchars($bookCount) ?></p>
        </div>
        <!-- جداکننده -->
        <div class="w-px h-12 bg-gray-700 mx-2" aria-hidden="true"></div>
        <!-- شمارنده متن‌ها -->
        <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="متن‌ها">
            <div class="p-2.5 rounded-full bg-gradient-to-r from-green-500 to-green-300 shadow-lg">
                <i class="bi bi-card-text text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
            </div>
            <p class="mt-2 text-sm font-bold"><?= htmlspecialchars($storyCount) ?></p>
        </div>
        <!-- جداکننده -->
        <div class="w-px h-12 bg-gray-700 mx-2" aria-hidden="true"></div>
        <!-- شمارنده اعضا -->
        <div class="flex flex-col items-center text-white hover:scale-110 transform transition-all duration-300" aria-label="اعضا">
            <div class="p-2.5 rounded-full bg-gradient-to-r from-red-500 to-red-300 shadow-lg">
                <i class="bi bi-person-circle text-white lg:text-lg  sm:text-sm" aria-hidden="true"></i>
            </div>
            <p class="mt-2 text-sm font-bold"><?= htmlspecialchars($userCount) ?></p>
        </div>

    </section>
</div>


                     <!-- پیام‌ها -->
                    <section class="flex-col-reverse justify-between h-full  px-4 font" dir="rtl">
                    <div class="flex-col flex sm:flex-row space-y-2 sm:space-y-0 justify-between mb-3">
                        <!-- تعداد درخواست ها -->
                    <a href="req.php" class="no-underline flex w-full ml-2 items-center bg-gray-800 text-white px-6 py-3 rounded-lg shadow-xl hover:bg-gray-700 transition hover:shadow-2xl duration-300">
                        <p class="font-sm mb-0">
                            <i class="bi bi-bell-fill text-red-300 ml-1" aria-hidden="true"></i>درخواست ها
                        </p>
                        
                        <span class="mx-1">:</span>
                        <p class="text-sm mb-0 opacity-80 text-red-500">
                            <?php echo $requestCount; ?>
                        </p>
                    </a>
                    <!-- تعداد پیام های خوانده نشده -->
                    <a href="mesuser.php" class="no-underline flex w-full ml-2 items-center bg-gray-800 text-white px-6 py-3 rounded-lg shadow-xl hover:bg-gray-700 transition hover:shadow-2xl duration-300">
                    <p class="font-sm mb-0">
                    <i class="bi bi-bell-fill text-red-300 ml-1" aria-hidden="true"></i> پیام‌ها
                    </p>
                    <span class="mx-1">:</span>
                    <p class="text-sm mb-0 opacity-80 text-red-500">
                        <?= htmlspecialchars($messageCount) ?>
                    </p>
                </a>


                                    <div class="hidden sm:block">
                                        <a href="../login/logout.php" class="btn btn-danger px-6 py-3 rounded-lg shadow-xl hover:shadow-2xl">خروج</a>
                                    </div>
                            </div>
                        <div class="w-full">
                            <article class="alert bg-gradient-to-r from-green-400 to-green-600 text-white px-4 py-2 rounded-lg shadow-md">
                                <header class="flex justify-between items-center mb-2">
                                    <p class="font-bold"><span><i class="bi bi-bell-fill text-red-300 ml-1" aria-hidden="true"></i></span>اعلان</p>
                                    <button type="button" class="btn-close text-white" data-bs-dismiss="alert" aria-label="بستن"></button>
                                </header>
                                <p class="text-sm">تعداد <span class="mx-2 text-yellow-400"><?php echo $requestCount; ?></span>نفر درخواست ثبت لیست خود دارند <a class="no-underline hover:text-gray-500 text-yellow-300" href="req.php">تکمیل کردن</a></p>
                            </article>

                        </div>

                    </section>
                    

                </main>
            </div>
            
        </div>
    </section>
    
    <script src="../panl/src/js.js"></script>
    <?php
    include '../views/pfooter.php'
    ?>
