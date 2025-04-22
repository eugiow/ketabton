<div data-dial-init class="fixed start-6 bottom-6 group lg:hidden md:hidden sm:show font-lg">
        <div id="speed-dial-menu-default" class="flex flex-col items-center hidden mb-4 space-y-2">
            <!-- out Button -->
            <button type="button" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                <a class="text-red-600 " href="../login/logout.php"><i class="bi bi-box-arrow-in-left"></i></a>
            </button>
            <!-- add Button -->
            <button type="button" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                <a class="text-green-600 " href="add.php"><i class="bi bi-plus-square"></i></a>
            </button>
            <!-- menu Button -->
            <button type="button" id="menuToggle" class="flex justify-center items-center w-[52px] h-[52px] text-gray-500 hover:text-gray-900 bg-white rounded-full border border-gray-200 shadow-sm hover:bg-gray-50 focus:ring-4 focus:ring-gray-300 focus:outline-none">
                <i class="bi bi-list"></i>
            </button>
            
        </div>
        <button type="button" data-dial-toggle="speed-dial-menu-default" aria-controls="speed-dial-menu-default" aria-expanded="false" class="flex items-center justify-center text-white bg-blue-700 rounded-full w-14 h-14 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 focus:outline-none">
            <svg class="w-5 h-5 transition-transform group-hover:rotate-45" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 18">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 1v16M1 9h16"/>
            </svg>
        </button>
    </div>
    
    
    <script src="../assets/js/js.js"></script>
    
</body>
</html>
