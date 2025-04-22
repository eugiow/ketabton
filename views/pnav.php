<?php 
include '../bin/get_user.php';
include '../bin/db.php'; 

if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}
$userId = $_SESSION['user_id'];

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$isAdmin = ($_SESSION['role'] ?? '') === 'admin';


?>

<div id="menuRight" class="lg:w-4/12 md:w-4/12 menu-right-b  lg:rounded-2xl md:rounded-2xl px-4 fixed top-0 right-0 lg:relative  lg:block transform lg:translate-x-0 translate-x-full transition-transform duration-300">
    <button type="button" id="closeMenu" class="lg:hidden sm:hidden sm:show text-gray-300 hover:text-gray-500 mt-2">
        <i class="bi bi-x-lg"></i>
    </button>
    <!-- پروفایل -->
    <div class="flex items-center justify-center mt-3">

        <?php if ($user['gender'] == 'male' ): ?>
            <img class="w-24 h-24 rounded-full " src="../assets/images/icon/men.jpg" alt="User Male Icon">
        <?php elseif ($user['gender'] == 'female'): ?>
            <img class="w-24 h-24 rounded-full " src="../assets/images/icon/women.jpg" alt="User Female Icon">
        <?php else: ?>
            <i class="bi bi-person-circle text-7xl text-gray-500"></i>
        <?php endif; ?>
    </div>

    <div class="flex flex-row text-white justify-center py-2 ">
        <p class="uppercase font-bold mb-0"><?php echo  htmlspecialchars($_SESSION['username']); ?></p>
        <a class="mt-1 text-[#7fffd4] ml-1 hover:text-[#639886]" href="info.php"><i class="bi bi-pencil-square"></i></a>
    </div>
    
    <div class="w-28 h-[1px] rounded-2xl bg-white mx-auto mb-3"></div>

    <div class="text-right">
        <ul class="flex flex-col menu-right ">
           <li class="py-2.5 rounded-lg mb-2">
                <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center  " href="../index.php">
                    خانه
                    <i class="bi bi-house-fill ml-2 hidden"></i>
                </a>
            </li>
            <li class="py-2.5 rounded-lg mb-2">
                <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center  " href="index.php">
                    داشبورد
                    <i class="bi bi-person-lines-fill ml-2 hidden "></i>
                </a>
            </li>

            <?php if ($isAdmin): ?>
                <!-- گزینه‌های مخصوص ادمین -->
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center" href="mguser.php">
                        مدیریت کاربران
                        <i class="bi bi-people-fill ml-2 hidden"></i>
                    </a>
                </li>
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center" href="mesuser.php">
                        پیام ها 
                        <i class="bi bi-gear-fill ml-2 hidden"></i>
                    </a>
                </li>
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center" href="req.php">
                         درخواست ها
                        <i class="bi bi-gear-fill ml-2 hidden"></i>
                    </a>
                </li>
            <?php else: ?>
                <!-- گزینه‌های مخصوص کاربر عادی -->
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center " href="add.php">
                        افزودن به لیست
                        <i class="bi bi-plus-square-fill ml-2 hidden "></i>
                    </a>
                </li>
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center  " href="info.php">
                    اطلاعات
                        <i class="bi bi-person-lines-fill ml-2 hidden "></i>
                    </a>
                </li>
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center" href="list.php">
                        لیست‌ها
                        <i class="bi bi-view-list ml-2 hidden "></i>
                    </a>
                </li>
                <li class="py-2.5 rounded-lg mb-2">
                    <a class="menu-item text-gray-400 hover:text-[#7fffd4] flex items-center" href="save.php">
                        ذخیره شده‌ها
                        <i class="bi bi-bookmark ml-2 hidden "></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
        
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const menuItems = document.querySelectorAll('.menu-item');
    const closeMenuButton = document.getElementById('closeMenu');
    const menuRight = document.getElementById('menuRight');

    // بازیابی منوی انتخاب‌شده از Local Storage
    const savedMenu = localStorage.getItem('selectedMenu');
    if (savedMenu) {
        menuItems.forEach((el) => {
            el.classList.remove('text-[#7fffd4]', 'font-bold');
            el.classList.add('text-gray-400');
            const icon = el.querySelector('i');
            if (icon) icon.classList.add('hidden');
        });

        const selectedMenu = document.querySelector(`.menu-item[href="${savedMenu}"]`);
        if (selectedMenu) {
            selectedMenu.classList.add('text-[#7fffd4]', 'font-bold');
            selectedMenu.classList.remove('text-gray-400');
            const icon = selectedMenu.querySelector('i');
            if (icon) icon.classList.remove('hidden');
        }
    }

    // مدیریت انتخاب آیتم‌ها
    menuItems.forEach((item) => {
        item.addEventListener('click', (e) => {
            const href = item.getAttribute('href');
            if (href === '#') {
                e.preventDefault();
            } else {
                // ذخیره لینک منوی انتخاب‌شده در Local Storage
                localStorage.setItem('selectedMenu', href);
            }

            menuItems.forEach((el) => {
                el.classList.remove('text-[#7fffd4]', 'font-bold');
                el.classList.add('text-gray-400');
                const icon = el.querySelector('i');
                if (icon) icon.classList.add('hidden');
            });

            item.classList.add('text-[#7fffd4]', 'font-bold');
            item.classList.remove('text-gray-400');
            const icon = item.querySelector('i');
            if (icon) icon.classList.remove('hidden');
        });
    });

    // مدیریت بسته شدن منو
    closeMenuButton.addEventListener('click', () => {
        menuRight.classList.add('translate-x-full');
    });
});
</script>
