<?php
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
    LIMIT 8
");
$qry_promo_free_shipping = $conn->query("SELECT * FROM promotions_list WHERE type = 'free_shipping' AND status = 1 AND delete_flag = 0 AND start_date <= NOW() AND end_date >= NOW() ORDER BY date_created ASC, name ASC");
$qry_promo = $conn->query("SELECT * FROM promotions_list WHERE status = 1 AND delete_flag = 0 AND start_date <= NOW() AND end_date >= NOW()  ORDER BY date_created ASC, name ASC");
// ตรวจสอบวันหมดอายุของโปรโมชัน
$current_time = time(); // เวลาปัจจุบัน

$has_active_promotions = false; // ตัวแปรเพื่อตรวจสอบว่ามีโปรโมชันที่ยังคงใช้งานได้หรือไม่


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

$page_title = "โปรโมชันทั้งหมด"; // ตั้งชื่อหน้าเริ่มต้น
$page_description = "";

$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">โปรโมชันทั้งหมด</li>'; // HTML สำหรับ Breadcrumb เส้นที่ 2 (ค่าเริ่มต้น)

// $qry_promo คือโปรโมชันทั้งหมดอยู่แล้ว ใช้ตัวนี้ตัวเดียวในการนับ total
$total_promotions = $qry_promo->num_rows;
?>

<style>
    .custom-promo-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start !important;
        /* ชิดซ้าย */
        gap: 24px;
    }

    .custom-promo-card-wrapper {
        width: 323.5px;
        display: flex;
    }

    .custom-promo-card-wrapper a.text-decoration-none {
        display: block;
        width: 100%;
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
                <li class="breadcrumb-item"><a href="./?p=promotions" class="plain-link text-dark">โปรโมชัน</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>
        <?php if ($total_promotions > 0): ?>
            <?php if ($qry_promo_recommand->num_rows > 0): ?>
                <div class="d-flex justify-content-between mt-3">
                    <h3>โปรโมชันแนะนำ</h3>
                </div>
                <div class="card rounded-0 pt-4 px-4">
                    <div class="custom-promo-container">
                        <?php
                        mysqli_data_seek($qry_promo_recommand, 0);
                        while ($row = $qry_promo_recommand->fetch_assoc()): ?>
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
                        <?php endwhile; ?>
                    </div>
                </div>
            <?php endif; ?>
    </section>

    <?php if ($qry_promo_free_shipping->num_rows > 0): ?>
        <section class="mx-5">
            <div class="d-flex justify-content-between mt-3">
                <h3>โปรโมชันส่งฟรีทั้งหมด</h3>
            </div>
            <div class="card rounded-0 pt-4 px-4">
                <div class="custom-promo-container">
                    <?php
                    mysqli_data_seek($qry_promo_free_shipping, 0);
                    while ($row = $qry_promo_free_shipping->fetch_assoc()): ?>
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
                    <?php endwhile; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($qry_promo->num_rows > 0): ?>
        <section class="pt-5 mx-5">
            <div class="d-flex justify-content-between mt-3">
                <h3>โปรโมชันทั้งหมด</h3>
            </div>
            <div class="card rounded-0 pt-4 px-4">
                <div class="custom-promo-container">
                    <?php
                    mysqli_data_seek($qry_promo, 0);
                    while ($row = $qry_promo->fetch_assoc()): ?>
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
                    <?php endwhile; ?>

                </div>
            </div>
        </section>
    <?php endif; ?>
<?php else: ?>
    <div class="d-flex justify-content-center align-items-center py-5">
        <h4 class="text-muted">ไม่มีโปรโมชันในขณะนี้</h4>
    </div>
<?php endif; ?>
</div>