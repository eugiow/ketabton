<?php
include 'bin/db.php';
include 'bin/get_user.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}


if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // گرفتن اطلاعات کاربر
    $query = "SELECT * FROM users WHERE id = ?"; 
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id); 
    $stmt->execute();
    $result = $stmt->get_result();

    // بررسی وجود کاربر
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        echo "کاربری با این مشخصات یافت نشد.";
        exit;
    }
} else {
    echo "مشخصات کاربر ارسال نشده است.";
    exit;
}

// دریافت اطلاعات کتاب‌ها برای این کاربر
$booksQuery = "SELECT book_title, book_image FROM books WHERE user_id = ?";
$booksStmt = $conn->prepare($booksQuery);
$booksStmt->bind_param("i", $user['id']);
$booksStmt->execute();
$booksResult = $booksStmt->get_result();

// ذخیره کتاب‌ها در آرایه‌ای برای نمایش
$books = [];
while ($book = $booksResult->fetch_assoc()) {
    $books[] = $book;
}

// کوئری برای دریافت داستان‌های کاربر
$storiesQuery = "SELECT text_title, story FROM stories WHERE user_id = ?";
$storiesStmt = $conn->prepare($storiesQuery);
$storiesStmt->bind_param("i", $user['id']);
$storiesStmt->execute();
$storiesResult = $storiesStmt->get_result();

// ذخیره داستان‌ها در آرایه‌ای برای نمایش
$stories = [];
while ($story = $storiesResult->fetch_assoc()) {
    $stories[] = $story;
}



// تعداد کتاب‌ها
$booksCountQuery = "SELECT COUNT(*) AS book_count FROM books WHERE user_id = ?";
$booksStmt = $conn->prepare($booksCountQuery);
$booksStmt->bind_param("i", $user['id']);
$booksStmt->execute();
$booksCountResult = $booksStmt->get_result();
$booksCount = $booksCountResult->fetch_assoc()['book_count'];

// تعداد داستان‌ها
$storiesCountQuery = "SELECT COUNT(*) AS story_count FROM stories WHERE user_id = ?";
$storiesStmt = $conn->prepare($storiesCountQuery);
$storiesStmt->bind_param("i", $user['id']);
$storiesStmt->execute();
$storiesCountResult = $storiesStmt->get_result();
$storiesCount = $storiesCountResult->fetch_assoc()['story_count'];

// محاسبه مدت زمان عضویت
$registrationDate = new DateTime($user['created_at']);
$currentDate = new DateTime();
$membershipDuration = $registrationDate->diff($currentDate);
$daysOfMembership = $membershipDuration->days;


// دریافت تعداد لایک‌ها برای کاربر
$likesQuery = "SELECT COUNT(*) AS like_count FROM user_reactions WHERE item_id = ? AND reaction_type = 'like'";
$likesStmt = $conn->prepare($likesQuery);
$likesStmt->bind_param("i", $user['id']);
$likesStmt->execute();
$likesResult = $likesStmt->get_result();
$likeCount = $likesResult->fetch_assoc()['like_count'];


// دریافت تعداد ذخیره‌ها برای کاربر
$savesQuery = "SELECT COUNT(*) AS save_count FROM user_reactions WHERE user_id = ? AND reaction_type = 'save'";
$savesStmt = $conn->prepare($savesQuery);
$savesStmt->bind_param("i", $user['id']);
$savesStmt->execute();
$savesResult = $savesStmt->get_result();
$saveCount = $savesResult->fetch_assoc()['save_count'];


include ('views/header.php');
?>



<body class="py-4 container font">
    <section class="mt-3">
        <div class="flex justify-center  w-full h-14 mt-3">
    <!-- پروفایل -->
    <?php if ($user['gender'] == 'male' ): ?>
            <div class="flex flex-row-reverse items-center justify-center  w-full rounded-2xl  bg-[#87D1F8]  ">
            <?php else: ?>
                <div class="flex flex-row-reverse items-center justify-center  w-full rounded-2xl  bg-[#F7BDD3]  ">
            <?php endif; ?>
                <div class="basis-1/3 flex items-center justify-center ">
                        <p class="mb-0 text-slate-700  font-bold font  "><?php echo htmlspecialchars($user['username']); ?></p>
                </div>

                <div class="basis-1/3 flex items-center justify-center ">
                        <?php if ($user['gender'] == 'male' ): ?>
                            <img class="w-24 h-24 rounded-full " src="assets/images/icon/men.jpg" alt="User Male Icon">
                        <?php elseif ($user['gender'] == 'female'): ?>
                            <img class="w-24 h-24 rounded-full " src="assets/images/icon/women.jpg" alt="User Female Icon">
                        <?php else: ?>
                            <i class="bi bi-person-circle text-7xl text-gray-500"></i>
                        <?php endif; ?>
                </div>

<div class="basis-1/3 space-x-3 flex items-center justify-center">
    <div class="flex items-center">
        <p class="mb-0 mr-1"><?php echo $likeCount; ?></p>
        <i class="bi bi-heart-fill mb-0"></i>
    </div>
    <div class="flex items-center">
        <p class="mb-0 mr-1"><?php echo $saveCount; ?></p>
        <i class="bi bi-bookmark-fill mb-0"></i>
    </div>
</div>

            </div>
        </div>
    </section>

    <section class="flex justify-between mt-16 px-3 py-2 bg-slate-800 rounded-2xl text-slate-300 border-2 <?php echo $user['gender'] == 'male' ? 'border-[#87D1F8]' : 'border-[#F7BDD3]'; ?> text-sm lg:text-lg">
        <div class="w-1/3 flex flex-col justify-center items-center">
            <p class="mb-0">تعداد کتاب‌ها</p>
            <p class="mb-0"><?php echo $booksCount; ?></p>
        </div>
        <div class="w-1/3 flex flex-col justify-center items-center">
            <p class="mb-0">تعداد متن‌ها</p>
            <p class="mb-0"><?php echo $storiesCount; ?></p>
        </div>
        <div class="w-1/3 flex flex-col justify-center items-center">
            <p class="mb-0">روزهای عضویت</p>
            <p class="mb-0"><?php echo $daysOfMembership; ?></p>
        </div>
    </section>

    <section class="mt-2 h-80  ">
    <div class="flex justify-between  space-x-3 bg-slate-800 rounded-2xl text-white px-3 py-2">

            <div class="flex items-center">
            <i class="bi bi-book font-bold text-lg   mr-2"></i>
            <p class="mb-0"><?php echo $booksCount; ?></p>
            </div>
            <div>
                <p class="mb-0 font-bold  ">کتاب ها</p>
            </div>
     </div>
    <?php if (!empty($books)): ?>
        <div id="splide" class="splide mt-2 ">
            <div class="splide__track bg-slate-800 rounded-2xl p-2">
                <ul class="splide__list  ">
                    <?php foreach ($books as $book): ?>
                        <li class="splide__slide ">
                            <div class="h-full flex flex-col justify-between items-center ">
                                <?php if (!empty($book['book_image'])): ?>
                                    <img class="w-40 h-60  rounded-md" src="users/add/uploads/<?php echo htmlspecialchars($book['book_image']); ?>" alt="<?php echo htmlspecialchars($book['book_title']); ?>">
                                <?php else: ?>
                                    <div class="w-32 h-32 flex items-center justify-center bg-gray-200 rounded-md">
                                        <p class="text-sm text-gray-500">بدون تصویر</p>
                                    </div>
                                <?php endif; ?>
                                <h3 class="mt-2 text-lg text-white  "><?php echo htmlspecialchars($book['book_title']); ?></h3>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-gray-500 mt-4">این کاربر هنوز کتابی ثبت نکرده است.</p>
    <?php endif; ?>
</section>


  <!-- بخش داستان‌ها -->
  <section class="mt-28  h-80">
  <div class="flex justify-between  space-x-3 bg-slate-800 rounded-2xl text-white p-2">
  <div class="flex items-center">
            <i class="i bi-fonts font-bold text-lg    mr-2"></i>
            <p class="mb-0"><?php echo $storiesCount; ?></p>
            </div>  
            
            <div>
                <p class="mb-0 font-bold  ">متن ها</p>
            </div>
     </div>
        <?php if (!empty($stories)): ?>
            <div id="splidee" class="splide mt-2">
                <div class="splide__track">
                    <ul class="splide__list ">
                        <?php foreach ($stories as $story): ?>
                            <li class="splide__slide flex items-center justify-center  ">
                                <div class="py-2 px-3 bg-slate-800 rounded-lg shadow-md  h-72 w-full   flex flex-col  ">
                                    <h3 class="text-xl text-gray-300 font-semibold mb-2 text-center"><?php echo htmlspecialchars($story['text_title']); ?></h3>
                                    <div class="w-32 bg-slate-200   h-0.5 mx-auto   "></div>
                                    <div class=" your-text-body overflow-y-auto p-2">
                                    <p class="text-gray-400 text-right text-sm "><?php echo nl2br(htmlspecialchars($story['story'])); ?></p>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <p class="text-center text-gray-500 mt-4">این کاربر هنوز داستانی ثبت نکرده است.</p>
        <?php endif; ?>
    </section>




<script>
    document.addEventListener('DOMContentLoaded', function () {
        new Splide('#splidee', {
            perPage: 4,   
            perMove: 1,  
            gap: '1rem',
            breakpoints: {
                1200: { perPage: 2 }, 
                1024: { perPage: 2 }, 
                768: { perPage: 1 }, 
                480: { perPage: 1 },  
            },
            pagination: false, 
            arrows: true,      
        }).mount();
    });
</script>



<script>
        document.addEventListener('DOMContentLoaded', function () {
            // اسپلید کتاب‌ها
            new Splide('#splide', {
                perPage: 4,
                perMove: 1,
                breakpoints: {
                    1024: { perPage: 3 },
                    768: { perPage: 2 },
                    480: { perPage: 1 },
                },
                pagination: false,
                arrows: true,
            }).mount();
        });
    </script>

</body>
</html>
