
function openNav() {
  document.getElementById("mySidenav").style.width = "220px";
}

function closeNav() {
  document.getElementById("mySidenav").style.width = "0";
}



// login


// users

    // برای باز و بسته کردن منو در موبایل
    const menuToggle = document.getElementById('menuToggle');
    const menuRight = document.getElementById('menuRight');
    
    menuToggle.addEventListener('click', () => {
        menuRight.classList.toggle('open');
    });
// برای بستن منو
const closeMenu = document.getElementById('closeMenu'); // دکمه یا المنتی برای بستن منو
closeMenu.addEventListener('click', () => {
    menuRight.classList.remove('open');
});





const searchToggle = document.getElementById("searchToggle");
const searchBox = document.getElementById("searchBox");
const overlay = document.getElementById("overlay");

// Toggle search box and overlay
searchToggle.addEventListener("click", () => {
    const isHidden = searchBox.classList.contains("hidden");
    if (isHidden) {
        searchBox.classList.remove("hidden");
        searchBox.classList.add("visible");
        overlay.classList.remove("hidden");
        overlay.classList.add("visible");
    } else {
        closeSearchBox();
    }
});

// Close search box when clicking outside
overlay.addEventListener("click", () => {
    closeSearchBox();
});

function closeSearchBox() {
    searchBox.classList.remove("visible");
    searchBox.classList.add("hidden");
    overlay.classList.remove("visible");
    overlay.classList.add("hidden");
}




///الرت های خطا 









