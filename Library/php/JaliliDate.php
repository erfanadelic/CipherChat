<?php 
date_default_timezone_set('Asia/Tehran');
function GetJaliliDate($Pattern): bool|string{
    $DateFormatter = new IntlDateFormatter(
        'fa_IR',
        IntlDateFormatter::NONE,
        IntlDateFormatter::NONE,
        'Asia/Tehran',
        IntlDateFormatter::TRADITIONAL,
        $Pattern);
    return $DateFormatter->format(time());}


    function convertPersianTimeToEnglish($time) {
        // تبدیل اعداد فارسی به انگلیسی
        $persian = ['۰','۱','۲','۳','۴','۵','۶','۷','۸','۹'];
        $english = ['0','1','2','3','4','5','6','7','8','9'];
        $time = str_replace($persian, $english, $time);
    
        // اطمینان از اینکه زمان فرمت HH:MM:SS دارد
        $parts = explode(':', $time);
        foreach ($parts as &$part) {
            $part = str_pad($part, 2, '0', STR_PAD_LEFT); // اضافه کردن صفر پیشوندی
        }
        return implode(':', $parts);
    }
    
    $persianTime = '۱۲:۵:۲۴';
    $englishTime = convertPersianTimeToEnglish($persianTime);
    