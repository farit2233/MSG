<style>
    /*.carousel-item>img{
        object-fit:cover !important;
    }*/
    .carousel {
        width: 100% !important;
        overflow: hidden !important;
        right: 0 !important;
    }

    .carousel-inner {
        aspect-ratio: 1920 / 600;
        /* หรือ 3.2 */
    }

    .carousel-item {
        height: 100%;
    }

    .carousel-item img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /*#carouselExampleControls .carousel-inner{
        height:35em !important;
    }*/
    .product-img-holder {
        width: 100%;
        aspect-ratio: 1 / 1;
        /* ทำให้กล่องภาพเป็นจัตุรัส */
        overflow: hidden;
        background: #f5f5f5;
        position: relative;
    }

    .product-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center center;
        transition: all .3s ease-in-out;
    }

    .product-item:hover .product-img {
        transform: scale(1.1)
    }

    .bg-gradient-dark-FIXX {
        background-color: #202020;
    }

    .banner-wrapper {
        width: 100%;
        height: auto;
        object-fit: cover;
        /* ครอบคลุมทั้งจอ */
        display: block;
    }

    .banner-wrapper img {
        width: 100%;
        /* เต็มความกว้าง */
        height: auto;
        /* รักษาสัดส่วนภาพ */
        display: block;
        object-fit: cover;
        /* หรือ contain แล้วแต่ภาพ */
    }

    .baby-font {
        color: white;
        font-weight: bold;
        font-size: 20px;
    }

    .card-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* จำนวนบรรทัด */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-bb02 {
        background-color: #fc573b;
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: center;
        margin: 4px 2px;
        transition: 0.3s;
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        filter: brightness(100%);
        transition: all 0.2s ease-in-out;
    }

    .btn-bb34 {
        background-color: #be63f9;
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: center;
        margin: 4px 2px;
        transition: 0.3s;
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        filter: brightness(100%);
        transition: all 0.2s ease-in-out;
    }

    .btn-bb56 {
        background-color: #ffd200;
        border: none;
        color: white;
        padding: 16px 32px;
        text-align: center;
        margin: 4px 2px;
        transition: 0.3s;
        display: inline-block;
        text-decoration: none;
        cursor: pointer;
        filter: brightness(100%);
        transition: all 0.2s ease-in-out;
    }

    .btn-bb02,
    .btn-bb34,
    .btn-bb56 {
        border-radius: 13px;
    }

    .btn-bb02:hover,
    .btn-bb34:hover,
    .btn-bb56:hover,
    .btn-product:hover {
        color: white;
        filter: brightness(90%);
    }

    .btn-product {
        font-size: 20px;
        color: white;
        background-color: #f57421;
        padding: 10px 100px;
        margin: 1rem;
        transition: all 0.2s ease-in-out;
    }

    .banner-price {
        font-size: 20px;
        color: #f57421;
    }

    .badge-sm {
        font-size: 12px;
        /* ลดขนาดฟอนต์ */
        padding: 4px 5px;
        /* ปรับ padding */
        background-color: #f79c60;
    }

    @media only screen and (max-width: 768px) {
        .btn-product {
            padding: 10px 80px;
        }
    }
</style>
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

    <div class="container">
        <div class="container py-5">
            <h2 class="text-center mb-4"> ของเล่นหมวดหมู่ <span class="text-primary">ตามอายุ</span></h2>
            <p class="text-center mb-5">"เลือกของเล่นที่ใช่ ตามวัยของลูก"</p>

            <div class="row text-center justify-content-center">
                <div class="col-6 col-md-2  mb-4">
                    <a href="?p=products&cid=1">
                        <img src="./uploads/icon/baby0-12.png" alt="0 – 2 ปี" class="img-fluid mb-2">
                        <button type="button" class="btn baby-font btn-bb02">0 – 2 ปี</button>
                    </a>
                </div>
                <div class="col-6 col-md-2 mb-4">
                    <a href="?p=products&cid=2">
                        <img src="./uploads/icon/baby3-4.png" alt="3 – 4 ปี" class="img-fluid mb-2">
                        <button type="button" class="btn baby-font btn-bb34">3 – 4 ปี</button>
                    </a>
                </div>
                <div class="col-6 col-md-2 mb-4">
                    <a href="?p=products&cid=3">
                        <img src="./uploads/icon/baby5-6.png" alt="5 – 6 ปี" class="img-fluid mb-2">
                        <button type="button" class="btn baby-font btn-bb56">5 – 6 ปี</button>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
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
                                    ORDER BY 
                                        IF(discounted_price IS NOT NULL AND discounted_price < price, 1, 0) DESC, 
                                        RAND() 
                                    LIMIT 4
                                ");
                            while ($row = $qry->fetch_assoc()):
                            ?>
                                <div class="col-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">

                                    <a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100" href="./?p=products/view_product&id=<?= $row['id'] ?>">
                                        <div class="position-relative">
                                            <div class="img-top position-relative product-img-holder">
                                                <img src="<?= validate_image($row['image_path']) ?>" alt="" class="product-img">
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div style="line-height:1.5em">
                                                <div class="card-title w-100 mb-0"><?= $row['name'] ?></div>
                                                <div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
                                                    <div class="w-100">
                                                        <small class="text-muted" style="line-height: 1.25em; display: block;">
                                                            <?= $row['brand'] ?>
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="d-flex justify-content-end align-items-center">
                                                    <?php if (!is_null($row['discounted_price']) && $row['discounted_price'] < $row['price']): ?>
                                                        <?php
                                                        // คำนวณส่วนลด
                                                        $discount_percentage = round((($row['price'] - $row['discounted_price']) / $row['price']) * 100);
                                                        ?>

                                                        <span class="banner-price fw-bold me-2"><?= format_price_custom($row['discounted_price'], 2) ?> ฿</span>
                                                        <span class="badge badge-sm text-white">ลด <?= $discount_percentage ?>%</span>

                                                    <?php else: ?>
                                                        <span class="banner-price"><?= format_price_custom($row['price'], 2) ?> ฿</span>
                                                    <?php endif; ?>
                                                </div>

                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                    <div class="text-center py-1">
                        <a href="./?p=products" class="btn btn-product rounded-pill">ดูสินค้าอื่น ๆ</a>
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