<style>
    body {
        background-color: #FAFAFA;
    }

    .about {
        font-size: 50px;
        color: #ff6f00;
        padding: 2rem 2rem;
    }

    img {
        display: block;
        margin: auto;
    }

    .contact-size {
        font-size: 18px;
    }

    .card-title {
        font-size: 24px !important;
        font-weight: bold;
    }

    .coupon-code {
        font-size: 18px;
    }

    .copy {
        font-size: 18px !important;
        font-weight: bold;
    }

    /* --- CSS ที่เพิ่มเข้ามาสำหรับ Tooltip --- */
    .copy-tooltip {
        position: relative;
        display: inline-block;
    }

    .copy-tooltip .tooltip-text {
        visibility: hidden;
        width: 120px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px 0;
        position: absolute;
        z-index: 1;
        bottom: 150%;
        /* ตำแหน่งด้านบนของปุ่ม */
        left: 50%;
        margin-left: -60px;
        /* จัดให้อยู่กึ่งกลาง */
        opacity: 0;
        transition: opacity 0.3s;
    }

    /* สร้างลูกศรชี้ลง */
    .copy-tooltip .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #555 transparent transparent transparent;
    }

    /* คลาสสำหรับแสดง tooltip */
    .copy-tooltip .tooltip-text.show {
        visibility: visible;
        opacity: 1;
    }

    .container-custom {
        max-width: 1400px;
        /* กำหนดความกว้างสูงสุด */
        margin: 0 auto;
        /* จัดให้กลาง */
    }
</style>

<?php
$i = 1;
$qry = $conn->query("SELECT * FROM `coupon_code_list` ORDER BY `date_created`  ASC, `name` ASC");
$qry_promo = $conn->query("SELECT * FROM `promotions_list` ORDER BY `date_created`  ASC, `name` ASC");
?>

<section class="py-5">
    <div class="container">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
            <h1 class="text-center about fw-bold text-orange">
                <i class="fa fa-envelope"></i> โปรโมชั่นทั้งหมด
            </h1>
        </div>
        <h3>คูปอง</h3>
        <div class="container mt-4">
            <div class="row">
                <?php while ($row = $qry->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4"> <!-- เปลี่ยนจาก col-md-4 เป็น col-md-3 -->
                        <div class="card" style="width: 16.5rem;">
                            <div class="card-img" style="position: relative;">
                                <img src="../promotions/new-blackcoupon.png" class="card-img-top" alt="Coupon Image">
                                <div class="card-body" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; color: white; padding: 10px;">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="card-title mb-0">
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
                                    <p class="card-text"><?= $row['description'] ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
    <div class="container-custom">
        <div class="card-group">
            <div class="row g-4"> <!-- เพิ่มระยะห่างระหว่างคอลัมน์ -->
                <?php while ($row = $qry_promo->fetch_assoc()): ?>
                    <div class="col-md-3 mb-4"> <!-- ใช้ col-md-3 เพื่อแสดง 4 คอลัมน์ในแถว -->
                        <div class="card" style="width: 21rem;">
                            <img class="card-img-top" src="..." alt="Card image cap">
                            <div class="card-body">
                                <h5 class="card-title"><?= $row['name'] ?></h5>
                                <p class="card-text"><?= $row['description'] ?></p>
                                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</section>

<script>
    /* HTTPS
    document.addEventListener("DOMContentLoaded", function() {
        const copyButtons = document.querySelectorAll('a[id^="copy-button-"]');

        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // ป้องกันไม่ให้ลิงก์ทำงาน (ไม่ให้หน้าเว็บเลื่อน)
                e.preventDefault();

                const couponCode = this.getAttribute('data-code');
                const tooltip = this.closest('.copy-tooltip').querySelector('.tooltip-text');

                // ใช้ Clipboard API (ทันสมัยและปลอดภัยกว่า) หรือ fallback ไปใช้ execCommand
                navigator.clipboard.writeText(couponCode).then(() => {
                    // แสดง tooltip
                    tooltip.classList.add('show');

                    // ซ่อน tooltip หลังจาก 1.5 วินาที
                    setTimeout(() => {
                        tooltip.classList.remove('show');
                    }, 1500);

                }).catch(err => {
                    console.error('ไม่สามารถคัดลอกได้: ', err);
                });
            });
        });
    });*/
    document.addEventListener("DOMContentLoaded", function() {
        const copyButtons = document.querySelectorAll('a[id^="copy-button-"]');

        copyButtons.forEach(button => {
            button.addEventListener('click', function(e) {
                // ป้องกันไม่ให้ลิงก์ทำงาน (ไม่ให้หน้าเว็บเลื่อน)
                e.preventDefault();

                const couponCode = this.getAttribute('data-code');
                const tooltip = this.closest('.copy-tooltip').querySelector('.tooltip-text');

                // ใช้ execCommand สำหรับ Local (ไม่ต้องใช้ HTTPS)
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