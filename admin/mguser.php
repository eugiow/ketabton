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


include '../views/pheader.php'; 
?>

<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">
                    
                     <!-- Filter Section -->
                     <div class="px-2 mt-2" dir="rtl">
  <section class="p-2 bg-[#1E2734] rounded-2xl font border-1 border-blue-800     ">
    <form method="GET" class="flex flex-row flex-wrap justify-between items-center mb-0 space-y-1 " id="filterForm">
      <!-- فیلتر -->
      <div class="w-full lg:w-auto">
      <select
      
  name="filter"
  class="text-gray-500 text-sm bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-right w-full sm:w-auto shadow-md "
  onchange="document.getElementById('filterForm').submit();"
>
  <option style="font-size: smaller; color:#4E9B81;" value="">فیلتر</option>
  <option style="font-size: smaller;" value="">همه</option>
  <option style="font-size: smaller;" value="admin">ادمین ها</option>
  <option style="font-size: smaller;" value="female">خانوم ها</option>
  <option style="font-size: smaller;" value="male">مرد ها</option>
  <option style="font-size: smaller;" value="books">دارندگان کتاب </option>
  <option style="font-size: smaller;" value="stories">دارندگان متن </option>
  <option style="font-size: smaller;" value="week_registered">ثبت نامی های هفته گذشته</option>
  <option style="font-size: smaller;" value="today_registered">ثبت نامی های روز گذشته</option>

</select>

      </div>

      <div class="flex w-full lg:w-auto">
        <input
          type="text"
          name="search"
          value=""
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
                
        
        <!-- List of Books -->
        <section class="flex flex-col pr-7 py-3 overflow-y-auto list-sc font" >
            <ul class=" grid lg:grid-cols-2 md:grid-cols-1 gap-2 ">
            <?php

        $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';


        $sql = "SELECT 
                    users.id,
                    users.fullname,
                    users.username,
                    users.email,
                    users.gender,
                    users.role,
                    users.created_at, 
                    COUNT(DISTINCT books.id) AS book_count,
                    COUNT(DISTINCT stories.id) AS story_count
                FROM users
                LEFT JOIN books ON books.user_id = users.id
                LEFT JOIN stories ON stories.user_id = users.id
                WHERE 1";

                // اعمال فیلترهای مختلف
                if ($filter == 'books') {
                    $sql .= " AND books.user_id IS NOT NULL";  
                } elseif ($filter == 'stories') {
                    $sql .= " AND stories.user_id IS NOT NULL"; 
                } elseif ($filter == 'week_registered') {
                    $sql .= " AND users.created_at >= CURDATE() - INTERVAL 7 DAY"; 
                } elseif ($filter == 'today_registered') {
                    $sql .= " AND users.created_at >= CURDATE() - INTERVAL 1 DAY";  
                } elseif ($filter == 'male') {
                    $sql .= " AND users.gender = 'male'";  
                } elseif ($filter == 'female') {
                    $sql .= " AND users.gender = 'female'";  
                } elseif ($filter == 'admin') {
                    $sql .= " AND  users.role = 'admin'"; 
                }


                if (!empty($search)) {
                    $searchParam = "%" . $search . "%";
                    $sql .= " AND (users.fullname LIKE ? OR users.username LIKE ?)";
                }

                // اجرای کوئری
                $sql .= " GROUP BY users.id";
                $stmt = $conn->prepare($sql);

                if (!empty($search)) {
                    $stmt->bind_param('ss', $searchParam, $searchParam); 
                }



                $stmt->execute();
                $result = $stmt->get_result();

                if ($result && $result->num_rows > 0) {
                    while ($user = $result->fetch_assoc()) {
                        // نمایش نتایج کاربران
                        ?>
                        <li class="flex flex-col justify-between rounded-lg bg-[#1E2734] p-2">
                            <div class="flex justify-between">
                                <p class="font-smer text-white"><?= date("Y/m/d", strtotime($user['created_at'])) ?></p>
                                <div class="dropdown">
                                    <a class="text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </a>
                                    <div class="dropdown-menu rounded-lg shadow">
                                        <div class="py-2 text-sm text-gray-700 flex flex-col px-2">
                                            <div class="hover:bg-blue-100 text-center rounded-lg py-2">
                                                <a href="user_actions.php?action=edit&user_id=<?= $user['id'] ?>" class="px-4 py-2 text-blue-600 no-underline">ویرایش</a>
                                            </div>
                                            <div class="hover:bg-blue-100 text-center rounded-lg py-2">
                                                <?php if (isset($user['role']) && $user['role'] == 'admin'): ?>
                                                    <a href="user_actions.php?action=remove_admin&user_id=<?= $user['id'] ?>" class="px-4 py-2 text-gray-600 no-underline">حذف ادمین</a>
                                                <?php else: ?>
                                                    <a href="user_actions.php?action=make_admin&user_id=<?= $user['id'] ?>" class="px-4 py-2 text-gray-600 no-underline">ادمین کردن</a>
                                                <?php endif; ?>
                                            </div>

                                            <div class="hover:bg-gray-100 text-center rounded-lg py-2">
                                                <a href="user_actions.php?action=show_list&user_id=<?= $user['id'] ?>" class="px-4 py-2 text-gray-600 no-underline">نمایش لیست</a>
                                            </div>
                                        </div>
                                        <div class="bg-red-100 text-center rounded-lg mx-2 py-2">
                                            <a href="user_actions.php?action=delete&user_id=<?= $user['id'] ?>" class="px-4 py-2 text-red-600 no-underline">حذف</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- نمایش تصویر براساس جنسیت -->
                            <div class="flex flex-row justify-center ">
                                <?php if ($user['role'] == 'admin'): ?>

                                    <?php if ($user['gender'] == 'male'): ?>
                                        <img class="w-20 h-20 rounded-full ring-4 ring-amber-500" src="../assets/images/icon/men.jpg" alt="مرد">
                                    <?php elseif ($user['gender'] == 'female'): ?>
                                        <img class="w-20 h-20 rounded-full ring-4 ring-amber-500" src="../assets/images/icon/women.jpg" alt="زن">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-4xl text-gray-500"></i>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <!--   کاربران  -->
                                    <?php if ($user['gender'] == 'male'): ?>
                                        <img class="w-20 h-20 rounded-full" src="../assets/images/icon/men.jpg" alt="مرد">
                                    <?php elseif ($user['gender'] == 'female'): ?>
                                        <img class="w-20 h-20 rounded-full" src="../assets/images/icon/women.jpg" alt="زن">
                                    <?php else: ?>
                                        <i class="bi bi-person-circle text-4xl text-gray-500"></i>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </div>


            <div class="w-16  h-0.5 bg-gray-500 mx-auto my-3"></div>

            <!-- نمایش اطلاعات کاربر -->
            <div class="px-4 flex flex-row-reverse justify-between">
                <div class="flex flex-col text-right text-white">
                    <div class="flex-col">
                        <label class="text-[8px] text-green-400">نام</label>
                        <p class="text-sm font-semibold"><?= htmlspecialchars($user['fullname']) ?></p>
                    </div>
                    <div class="flex-col">
                        <label class="text-[8px] text-green-400">نام کاربری</label>
                        <p class="text-sm font-semibold"><?= htmlspecialchars($user['username']) ?></p>
                    </div>
                    <div class="flex-col">
                        <label class="text-[8px] text-green-400">ایمیل</label>
                        <p class="text-sm font-semibold"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                </div>
                <div class="flex flex-col text-right text-white">
                    <div class="flex-col">
                        <label class="text-[8px] text-purple-400">کتاب‌ها</label>
                        <p class="text-sm font-semibold"><?= $user['book_count'] ?></p>
                    </div>
                    <div class="flex-col">
                        <label class="text-[8px] text-purple-400">متن‌ها</label>
                        <p class="text-sm font-semibold"><?= $user['story_count'] ?></p>
                    </div>
                </div>
            </div>
        </li>
        <?php
    }
} else {
    echo "<p class='text-white text-center'>هیچ کاربری یافت نشد.</p>";
}
?>

                    </ul>
                </section>
            </main>
        </div>
        

        </div>
    </section>



    <?php
    include '../views/pfooter.php'
    ?>