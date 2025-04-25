<?php 
date_default_timezone_set('Asia/Tehran');

// تابع برای گرفتن تاریخ شمسی
function GetJaliliDate($Pattern) {
    // ساخت یک نمونه از IntlDateFormatter برای فرمت تاریخ شمسی
    $DateFormatter = new IntlDateFormatter(
        'fa_IR',  // منطقه فارسی ایران
        IntlDateFormatter::LONG, // فرمت طولانی تاریخ (شما می‌توانید تغییر دهید)
        IntlDateFormatter::NONE, // نیازی به زمان نیست
        'Asia/Tehran',  // منطقه زمانی تهران
        IntlDateFormatter::TRADITIONAL, // استفاده از تقویم سنتی (هجری شمسی)
        $Pattern  // الگوی تاریخ شمسی
    );

    // گرفتن تاریخ فعلی و فرمت کردن آن به تاریخ شمسی
    $jalaliDate = $DateFormatter->format(time());
    echo $jalaliDate;
    return $jalaliDate;
}




    function convertPersianTimeToEnglish($time) {
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $time = str_replace($persian, $english, $time);
    
        $parts = explode(':', $time);
        foreach ($parts as &$part) {
            $part = str_pad($part, 2, '0', STR_PAD_LEFT); // اضافه کردن صفر پیشوندی
        }
        return implode(':', $parts);}
    
    