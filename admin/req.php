<?php
include '../bin/db.php'; 


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['request_id']) && isset($_POST['action'])) {
        $request_id = intval($_POST['request_id']);
        $action = $_POST['action'];


        if ($action === 'approve' || $action === 'reject') {
            $status = $action === 'approve' ? 'approved' : 'rejected';


            if ($status === 'rejected') {
                $delete_sql = "DELETE FROM subscription_requests WHERE id = ?";
                $stmt = $conn->prepare($delete_sql);
                $stmt->bind_param("i", $request_id);
                if ($stmt->execute()) {
                } else {
                    echo "<p class='text-red-500'>خطا در حذف درخواست.</p>";
                }
            } else {
                $update_sql = "UPDATE subscription_requests SET status = ?, updated_at = NOW() WHERE id = ?";
                $stmt = $conn->prepare($update_sql);
                $stmt->bind_param("si", $status, $request_id);
                if ($stmt->execute()) {
                    echo "<div  class='alert text-center alert-success alert-dismissible fade show' role='alert'> درخواست تایید شد 
                            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                } else {
                    echo "<p class='text-red-500'>خطا در به‌روزرسانی وضعیت درخواست.</p>";
                }
            }
        }
    }
}
include '../views/pheader.php';
?>

<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse">
            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">
                <div class="px-2 mt-2" dir="rtl">
  <section class="p-2 bg-[#1E2734] rounded-2xl font border-1 border-blue-800">



    <form method="GET" class="flex flex-row flex-wrap justify-between items-center mb-0 space-y-1 lg:flex lg:space-y-0 lg:space-x-2" id="filterForm">
      <!-- Filter -->
      <div class="w-full lg:w-auto">
        <select name="filter" class="text-gray-500 text-sm bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-right w-full sm:w-auto shadow-md" onchange="document.getElementById('filterForm').submit();">
          <option style="font-size: smaller; color:#4E9B81;" value="">فیلتر</option>
          <option style="font-size: smaller;" value="">همه</option>
          <option style="font-size: smaller;" value="approved">ثبت شده ها</option>
          <option style="font-size: smaller;" value="pending">در انتظار</option>
        </select>
      </div>

      <!-- Search -->
      <div class="flex w-full lg:w-auto">
        <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="جستجو..." style="border-radius:0 20px 20px 0;" class="w-full py-2 pl-10 pr-4 border border-gray-300 focus:outline-none" />
        <button type="submit" style="border-radius: 20px 0 0 20px; background-color: #4e9b81;" class="text-white bg-blue-500 px-4 py-2 rounded-lg">
          اعمال
        </button>
      </div>
    </form>
  </section>
</div>

                    <!-- List of Subscription Requests -->
                    <section class="flex flex-col pr-7 py-3 overflow-y-auto list-sc font">
                        <ul class="grid lg:grid-cols-2 md:grid-cols-2 gap-2">
                            <?php

                            $filter = isset($_GET['filter']) ? $_GET['filter'] : '';
                            $search = isset($_GET['search']) ? $_GET['search'] : '';


                            $where_clauses = [];
                            $where_clauses[] = "sr.status IN ('pending', 'approved', 'rejected')";

                            if ($filter) {
                                $where_clauses[] = "sr.status = '$filter'";
                            }
                            if ($search) {
                                $search = $conn->real_escape_string($search);
                                $where_clauses[] = "(u.fullname LIKE '%$search%' OR u.username LIKE '%$search%')";
                            }

                            // ترکیب شرایط WHERE
                            $where_sql = implode(' AND ', $where_clauses);

                            $sql = "
                            SELECT 
                                sr.id AS request_id, 
                                sr.user_id, 
                                u.fullname, 
                                u.username, 
                                (SELECT COUNT(*) FROM books b WHERE b.user_id = u.id) AS book_count, 
                                (SELECT COUNT(*) FROM stories s WHERE s.user_id = u.id) AS text_count, 
                                sr.created_at, 
                                sr.status
                            FROM subscription_requests sr 
                            JOIN users u ON sr.user_id = u.id 
                            WHERE $where_sql
                            ORDER BY sr.created_at DESC
                            ";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                // نمایش درخواست‌ها
                                while ($row = $result->fetch_assoc()) {
                                    $fullname = $row['fullname'];
                                    $book_count = $row['book_count'];
                                    $text_count = $row['text_count'];
                                    $created_at = $row['created_at'];
                                    $request_id = $row['request_id'];
                                    $status = $row['status'];
                            ?>
                            <li class="rounded-2xl bg-[#1E2734] w-full p-4">
                                <div class="flex justify-between">
                                    <div class="dropdown mb-3">
                                        <a class="text-white" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </a>
                                        <div class="dropdown-menu rounded-lg shadow">
                                            <form class="text-center" method="POST" action="">
                                                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>" />
                                                <input type="hidden" name="action" value="approve" />
                                                <button type="submit" class="px-4 py-2 text-green-600 no-underline">ثبت</button>
                                            </form>
                                            <form class="text-center" method="POST" action="">
                                                <input type="hidden" name="request_id" value="<?php echo $request_id; ?>" />
                                                <input type="hidden" name="action" value="reject" />
                                                <button type="submit" class="px-4 py-2 text-red-600 no-underline">رد کردن</button>
                                            </form>
                                            <form class="text-center" method="POST" action="user_books.php">
                                                <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>" />
                                                <button type="submit" class="px-4 py-2 text-blue-600 no-underline">نمایش لیست</button>
                                            </form>
                                        </div>
                                    </div>


                                    <?php if ($status === 'approved') { ?>
                                        <p class="text-xs text-green-500">#تاییدشده</p>
                                    <?php } ?>
                                </div>
                                <div class="flex flex-col justify-between sm:flex-row-reverse items-center px-3 pb-3">
                                    <div class="flex -space-x-4 rtl:space-x-reverse justify-center sm:justify-start w-full sm:w-1/4 mb-4 sm:mb-0">
                                        <p class="mb-0 flex items-center justify-center w-16 h-16 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800">+<?php echo htmlspecialchars($text_count); ?></p>
                                        <p class="mb-0 flex items-center justify-center w-16 h-16 text-xs font-medium text-white bg-gray-700 border-2 border-white rounded-full hover:bg-gray-600 dark:border-gray-800">+<?php echo $book_count; ?></p>
                                    </div>
                                    <div class="text-white text-center sm:text-left sm:mb-0">
                                        <p class="text-lg font-semibold"><?php echo $fullname; ?></p>
                                        <p class="text-sm">تعداد کتاب‌ها: <span><?php echo $book_count; ?></span></p>
                                        <p class="text-sm">تعداد داستان‌ها: <span><?php echo htmlspecialchars($text_count); ?></span></p>
                                        <p class="text-xs text-gray-400"><?php echo date("Y/m/d", strtotime($created_at)); ?></p>
                                    </div>
                                </div>
                            </li>
                            <?php
                                }
                            } else {
                                echo "<p class='text-white'>هیچ درخواستی برای نمایش وجود ندارد.</p>";
                            }
                            ?>
                        </ul>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <script src="../panl/src/js.js"></script>
    <?php include '../views/pfooter.php'; ?>
</body>
