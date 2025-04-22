<?php
include '../bin/db.php';

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


$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';


$query = "SELECT ur.item_id, ur.item_type, ur.reaction_type, b.id AS book_id, b.book_title, b.author_name, b.book_image, s.id AS story_id, s.text_title, s.story, u.id AS user_id, u.fullname
          FROM user_reactions ur
          LEFT JOIN books b ON ur.item_id = b.id AND ur.item_type = 'book'
          LEFT JOIN stories s ON ur.item_id = s.id AND ur.item_type = 'story'
          LEFT JOIN users u ON ur.user_id = u.id
          WHERE ur.user_id = ? AND ur.reaction_type = 'save'";


if ($filter === 'books') {
    $query .= " AND ur.item_type = 'book'";
} elseif ($filter === 'stories') {
    $query .= " AND ur.item_type = 'story'";
}


if ($search) {
    $query .= " AND (b.book_title LIKE ? OR s.text_title LIKE ?)";
}


$stmt = $conn->prepare($query);


if ($search) {
    $searchTerm = "%" . $search . "%";
    $stmt->bind_param('iss', $userId, $searchTerm, $searchTerm); 
} else {
    $stmt->bind_param('i', $userId); 
}

$stmt->execute();
$result = $stmt->get_result();

// بررسی داده‌ها
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<pre>";
        print_r($row); 
        echo "</pre>";
    }
} else {
    echo "هیچ داده‌ای یافت نشد.";
}
?>

<?php include '../views/pheader.php'; ?>

<body class="bgg-body my-2">
    
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse">
            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col justify-between w-full lg:w-11/12 mx-auto">
                    
                    <!-- بخش فیلتر -->
                    <div class="px-2 mt-2" dir="rtl">
                        <section class="p-2 bg-[#1E2734] rounded-2xl font border-1 border-blue-800">
                            <form method="GET" class="flex flex-row flex-wrap justify-between items-center mb-0 space-y-1 " id="filterForm">
                                <div class="flex">
                                    <div>
                                        <select name="filter"
                                                class="text-gray-500 text-sm bg-white border border-gray-300 rounded-lg px-3 py-1.5 text-right w-full sm:w-auto shadow-md"
                                                onchange="document.getElementById('filterForm').submit();">
                                            <option style="font-size: smaller;" value="">همه</option>
                                            <option style="font-size: smaller;" value="books" <?= isset($_GET['filter']) && $_GET['filter'] === 'books' ? 'selected' : '' ?>>کتاب‌ها</option>
                                            <option style="font-size: smaller;" value="stories" <?= isset($_GET['filter']) && $_GET['filter'] === 'stories' ? 'selected' : '' ?>>متن‌ها</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex">
                                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="جستجو..." style="border-radius:0 20px 20px 0;" class="w-full py-2 pl-10 pr-4 border border-gray-300 focus:outline-none"/>
                                    <button type="submit" style="border-radius: 20px 0 0 20px;  background-color: #4e9b81;" class="text-white bg-blue-500 px-4 py-2 rounded-lg">
                                        اعمال
                                    </button>
                                </div>
                            </form>
                        </section>
                    </div>

                    <!-- نمایش کتاب‌ها و متن‌های سیو شده -->
                    <section class="flex flex-col pr-7 py-3 overflow-y-auto list-sc font">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php if ($row['item_type'] == 'book' && !empty($row['book_title'])): ?>
                                    <div class="mb-4">
                                        <div class="flex space-x-4">
                                            <img src="../users/add/uploads/<?= htmlspecialchars($row['book_image']) ?>" alt="book image" class="w-24 h-32 object-cover">
                                            <div>
                                                <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['book_title']) ?></h3>
                                                <p class="text-sm"><?= htmlspecialchars($row['author_name']) ?></p>
                                                <p class="text-xs text-gray-500">Book ID: <?= htmlspecialchars($row['book_id']) ?></p>
                                            </div>
                                        </div>
                                    </div>
                                <?php elseif ($row['item_type'] == 'story' && !empty($row['text_title'])): ?>
                                    <div class="mb-4">
                                        <h3 class="text-lg font-semibold"><?= htmlspecialchars($row['text_title']) ?></h3>
                                        <p class="text-sm"><?= substr(htmlspecialchars($row['story']), 0, 150) . '...' ?></p>
                                        <p class="text-xs text-gray-500">Story ID: <?= htmlspecialchars($row['story_id']) ?></p>
                                    </div>
                                <?php endif; ?>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500">هیچ کتاب یا متنی سیو نشده است.</p>
                        <?php endif; ?>
                    </section>
                </main>
            </div>
        </div>
    </section>

    <?php
        include '../views/mesg.php'; 
    include '../views/pfooter.php'; ?>
    
    <!-- اضافه کردن اسکریپت SplideJS -->
    <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.7/dist/js/splide.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new Splide('.splide', {
                type       : 'loop',
                perPage    : 3,
                gap        : '1rem',
                pagination: false,
                arrows     : true,
                breakpoints: {
                    1024: { perPage: 2 },
                    600: { perPage: 1 },
                },
            }).mount();
        });
    </script>
</body>
</html>
