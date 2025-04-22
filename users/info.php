<?php
session_start();
include '../bin/get_user.php';
include '../bin/db.php';  


$userId = $_SESSION['user_id'];

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // گزارش خطاهای MySQL
try {

    $conn = new mysqli($servername, $username, $password, $dbname);
    

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    

    
    if (!$user) {
        throw new Exception("User not found.");
    }

} catch (Exception $e) {
    // مدیریت خطاها
    $_SESSION['db_error'] = "خطا در اتصال به پایگاه داده: " . $e->getMessage();
    header("Location: error.php"); 
    exit();
}
?>

<?php
include '../views/pheader.php';
?>

<body class="bgg-body my-2">
    <section class="container-lg h-full">
        <div class="h-full flex justify-between flex-row-reverse ">

            <?php include '../views/pnav.php'; ?>

            <div class="w-full lg:w-9/12 md:px-4 lg:px-4 sm:px-0">
                <main class="sm:border-0 md:border-2 lg:border-2 border-[#1E2734] rounded-3xl lg:h-full md:h-full sm:h-auto flex flex-col w-full lg:w-11/12 mx-auto">

                    <?php

                        if (isset($_SESSION['db_success'])) {
                            echo "<div class='text-center text-green-500 bg-green-300/10 mx-4 rounded-2xl p-2' id='success-alert' '>" . htmlspecialchars($_SESSION['db_success']) . "</div>";
                            unset($_SESSION['db_success']);
                        }

                        if (isset($_SESSION['db_error'])) {
                            echo "<div class='text-center text-red-500 bg-red-300/10 mx-4 rounded-2xl p-2' id='error-alert'  '>" . htmlspecialchars($_SESSION['db_error']) . "</div>";
                            unset($_SESSION['db_error']);
                        }
                    ?>

                    <div class="flex flex-row justify-center my-3">
                        <?php if ($user['gender'] == 'male'): ?>
                            <img class="w-20 h-20 rounded-full " src="../assets/images/icon/men.jpg" alt="">
                        <?php elseif ($user['gender'] == 'female'): ?>
                            <img class="rounded-full w-20 h-20 " src="../assets/images/icon/women.jpg" alt="">
                        <?php else: ?>
                            <i class="bi bi-person-circle text-4xl text-gray-500"></i>
                        <?php endif; ?>
                    </div>

                    <!-- Form Section -->
                    <div class="px-4" dir="rtl">
                    <form class="max-w-md mx-auto" style="font-family: vazir;" method="POST" action="update_user.php">
                        <!-- Full Name Input -->
                        <div class="relative z-0 w-full mb-5 group">
                            <input type="text" name="fullname" id="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>"
                                class="block py-2.5 px-0 w-full text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                placeholder=" " required />
                            <label for="fullname" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                نام کامل
                            </label>
                            <div class="w-full h-[1px] bg-slate-600"></div>
                        </div>

                        <!-- Name and Email Inputs -->
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <!-- Username -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']); ?>"
                                    class="block py-2.5 px-0 w-full text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="username" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    نام کاربری <span class="text-red-400 text-[5px]">انگلیسی</span>
                                </label>
                                <div class="w-full h-[1px] bg-slate-600"></div>
                            </div>

                            <!-- Email Input -->
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>"
                                    class="block py-2.5 px-0 w-full text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " required />
                                <label for="email" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    ایمیل
                                </label>
                                <div class="w-full h-[1px] bg-slate-600"></div>
                            </div>
                        </div>

                        <!-- Password and Repeat Password Inputs -->
                        <div class="grid md:grid-cols-2 md:gap-6">
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="password" name="password" id="password" class="block py-2.5 px-0 w-full text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    رمز ورود
                                </label>
                                <div class="w-full h-[1px] bg-slate-600"></div>
                            </div>
                            <div class="relative z-0 w-full mb-5 group">
                                <input type="password" name="repeat_password" id="repeat_password" class="block py-2.5 px-0 w-full text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer"
                                    placeholder=" " />
                                <label for="repeat_password" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">
                                    تکرار رمز ورود
                                </label>
                                <div class="w-full h-[1px] bg-slate-600"></div>
                            </div>
                        </div>
                            <!-- Gender Selection (Radio Buttons) -->
                            <div class="flex-row flex justify-center text-white">
                                <div class="flex items-center mr-4">
                                    <input <?php if ($user['gender'] == 'male') echo 'checked'; ?> id="male" type="radio" value="male" name="gender"
                                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="male" class="ms-2 text-lg dark:text-gray-300"><i class="bi bi-person-standing"></i></label>
                                </div>
                                <div class="flex items-center">
                                    <input <?php if ($user['gender'] == 'female') echo 'checked'; ?> id="female" type="radio" value="female" name="gender"
                                        class="w-4 h-4 text-pink-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="female" class="ms-2 text-lg dark:text-gray-300"><i class="bi bi-person-standing-dress"></i></label>
                                </div>
                            </div>

                                <!-- Submit Button -->
                                <div class="flex justify-center mt-4">
                                    
                                    <button type="submit"
                                        class="text-white bg-gray-500 transition hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                        ثبت اطلاعات
                                    </button>
                                </div>
                            </form>
                    </div>
                </main>
            </div>
        </div>
    </section>



    <script>
// جاوا اسکریپت برای محو کردن پیام‌ها بعد از 2 ثانیه
setTimeout(function() {
    var successAlert = document.getElementById('success-alert');
    if (successAlert) {
        successAlert.style.display = 'none'; 
    }
    
    var errorAlert = document.getElementById('error-alert');
    if (errorAlert) {
        errorAlert.style.display = 'none';
    }
}, 2000); 
</script>

    <?php
        include '../views/mesg.php';
    include '../views/pfooter.php'
    ?>


