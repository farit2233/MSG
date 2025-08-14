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
        margin: 0 auto;
    }

    /* ไล่ความมืดจากบนลงล่าง */
    .card-promotion-holder {
        position: relative;
        overflow: hidden;
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.6) 100%);
    }

    .card-promotion-holder img {
        width: 100%;
        height: auto;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .card-promotion-holder::after {
        content: "";
        position: absolute;
        top: 0;
        /* เริ่มที่ด้านบน */
        left: 0;
        width: 100%;
        height: 100%;
        /* ให้ gradient ครอบคลุมทั้งภาพ */
        background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 75%, rgba(0, 0, 0, 0.3) 100%);
        z-index: 1;
        /* ให้ gradient ซ้อนอยู่เหนือรูปภาพ */
    }

    .card-title-promotion {
        position: absolute;
        bottom: 10px;
        left: 10px;
        color: white;
        padding: 5px;
        z-index: 2;
        /* ทำให้ชื่อโปรโมชันอยู่เหนือ gradient */
        font-size: 20px !important;
        font-weight: bold;
    }

    .card-promotion {
        cursor: pointer;
        transition: box-shadow 0.3s ease;
    }

    /* เมื่อ hover ที่การ์ด จะขยายขนาดเล็กน้อย */
    .card-promotion:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    }

    /* เพิ่ม hover effect ให้กับรูปภาพเมื่อ hover ที่การ์ด */
    .card-promotion:hover .card-img-top {
        transform: scale(1.1);
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        transition: all .3s ease-in-out;
    }

    .card-body p {
        margin-bottom: 1rem;
        /* เพิ่มระยะห่างระหว่างบรรทัด */
    }

    .coupon-description,
    .promotion-description {
        font-size: 16px;
        min-height: 48px;
        font-weight: 300;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* กำหนดให้แสดงผลสูงสุด 2 บรรทัด */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<?php
$i = 1;
$qry = $conn->query("SELECT * FROM coupon_code_list ORDER BY date_created ASC, name ASC");
$qry_promo = $conn->query("SELECT * FROM promotions_list ORDER BY date_created ASC, name ASC");

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
                                    <p class="card-text coupon-description"><?= $row['description'] ?></p>
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
    <h3>โปรโมชั่นทั้งหมด</h3>
    <div class="card rounded-0 pt-4">
        <div class="container-custom">
            <div class="card-group">
                <div class="row g-4">
                    <?php while ($row = $qry_promo->fetch_assoc()): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card card-promotion h-100">
                                <div class="card-promotion-holder">
                                    <img class="card-img-top" src=" <?= $row['image_path'] ?>" alt="Card image cap">
                                    <h5 class="card-title card-title-promotion">
                                        <?= $row['name'] ?>
                                    </h5>
                                </div>
                                <div class="card-body d-flex flex-column">
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