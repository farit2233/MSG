<style>
    .page-subtitle {
        font-size: 20px;
        font-weight: normal;
        font-style: italic;
    }

    .separator {
        font-size: 18px;
        padding: 0 10px;
        font-weight: bold;
        color: #333;
    }

    .shopee-style-filter {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        border: 1px solid #e0e0e0;
        border-radius: 4px;
        background-color: #ffffff;
    }

    .filter-header {
        font-size: 16px;
        font-weight: 500;
        color: #333;
        text-transform: uppercase;
    }

    .price-inputs .form-control {
        border: 1px solid #ced4da;
        border-radius: 2px;
        font-size: 14px;
        height: 34px;
    }

    .price-inputs .form-control::placeholder {
        color: #bbb;
    }

    .price-inputs .form-control:focus {
        border-color: #ef3624;
        box-shadow: 0 0 0 0.2rem rgba(238, 77, 45, 0.25);
    }

    .price-inputs .separator {
        color: #757575;
    }

    .btn-filter {
        background-color: #ef3624;
        color: #fff;
        border: 1px solid #ee4d2d;
        border-radius: 2px;
        font-size: 14px;
        font-weight: 500;
        padding: 0.5rem;
        transition: all 0.2s ease-in-out;
    }

    .btn-filter:hover {
        filter: brightness(90%);
        color: white;
    }


    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;
    }
</style>
<?php
$page_title = "สินค้าทั้งหมด";
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

// ตรวจสอบการเลือกโปรโมชัน
if (isset($_GET['pid']) && is_numeric($_GET['pid'])) {
    $current_pid = $_GET['pid'];
    // ดึงข้อมูลโปรโมชัน
    $promotion_qry = $conn->query("SELECT * FROM `promotions_list` WHERE `id` = '{$current_pid}' AND `status` = 1 AND `delete_flag` = 0");
    if ($promotion_qry->num_rows > 0) {
        $promotion_result = $promotion_qry->fetch_assoc();
        $page_title = $promotion_result['name'];
        $page_description = $promotion_result['description'];
        $breadcrumb_item_2_html = '<li class="breadcrumb-item"><a href="./?p=products&pid=' . $current_pid . '" class="plain-link">' . $promotion_result['name'] . '</a></li>';
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">' . $promotion_result['name'] . '</li>';
    } else {
        // กรณีไม่พบโปรโมชัน
        $page_title = "ไม่พบโปรโมชัน";
        $page_description = "โปรโมชันที่คุณระบุไม่ถูกต้องหรือไม่สามารถใช้งานได้";
        $breadcrumb_item_2_html = '<li class="breadcrumb-item active" aria-current="page">ไม่พบโปรโมชัน</li>';
    }
}

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
?>

<section class="py-3">
    <div class="container">
        <div class="content py-5 px-3">
            <h1>
                <?= $page_title ?>

                <?php if (!empty($page_description)): ?>
                    <em class="page-subtitle"> - <?= html_entity_decode($page_description) ?></em>
                <?php endif; ?>
            </h1>
            <hr>
        </div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent px-0">
                <li class="breadcrumb-item"><a href="./" class="plain-link">HOME</a></li>
                <?= $breadcrumb_item_2_html ?>
            </ol>
        </nav>
        <div class="row">
            <div class="col-lg-3 col-md-4">
                <div class="card rounded-0 mb-3">
                    <div class="card-body">
                        <h5 class="mb-3">หมวดหมู่สินค้า</h5>
                        <div id="category-filter-group">
                            <?php
                            $categories_qry = $conn->query("
                                SELECT id, `name`, `other` 
                                FROM `category_list` 
                                WHERE `status` = 1 AND `delete_flag` = 0 
                                ORDER BY `other` ASC, `name` DESC
                            ");
                            $counter = 0;
                            $max_categories = 10;
                            while ($cat = $categories_qry->fetch_assoc()):
                                $counter++;
                            ?>
                                <div class="form-check category-item custom-category-gap <?= $counter > $max_categories ? 'd-none' : '' ?>">
                                    <input class="form-check-input category-filter" type="checkbox" value="<?= $cat['id'] ?>" id="cat_<?= $cat['id'] ?>" <?= ($current_cid == $cat['id']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="cat_<?= $cat['id'] ?>">
                                        <?= $cat['name'] ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                        <button class="btn btn-link w-100" id="toggle-categories">
                            <span id="toggle-text">แสดงผลเพิ่มเติม</span>
                        </button>
                    </div>
                    <hr>
                    <div class="card-body">
                        <h5 class="filter-header mb-3">กรองตามราคา</h5>
                        <div class="price-inputs d-flex align-items-center mb-3">
                            <input type="number" class="form-control text-center me-2" id="min_price" placeholder="฿ ต่ำสุด">
                            <div class="separator">-</div>
                            <input type="number" class="form-control text-center ms-2" id="max_price" placeholder="฿ สูงสุด">
                        </div>
                        <button class="btn btn-filter w-100" id="apply-price-filter">กรอง <i class="fas fa-filter"></i></button>
                    </div>
                </div>
            </div>
            <div class="col-lg-9 col-md-8">
                <div class="card card-outline rounded-0">
                    <div class="card-body">
                        <div class="row mb-3 align-items-center justify-content-end">
                            <div class="col-auto">
                                <label for="sort_by" class="form-label mb-0">เรียงตาม:</label>
                            </div>
                            <div class="col-auto">
                                <select class="form-select form-select-sm" id="sort_by">
                                    <option value="date_desc">สินค้าใหม่ล่าสุด</option>
                                    <option value="date_asc">สินค้าเก่าสุด</option>
                                    <option value="price_asc">ราคา: น้อยไปมาก</option>
                                    <option value="price_desc">ราคา: มากไปน้อย</option>
                                    <option value="name_asc">ชื่อ: A-Z</option>
                                    <option value="name_desc">ชื่อ: Z-A</option>
                                </select>
                            </div>
                        </div>
                        <div class="row gy-3 gx-3" id="product-list-container">
                            <div class="col-12 text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only"></span>
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
    $(document).ready(function() {
        var currentTid = "<?= $current_tid ?>";
        var currentPid = "<?= $current_pid ?>";

        function loadProducts(page = 1) {
            var sortBy = $('#sort_by').val();
            var productContainer = $('#product-list-container');
            var loadingSpinnerHTML = `<div class="col-12 text-center py-5">
        <div class="spinner-border text-primary" role="status"><span class="sr-only"></span></div>
        <p class="mt-2">กำลังโหลดสินค้า...</p>
      </div>`;

            productContainer.html(loadingSpinnerHTML);

            var selectedCategories = [];
            $('.category-filter:checked').each(function() {
                selectedCategories.push($(this).val());
            });

            var minPrice = $('#min_price').val();
            var maxPrice = $('#max_price').val();

            $.ajax({
                url: './ajax/fetch_products.php',
                method: 'GET',
                data: {
                    page: page,
                    sort: sortBy,
                    cids: selectedCategories,
                    min_price: minPrice,
                    max_price: maxPrice,
                    tid: currentTid,
                    pid: currentPid
                },
                success: function(response) {
                    // 1. โหลดเนื้อหาใหม่
                    productContainer.html(response);

                    // 2. ⬆️ สั่งให้เลื่อนไปบนสุดของหน้า
                    $('html, body').animate({
                        scrollTop: 0
                    }, 400);
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", status, error);
                    productContainer.html('<div class="col-12 text-center py-5 text-danger">เกิดข้อผิดพลาดในการโหลดสินค้า กรุณาลองใหม่อีกครั้ง</div>');
                }
            });
        }

        // --- ส่วนที่เหลือไม่ต้องแก้ไข ---
        loadProducts(1);

        $('#sort_by').on('change', function() {
            loadProducts(1);
        });
        $('.category-filter').on('change', function() {
            loadProducts(1);
        });
        $('#apply-price-filter').on('click', function() {
            loadProducts(1);
        });
        $('#min_price, #max_price').on('keypress', function(e) {
            if (e.which == 13) {
                loadProducts(1);
            }
        });
        $(document).on('click', '.pagination .page-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            if (page) {
                loadProducts(page);
            }
        });
        $('#toggle-categories').on('click', function() {
            var categoryItems = $('.category-item');
            var maxCategories = 10;
            var toggleText = $('#toggle-text');

            if (categoryItems.hasClass('d-none')) {
                categoryItems.removeClass('d-none');
                toggleText.text('แสดงผลน้อยลง');
            } else {
                categoryItems.each(function(index) {
                    if (index >= maxCategories) {
                        $(this).addClass('d-none');
                    }
                });
                toggleText.text('แสดงผลเพิ่มเติม');
            }
        });
    });
</script>