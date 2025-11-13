<?php
$page_title = "ติดต่อเรา"; // ตั้งชื่อหน้าเริ่มต้น
$page_description = "รวมคูปองโค้ด โปรโมชันเพื่อคุณ";

?>

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

    .card-contact {
        border-radius: 13px;
    }

    .contact-info dt {
        font-size: 17px;
        padding-bottom: 2px;
    }

    .contact-info dd {
        padding-bottom: 4px;
    }

    .contact-info .yt {
        color: #FF0000;
    }

    .icon-contact {
        font-size: 20px;
    }

    .company {
        font-size: 24px !important;
        padding-bottom: 4px;
    }
</style>

<section class="py-5">
    <div class="container">
        <div class="content py-4">
            <h1 class=" text-center">
                <?= $page_title ?>
            </h1>
        </div>
        <div class="card card-contact">
            <div class="card-body">
                <dl class="contact-info">

                    <?php if (!empty($_settings->info('company_name'))): ?>
                        <dt class="company"><i class="fa-solid fa-building" style="color: #ff6f00;"></i> <?= $_settings->info('company_name') ?></dt>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('head_office'))): ?>
                        <dt><i class="fa fa-location-dot icon-contact" style="color: red;"></i> สำนักงานใหญ่</dt>
                        <dd><?= $_settings->info('head_office') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('branch_office'))): ?>
                        <dt><i class="fa fa-location-dot icon-contact text-primary"></i> สาขาย่อย</dt>
                        <dd><?= $_settings->info('branch_office') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('phone'))): ?>
                        <dt><i class="fa fa-phone icon-contact" style="color: #28d230;"></i> หมายเลขโทรศัพท์</dt>
                        <dd><?= $_settings->info('phone') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('mobile'))): ?>
                        <dt><i class="fa-solid fa-mobile-screen-button icon-contact" style="color: #25A7DA;"></i> หมายเลขมือถือ</dt>
                        <dd><?= $_settings->info('mobile') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('email'))): ?>
                        <dt><i class="fa fa-envelope icon-contact" style="color: #dc3545;"></i> อีเมล</dt>
                        <dd><?= $_settings->info('email') ?></dd>
                    <?php endif; ?>

                    <?php if (!empty($_settings->info('office_hours'))): ?>
                        <dt><i class="fa fa-clock icon-contact"></i> วันเวลาเปิดทำการบริษัท</dt>
                        <dd><?= $_settings->info('office_hours') ?></dd>
                    <?php endif; ?>


                    <?php
                    // --- [START] ส่วนจัดการ Social Media ---

                    // 1. สร้าง Array ว่างๆ เพื่อเก็บลิงก์
                    $social_links = [];

                    // 2. ตรวจสอบทีละค่า ถ้ามีข้อมูล ให้สร้าง HTML แล้วยัดใส่ Array
                    if (!empty($_settings->info('Line'))) {
                        $social_links[] = '<i class="fab fa-line text-success icon-contact"></i> <a href="https://line.me/ti/p/~' . htmlspecialchars($_settings->info('Line')) . '" target="_blank">Line</a>';
                    }
                    if (!empty($_settings->info('Facebook'))) {
                        $social_links[] = '<i class="fab fa-facebook text-primary icon-contact"></i> <a href="' . htmlspecialchars($_settings->info('Facebook')) . '" target="_blank"> Facebook</a>';
                    }
                    if (!empty($_settings->info('Instagram'))) {
                        $social_links[] = '<i class="fab fa-instagram text-danger icon-contact"></i> <a href="' . htmlspecialchars($_settings->info('Instagram')) . '" target="_blank"> Instagram</a>';
                    }
                    if (!empty($_settings->info('YouTube'))) {
                        $social_links[] = '<i class="fab fa-youtube icon-contact yt"></i> <a href="' . htmlspecialchars($_settings->info('YouTube')) . '" target="_blank"> YouTube</a>';
                    }
                    if (!empty($_settings->info('TikTok'))) {
                        $social_links[] = '<i class="fa-brands fa-tiktok icon-contact"></i> <a href="' . htmlspecialchars($_settings->info('TikTok')) . '" target="_blank"> TikTok</a>';
                    }

                    // 3. ตรวจสอบว่ามีลิงก์ใน Array อย่างน้อย 1 อันหรือไม่
                    if (!empty($social_links)):
                    ?>
                        <dt><i class="fa fa-globe icon-contact" style="color: skyblue;"></i> Social Media</dt>
                        <dd>
                            <?php
                            // 4. เชื่อมทุกอย่างใน Array ด้วย ", "
                            echo implode(', ', $social_links);
                            ?>
                        </dd>
                    <?php
                    endif;
                    // --- [END] ส่วนจัดการ Social Media ---
                    ?>

                </dl>
            </div>
        </div>
    </div>
</section>