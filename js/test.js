document.addEventListener('DOMContentLoaded', function () {
    // دریافت همه دکمه‌ها و منوهای کشویی
    const menuButtons = document.querySelectorAll('[id^="menu-"][id$="-btn"]');
    
    menuButtons.forEach(function (button) {
        const menuId = button.id.replace('-btn', ''); // گرفتن id منو مرتبط با دکمه
        const menu = document.getElementById(menuId); // پیدا کردن منو

        // وقتی روی دکمه کلیک می‌شود
        button.addEventListener('click', function (event) {
            event.stopPropagation();  // جلوگیری از بسته شدن منو در حین کلیک
           
            // پیدا کردن موقعیت دکمه
            const rect = button.getBoundingClientRect();
            
            // تنظیم موقعیت منو زیر دکمه
            menu.style.display = 'block';
            menu.style.top = `${rect.bottom }px`;  // قرار دادن منو زیر دکمه
            menu.style.left = `${(rect.left - menu.offsetWidth) + rect.width}px`;  // تنظیم موقعیت افقی منو
        });
    });

    // برای بستن منوها زمانی که روی هر جای دیگری از صفحه کلیک می‌شود
    document.addEventListener('click', function (event) {
        // فقط منوهایی که خارج از آن‌ها کلیک شده را مخفی کن
        if (!event.target.closest('.dropmenu') && !event.target.closest('[id^="menu-"][id$="-btn"]')) {
            document.querySelectorAll('.dropmenu').forEach(function (menu) {
                menu.style.display = 'none';
            });
        }
    });
});





function OpenSettings() {
    document.getElementById("SettingsSideBar").classList.toggle('open');
    GetUserInfo(getCookie("Uuid"), function(chats) {
        console.log(chats);
        
        document.querySelector(".ProfilePicture img").src = "Files/ProfileIMG/" + chats.ProfilePicture;
      });
    
}
