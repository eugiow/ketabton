<?php

session_start();
include '../bin/db.php';  


$userId = $_SESSION['user_id'];



if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$booksSql = "SELECT * FROM books WHERE user_id = ?";
$storiesSql = "SELECT * FROM stories WHERE user_id = ?";

// آماده‌سازی کوئری‌ها
$stmtBooks = $conn->prepare($booksSql);
$stmtBooks->bind_param("i", $userId);  // بایند کردن پارامتر به کوئری
$stmtBooks->execute();  
$resultBooks = $stmtBooks->get_result();  

$stmtStories = $conn->prepare($storiesSql);
$stmtStories->bind_param("i", $userId);  
$stmtStories->execute(); 
$resultStories = $stmtStories->get_result(); 





$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';


$booksSql = "SELECT * FROM books WHERE user_id = ?";
$storiesSql = "SELECT * FROM stories WHERE user_id = ?";


if (!empty($search)) {
    $booksSql .= " AND book_title LIKE ?";
    $storiesSql .= " AND text_title LIKE ?";
}

// آماده‌سازی کوئری‌ها
$stmtBooks = $conn->prepare($booksSql);
$stmtStories = $conn->prepare($storiesSql);

// بایند کردن پارامترها
if (!empty($search)) {
    $searchParam = "%$search%";
    $stmtBooks->bind_param("is", $userId, $searchParam);
    $stmtStories->bind_param("is", $userId, $searchParam);
} else {
    $stmtBooks->bind_param("i", $userId);
    $stmtStories->bind_param("i", $userId);
}


if ($filter !== 'stories') {
    $stmtBooks->execute();
    $resultBooks = $stmtBooks->get_result();
}
if ($filter !== 'books') {
    $stmtStories->execute();
    $resultStories = $stmtStories->get_result();
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subscription_request'])) {
    include '../bin/classes.php'; 
    $auth = new Auth($conn);


    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        die("خطا: کاربر وارد نشده است.");
    }


$result = $auth->requestSubscription($userId);

if (isset($result['success_s'])) {
    $_SESSION['alert'] = ['type' => 'success_s', 'message' => $result['success_s']];
} elseif (isset($result['error'])) {
    $_SESSION['alert'] = ['type' => 'error_s', 'message' => $result['error_s']];
}

header("Location: " . $_SERVER['PHP_SELF']); 
exit;

}

// لغو درخواست
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_request'])) {
    include '../bin/classes.php'; 
    $auth = new Auth($conn);


    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    } else {
        die("خطا: کاربر وارد نشده است.");
    }


$result = $auth->cancelSubscriptionRequest($userId);


if (isset($result['success'])) {
    $_SESSION['alert'] = ['type' => 'success', 'message' => $result['success']];
} elseif (isset($result['error'])) {
    $_SESSION['alert'] = ['type' => 'error', 'message' => $result['error']];
}


header("Location: " . $_SERVER['PHP_SELF']);
exit;
}


?>

<?php include '../views/pheader.php'; ?>

<body class="bgg-body my-2">


<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success text-center py-2  bg-green-800 rounded-lg" id="alertMessage">
        <?= htmlspecialchars($_SESSION['message']); unset($_SESSION['message']); ?>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['messagee'])): ?>
    <div class="alert alert-success text-center py-2  bg-green-800 rounded-lg" id="alertMessagee">
        <?= htmlspecialchars($_SESSION['messagee']); unset($_SESSION['messagee']); ?>
    </div>
<?php endif; ?>

<?php
if (isset($_SESSION['alert'])) {
    $alertType = $_SESSION['alert']['type'] === 'success_s' ? 'alert alert-success text-center py-2 ' : 'alert alert-danger text-center py-2';
    $message = $_SESSION['alert']['message'];
    echo "
    <div class='p-4 mb-4 rounded-lg border $alertType' id='alert'>
        $message
    </div>";
    unset($_SESSION['alert']); 
}
?>

<script>

    setTimeout(function() {
        const alertMessage = document.getElementById('alertMessage');
        const alertMessagee = document.getElementById('alertMessagee');
        const alert = document.getElementById('alert');
        
        if (alertMessage) alertMessage.style.display = 'none';
        if (alertMessagee) alertMessagee.style.display = 'none';
        if (alert) alert.style.display = 'none';
    }, 3000); 
</script>





    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col justify-between w-full lg:w-11/12 mx-auto">
                    
                     <!-- Filter Section -->
<div class="px-2 mt-2" dir="rtl">
  <section class="p-2 bg-[#1E2734] rounded-2xl font border-1 border-blue-800">

    <form method="GET" class="flex flex-row flex-wrap justify-between  items-center mb-0 space-y-1 " id="filterForm">
    <div class=" flex  ">

      <div  class="">
      <select
      
  name="filter"
  class="text-gray-500 text-sm bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-right w-full sm:w-auto shadow-md "
  onchange="document.getElementById('filterForm').submit();"
>
  <option style="font-size: smaller;" value="">همه</option>
  <option style="font-size: smaller;" value="books" <?= isset($_GET['filter']) && $_GET['filter'] === 'books' ? 'selected' : '' ?>>کتاب‌ها</option>
  <option style="font-size: smaller;" value="stories" <?= isset($_GET['filter']) && $_GET['filter'] === 'stories' ? 'selected' : '' ?>>متن‌ها</option>
</select>

      </div>
      </div>
      <div class="flex ">
        <input
          type="text"
          name="search"
          value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
          placeholder="جستجو..."
          style="border-radius:0 20px 20px 0;"
          class="w-full py-2 pl-10 pr-4 border border-gray-300 focus:outline-none"
        />
        <button
          type="submit"
          style="border-radius: 20px 0 0 20px;  background-color: #4e9b81;"
          class="text-white bg-blue-500 px-4 py-2 rounded-lg"
        >
          اعمال
        </button>
      </div>

    </form>
  </section>
</div>

                    <!-- List of Books and Stories -->
                    <section class="flex flex-col pr-7 py-3 overflow-y-auto list-sc font">
                        <ul class="grid lg:grid-cols-2 md:grid-cols-2 gap-2">

<?php if ($filter !== 'stories'): ?>
    <?php 
    $bookCount = 1; 
    while ($book = $resultBooks->fetch_assoc()): ?>
        <li class="rounded-3xl bg-[#1E2734] h-full  flex flex-col justify-between">
            <div class="flex justify-between px-3 py-2">
                <p class="text-white"><?= htmlspecialchars($bookCount); ?></p> 
                <p class="text-white">کتاب</p> 
            </div>
            <div class="flex flex-col sm:flex-row-reverse items-center px-3 pb-3">
                <div class="flex justify-center sm:justify-start w-full sm:w-1/4 mb-4 sm:mb-0">

                    <?php if (!empty($book['book_image'])): ?>
                        <img class="w-16 h-16 rounded-full object-cover ring-2 ring-white dark:ring-[#7FFFD4] hover:ring-[#FF6347] transition-all duration-300" 
                             src="<?= 'add/uploads/' . htmlspecialchars($book['book_image']); ?>" alt="<?= htmlspecialchars($book['book_title']); ?> book cover">
                    <?php else: ?>
                        <img class="w-16 h-16 rounded-full object-cover ring-2 ring-white dark:ring-[#7FFFD4] hover:ring-[#FF6347] transition-all duration-300" 
                             src="../../res/images/story_placeholder.jpg" alt="Placeholder image">
                    <?php endif; ?>                        
                </div>
                <div class="flex flex-col sm:flex-row-reverse w-full sm:w-3/4">
                    <div class="text-white text-center sm:text-left sm:mb-0">
                        <p class="text-lg font-semibold"><?= htmlspecialchars($book['book_title']); ?></p>
                        <p class="text-sm"><?= htmlspecialchars($book['author_name']); ?></p>

                    </div>
                </div>
            </div>
            <div class="flex justify-between px-2">
            <div class="dropdown">
                        <a class="text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </a>
                        <div class="dropdown-menu rounded-lg shadow">
                            <div class="py-2 text-sm text-gray-700 flex flex-col px-2">
                                <div class="hover:bg-blue-100 text-center rounded-lg py-2">
                                    <a href="edit/edit_book.php?id=<?= $book['id']; ?>" class="px-4 py-2 text-blue-600 no-underline">ویرایش</a>
                                </div>
                            </div>
                            <div class="hover:bg-red-100 text-center rounded-lg mx-2 py-2">
                                    <a 
                                    href="edit/delete_book.php?id=<?= $book['id']; ?>" 
                                    class="px-4 py-2 text-red-600 no-underline"
                                    onclick="return confirm('آیا مطمئن هستید که می‌خواهید این کتاب را حذف کنید؟');"
                                    >
                                    حذف
                                </a>
                            </div>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($book['release_date']); ?></p>
            </div>
          
        </li>
        <?php $bookCount++; ?>
    <?php endwhile; ?>
<?php endif; ?>


                            <?php if ($filter !== 'books'): ?>
                            <?php 
                            $storyCount = 1; 
                            while ($story = $resultStories->fetch_assoc()): ?>
                               <li class="rounded-3xl bg-[#1a2028] h-full flex flex-col justify-between">
                                <div class="flex justify-between px-3 py-2">
                                    <p class="text-white"><?= htmlspecialchars($storyCount ); ?></p> 
                                    <p class="text-white">متن</p> 
                                </div>

                                <div class="flex flex-col sm:flex-row-reverse px-3 pb-3">
                                    <div class="flex flex-col sm:flex-row-reverse w-full sm:w-3/4">
                                        <div class="text-white text-center sm:text-left sm:mb-0 w-full">
                                            <p class="text-lg font-semibold"><?= htmlspecialchars($story['text_title']); ?></p>
                                            <div class=" h-40 overflow-y-auto">
                                                <p class="text-xs text-gray-400 break-words"><?= htmlspecialchars($story['story']); ?></p> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex justify-between px-2">
                                    <div class="dropdown">
                                        <a class="text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu rounded-lg shadow">
                                            <div class="py-2 text-sm text-gray-700 flex flex-col px-2">
                                                <!-- لینک ویرایش داستان -->
                                                <div class="hover:bg-blue-100 text-center rounded-lg py-2">
                                                    <a href="edit/edit_story.php?story_id=<?php echo $story['id']; ?>" class="no-underline text-blue-600">ویرایش داستان</a>
                                                </div>
                                            </div>
                                            <!-- فرم حذف داستان -->
                                            <div class="hover:bg-red-100 text-center rounded-lg py-2 mx-2">
                                                <form class="mb-0 px-4 py-2" method="POST" action="edit/delete_story.php" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این داستان را حذف کنید؟');">
                                                    <input type="hidden" name="story_id" value="<?php echo $story['id']; ?>">
                                                    <button type="submit" class="delete-btn text-red-600 no-underline">حذف داستان</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400"><?= htmlspecialchars($story['created_at']); ?></p>
                                </div>
                            </li>

                                <?php $storyCount++; ?>
                            <?php endwhile; ?>
                            <?php endif; ?>
                        </ul>
                    </section>
                    <div class="items-center fixed bottom-0 right-0 w-full flex justify-end  h-20  md:static md:mr-72  lg:mx-5 lg:static md:h-16 md:w-auto ">
                    <?php
                        $userId = $_SESSION['user_id'];

                        // بررسی وضعیت درخواست کاربر
                        $sqlCheckRequest = "SELECT status FROM subscription_requests WHERE user_id = ? ORDER BY created_at DESC LIMIT 1";
                        $stmt = $conn->prepare($sqlCheckRequest);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        // بررسی اینکه آیا کاربر کتابی دارد
                        $sqlCheckBooks = "SELECT id FROM books WHERE user_id = ?";
                        $stmt = $conn->prepare($sqlCheckBooks);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $booksResult = $stmt->get_result();

                        // بررسی اینکه آیا کاربر متن‌هایی دارد
                        $sqlCheckStories = "SELECT id FROM stories WHERE user_id = ?";
                        $stmt = $conn->prepare($sqlCheckStories);
                        $stmt->bind_param("i", $userId);
                        $stmt->execute();
                        $storiesResult = $stmt->get_result();

                        // اگر نه کتابی و نه متنی وجود داشته باشد
                        if ($booksResult->num_rows === 0 && $storiesResult->num_rows === 0) {

                        } else {
                            if ($result->num_rows > 0) {
                                // گرفتن وضعیت آخرین درخواست
                                $row = $result->fetch_assoc();
                                $status = $row['status'];

                                if ($status === 'pending') {

                                    echo '<form class="mb-0 flex border border-[#1E429F] rounded-lg p-2" method="POST">
                        <input type="hidden" name="cancel_request" value="1">
                        <button 
                            type="submit" 
                            class="text-red-500 mb-1">
                            لغو
                        </button>
                        <p class="text-gray-500 mb-0 ml-1">درحال برسی</p>
                    </form>

                    ';
                                } elseif ($status === 'approved') {
                                    // درخواست تأیید شده
                                    echo '<form class="mb-0 flex border border-[#1E429F] rounded-lg p-2" method="POST">
                        <input type="hidden" name="cancel_request" value="1">
                        <button 
                            type="submit" 
                            class="text-red-500 mb-1 ">
                            لغو
                        </button>
                            <p class="text-green-500 mb-0 ml-2">تاید شد</p>
                    </form>

                    ';
            } elseif ($status === 'rejected') {
                // درخواست رد شده
                echo '<p class="text-red-500 border border-[#1E429F] rounded-lg p-1">رد شد درخواست اول لطفا ویرایش و بعد درخواست بدید</p>';
                echo '
                <form class="mb-0" method="POST" >
                <input type="hidden" name="subscription_request" value="1">
                <button 
                    type="submit" 
                    class="no-underline transition text-white bg-[#4E9B81] hover:bg-[#1E429F] text-sm border border-[#1E429F] px-3 py-1.5 rounded-lg shadow-xl hover:shadow-2xl">
                    درخواست اشتراک همگانی
                </button>
                </form>
                ';
            }
        } else {
            // اگر هیچ درخواست قبلی وجود نداشته باشد
            echo '
            <form class="mb-0" method="POST" >
            <input type="hidden" name="subscription_request" value="1">
            <button 
                type="submit" 
                class="no-underline transition text-white bg-[#4E9B81] hover:bg-[#1E429F] text-sm border border-[#1E429F] px-3 py-1.5 rounded-lg shadow-xl hover:shadow-2xl">
                 اشتراک همگانی
            </button>
            </form>
            ';
        }
    }

    $stmt->close();
?>

</div>
                    </div>

                </main>
            </div>
        </div>
    </section>

    <?php
        include '../views/mesg.php';
    include '../views/pfooter.php'
    
    ?>
