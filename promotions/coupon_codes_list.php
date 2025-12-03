<?php
$i = 1;

// Query 1: คูปองแนะนำ (Limit 4)
$qry_limit = $conn->query("
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

// Query 2: คูปองส่งฟรี
$qry_free_shipping = $conn->query("SELECT * FROM coupon_code_list
    WHERE status = 1 
      AND type = 'free_shipping'
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW()
    ORDER BY date_created ASC, name ASC");

// Query 3: คูปองทั้งหมด
$qry = $conn->query("SELECT * FROM coupon_code_list 
    WHERE status = 1 
      AND delete_flag = 0
      AND start_date <= NOW() 
      AND end_date >= NOW() 
    ORDER BY date_created ASC, name ASC");

function formatDateThai($date)
{
    $timestamp = strtotime($date);
    $day = date("j", $timestamp);
    $month = date("n", $timestamp);
    $year = date("Y", $timestamp);
    return "{$day}/{$month}/{$year}";
}

$page_title = "คูปองทั้งหมด";
$page_description = "";
$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">คูปองทั้งหมด</li>';

?>
<style>
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

    /* --- CSS GRID สำหรับจัด 4 การ์ดต่อแถว --- */
    .coupon-grid-wrapper {
        width: 100%;
        display: flex;
        /* ใช้ Flex เพื่อจัดกึ่งกลางตัว Grid */
        justify-content: center;
    }

    .custom-grid {
        display: grid;
        gap: 13px;
        width: fit-content;
        /* ให้ความกว้างพอดีกับเนื้อหา */
    }

    /* บนมือถือ/แท็บเล็ต: ให้ Auto ตามความกว้างหน้าจอ */
    @media (max-width: 1199px) {
        .custom-grid {
            grid-template-columns: repeat(auto-fit, 16.5rem);
            justify-content: center;
        }
    }

    /* บน Desktop (จอใหญ่): บังคับ 4 คอลัมน์ เท่านั้น */
    @media (min-width: 1200px) {
        .custom-grid {
            /* เลข 4 คือจำนวนต่อบรรทัด */
            grid-template-columns: repeat(4, 16.5rem);
        }
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
                <li class="breadcrumb-item"><a href="./?p=promotions" class="plain-link text-dark">โปรโมชัน</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>

        <?php if ($qry->num_rows > 0): ?>

            <div class="d-flex justify-content-between mt-3 mb-3">
                <h3>คูปองแนะนำ</h3>
            </div>

            <div class="coupon-grid-wrapper">
                <div class="custom-grid">
                    <?php while ($row = $qry_limit->fetch_assoc()): ?>
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
                                            <a href="#" class="text-white copy" id="copy-button-<?= $row['coupon_code'] ?>" data-code="<?= $row['coupon_code'] ?>"><u>คัดลอก</u></a>
                                        </div>
                                    </div>
                                    <p class="card-text coupon-code"><?= $row['coupon_code'] ?></p>
                                    <p class="card-text coupon-description"><?= $row['description'] ?></p>
                                    <small>
                                        <div class="d-flex justify-content-between">
                                            <span>วันนี้ ถึง <?= formatDateThai($row['end_date']) ?></span>
                                            <a class="text-white" type="button" id="coupon_code_conditions" data-coupon-id="<?= $row['id'] ?>">เงื่อนไข</a>
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

    </div>
</section>

<section class="py-3">
    <div class="container">
        <h3>คูปองส่งฟรีทั้งหมด</h3>
        <div class="card rounded-0 pt-4 border-0">
            <?php if ($qry_free_shipping->num_rows > 0): ?>

                <div class="coupon-grid-wrapper">
                    <div class="custom-grid">
                        <?php while ($row = $qry_free_shipping->fetch_assoc()): ?>
                            <div class="card" style="width: 16.5rem;">
                                <div class="card-img" style="position: relative;">
                                    <img src="../uploads/coupon/coupon.webp" class="card-img-top" alt="Coupon Image">
                                    <div class="card-body card-coupon-body">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label class="card-title coupon-head mb-0">
                                                <?php echo ($row['type'] == 'free_shipping') ? 'ส่งฟรี' : 'ลด ' . $row['discount_value']; ?>
                                            </label>
                                            <div class="copy-tooltip">
                                                <span class="tooltip-text">คัดลอกแล้ว</span>
                                                <a href="#" class="text-white copy" id="copy-button-<?= $row['coupon_code'] ?>" data-code="<?= $row['coupon_code'] ?>"><u>คัดลอก</u></a>
                                            </div>
                                        </div>
                                        <p class="card-text coupon-code"><?= $row['coupon_code'] ?></p>
                                        <p class="card-text coupon-description"><?= $row['description'] ?></p>
                                        <small>
                                            <div class="d-flex justify-content-between">
                                                <span>วันนี้ ถึง <?= formatDateThai($row['end_date']) ?></span>
                                                <a class="text-white" type="button" id="coupon_code_conditions" data-coupon-id="<?= $row['id'] ?>">เงื่อนไข</a>
                                            </div>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>

            <?php else: ?>
                <div class="d-flex justify-content-center align-items-center py-5">
                    <h4 class="text-muted">ไม่มีคูปองส่งฟรีในขณะนี้</h4>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-3">
    <div class="container">
        <h3>คูปองทั้งหมด</h3>
        <div class="card rounded-0 pt-4 border-0">
            <div class="coupon-grid-wrapper">
                <div class="custom-grid">
                    <?php
                    mysqli_data_seek($qry, 0);
                    while ($row = $qry->fetch_assoc()): ?>
                        <div class="card" style="width: 16.5rem;">
                            <div class="card-img" style="position: relative;">
                                <img src="../uploads/coupon/coupon.webp" class="card-img-top" alt="Coupon Image">
                                <div class="card-body card-coupon-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="card-title coupon-head mb-0">
                                            <?php echo ($row['type'] == 'free_shipping') ? 'ส่งฟรี' : 'ลด ' . $row['discount_value']; ?>
                                        </label>
                                        <div class="copy-tooltip">
                                            <span class="tooltip-text">คัดลอกแล้ว</span>
                                            <a href="#" class="text-white copy" id="copy-button-<?= $row['coupon_code'] ?>" data-code="<?= $row['coupon_code'] ?>"><u>คัดลอก</u></a>
                                        </div>
                                    </div>
                                    <p class="card-text coupon-code"><?= $row['coupon_code'] ?></p>
                                    <p class="card-text coupon-description"><?= $row['description'] ?></p>
                                    <small>
                                        <div class="d-flex justify-content-between">
                                            <span>วันนี้ ถึง <?= formatDateThai($row['end_date']) ?></span>
                                            <a class="text-white" type="button" id="coupon_code_conditions" data-coupon-id="<?= $row['id'] ?>">เงื่อนไข</a>
                                        </div>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php else: ?>
    <div class="d-flex justify-content-center align-items-center py-5">
        <h4 class="text-muted">ไม่มีคูปองโค้ดในขณะนี้</h4>
    </div>
<?php endif; ?>

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