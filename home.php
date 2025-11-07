<section class="py-3 flex-fill">
    <div id="carouselExampleControls" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <a href="?p=products">
                <?php
                $upload_path = "uploads/banner";
                if (is_dir(base_app . $upload_path)):
                    $file = scandir(base_app . $upload_path);
                    $_i = 0;
                    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    foreach ($file as $img):
                        if (in_array($img, array('.', '..')))
                            continue;

                        $ext = strtolower(pathinfo($img, PATHINFO_EXTENSION));
                        if (!in_array($ext, $allowed_ext))
                            continue;

                        $_i++;
                ?>
                        <div class="carousel-item w-100 h-100 <?php echo $_i == 1 ? "active" : '' ?>">
                            <img src="<?php echo validate_image($upload_path . '/' . $img) ?>" class="d-block w-100" alt="<?php echo $img ?>">
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </a>
        </div>
        <a class="carousel-control-prev" href="#carouselExampleControls" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleControls" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <div class="container py-5">
        <h2 class="text-center mb-4">ประเภทสินค้า</h2>
        <p class="text-center mb-5">"สำรวจประเภทสินค้าที่ออกแบบมาเพื่อสนับสนุนการเรียนรู้ของเด็ก พร้อมตอบโจทย์ความต้องการของคุณครู และผู้ปกครอง"</p>
        <div class="row gx-3 gy-4 justify-content-center">
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products&tid=1" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-puzzle-piece fa-2x mb-2 text-primary"></i>
                            <h6 class="mb-0">เครื่องเล่น</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products&tid=2" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-gamepad fa-2x mb-2 text-danger"></i>
                            <h6 class="mb-0">ของเล่น</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products&tid=3" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-book fa-2x mb-2 text-success"></i>
                            <h6 class="mb-0">หนังสือ</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products&tid=4" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-film fa-2x mb-2 text-warning"></i>
                            <h6 class="mb-0">สื่อ</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products&tid=5" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-clipboard-check fa-2x mb-2 text-info"></i>
                            <h6 class="mb-0">ชุดประเมิน</h6>
                        </div>
                    </div>
                </a>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2">
                <a href="?p=products" class="text-decoration-none">
                    <div class="card shadow-sm border-0 text-center h-100 hover-card">
                        <div class="card-body d-flex flex-column align-items-center justify-content-center">
                            <i class="fa-solid fa-ellipsis fa-2x mb-2 text-secondary"></i>
                            <h6 class="mb-0">อื่น ๆ</h6>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row mt-n3 justify-content-center">
            <div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <h1 align="center">สินค้าใหม่</h1>
                        <div class="row gy-3 gx-3">
                            <?php
                            if (!function_exists('format_price_custom')) {
                                function format_price_custom($price)
                                {
                                    $formatted_price = format_num($price, 2);
                                    if (substr($formatted_price, -3) == '.00') {
                                        return format_num($price, 0);
                                    }
                                    return $formatted_price;
                                }
                            }

                            $qry = $conn->query("
                                SELECT *, 
                                    (COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id), 0) - 
                                    COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) as `available` 
                                FROM `product_list` 
                                WHERE 
                                    (COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id), 0) - 
                                    COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) > 0 
                                    AND `status` = 1
                                    AND `delete_flag` = 0
                                ORDER BY 
                                    `date_created` DESC 
                                LIMIT 8
                            ");
                            while ($row = $qry->fetch_assoc()):
                            ?>
                                <div class="col-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
                                    <a class="card rounded-0 product-item text-decoration-none text-reset h-100" href="./?p=products/view_product&id=<?= $row['id'] ?>">
                                        <div class="position-relative">
                                            <div class="img-top position-relative product-img-holder">
                                                <div class="position-absolute top-0 start-0 bg-danger text-white px-2 py-1 small" style="z-index: 1;">
                                                    ใหม่
                                                </div>
                                                <?php
                                                // 1. ดึง Path หลัก
                                                $new_product_main_path = $row['image_path'];
                                                // 2. แปลงเป็น Path ขนาดกลาง (Medium)
                                                $new_product_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_medium.webp$2', $new_product_main_path);
                                                ?>
                                                <img src="<?= validate_image($new_product_medium_path) ?>" alt="" class="product-img">
                                            </div>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <div>
                                                <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                                                <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                                                    <div class="w-100">
                                                        <small class="text-muted" style="line-height: 1.25em; display: block;">
                                                            <?= $row['brand'] ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-auto">
                                                <?php
                                                $display_price = isset($row['price']) && $row['price'] > 0 ? $row['price'] : 0;
                                                if (!is_null($row['discounted_price']) && $row['discounted_price'] > 0 && $row['discounted_price'] < $row['price']) {
                                                    $display_price = $row['discounted_price'];
                                                    $discount_percentage = round((($row['vat_price'] - $row['discounted_price']) / $row['vat_price']) * 100);
                                                    echo '<span class="banner-price fw-bold me-2">' . format_price_custom($display_price) . ' ฿</span>';
                                                    echo '<span class="badge badge-sm prdouct-badge text-white">- ' . $discount_percentage . '%</span>';
                                                } elseif (!is_null($row['vat_price']) && $row['vat_price'] > 0) {
                                                    $display_price = $row['vat_price'];
                                                    echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
                                                } else {
                                                    echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container py-4 text-center">
        <div class="position-relative d-inline-block">
            <span class="text-muted px-3 position-relative z-1" style="font-weight: 500;">
                หรือดูสินค้าทั้งหมด
            </span>
            <div class="position-absolute top-50 start-0 w-100 translate-middle-y" style="height: 1px; background: #ccc; z-index: 0;"></div>
        </div>
        <div class="pt-3">
            <a href="./?p=products" class="btn btn-product rounded-pill">
                ดูสินค้าอื่น ๆ คลิก <span class="arrow-move"><i class="fa-solid fa-angles-right"></i></span>
            </a>
        </div>
    </div>

    <div class="container py-4">
        <div class="row mt-n3 justify-content-center">
            <div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <h1 align="center">สินค้าแนะนำ</h1>
                        <div class="row gy-3 gx-3">
                            <?php
                            if (!function_exists('format_price_custom')) {
                                function format_price_custom($price)
                                {
                                    $formatted_price = format_num($price, 2);
                                    if (substr($formatted_price, -3) == '.00') {
                                        return format_num($price, 0);
                                    }
                                    return $formatted_price;
                                }
                            }

                            $qry = $conn->query("
                                SELECT *, 
                                    (COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id), 0) - 
                                    COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) as `available` 
                                FROM `product_list` 
                                WHERE 
                                    (COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id), 0) - 
                                    COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) > 0 
                                    AND `status` = 1
                                    AND `delete_flag` = 0
                                ORDER BY 
                                    IF(discounted_price IS NOT NULL AND discounted_price < price, 1, 0) DESC, 
                                    RAND() 
                                LIMIT 24
                            ");
                            while ($row = $qry->fetch_assoc()):
                            ?>
                                <div class="col-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
                                    <a class="card rounded-0 product-item text-decoration-none text-reset h-100" href="./?p=products/view_product&id=<?= $row['id'] ?>">
                                        <div class="position-relative">
                                            <div class="img-top position-relative product-img-holder">
                                                <?php
                                                // 1. ดึง Path หลัก
                                                $rec_product_main_path = $row['image_path'];
                                                // 2. แปลงเป็น Path ขนาดกลาง (Medium)
                                                $rec_product_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_medium.webp$2', $rec_product_main_path);
                                                ?>
                                                <img src="<?= validate_image($rec_product_medium_path) ?>" alt="" class="product-img">
                                            </div>
                                        </div>
                                        <div class="card-body d-flex flex-column">
                                            <div>
                                                <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                                                <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                                                    <div class="w-100">
                                                        <small class="text-muted" style="line-height: 1.25em; display: block;">
                                                            <?= $row['brand'] ?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="d-flex justify-content-end align-items-center mt-auto">
                                                <?php
                                                $display_price = isset($row['price']) && $row['price'] > 0 ? $row['price'] : 0;
                                                if (!is_null($row['discounted_price']) && $row['discounted_price'] > 0 && $row['discounted_price'] < $row['price']) {
                                                    $display_price = $row['discounted_price'];
                                                    $discount_percentage = round((($row['vat_price'] - $row['discounted_price']) / $row['vat_price']) * 100);
                                                    echo '<span class="banner-price fw-bold me-2">' . format_price_custom($display_price) . ' ฿</span>';
                                                    echo '<span class="badge badge-sm prdouct-badge text-white">- ' . $discount_percentage . '%</span>';
                                                } elseif (!is_null($row['vat_price']) && $row['vat_price'] > 0) {
                                                    $display_price = $row['vat_price'];
                                                    echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
                                                } else {
                                                    echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const guestCart = JSON.parse(localStorage.getItem('guest_cart') || '[]');
            if (guestCart.length > 0) {
                fetch('classes/Master.php?f=migrate_guest_cart', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            cart: guestCart
                        })
                    })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.status === 'success') {
                            localStorage.removeItem('guest_cart');
                            location.href = 'index.php?page=cart'; // หรือไปหน้าอื่นที่ต้องการ
                        }
                    });
            }
        });
    </script>
<?php endif; ?>