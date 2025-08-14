<style>
    .plain-link {
        color: inherit;
        text-decoration: none;
        cursor: pointer;
        margin-left: 0.5rem;
    }

    .plain-link,
    .plain-link:visited,
    .plain-link:hover,
    .plain-link:active {
        color: inherit;
        text-decoration: none;
    }

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

    .product-item {
        cursor: pointer;
        /* แสดง pointer เมื่อ hover */
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        /* เพิ่ม transition สำหรับ effect */
    }

    /* เมื่อ hover ที่การ์ด จะขยายขนาดเล็กน้อย */
    .product-item:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        /* เพิ่มเงาเมื่อ hover */
    }

    .product-item:hover .product-img {
        transform: scale(1.1)
    }

    .bg-gradient-dark-FIXX {
        background-color: #202020;
    }

    .card-title {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        /* จำนวนบรรทัด */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .banner-price {
        font-size: 25px;
        color: #f57421;
    }

    /* สำหรับป้ายสินค้าหมด */
    .out-of-stock-label {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background-color: rgba(255, 0, 0, 0.7);
        /* สีแดงโปร่งแสง */
        color: white;
        padding: 8px 15px;
        border-radius: 13px;
        font-weight: bold;
        z-index: 10;
        /* ให้แสดงทับบนรูปภาพ */
        text-align: center;
        white-space: nowrap;
    }

    /* สำหรับสินค้าที่หมดสต็อก */
    .out-of-stock .product-img {
        filter: grayscale(100%);
        /* ทำให้รูปภาพเป็นขาวดำ */
        opacity: 0.6;
        /* ทำให้รูปภาพจางลง */
    }

    /* อาจจะเพิ่ม Overlay เมื่อสินค้าหมด */
    .out-of-stock .product-img-holder::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.3);
        /* Overlay สีดำโปร่งแสง */
        z-index: 5;
        /* อยู่ใต้ label แต่ทับรูปภาพ */
    }

    .badge-sm {
        font-size: 12px;
        /* ลดขนาดฟอนต์ */
        padding: 4px 5px;
        /* ปรับ padding */
        background-color: #f79c60;
    }
</style>
<?php
// ... (ส่วนโค้ด PHP ที่อยู่ด้านบนของ products.php เหมือนเดิม) ...

$page_title = "สินค้าทั้งหมด"; // ตั้งชื่อหน้าเริ่มต้น
$page_description = "";
$current_cid = '';
$current_tid = '';
$current_pid = '';

$breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">สินค้าทั้งหมด</li>'; // HTML สำหรับ Breadcrumb เส้นที่ 2 (ค่าเริ่มต้น)

if (isset($_GET['cid']) && is_numeric($_GET['cid'])) {
    $category_qry = $conn->query("SELECT * FROM `category_list` where `id` = '{$_GET['cid']}' and `status` = 1 and `delete_flag` = 0");
    if ($category_qry->num_rows > 0) {
        $cat_result = $category_qry->fetch_assoc();
        $page_title = $cat_result['name'];
        $page_description = $cat_result['description'];
        $current_cid = $_GET['cid'];
        // ถ้ามี CID และพบหมวดหมู่ ให้ Breadcrumb เส้นที่ 2 เป็นลิงก์ไปยังหมวดนั้น
        $breadcrumb_item_2_html = '<li class="breadcrumb-item"><a href="./?p=products&cid=' . $current_cid . '" class="plain-link">' . $cat_result['name'] . '</a></li>';
        // และอาจจะต้องมีเส้นที่ 3 เป็น active item สำหรับหน้าปัจจุบัน ถ้าเป็นหน้าหมวดหมู่ย่อย
        // แต่ในกรณีนี้คุณต้องการแสดงหน้าหมวดหมู่หลักเลย (products) ดังนั้นเส้นนี้ควร active
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">' . $cat_result['name'] . '</li>';
        // ผมคิดว่าคุณต้องการแบบนี้มากกว่า:
        // HOME > ชื่อหมวดหมู่ (เมื่ออยู่หน้าหมวดหมู่นั้นๆ)
        // HOME > สินค้าทั้งหมด (เมื่ออยู่หน้า products ไม่มี cid)
    } else {
        // กรณีที่ cid ไม่ถูกต้องหรือไม่พบหมวดหมู่
        $page_title = "ไม่พบหมวดหมู่";
        $page_description = "หมวดหมู่ที่คุณระบุไม่ถูกต้องหรือไม่สามารถใช้งานได้";
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">ไม่พบหมวดหมู่</li>';
    }
}

if (isset($_GET['tid']) && is_numeric($_GET['tid'])) {
    $product_type_qry = $conn->query("SELECT * FROM `product_type` where `id` = '{$_GET['tid']}' and `status` = 1 and `delete_flag` = 0");
    if ($product_type_qry->num_rows > 0) {
        $pdt_result = $product_type_qry->fetch_assoc();
        $page_title = $pdt_result['name'];
        $page_description = $pdt_result['description'];
        $current_tid = $_GET['tid'];
        $breadcrumb_item_2_html = '<li class="breadcrumb-item"><a href="./?p=products&tid=' . $current_tid . '" class="plain-link">' . $pdt_result['name'] . '</a></li>';
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">' . $pdt_result['name'] . '</li>';
    } else {
        $page_title = "ไม่พบหมวดหมู่";
        $page_description = "หมวดหมู่ที่คุณระบุไม่ถูกต้องหรือไม่สามารถใช้งานได้";
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">ไม่พบหมวดหมู่</li>';
    }
}

// ตรวจสอบการเลือกโปรโมชั่น
if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
    $current_pid = $_GET['pid'];
    // ดึงข้อมูลโปรโมชั่น
    $promotion_qry = $conn->query("SELECT * FROM `promotions_list` WHERE `id` = '{$current_pid}' AND `status` = 1 AND `delete_flag` = 0");
    if ($promotion_qry->num_rows > 0) {
        $promotion_result = $promotion_qry->fetch_assoc();
        $page_title = $promotion_result['name'];
        $page_description = $promotion_result['description'];
        $breadcrumb_item_2_html = '<li class="breadcrumb-item"><a href="./?p=products&pid=' . $current_pid . '" class="plain-link">' . $promotion_result['name'] . '</a></li>';
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">' . $promotion_result['name'] . '</li>';
    } else {
        // กรณีไม่พบโปรโมชั่น
        $page_title = "ไม่พบโปรโมชั่น";
        $page_description = "โปรโมชั่นที่คุณระบุไม่ถูกต้องหรือไม่สามารถใช้งานได้";
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">ไม่พบโปรโมชั่น</li>';
    }
}
?>

<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3" align="center">
            <h1 class=""><?= $page_title ?></h1>
            <?php if (!empty($page_description)): ?>
                <hr>
                <p class="m-0"><small><em><?= html_entity_decode($page_description) ?></em></small></p>
            <?php endif; ?>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link">HOME</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>
        <div class="row mt-n3 justify-content-center">
            <div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center justify-content-end">
                            <div class="col-auto">
                                <label for="sort_by" class="form-label mb-0">เรียงตาม:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="sort_by" onchange="sortProducts()">
                                    <option value="date_desc" <?= (!isset($_GET['sort']) || $_GET['sort'] == 'date_desc') ? 'selected' : '' ?>>สินค้าใหม่ล่าสุด</option>
                                    <option value="date_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'date_asc') ? 'selected' : '' ?>>สินค้าเก่าสุด</option>
                                    <option value="price_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : '' ?>>ราคา: น้อยไปมาก</option>
                                    <option value="price_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : '' ?>>ราคา: มากไปน้อย</option>
                                    <option value="name_asc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : '' ?>>ชื่อ: A-Z</option>
                                    <option value="name_desc" <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : '' ?>>ชื่อ: Z-A</option>
                                </select>
                            </div>
                        </div>
                        <div class="row gy-3 gx-3" id="product-list-container">
                            <div class="col-12 text-center py-5" id="loading-spinner">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                                <p class="mt-2">กำลังโหลดสินค้า...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // เก็บค่า category ID ปัจจุบันจาก PHP เพื่อใช้ใน JavaScript
    var currentCid = "<?= $current_cid ?>";
    var currentTid = "<?= $current_tid ?>";
    var currentPid = "<?= $current_pid ?>"

    function sortProducts() {
        var sortBy = $('#sort_by').val(); // ดึงค่าที่เลือกจาก dropdown
        var productContainer = $('#product-list-container');
        var loadingSpinner = $('#loading-spinner');

        // แสดง loading spinner
        loadingSpinner.show();
        productContainer.html(''); // เคลียร์เนื้อหาเก่า

        $.ajax({
            url: './ajax/fetch_products.php', // แก้ไขพาธนี้ให้ถูกต้อง (เช่น './products/fetch_products.php' หรือ './fetch_products.php')
            method: 'GET',
            data: {
                sort: sortBy,
                cid: currentCid,
                tid: currentTid,
                pid: currentPid
            },
            success: function(response) {
                // ซ่อน loading spinner
                loadingSpinner.hide();
                productContainer.html(response); // นำ HTML ที่ได้มาใส่ใน container
            },
            error: function(xhr, status, error) {
                // ซ่อน loading spinner
                loadingSpinner.hide();
                console.error("AJAX Error: ", status, error);
                productContainer.html('<div class="col-12 text-center py-5 text-danger">เกิดข้อผิดพลาดในการโหลดสินค้า กรุณาลองใหม่อีกครั้ง</div>');
            }
        });
    }

    // เรียกใช้ฟังก์ชันเมื่อหน้าโหลดเสร็จครั้งแรก เพื่อแสดงสินค้าตามการเรียงลำดับเริ่มต้น
    $(document).ready(function() {
        sortProducts();
    });
</script>