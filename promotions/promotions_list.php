<?php
$qry_promo_recommand = $conn->query("
    SELECT * 
    FROM promotions_list 
    ORDER BY 
        -- ให้ 'freeshipping' อยู่หน้าสุด
        CASE 
            WHEN type = 'free_shipping' THEN 0 
            WHEN type = 'percent' THEN 1 
            ELSE 2 
        END, 
        
        -- ถ้าประเภทเป็น 'percent' คำนวณส่วนลดจาก 'discount_value' และจัดลำดับจากมากไปหาน้อย
        CASE 
            WHEN type = 'percent' THEN discount_value 
            ELSE 0 
        END DESC,
        
        -- ตรวจสอบ 'minimum_order' เพื่อให้รายการที่มี 'minimum_order' ต่ำสุดอยู่หน้าสุด
        minimum_order ASC,
        
        -- ใช้วันที่สร้าง (ถ้าต้องการใช้)
        date_created ASC,

        -- กรณีที่ต้องการให้จัดตามชื่อ (ถ้าต้องการ)
        name ASC
    LIMIT 8
");


$qry_promo_free_shipping = $conn->query("SELECT * FROM promotions_list WHERE type = 'free_shipping' ORDER BY date_created ASC, name ASC");
$qry_promo = $conn->query("SELECT * FROM promotions_list ORDER BY date_created ASC, name ASC");
// ตรวจสอบวันหมดอายุของโปรโมชั่น
$current_time = time(); // เวลาปัจจุบัน
$pro_qry = $conn->query("SELECT * FROM `promotions_list` 
                           WHERE `status` = 1 AND `delete_flag` = 0 AND `promotion_category_id` = {$pcid} 
                           ORDER BY `date_created` ASC");

$has_active_promotions = false; // ตัวแปรเพื่อตรวจสอบว่ามีโปรโมชั่นที่ยังคงใช้งานได้หรือไม่


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

$page_title = "โปรโมชั่นทั้งหมด"; // ตั้งชื่อหน้าเริ่มต้น
$page_description = "";

$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">โปรโมชั่นทั้งหมด</li>'; // HTML สำหรับ Breadcrumb เส้นที่ 2 (ค่าเริ่มต้น)

?>
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
                <li class="breadcrumb-item"><a href="./?p=promotions" class="plain-link text-dark">โปรโมชั่น</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>
        <div class="d-flex justify-content-between mt-3">
            <h3>โปรโมชั่นแนะนำ</h3>
        </div>
        <div class="card rounded-0 pt-4">
            <div class="container-custom">
                <div class="card-group">
                    <div class="row g-4">
                        <?php
                        mysqli_data_seek($qry_promo_recommand, 0);
                        while ($row = $qry_promo_recommand->fetch_assoc()): ?>
                            <div class="col-md-3 mb-4">
                                <!-- ลิงก์ไปยังหน้าโปรโมชั่น -->
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
            </div>
        </div>
    </section>

    <section class="mx-5">
        <div class="d-flex justify-content-between mt-3">
            <h3>โปรโมชั่นส่งฟรีทั้งหมด</h3>
        </div>
        <div class="card rounded-0 pt-4">
            <div class="container-custom">
                <div class="card-group">
                    <div class="row g-4">
                        <?php
                        mysqli_data_seek($qry_promo_free_shipping, 0);
                        while ($row = $qry_promo_free_shipping->fetch_assoc()): ?>
                            <div class="col-md-3 mb-4">
                                <!-- ลิงก์ไปยังหน้าโปรโมชั่น -->
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
            </div>
        </div>
    </section>

    <section class="pt-5 mx-5">
        <div class="d-flex justify-content-between mt-3">
            <h3>โปรโมชั่นทั้งหมด</h3>
        </div>
        <div class="card rounded-0 pt-4">
            <div class="container-custom">
                <div class="card-group">
                    <div class="row g-4">
                        <?php
                        mysqli_data_seek($qry_promo, 0);
                        while ($row = $qry_promo->fetch_assoc()): ?>
                            <div class="col-md-3 mb-4">
                                <!-- ลิงก์ไปยังหน้าโปรโมชั่น -->
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
            </div>
        </div>
    </section>
</div>