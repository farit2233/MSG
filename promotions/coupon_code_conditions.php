<?php
require_once('../config.php');

// ดึงข้อมูลโปรโมชั่น (ถ้ามี)
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM coupon_code_list WHERE id = '{$_GET['id']}' AND delete_flag = 0");
    if ($qry->num_rows > 0) {
        $coupon = $qry->fetch_assoc(); // ดึงข้อมูลคูปองใน array
        // คุณสามารถใช้ข้อมูลที่ได้ในตัวแปร $coupon เพื่อแสดงใน modal
        $coupon_code = $coupon['coupon_code'];
        $discount_value = $coupon['discount_value'];
        $type = $coupon['type'];
        $description = $coupon['description'];
        $cpromo = $coupon['cpromo'];
        $limit_coupon = $coupon['limit_coupon'];
        $unl_coupon = $coupon['unl_coupon'];
        $start_date = formatDateThai($coupon['start_date']); // หากต้องการแสดงวันที่แบบไทย
        $end_date = formatDateThai($coupon['end_date']);
        $start_dateConditions = formatDateThaiConditions($coupon['start_date']); // หากต้องการแสดงวันที่แบบไทย
        $end_dateConditions = formatDateThaiConditions($coupon['end_date']);
    }
}

// ฟังก์ชันแปลงวันที่เป็นไทย
function formatDateThai($date)
{
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    return "{$day}/{$month}/{$year}";
}

function formatDateThaiConditions($date)
{
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    $hour = date("H", $timestamp); // ชั่วโมง (00-23)
    $minute = date("i", $timestamp); // นาที (00-59)
    return "{$day}/{$month}/{$year} {$hour}:{$minute}";
}

// เช็ค limit_coupon และ unl_coupon
if ($limit_coupon == 0 || $limit_coupon === null) {
    $limit_text = ($unl_coupon == 1) ? "ไม่จำกัด" : "สามารถใช้ได้ {$limit_coupon} ครั้ง / การสั่งซื้อ / บัญชี E-mail";
} else {
    $limit_text = "$limit_coupon";
}

// เช็ค cpromo
if ($cpromo == 1) {
    $promo_text = "คูปองนี้สามารถใช้ร่วมกับโปรโมชั่นอื่นๆ ได้";
} else {
    $promo_text = "คูปองนี้ไม่สามารถใช้ร่วมกับโปรโมชั่นอื่นๆ ได้";
}
?>
<style>
    .container ul li {
        margin-bottom: 8px;
        /* ระยะห่างระหว่างรายการ */
    }

    .container ul li p {
        margin: 0;
        /* ลบระยะห่างจาก p */
        padding: 0;
        /* ลบการ padding ของ p */
    }

    .head-conditions {
        font-size: 18px;
        font-weight: bold;
    }
</style>
<!-- แสดงคูปองแค่ 1 อัน -->
<div class="container d-flex justify-content-center">
    <div class="card" style="width: 16.5rem;">
        <div class="card-img" style="position: relative;">
            <img src="../uploads/coupon/coupon3.png" class="card-img-top" alt="Coupon Image">
            <div class="card-body card-coupon-body">
                <div class="d-flex justify-content-between align-items-center">
                    <label class="card-title coupon-head mb-0">
                        <?php
                        if ($coupon['type'] == 'free_shipping') {
                            echo 'ส่งฟรี';
                        } elseif ($coupon['discount_value'] > 0) {
                            echo 'ลด ' . $coupon['discount_value'];
                            switch ($coupon['type']) {
                                case 'fixed':
                                    echo ' บาท';
                                    break;
                                case 'percent':
                                    echo '%';
                                    break;
                            }
                        }
                        ?>
                    </label>
                    <div class="copy-tooltip">
                        <span class="tooltip-text">คัดลอกแล้ว</span>
                        <a href="#" class="text-white copy" id="copy-button-<?= $coupon['coupon_code'] ?>" data-code="<?= $coupon['coupon_code'] ?>">
                            <u>คัดลอก</u>
                        </a>
                    </div>
                </div>
                <p class="card-text coupon-code"><?= $coupon['coupon_code'] ?></p>
                <p class="card-text coupon-description"><?= $coupon['description'] ?></p>
                <small>
                    <div class="d-flex justify-content-between">
                        <span>วันนี้ ถึง <?= $end_date ?></span>
                        <a class="text-white">เงื่อนไข</a>
                    </div>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- เงื่อนไขการใช้งาน -->
<div class="container">
    <h4>เงื่อนไขการใช้งานคูปอง</h4>
    <ul>
        <li>
            <p><?= $description ?></p>
        </li>
        <li>
            <p>ลูกค้าจำเป็นต้องเข้าสู่ระบบก่อนใช้งาน</p>
        </li>
        <li>ใช้ได้ตั้งแต่ <?= $start_dateConditions ?> ถึง <?= $end_dateConditions ?></li>
        <li>
            ใช้ได้กับสินค้าที่ร่วมรายการเท่านั้น:
        </li>
        <ul>
            <li></li>
            <li></li>
            <li></li>
            <li></li>
        </ul>
        <li>
            <p>สามารถใช้ได้ <?= $limit_text ?> ครั้ง / การสั่งซื้อ / บัญชี E-mail</p>
        </li>

        <li>
            <?= $promo_text ?>
        </li>

        <li>
            <p>ข้อกำหนดและเงื่อนไขอื่น ๆ เป็นไปตามที่บริษัทกำหนด</p>
        </li>
    </ul>
</div>