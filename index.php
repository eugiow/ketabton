<?php
include 'bin/classes.php'; 
include 'bin/db.php';  


if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

if (isset($_COOKIE['username']) && isset($_COOKIE['fullname']) && isset($_COOKIE['role'])) {
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['fullname'] = $_COOKIE['fullname'];
    $_SESSION['role'] = $_COOKIE['role'];
    $_SESSION['user_id'] = $_COOKIE['user_id'];
}


$query = "
    SELECT u.id,  u.fullname, u.username, COUNT(s.id) AS story_count
    FROM users u
    JOIN stories s ON s.user_id = u.id
    GROUP BY u.id
    ORDER BY story_count DESC
    LIMIT 3
";

include ('views/header.php');
?>

<body class="max-w-screen-2xl  mx-auto ">

<header class="mt-2">
            <div class="back-head mx-auto">
                
            <!-- Main Navigation -->
                <nav class="w-full h-16 flex justify-between items-center px-3 mb-12" aria-label="Navigation">
                    <?php if (isset($_SESSION['username'])): ?>
                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a href="admin/index.php" aria-label="پنل ادمین">
                                <i class="bi bi-gear-fill text-white  text-lg lg:text-2xl "></i>
                            </a>
                        <?php else: ?>
                            <a href="users/index.php" aria-label="پنل کاربری">
                                <i class="bi-columns-gap text-white text-lg lg:text-2xl "></i>
                            </a>
                        <?php endif; ?>
                    <?php else: ?>
                        <i class="bi bi-person text-white text-[2rem]" onclick="window.location.href='login/login.php'"></i>
                    <?php endif; ?>
                    <a href="index.php" aria-label="صفحه اصلی">
                        <img class="w-20" src="assets/images/icon/logotextw.png" alt="Logo">
                    </a>
                    <!-- drawer init and show -->
                    <span class="text-white  text-lg lg:text-2xl " style="font-size:30px;cursor:pointer" onclick="openNav()">&#9776;</span>
                </nav>

        
                <!-- Text Section -->
                <section class="flex flex-col text-center text-white txt-head font">
                    <h1 class="lg:text-2xl text-lg">میخوایی یک لیست از کتاب هایی که خوندی داشته باشی؟</h1>
                    <p class="m-0 text-sm">حتی میتونی نوشته ها یا شعر های خودتو بذاری تا دیده بشن</p>
                    <p class="text-sm">یا اینکه لیستت رو برای بقیه به نمایش بذاری؟</p>
                    <div>

                    <?php if (isset($_SESSION['username'])): ?>
                        <a href="users/add.php" class="no-underline p-2 mt-4 bg-[#E63946]  w-24 text-gray-200   rounded-xl text-center  text-xs " aria-label="ورود">ساخت لیست</a>
                    <?php else: ?>

                        <a href="login/login.php" class="no-underline p-2 mt-4 bg-[#E63946]  w-24 text-gray-200   rounded-xl text-center  text-xs " aria-label="ورود">ثبت نام</a>

                    <?php endif; ?>
                    

                    </div>
                </section>
        
                <!-- Image Slider Section -->
                <section class=" mt-[110px]">
                    <div id="slide-head" class="splide" aria-labelledby="carousel-heading">
                        <div class="splide__track">
                            <ul class="splide__list">
                                <li class="splide__slide slid-intro">
                                    <a href="#" aria-label="کتاب Lore of Aetherra">
                                        <img class=" w-64" src="assets/images/books/Lore of Aetherra.jpg" alt="کتاب Lore of Aetherra">
                                    </a>
                                </li>
                                <li class="splide__slide slid-intro">
                                    <a href="#" aria-label="کتاب The Riddle of the Sea">
                                        <img class=" w-64" src="assets/images/books/The Riddle of the Sea by Jonne Kramer.jpg" alt="کتاب The Riddle of the Sea">
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </section>
                
            </div>
        
            <div id="mySidenav" class="sidenav shadow-slate-700 shadow-2xl">
                <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
                <?php if (isset($_SESSION['username'])): ?>
                        <!-- بخش خوش‌آمدگویی -->
                         <div class="bg-sky-400/20 p-2 rounded-lg mb-4 mx-4 flex flex-row-reverse items-center ">

                            <div class="flex flex-col text-white text-sm font-semibold mr-2 text-right ">
                                <p class="m-0" ><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                                <p class="m-0">خوش اومدی</p>
                             </div>
                         </div>
                        <div class="text-sm sm:text-lg space-y-5 mr-5 text-right">


                        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                            <a  href="admin/index.php">پنل کاربری <i class="bi bi-collection px-1"></i></a>
                        <?php else: ?>
                            <a  href="users/index.php">پنل کاربری <i class="bi bi-collection px-1"></i></a>
                        <?php endif; ?>
                        <a href="login/logout.php">خروج <i class="bi bi-box-arrow-right px-1"></i></a>
                        </div>
                    <?php else: ?>
                        <div class="text-sm sm:text-lg space-y-5 mr-5 text-right">
                        <a href="login.php">ورود<i class="bi bi-box-arrow-in-right px-1"></i></a>

                        </div>
                    <?php endif; ?>
            </div>

</header>
        


<!-- author --> 

<section class="w-[97%] mx-auto author-back">
    <div class="mt-10 mb-4 text-white text-center font">
        <h2>بهترین نویسنده‌ها</h2>
    </div>


   <div class="flex justify-center flex-wrap p-3 space-y-2 sm:space-x-2">
    <?php

    $query = "
        SELECT u.id, u.fullname, u.username, u.gender, COUNT(s.id) AS story_count
        FROM users u
        JOIN stories s ON s.user_id = u.id
        GROUP BY u.id
        ORDER BY story_count DESC
        LIMIT 3
    ";
    $result = mysqli_query($conn, $query);

    // نمایش هر نویسنده
    while ($author = mysqli_fetch_assoc($result)) {
 
        echo '
        <article class="w-full sm:w-2/12 md:w-4/12 lg:w-2/12 h-full flex flex-col justify-between items-center p-4 bg-[#1E2734] rounded-[70px] mb-4 ">
            <div class="flex flex-col items-center justify-center mt-3">
                <!-- نمایش آیکون بر اساس جنسیت -->
                ';
                if ($author["gender"] == "male") {
                    echo '<img class="w-24 h-24 rounded-full" src="assets/images/icon/men.jpg" alt="User Male Icon">';
                } elseif ($author["gender"] == "female") {
                    echo '<img class="w-24 h-24 rounded-full" src="assets/images/icon/women.jpg" alt="User Female Icon">';
                } else {
                    echo '<i class="bi bi-person-circle text-7xl text-gray-500"></i>';
                }
        echo '
            
            <div class="text-center text-author flex flex-col items-center justify-between">
                <h2 class="text-lg m-0">' . htmlspecialchars($author['username']) . '</h2>
                <p class="mb-0 mt-3">این نویسنده <span>' . $author['story_count'] . '</span> داستان نوشته است.</p>
            </div>
            </div>
            <div class="btn-author mt-3">
              <a href="profile.php?user_id=' . htmlspecialchars($author["id"]) . '&username=' . htmlspecialchars($author["username"]) . '" class="text-blue-500 hover:text-blue-700">مشاهده پروفایل</a>
            </div>
        </article>';
    }
    ?>
</div>

</section>




</section>

<!-- baner -->

<section class="w-[100%] h-[32rem] cover-your-text flex sm:flex-col justify-center items-center mt-4 p-3 mb-5">
<div class="flex flex-row-reverse sm:flex-col justify-between">

        <div class=" w-2/12 sm:w-4/12 md:w-2/12" >
            <img class="w-64 z-50 " src="assets/images/img-site/vecteezy_book-icon-3d-render-accessories-for-learning-signs-of_35898562.png" alt="">
        </div>
         <div class="text-right font text-[#F5F5F5] w-10/12 sm:w-8/12 md:w-10/12" style="">
            <p class="mb-0 text-[#A8DADC] ">درباره ما</p>
            <h2>درباره کتابتون</h2>
            <p>سایت ما فضایی برای ذخیره‌سازی و اشتراک‌گذاری کتاب‌ها و داستان‌هاست. شما می‌توانید کتاب‌های مورد علاقه‌تان را ذخیره کنید، داستان‌های خود را بنویسید و با دیگر کاربران به اشتراک بگذارید. همچنین، مانند یک شبکه اجتماعی، می‌توانید کتاب‌ها و متن‌های دیگران را مشاهده، لایک و ذخیره کنید. هدف ما ایجاد ارتباطات جدید در دنیای ادبیات است تا نویسندگان و علاقه‌مندان به کتاب به راحتی با هم در ارتباط باشند و از دنیای خواندن و نوشتن لذت ببرند.</p>
         </div>
    </div>
</section>

<section class="mb-5">
    <div class="flex flex-col  bg-[#457B9D] p-3 font">
    <div class="flex flex-col md:flex-row-reverse justify-between p-3 text-right">
            <div class="flex flex-col justify-center ">
                <h2 class=" text-sm  text-[#A8DADC]">مقالات</h2>
                <p class="mb-0 text-lg text-[#F5F5F5]">جدیدترین مقالات</p>
            </div>
            <div class="flex flex-col justify-center ">
                <p class="mb-0 text-xs text-[#F5F5F5]  ">مقالات اموزشی یا تحقیاتی یا سرگرمی  رو درکتابتون مشاهده کنید</p>
                <a class="no-underline p-2 mt-4 bg-[#E63946]  w-24 text-gray-200   rounded-xl text-center  text-xs " href="#top">مشاهده همه</a>
            </div>
        </div>
        <div class="relative">
    <!-- پوشش تار و متن -->
    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center z-10 rounded-lg ">
        <p class="text-white font-bold text-3xl">به زودی</p>
    </div>

    <!-- اسلایدر -->
    <div id="splide" class="splide p-3 filter blur-sm ">
        <div class="splide__track">
            <ul class="splide__list text-center">
                <li class="splide__slide">
                    <img src="assets/images/books/The Riddle of the Sea by Jonne Kramer.jpg" class="p-2 w-64 h-44 mx-auto object-cover rounded-2xl" alt="...">
                    <p>مقاله شما</p>
                </li>
                <li class="splide__slide">
                    <img src="assets/images/books/ket.jpg" class="p-2 w-64 h-44 mx-auto object-cover rounded-2xl" alt="...">
                    <p>مقاله شما</p>
                </li>
                <li class="splide__slide">
                    <img src="assets/images/books/Lore of Aetherra.jpg" class="p-2 w-64 h-44 mx-auto object-cover rounded-2xl" alt="...">
                    <p>مقاله شما</p>
                </li>
                <li class="splide__slide">
                    <img src="assets/images/books/The Riddle of the Sea by Jonne Kramer.jpg" class="p-2 w-64 h-44 mx-auto object-cover rounded-2xl" alt="...">
                    <p>مقاله شما</p>
                </li>
                <li class="splide__slide">
                    <img src="assets/images/books/download (2).jpg" class="p-2 w-64 h-44 mx-auto object-cover rounded-2xl" alt="...">
                    <p>مقاله شما</p>
                </li>
            </ul>
        </div>
    </div>
</div>

</div>


    </div>
</section>


<script src="assets/js/js.js"></script>


<?php include 'views/footer.php';?>

<?php
include 'views/pages.php';
?>



</body>
</html>  
    

