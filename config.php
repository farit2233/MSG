<?php
ob_start();
ini_set('date.timezone', 'Asia/Bangkok');
date_default_timezone_set('Asia/Bangkok');
session_start();

require_once('initialize.php');
require_once('classes/DBConnection.php');
require_once('classes/SystemSettings.php');
$db = new DBConnection;
$conn = $db->conn;
function redirect($url = '')
{
    if (!empty($url)) {
        echo '<script>location.href="' . base_url . $url . '"</script>';
        exit; // <--- เพิ่มบรรทัดนี้เข้าไป
    }
}
function validate_image($file)
{
    global $_settings;
    if (!empty($file)) {
        // exit;
        $ex = explode("?", $file);
        $file = $ex[0];
        $ts = isset($ex[1]) ? "?" . $ex[1] : '';
        if (is_file(base_app . $file)) {
            return base_url . $file . $ts;
        } else {
            return base_url . ($_settings->info('logo'));
        }
    } else {
        return base_url . ($_settings->info('logo'));
    }
}

function format_num($number = '', $decimal = '')
{
    // ตรวจสอบว่าเป็นตัวเลขหรือไม่
    if (is_numeric($number)) {
        // แยกส่วนของจำนวนเต็มและทศนิยม
        $ex = explode(".", $number);

        // ตรวจสอบความยาวของทศนิยม ถ้ามี
        $decLen = isset($ex[1]) ? strlen($ex[1]) : 0;

        // ถ้ากำหนดทศนิยม (parameter $decimal)
        if (is_numeric($decimal)) {
            // แสดงทศนิยมตามที่กำหนด
            return number_format($number, $decimal);
        } else {
            // ถ้าทศนิยมเป็น 00 หรือไม่มีทศนิยม
            if ($decLen > 0 && intval($ex[1]) === 0) {
                // ถ้ามีทศนิยมเป็น 00 ให้แสดงเป็นจำนวนเต็ม
                return number_format($ex[0], 0);
            } else {
                // ถ้าไม่มีทศนิยมหรือทศนิยมไม่ใช่ 00 ให้แสดงตามปกติ
                return number_format($number, $decLen > 0 ? $decLen : 0);
            }
        }
    } else {
        return "Invalid Input"; // ถ้าไม่ใช่ตัวเลข
    }
}

function isMobileDevice()
{
    $aMobileUA = array(
        '/iphone/i' => 'iPhone',
        '/ipod/i' => 'iPod',
        '/ipad/i' => 'iPad',
        '/android/i' => 'Android',
        '/blackberry/i' => 'BlackBerry',
        '/webos/i' => 'Mobile'
    );

    //Return true if Mobile User Agent is detected
    foreach ($aMobileUA as $sMobileKey => $sMobileOS) {
        if (preg_match($sMobileKey, $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }
    }
    //Otherwise return false..  
    return false;
}
ob_end_flush();
