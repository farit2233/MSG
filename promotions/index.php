<?php
$i = 1;
$qry = $conn->query("SELECT * FROM coupon_code_list ORDER BY date_created ASC, name ASC LIMIT 5");
$qry_promo = $conn->query("SELECT * FROM promotions_list ORDER BY date_created ASC, name ASC LIMIT 8");

function formatDateThai($date)
{
    // แปลงวันที่เป็นตัวแปร timestamp
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    $hour = date("H", $timestamp); // ชั่วโมง (00-23)
    $minute = date("i", $timestamp); // นาที (00-59)
    // สร้างวันที่ในรูปแบบไทย
    return "{$day}/{$month}/{$year}";
}
?>
<div class="promotion-background">
    <section class="py-5 mx-5">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
            <h1 class="text-center head-promotion fw-bold text-orange">
                <i class="fa-solid fa-ticket"></i> โปรโมชั่นทั้งหมด
            </h1>
        </div>
        <div class="d-flex justify-content-between mt-3">
            <h3>คูปอง</h3>
            <a href="./?p=promotions/coupon_codes_list" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a> <!-- จัดลิงก์ให้ไปขวา -->
        </div>
        <div class="container-coupon">
            <div class="container-custom">
                <div class="row row-cols-1 row-cols-md-5 g-4">
                    <?php
                    mysqli_data_seek($qry, 0);
                    while ($row = $qry->fetch_assoc()): ?>
                        <div class="col mb-4">
                            <div class="card" style="width: 16.5rem;">
                                <div class="card-img" style="position: relative;">
                                    <img src="../uploads/coupon/coupon3.png" class="card-img-top" alt="Coupon Image">
                                    <div class="card-body card-coupon-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="card-title coupon-head mb-0">
                                                <?php
                                                // เช็คว่าเป็น free shipping หรือไม่
                                                if ($row['type'] == 'free_shipping') {
                                                    echo 'ส่งฟรี'; // ถ้าเป็น free_shipping ให้แสดงแค่คำว่า "ส่งฟรี"
                                                } elseif ($row['discount_value'] > 0) {
                                                    // ถ้ามีส่วนลดจริงๆ จะโชว์ "ลด" และจำนวนที่เหมาะสม
                                                    echo 'ลด ' . $row['discount_value'];
                                                    switch ($row['type'] ?? '') {
                                                        case 'fixed':
                                                            echo ' บาท';
                                                            break;
                                                        case 'percent':
                                                            echo '%';
                                                            break;
                                                        default:
                                                            echo '-';
                                                    }
                                                } else {
                                                    echo ''; // ถ้าเป็น 0 หรือไม่มีส่วนลด จะไม่แสดงอะไร
                                                }
                                                ?>
                                            </label>
                                            <div class="copy-tooltip">
                                                <span class="tooltip-text">คัดลอกแล้ว</span>
                                                <a href="#" class="text-white copy" id="copy-button-<?= $row['coupon_code'] ?>" data-code="<?= $row['coupon_code'] ?>">
                                                    <u>คัดลอก</u>
                                                </a>
                                            </div>
                                        </div>
                                        <p class="card-text coupon-code"><?= $row['coupon_code'] ?></p>
                                        <p class="card-text coupon-description"><?= $row['description'] ?></p>
                                        <small>
                                            <div class="d-flex justify-content-between">
                                                <span>วันนี้ ถึง <?= formatDateThai($row['end_date']) ?></span>
                                                <!-- ปุ่มที่เรียกเปิด modal -->
                                                <a class="text-white" type="button" id="coupon_code_conditions" data-coupon-id="<?= $row['id'] ?>">
                                                    เงื่อนไข
                                                </a>

                                            </div>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
</div>
</section>

<section class="mx-5">
    <div class="d-flex justify-content-between mt-3">
        <h3>โปรโมชั่นทั้งหมด</h3>
        <a href="#" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a> <!-- จัดลิงก์ให้ไปขวา -->
    </div>
    <div class="card rounded-0 pt-4">
        <div class="container-custom">
            <div class="card-group">
                <div class="row g-4">
                    <?php while ($row = $qry_promo->fetch_assoc()): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card card-promotion h-100">
                                <div class="card-promotion-holder">
                                    <img class="card-img-top promotion-img" src=" <?= $row['image_path'] ?>" alt="Card image cap">
                                    <h5 class="card-title card-title-promotion">
                                        <?= $row['name'] ?>
                                    </h5>
                                </div>
                                <div class="card-body card-promotion-body d-flex flex-column">
                                    <p class="card-text promotion-description"><?= $row['description'] ?></p>
                                    <p class="card-text mt-auto">
                                        <small class="text-muted">
                                            <span>เริ่ม: <?= formatDateThai($row['start_date']) ?></span>
                                            <span> ถึง สิ้นสุด: <?= formatDateThai($row['end_date']) ?></span>
                                        </small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
<script>
    $(function() {
        // เมื่อคลิกที่ลิงก์เงื่อนไข
        $(document).on('click', '#coupon_code_conditions', function() {
            var coupon_id = $(this).data('coupon-id'); // ดึง id ของคูปอง
            // เรียก modal พร้อมกับหัวข้อที่กำหนด
            uni_modal_conditions("เงื่อนไขการใช้งาน ", "promotions/coupon_code_conditions.php?id=" + coupon_id);
        });
    });
    document.addEventListener("DOMContentLoaded", function() {
        const copyButtons = document.querySelectorAll('a[id^="copy-button-"]');
        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const couponCode = this.getAttribute('data-code');
                const tooltip = this.closest('.copy-tooltip').querySelector('.tooltip-text');
                const textArea = document.createElement('textarea');
                textArea.value = couponCode;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);

                tooltip.classList.add('show');
                setTimeout(() => {
                    tooltip.classList.remove('show');
                }, 1500);
            });
        });
    });
</script>