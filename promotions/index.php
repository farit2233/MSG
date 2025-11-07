<?php
$i = 1;
$qry = $conn->query("
    SELECT * FROM coupon_code_list 
    WHERE status = 1 
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW() 
    ORDER BY 
        type = 'free_shipping' DESC,  -- free_shipping อยู่หน้าสุด
        discount_value DESC,           -- จัดลำดับตามส่วนลดจากมากไปหาน้อย
        date_created DESC              -- ใหม่สุดอยู่ก่อน
    LIMIT 5
");
$qry_promo_recommand = $conn->query("
    SELECT * FROM promotions_list 
    WHERE status = 1 
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW() 
    ORDER BY 
        CASE 
            WHEN type = 'free_shipping' THEN 0 
            WHEN type = 'percent' THEN 1 
            ELSE 2 
        END, 
        CASE 
            WHEN type = 'percent' THEN discount_value 
            ELSE 0 
        END DESC,
        minimum_order ASC,
        date_created ASC,
        name ASC
    LIMIT 4
");



function formatDateThai($date)
{
    // แปลงวันที่เป็นตัวแปร timestamp
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp); // ปี (พ.ศ.)
    $hour = date("H", $timestamp); // ชั่วโมง (00-23)
    $minute = date("i", $timestamp); // นาที (00-59)
    // สร้างวันที่ในรูปแบบไทย
    return "{$day}/{$month}/{$year}";
}

$page_title = "โปรโมชัน"; // ตั้งชื่อหน้าเริ่มต้น
$page_description = "";

$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">โปรโมชัน</li>'; // HTML สำหรับ Breadcrumb เส้นที่ 2 (ค่าเริ่มต้น)

?>

<style>
    .custom-promo-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start !important;
        gap: 24px;
        padding-bottom: 24px;
    }

    .custom-promo-card-wrapper {
        width: 323.5px;
        display: flex;

    }

    .custom-promo-card-wrapper a.text-decoration-none {
        display: block;
        width: 100%;
    }

    .empty-message-center {
        width: 100% !important;
        text-align: center !important;
        /* ไม่จำเป็นต้องใช้ d-flex หรือ justify-content-center อีก */
    }
</style>
<div class="promotion-background">
    <section class="py-5 mx-5">
        <div class="d-flex flex-column justify-content-center align-items-center text-center">
            <h1 class="text-center head-promotion fw-bold text-orange">
                <i class="fa-solid fa-ticket"></i> <?= $page_title ?>
            </h1>
            <?php if (!empty($page_description)): ?>
                <hr>
                <p class="m-0"><small><em><?= html_entity_decode($page_description) ?></em></small></p>
            <?php endif; ?>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link text-dark">HOME</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>
        <div class="d-flex justify-content-between mt-3">
            <h3>คูปอง</h3>
            <a href="./?p=promotions/coupon_codes_list" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a>
        </div>

        <div class="container-coupon">
            <div class="container-custom">
                <?php
                // 1. ตรวจสอบข้อมูลก่อนสร้างแถว
                mysqli_data_seek($qry, 0);
                if ($qry->num_rows > 0):
                ?>
                    <div class="row row-cols-1 row-cols-md-5 g-4">
                        <?php while ($row = $qry->fetch_assoc()): ?>
                            <div class="col mb-4">
                                <div class="card" style="width: 16.5rem;">
                                    <div class="card-img" style="position: relative;">
                                        <img src="../uploads/coupon/coupon.webp" class="card-img-top" alt="Coupon Image">
                                        <div class="card-body card-coupon-body">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <label class="card-title coupon-head mb-0">
                                                    <?php
                                                    if ($row['type'] == 'free_shipping') {
                                                        echo 'ส่งฟรี';
                                                    } elseif ($row['discount_value'] > 0) {
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
                                                        echo '';
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
                    </div> <?php
                        else:
                            // 3. ถ้าไม่มีข้อมูล: แสดงข้อความ (โดยไม่อยู่ใน .row)
                            ?>
                    <div class="d-flex justify-content-center align-items-center py-5">
                        <h4 class="text-muted">ไม่มีคูปองโค้ดในขณะนี้</h4>
                    </div>
                <?php
                        endif; // สิ้นสุด if-else
                ?>
            </div>
        </div>
    </section>
</div>

<div class="promotion-background">
    <section class="mx-5">
        <div class="d-flex justify-content-between mt-3">
            <h3>โปรโมชันแนะนำ</h3>
            <a href="./?p=promotions/promotions_list" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a>
        </div>

        <div class="card rounded-0 pt-4 px-4">
            <div class="custom-promo-container">

                <?php
                // ตรวจสอบว่ามีข้อมูลโปรโมชันหรือไม่
                if ($qry_promo_recommand->num_rows > 0):
                    // ⬇️ ถ้ามีข้อมูล ให้เริ่มวนลูป while
                    while ($row = $qry_promo_recommand->fetch_assoc()):
                ?>
                        <div class="custom-promo-card-wrapper">
                            <a href="<?= base_url . "?p=products&pid=" . $row['id'] ?>" class="text-decoration-none">
                                <div class="card card-promotion h-100">
                                    <div class="card-promotion-holder">
                                        <img class="card-img-top promotion-img" src="<?= $row['image_path'] ?>" alt="Card image cap">
                                        <h5 class="card-title card-title-promotion">
                                            <?= $row['name'] ?>
                                        </h5>
                                    </div>
                                    <div class="card-body card-promotion-body d-flex flex-column">
                                        <p class="card-text promotion-description text-dark"><?= $row['description'] ?></p>
                                        <p class="card-text mt-auto">
                                            <small class="text-muted">
                                                <span>เริ่ม: <?= formatDateThai($row['start_date']) ?></span>
                                                <span> ถึง สิ้นสุด: <?= formatDateThai($row['end_date']) ?></span>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php
                    endwhile; // สิ้นสุด while loop
                else:
                    // ⬇️ ถ้าไม่มีข้อมูลเลย (num_rows == 0)
                    ?>
                    <div class="w-100 empty-message-center py-5">
                        <h4 class="text-muted">ไม่มีโปรโมชันในขณะนี้</h4>
                    </div>
                <?php
                endif; // สิ้นสุด if-else
                ?>
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