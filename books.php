<?php
include 'bin/classes.php';
include 'bin/db.php';  

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login/login.php");
    exit();
}

$userId = $_SESSION['user_id'];


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$isAdmin = ($_SESSION['role'] ?? '') === 'admin';

// بررسی اینکه آیا سشن‌ها و کوکی‌ها موجود هستند
if (isset($_COOKIE['username']) && isset($_COOKIE['fullname']) && isset($_COOKIE['role'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['fullname'] = $_COOKIE['fullname'];
    $_SESSION['role'] = $_COOKIE['role'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}

// دریافت کاربران با درخواست‌های تأیید شده که کتاب دارند
$sql = "
    SELECT 
        users.id AS user_id, 
        users.fullname, 
        users.username, 
        users.gender, 
        subscription_requests.created_at AS request_date, 
        GROUP_CONCAT(DISTINCT books.book_title SEPARATOR ', ') AS book_titles,
        GROUP_CONCAT(DISTINCT books.book_image SEPARATOR ', ') AS book_images
    FROM subscription_requests 
    JOIN users ON subscription_requests.user_id = users.id 
    LEFT JOIN books ON users.id = books.user_id 
    WHERE subscription_requests.status = 'approved'
    GROUP BY users.id
    HAVING COUNT(books.id) > 0  
    ORDER BY subscription_requests.created_at DESC
";

$result = $conn->query($sql);

include ('views/header.php');
?>

<body>
<div class="container flex">

    <div class="py-2 w-10/12 mx-auto flex flex-col items-center justify-center">
        <ul class="m-0 p-0 w-full ">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li class="mb-4 bg-[#1F2937]  rounded-2xl h-auto p-4 border-b border-gray-700">
                        <div class="flex flex-row-reverse justify-between pb-4">
                            <div class="flex flex-row-reverse">

                                <div class="flex items-center justify-center ">
                                    <?php if ($row['gender'] == 'male'): ?>
                                        <a href="profile.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>&username=<?php echo htmlspecialchars($row['username']); ?>">
                                            <img class="w-14 h-14 md:w-20 md:h-20 rounded-full" src="assets/images/icon/<?php echo $row['gender'] == 'male' ? 'men.jpg' : 'women.jpg'; ?>" alt="User Icon">
                                        </a>
                                    <?php elseif ($row['gender'] == 'female'): ?>
                                        <a href="profile.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>&username=<?php echo htmlspecialchars($row['username']); ?>">
                                            <img class="w-14 h-14 md:w-20 md:h-20 rounded-full" src="assets/images/icon/women.jpg" alt="User Female Icon">
                                        </a>
                                    <?php else: ?>
                                        <a href="profile.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>&username=<?php echo htmlspecialchars($row['username']); ?>">
                                            <i class="bi bi-person-circle text-7xl text-gray-500"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <div class="flex items-center px-2 text-white ">
                                    <div class="flex flex-row-reverse items-center">
                                        <p class="mb-0 font-lg"><?php echo htmlspecialchars($row['username']); ?></p>
                                        <span class="mx-1">.</span>
                                        <p class="mb-0 font-smer">
                                        <?php
                                            $now = new DateTime();
                                            $requestDate = new DateTime($row['request_date']);
                                            $interval = $now->diff($requestDate);

                                            if ($interval->y > 0) {
                                                echo $interval->y . ' سال پیش';
                                            } elseif ($interval->m > 0) {
                                                echo $interval->m . ' ماه پیش';
                                            } elseif ($interval->d > 0) {
                                                echo $interval->d . ' روز پیش';
                                            } elseif ($interval->h > 0) {
                                                echo $interval->h . ' ساعت پیش';
                                            } elseif ($interval->i > 0) {
                                                echo $interval->i . ' دقیقه پیش';
                                            } else {
                                                echo $interval->s . ' ثانیه پیش';
                                            }
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- قسمت لایک و ذخیره -->
                            <?php
                            include 'views/react.php';
                            ?>
                        </div>
                        
                        <!-- تصویر کتاب‌ها -->
                        <?php 
                        $bookTitles = explode(', ', $row['book_titles']);
                        $bookImages = explode(', ', $row['book_images']);
                        if (!empty($bookTitles[0])): ?>
                            <div>
                            <div id="splide<?php echo htmlspecialchars($row['user_id']); ?>" class="splide h-72">
                            <div class="splide__track">
                                        <ul class="splide__list">
                                            <?php foreach ($bookTitles as $index => $title): ?>
                                                <li class="splide__slide flex flex-col justify-center  ">
                                                    <?php if (!empty($bookImages[$index])): ?>
                                                        <img class="rounded-lg w-40 h-60 mx-auto" src="users/add/uploads/<?php echo htmlspecialchars($bookImages[$index]); ?>" alt="<?php echo htmlspecialchars($title); ?>">
                                                    <?php else: ?>
                                                        <div class="w-40 h-60 mx-auto flex items-center justify-center bg-gray-200 rounded-md font">
                                                            <p class="text-sm text-gray-500">بدون تصویر</p>
                                                        </div>
                                                    <?php endif; ?>
                                                    <p class="mb-0 text-center font text-white"><?php echo htmlspecialchars($title); ?></p>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
</div>

                            </div>
                        <?php endif; ?>
                    </li>
                <?php endwhile; ?>
            <?php else: ?>
                <p>هیچ درخواستی با وضعیت تأیید شده یافت نشد.</p>
            <?php endif; ?>
        </ul>
    </div>

</div>

<script src="assets/js/js.js"></script>
<script>

document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.splide').forEach(function (element) {
            new Splide(element, {
                perPage: 4,
                gap: '1rem',
                pagination: false,
                arrows: true,
                breakpoints: {
                768: {
                    perPage: 2,   
                },
                480: {
                    perPage: 1,  
                    arrows:true,
                },
                1024: {
                    perPage: 3,
                },
                1200: {
                    perPage: 4, 
                },
            },

            }).mount();
        });
    });



    

    document.addEventListener('DOMContentLoaded', function () {
    // دکمه‌های لایک
    document.querySelectorAll('.reaction-like').forEach(function (likeButton) {
        likeButton.addEventListener('click', function () {
            let itemId = this.getAttribute('data-item-id');
            let itemType = this.getAttribute('data-item-type');
            
            fetch('bin/like_save_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'like',
                    user_id: <?php echo $userId; ?>,
                    item_id: itemId,
                    item_type: itemType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.querySelector('.like-count').textContent = data.new_like_count;
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });

    // دکمه‌های ذخیره
    document.querySelectorAll('.reaction-save').forEach(function (saveButton) {
        saveButton.addEventListener('click', function () {
            let itemId = this.getAttribute('data-item-id');
            let itemType = this.getAttribute('data-item-type');
            
            fetch('bin/like_save_handler.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'save',
                    user_id: <?php echo $userId; ?>,
                    item_id: itemId,
                    item_type: itemType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.querySelector('.save-count').textContent = data.new_save_count;
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});
</script>


<?php
include 'views/pages.php';
?>
</body>
</html>
