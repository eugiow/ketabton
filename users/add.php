<?php
session_start();

include '../bin/db.php';

$userId = $_SESSION['user_id'];

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
                    
                    <div x-data="{ openBookForm: true, openStoryForm: false }" dir="rtl" class="text-font-sm">
                        <div class="flex flex-row mt-2 mb-5 mx-2 h-10">
                            <!-- Button to Toggle Book Form -->
                            <button @click="openBookForm = !openBookForm; openStoryForm = false" class="w-2/4 bg-[#1E2734] text-white rounded-s-2xl hover:bg-slate-700 focus:outline-none focus:bg-[#25ABC9]">
                                اضافه کردن کتاب
                            </button>
                            <div class="h-4 w-[2px] rounded-full my-auto bg-white"></div>
                            <!-- Button to Toggle Story Form -->
                            <button @click="openStoryForm = !openStoryForm; openBookForm = false" class="w-2/4 bg-[#1E2734] text-white rounded-e-2xl hover:bg-slate-700 focus:outline-none focus:bg-[#25ABC9]">
                                اضافه کردن متن
                            </button>
                        </div>

                        <!-- Book Form (Add Book) -->
                        <div x-show="openBookForm" x-transition.duration.300ms>
                            <div class="px-4 flex items-center justify-center">
                                <!-- Form for Adding Book -->



                                <form class="w-full flex flex-col items-center" action="add/addBook.php" method="POST" enctype="multipart/form-data">
                                    <!-- Image Upload Field -->
                                    <div class="flex flex-col items-center mb-4">
                                        <label for="dropzone-file" class="flex flex-col items-center justify-center w-20 h-20 border-2 border-gray-300 border-dashed rounded-full cursor-pointer bg-gray-50 dark:hover:bg-gray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500">
                                            <div class="text-center text-font-sm">
                                                <p class="m-0">آپلود تصویر</p>
                                            </div>
                                            <input id="dropzone-file" type="file" name="book_image" class="hidden" />
                                        </label>
                                        <img id="imagePreview" src="" alt="Preview" class="mt-2 w-24 h-24 rounded-full object-cover hidden">
                                    </div>

                                    <!-- Input for Book Title -->
                                    <div class="relative z-0 w-full  mb-4 group">
                                        <input type="text" name="book_title" id="book_title" class="block py-2.5 px-0 text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                                        <label for="book_title" class="mr-1 peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">نام کتاب <span class="text-red-400 text-[5px] bg-transparent">فارسی</span></label>
                                        <div class="w-full h-[1px] bg-slate-600"></div>
                                    </div>

                                    <!-- Input for Author Name -->
                                    <div class="relative z-0 w-full  mb-4 group">
                                        <input type="text" name="author_name" id="author_name" class="block py-2.5 px-0 text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                                        <label for="author_name" class="peer-focus:font-medium absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">نام نویسنده</label>
                                        <div class="w-full h-[1px] bg-slate-600"></div>
                                    </div>

                                    <!-- Input for Release Date -->
                                    <div class="flex flex-col lg:flex-row md:flex-row items-center justify-between mt-3 py-1">

                                        <div class="text-white">
                                            <label for="releaseDate" class="block  text-gray-500 dark:text-gray-400">تاریخ عرضه</label>
                                            <input type="date" name="release_date" id="release_date"
                                                class="text-white bg-transparent mt-1 block border-1 border-b-2 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" />
                                        </div>
                                        <div class="w-7"></div>
                                        <!-- Submit Button -->
                                        <div class="mt-4">
                                            <button type="submit" name="add_book" class="py-2 px-4 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                اضافه کردن کتاب
                                            </button>
                                        </div>
                                        
                                    </div>
                                </form>
                            </div>
                        </div>


                        <!-- Story Form (Write Your Story) -->
                        <div x-show="openStoryForm" x-transition.duration.300ms>
                            <div class="max-w-4xl mx-auto px-4">
                                <div class="">
                                    <form action="add/addText.php" method="post">
                                        <!-- Input for Text -->
                                        <div>
                                            <label for="story" class="block text-white">متن شما</label>
                                            <textarea id="story" name="story" rows="6" class="text-white w-full mt-2 p-3 bg-[#1E2734] border border-gray-300 rounded-md focus:bg-opacity-25 focus:bg-slate-600 focus:ring-2 focus:ring-blue-500" placeholder="متن خود را اینجا بنویسید..." required></textarea>
                                        </div>

                                        <!-- Input for Text Title -->
                                        <div class="flex flex-col lg:flex-row justify-between mt-3 h-20">
                                            <div class="relative group">
                                                <input type="text" name="text_title" id="text_title" class="block w-full py-2.5 px-0 text-sm text-white bg-transparent border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 dark:focus:border-blue-500 focus:outline-none focus:ring-0 focus:border-blue-600 peer" placeholder=" " required />
                                                <label for="text_title" class="peer-focus:font-medium absolute text-white dark:text-gray-400 duration-300 transform -translate-y-6 scale-75 top-3 -z-10 origin-[0] peer-focus:start-0 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto peer-focus:text-blue-600 peer-focus:dark:text-blue-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-6">عنوان</label>
                                                <div class="w-full h-[1px] bg-slate-300"></div>
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="mt-4">
                                                <button type="submit" name="add_text" class="w-full py-2 px-3 bg-green-700 text-white rounded-md hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                                    اضافه کردن متن
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php

                        if (isset($_SESSION['success'])) {
                            echo "<div class='mb-2 text-center text-green-500 bg-green-300/10 mx-4 rounded-2xl p-2' id='success-alert' '>" . htmlspecialchars($_SESSION['success']) . "</div>";
                            unset($_SESSION['success']);
                        }
                        if (isset($_SESSION['warning'])) {
                            echo "<div class=' text-center text-yellow-400' id='error-alert'  '>" . htmlspecialchars($_SESSION['warning']) . "</div>";
                            unset($_SESSION['warning']);
                        }
                        if (isset($_SESSION['error'])) {
                            echo "<div class='mb-2 text-center text-red-500 bg-red-300/10 mx-4 rounded-2xl p-2' id='error-alert'  '>" . htmlspecialchars($_SESSION['error']) . "</div>";
                            unset($_SESSION['error']);
                        }
                    ?>
                </main>
            </div>
        </div>
    </section>

    <script>

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