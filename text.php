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


$query = "
    SELECT 
        u.id AS user_id, u.fullname, u.username, u.email, u.gender,
        s.text_title, s.story, s.created_at
    FROM 
        users u
    INNER JOIN 
        stories s ON u.id = s.user_id
    INNER JOIN 
        subscription_requests sr ON u.id = sr.user_id
    WHERE 
        sr.status = 'approved'  -- فقط کاربران با وضعیت approved نمایش داده شوند
    ORDER BY 
        u.id, s.created_at DESC
";

$result = $conn->query($query);

// بررسی وجود داده
if ($result->num_rows > 0) {
    $usersAndStories = [];
    while ($data = $result->fetch_assoc()) {
        $usersAndStories[$data['user_id']][] = $data; 
    }
} else {
    $usersAndStories = [];
}

include ('views/header.php');
?>

<body class="min-h-screen bg-[#0D141D] text-[#F3F4F6]">
    <div class="container mx-auto p-6" dir="rtl">

        <div class="grid grid-cols-1 md:grid-cols-1 lg:grid-cols-2 gap-6">
            <?php if (!empty($usersAndStories)): ?>
                <?php foreach ($usersAndStories as $userId => $stories): ?>
                    <div class="bg-[#1F2937] rounded-lg shadow-md px-3 py-2">

                        <div class="flex justify-between">

                            <div class="flex">
                                <div class="flex items-center space-x-4">
                                    <a href="profile.php?user_id=<?php echo htmlspecialchars($userId); ?>&username=<?php echo htmlspecialchars($stories[0]['username']); ?>">
                                        <?php if ($stories[0]['gender'] == 'male'): ?>
                                            <img class="w-16 h-16 rounded-full" src="assets/images/icon/men.jpg" alt="User Male Icon">
                                        <?php elseif ($stories[0]['gender'] == 'female'): ?>
                                            <img class="w-16 h-16 rounded-full" src="assets/images/icon/women.jpg" alt="User Female Icon">
                                        <?php else: ?>
                                            <img class="w-16 h-16 rounded-full" src="https://via.placeholder.com/150" alt="User Profile">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="px-2 mt-2">
                                    <h2 class="text-sm"><?php echo htmlspecialchars($stories[0]['username']); ?></h2>
                                    <p class="font-smer mb-0 text-gray-400">
                                        <?php
                                        $now = new DateTime();
                                        $storyDate = new DateTime($stories[0]['created_at']);
                                        $interval = $now->diff($storyDate);

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
                                        }
                                        ?>
                                    </p>
                                </div>
                            </div>


                        </div>

                        <div class="mt-2 text-sm text-gray-300 h-60   ">
                            <div class="splide mt-4 " id="splide-<?php echo $userId; ?>">
                                <div class="splide__track">
                                    <ul class="splide__list">
                                        <?php foreach ($stories as $story): ?>
                                            <li class="splide__slide bg-[#0D141D]  your-text-body h-36 overflow-y-auto rounded-lg p-4">
                                                <div class="flex justify-between items-center ">
                                                <h3 class=" text-xs mb-0 text-gray-500"><span>عنوان</span>:<?php echo htmlspecialchars($story['text_title']); ?></h3>
                                                <p class="text-gray-500 mt-3 text-xs mb-0"><?php echo $story['created_at']; ?></p>
                                                </div>

                                                <p class="mb-2  "><?php echo nl2br(htmlspecialchars($story['story'])); ?></p>

                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center text-gray-400 mt-4">متنی برای نمایش وجود ندارد.</p>
            <?php endif; ?>
        </div>
    </div>

    <?php include 'views/pages.php'; ?>

    <script>

            document.querySelectorAll('.splide').forEach(function (splideElement) {
                var splide = new Splide(splideElement, {
                    direction: 'ttb', 
                    height   : '12rem', 
                    gap      : '1rem',
                    perPage  : 1,
                    perMove  : 1,
                    pagination: false, 
                    arrows   : true, 
                    autoplay: true,  
                    interval: 9000  
                    
                });

                splide.mount(); 
            });

    </script>
</body>
</html>

</html>
