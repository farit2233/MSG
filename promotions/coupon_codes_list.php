<?php
$i = 1;
$qry_limit = $conn->query("SELECT * FROM coupon_code_list ORDER BY date_created ASC, name ASC LIMIT 5");
$qry_free_shipping = $conn->query("SELECT * FROM coupon_code_list WHERE type = 'free_shipping' ORDER BY date_created ASC, name ASC");
$qry = $conn->query("SELECT * FROM coupon_code_list ORDER BY date_created ASC, name ASC");
function formatDateThai($date)
{
    // แปลงวันที่เป็นตัวแปร timestamp
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    // สร้างวันที่ในรูปแบบไทย
    return "{$day}/{$month}/{$year}";
}
?>
<div class="promotion-background">
    <section class="py-5 mx-5">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
            <h1 class="text-center head-promotion fw-bold text-orange">
                <i class="fa-solid fa-ticket"></i> คูปองทั้งหมด
            </h1>
        </div>
        <div class="mt-3">
            <h3>คูปองแนะนำ</h3>
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
                                                <a href="./?p=promotions/coupon_codes_list" class="text-white"><u>ดูเพิ่มเติม</u></a>
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
    </section>

    <section class="mx-5">
        <h3>คูปองส่งฟรีทั้งหมด</h3>
        <div class="card rounded-0 pt-4">
            <div class="container-coupon">
                <div class="row row-cols-1 row-cols-md-5 g-4">
                    <?php
                    mysqli_data_seek($qry_free_shipping, 0);
                    while ($row = $qry_free_shipping->fetch_assoc()): ?>
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
                                                <a href="./?p=promotions/coupon_codes_list" class="text-white"><u>ดูเพิ่มเติม</u></a>
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
    </section>

    <section class="py-5 mx-5">
        <h3>คูปองทั้งหมด</h3>
        <div class="card rounded-0 pt-4">
            <div class="container-coupon">
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
                                                <a href="./?p=promotions/coupon_codes_list" class="text-white"><u>ดูเพิ่มเติม</u></a>
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
    </section>
</div>
<script>
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