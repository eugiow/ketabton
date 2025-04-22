<div class="fixed w-full bottom-3 left-0 h-14  ">
    <div  class="w-72  bg-gray-200/90 mx-auto  h-full rounded-2xl  flex items-center  shadow-lg  justify-between p-3" >

            <div>
                <a href="books.php" class="text-lg no-underline  text-gray-800  hover:text-gray-400  "><i class="bi bi-journals"></i></a>
            </div>
            <div>
                <a href="text.php" class="text-lg no-underline  text-gray-800  hover:text-gray-400  "><i class="bi bi-journal-text"></i></a>
            </div>
            <div>
                <a href="users/add.php" class="text-3xl no-underline text-sky-500 hover:text-sky-300 "><i class="bi bi-plus-circle"></i></a>
            </div>
            <?php if (isset($_SESSION['username'])): ?>
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <a href="admin/index.php" class="text-lg no-underline  text-gray-800  hover:text-gray-400  " aria-label="پنل ادمین" >
                <i class="bi bi-gear-fill  "></i>
            </a>
        <?php else: ?>
            <a href="users/index.php" class="text-lg no-underline  text-gray-800  hover:text-gray-400  " aria-label="پنل کاربری">
                <i class="bi-columns-gap   "></i>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <i class="bi bi-person text-2xl    text-gray-800  hover:text-gray-400" onclick="window.location.href='login/login.php'"></i>
    <?php endif; ?>
            <div>
                <a href="index.php" class="text-lg no-underline  text-gray-800  hover:text-gray-400  "><i class="bi bi-house"></i></a>
            </div>
    </div>
</div>