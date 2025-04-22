<?php
include '../bin/db.php'; 


if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$userId = $_SESSION['user_id'];

// بررسی وجود user_id در سشن یا کوکی
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

// بررسی نقش کاربر
if ($_SESSION['role'] !== 'admin') {
    echo "شما اجازه دسترسی به این صفحه را ندارید.";
    exit();
}


// واکشی پیام‌ها از دیتابیس
$sql = "SELECT um.id, u.fullname, um.message, um.status, um.created_at 
        FROM user_messages um 
        JOIN users u ON um.user_id = u.id
        ORDER BY um.created_at DESC";

$result = $conn->query($sql);

if (!$result) {
    die("خطا در اجرای کوئری: " . $conn->error);
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['message_id'])) {
    $messageId = intval($_POST['message_id']);
    $action = $_POST['action'];

    // بررسی معتبر بودن ورودی
    if (in_array($action, ['read', 'resolved'])) {
        $updateSql = "UPDATE user_messages SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($updateSql);
        $stmt->bind_param("si", $action, $messageId);

        if ($stmt->execute()) {
            echo "<script>alert('وضعیت با موفقیت تغییر کرد.');</script>";
        } else {
            echo "<script>alert('خطا در تغییر وضعیت.');</script>";
        }

        $stmt->close();
    }
}



include '../views/pheader.php'; 


?>

<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">
                    
                <div class="px-2 mt-2" dir="rtl">
    <section class="p-2 bg-[#1E2734] rounded-2xl font border-1 border-blue-800">
        <form method="GET" class="flex flex-row flex-wrap justify-between items-center mb-0 space-y-1" id="filterForm">
            <!-- فیلتر -->
            <div class="w-full lg:w-auto">
                <select
                    name="filter"
                    class="text-gray-500 text-sm bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-right w-full sm:w-auto shadow-md"
                    onchange="document.getElementById('filterForm').submit();"
                >
                    <option style="font-size: smaller; color:#4E9B81;" value="">فیلتر</option>
                    <option style="font-size: smaller;" value="all" <?= isset($_GET['filter']) && $_GET['filter'] == 'all' ? 'selected' : '' ?>>همه</option>
                    <option style="font-size: smaller;" value="read" <?= isset($_GET['filter']) && $_GET['filter'] == 'read' ? 'selected' : '' ?>>خوانده شده</option>
                    <option style="font-size: smaller;" value="unread" <?= isset($_GET['filter']) && $_GET['filter'] == 'unread' ? 'selected' : '' ?>>خوانده نشده</option>
                    <option style="font-size: smaller;" value="resolved" <?= isset($_GET['filter']) && $_GET['filter'] == 'resolved' ? 'selected' : '' ?>>رسیدگی شده</option>
                </select>
            </div>
        </form>
    </section>
</div>

                
        
                <!-- List of Books -->
<div class="flex flex-col overflow-y-auto list-sc font">
<?php
// دریافت فیلتر از URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

// کوئری بر اساس فیلتر انتخابی
$sql = "SELECT um.id, um.user_id, u.fullname, um.message, um.status, um.created_at 
        FROM user_messages um 
        JOIN users u ON um.user_id = u.id";


// اعمال فیلتر به کوئری
if ($filter == 'read') {
    $sql .= " WHERE um.status = 'read'";
} elseif ($filter == 'unread') {
    $sql .= " WHERE um.status = 'unread'";
} elseif ($filter == 'resolved') {
    $sql .= " WHERE um.status = 'resolved'";
} elseif ($filter == 'all' || $filter == '') {
    $sql .= " ORDER BY um.created_at DESC";
}



$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        ?>
        <section class="flex-col-reverse justify-between h-full px-4 font mt-4 " dir="rtl">
            <div class="w-full">
                <article class="alert bg-gradient-to-r from-green-400 to-green-600 text-white px-4 py-2 rounded-lg shadow-md">
                    <header class="flex justify-between items-center mb-2">
                        <p class="font-bold">
                            <span><i class="bi bi-bell-fill text-red-300 ml-1" aria-hidden="true"></i></span>
                            <?= htmlspecialchars($row['fullname']) ?>
                        </p>
                        <div class="dropdown mb-3">
                            <a class="text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-three-dots-vertical"></i>
                            </a>
                            <div class="dropdown-menu rounded-lg shadow">
                                <form class="text-center mb-0" method="POST" action="">
                                    <input type="hidden" name="message_id" value="<?= $row['id'] ?>" />
                                    <input type="hidden" name="action" value="read" />
                                    <button type="submit" class="px-4 py-2 text-green-600 no-underline">خوانده شد</button>
                                </form>
                                <form class="text-center mb-0" method="POST" action="">
                                    <input type="hidden" name="message_id" value="<?= $row['id'] ?>" />
                                    <input type="hidden" name="action" value="resolved" />
                                    <button type="submit" class="px-4 py-2 text-red-600 no-underline">رسیدگی شد</button>
                                </form>
                            </div>
                        </div>
                    </header>
                    <p class="text-sm"><?= htmlspecialchars($row['message']) ?></p>
                </article>
            </div>
        </section>
        <?php
    }
} else {
    echo '<p class="text-white text-center">هیچ پیامی یافت نشد.</p>';
}
?>

</div>
                

            </main>
        </div>
        

        </div>
    </section>



    <?php
    include '../views/pfooter.php'
    ?>