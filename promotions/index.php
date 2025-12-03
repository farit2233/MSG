<?php
// ... (ส่วน PHP Query ด้านบนเหมือนเดิม ไม่ต้องแก้) ...
$i = 1;
$qry = $conn->query("
    SELECT * FROM coupon_code_list 
    WHERE status = 1 
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW() 
    ORDER BY 
        type = 'free_shipping' DESC, 
        discount_value DESC, 
        date_created DESC 
    LIMIT 4
");
$qry_promo_recommand = $conn->query("
    SELECT * FROM promotions_list 
    WHERE status = 1 
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW() 
    ORDER BY 
        CASE WHEN type = 'free_shipping' THEN 0 WHEN type = 'percent' THEN 1 ELSE 2 END, 
        CASE WHEN type = 'percent' THEN discount_value ELSE 0 END DESC,
        minimum_order ASC,
        date_created ASC,
        name ASC
    LIMIT 3
");

function formatDateThai($date)
{
    $timestamp = strtotime($date);
    $day = date("j", $timestamp);
    $month = date("n", $timestamp);
    $year = date("Y", $timestamp);
    return "{$day}/{$month}/{$year}";
}

$page_title = "โปรโมชัน";
$page_description = "รวมคูปองโค้ด โปรโมชันเพื่อคุณ";
$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">โปรโมชัน</li>';
?>

<style>
    .page-subtitle {
        font-size: 20px;
        font-weight: normal;
        font-style: italic;
    }

    /* --- เพิ่ม CSS จากโค้ดที่ให้จำ (สำหรับคูปอง) --- */
    .copy-tooltip {
        position: relative;
        cursor: pointer;
    }

    .copy-tooltip .tooltip-text {
        visibility: hidden;
        width: 85px;
        background-color: #555;
        color: #fff;
        text-align: center;
        border-radius: 6px;
        padding: 5px;
        position: absolute;
        z-index: 1;
        bottom: 125%;
        left: 50%;
        margin-left: -40px;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .copy-tooltip .tooltip-text.show {
        visibility: visible;
        opacity: 1;
    }

    .coupon-grid-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .custom-grid {
        display: grid;
        gap: 13px;
        /* Gap 13px ตามที่ต้องการ */
        width: fit-content;
    }

    @media (max-width: 1199px) {
        .custom-grid {
            grid-template-columns: repeat(auto-fit, 16.5rem);
            justify-content: center;
        }
    }

    @media (min-width: 1200px) {
        .custom-grid {
            grid-template-columns: repeat(4, 16.5rem);
        }
    }

    /* ------------------------------------------- */

    .custom-promo-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center !important;
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
    }
</style>

<section class="py-3">
    <div class="container">
        <div class="py-5 px-3">
            <h1 class="text-center"><?= $page_title ?></h1>
            <hr>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link text-dark">HOME</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>

        <div class="d-flex justify-content-between mt-3 mb-3">
            <h3>คูปอง</h3>
            <a href="./?p=promotions/coupon_codes_list" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a>
        </div>

        <div class="coupon-grid-wrapper">
            <?php
            mysqli_data_seek($qry, 0);
            if ($qry->num_rows > 0):
            ?>
                <div class="custom-grid">
                    <?php while ($row = $qry->fetch_assoc()): ?>
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
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="d-flex justify-content-center align-items-center py-5 w-100">
                    <h4 class="text-muted">ไม่มีคูปองโค้ดในขณะนี้</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
    </div>
</section>


<section class="py-3">
    <div class="container">
        <div class="d-flex justify-content-between mt-3 mb-3">
            <h3>โปรโมชันแนะนำ</h3>
            <a href="./?p=promotions/promotions_list" class="text-dark"><u>ดูเพิ่มเติม <i class="fa-solid fa-arrow-right"></i></u></a>
        </div>

        <div class="card rounded-0 pt-4 px-4 border-0">
            <div class="custom-promo-container">
                <?php
                if ($qry_promo_recommand->num_rows > 0):
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
                    endwhile;
                else:
                    ?>
                    <div class="w-100 empty-message-center py-5">
                        <h4 class="text-muted">ไม่มีโปรโมชันในขณะนี้</h4>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<script>
    $(function() {
        $(document).on('click', '#coupon_code_conditions', function() {
            var coupon_id = $(this).data('coupon-id');
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