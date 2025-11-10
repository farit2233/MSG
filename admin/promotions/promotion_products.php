<?php
require_once('../../config.php');

// ดึงข้อมูลโปรโมชัน (ถ้ามี)
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions_list WHERE id = '{$_GET['id']}' AND delete_flag = 0");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}

// ดึงรายการสินค้าที่ถูกเลือกไว้ในโปรโมชันนี้
$selected_products = [];
if (isset($id)) {
    $pp_qry = $conn->query("SELECT product_id FROM `promotion_products` WHERE promotion_id = {$id}");
    while ($pp_row = $pp_qry->fetch_assoc()) {
        $selected_products[] = $pp_row['product_id'];
    }
}

// ดึงรายการสินค้าทั้งหมดที่อยู่ในโปรโมชัน "อื่น" ที่ยังไม่ถูกลบ
$products_in_other_promos = [];
$current_promotion_id = isset($id) ? $id : 0;

$other_promo_qry = $conn->query("
    SELECT DISTINCT pp.product_id 
    FROM promotion_products pp
    INNER JOIN promotions_list pl ON pp.promotion_id = pl.id
    WHERE pl.delete_flag = 0 AND pp.promotion_id != {$current_promotion_id}
");
while ($op_row = $other_promo_qry->fetch_assoc()) {
    $products_in_other_promos[] = $op_row['product_id'];
}
?>

<form action="" id="promotion_product">
    <input type="hidden" name="promotion_id" value="<?= isset($id) ? $id : '' ?>">
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="productSearch" class="form-label">ค้นหาสินค้า</label>
            <input type="text" id="productSearch" class="form-control" placeholder="ค้นหาด้วยชื่อ, แบรนด์...">
        </div>
        <div class="col-md-6">
            <label for="categoryFilter">หมวดหมู่สินค้า</label>
            <select id="categoryFilter" name="category_id" class="form-control select2">
                <option value="">-- ทุกหมวดหมู่ --</option>
                <?php
                $cat_q = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
                while ($cat = $cat_q->fetch_assoc()):
                ?>
                    <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
    </div>

    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <label class="form-label mb-0">แสดงผล:</label>
            <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-light btn-sm rounded active mr-2">
                    <input type="radio" name="product_filter" value="all" id="filter_all" autocomplete="off" checked> ทั้งหมด
                </label>
                <label class="btn btn-light btn-sm rounded ">
                    <input type="radio" name="product_filter" value="available" id="filter_available" autocomplete="off"> ยังไม่มีโปรโมชัน
                </label>
                <label class="btn btn-light btn-sm rounded">
                    <input type="radio" name="product_filter" value="in_promo" id="filter_in_promo" autocomplete="off"> มีโปรโมชันแล้ว
                </label>
            </div>
        </div>
    </div>
    <div id="selectedCount" class="mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>เลือกแล้ว: </strong><span id="selectedItemsCount">0</span> ชิ้น
            </div>
            <div>
                <button type="button" id="selectAllBtn" class="btn btn-light btn-sm rounded mr-2">เลือกทั้งหมด (ที่แสดง)</button>
                <button type="button" id="deselectAllBtn" class="btn btn-light btn-sm rounded">ยกเลิกทั้งหมด (ที่แสดง)</button>
            </div>
        </div>
    </div>

    <div id="productList" class="list-group border rounded p-3" style="max-height: 400px; overflow-y: auto;">
        <?php
        $qry = $conn->query("SELECT p.*, c.name AS category_name FROM product_list p INNER JOIN category_list c ON p.category_id = c.id WHERE p.delete_flag = 0 ORDER BY p.brand ASC, p.name ASC");
        while ($row = $qry->fetch_assoc()):

            $is_checked = in_array($row['id'], $selected_products) ? "checked" : "";
            $is_in_other_promo = in_array($row['id'], $products_in_other_promos);
            $is_disabled = !$is_checked && $is_in_other_promo ? "disabled" : "";

            // --- START: MODIFIED CODE FOR SORTING ---
            // เพิ่ม data-attribute เพื่อใช้ในการกรองด้วย JS
            $promo_status = ($is_checked || $is_in_other_promo) ? 'in_promo' : 'available';
            // --- END: MODIFIED CODE FOR SORTING ---
        ?>
            <div class="list-group-item product-item p-3 <?= !empty($is_disabled) ? 'text-muted' : '' ?>"
                data-category="<?= $row['category_id'] ?>"
                data-promo-status="<?= $promo_status ?>">
                <label class="w-100 mb-0 d-flex align-items-center" style="<?= !empty($is_disabled) ? 'cursor:not-allowed;' : 'cursor:pointer;' ?>">
                    <input type="checkbox" name="product_id[]" class="form-check-input product-checkbox mr-3" value="<?= $row['id'] ?>" <?= $is_checked ?> <?= $is_disabled ?>>

                    <?php
                    // --- [แก้ไข] START: สร้าง Path สำหรับรูป Thumb ---
                    // 1. ดึง path รูปหลัก
                    $image_path_with_query = $row['image_path'];

                    // 2. แยก path ออกจาก query string
                    $path_parts = explode('?', $image_path_with_query);
                    $clean_path = $path_parts[0];
                    $query_string = isset($path_parts[1]) ? '?' . $path_parts[1] : '';

                    // 3. สร้าง path ของ thumb
                    $thumb_path = str_replace('.webp', '_thumb.webp', $clean_path);

                    // 4. ประกอบ path กลับ
                    $final_thumb_path = $thumb_path . $query_string;
                    // --- [แก้ไข] END ---
                    ?>

                    <div class="product-info">
                        <img src="<?= validate_image($final_thumb_path) ?>" alt="" class="img-thumbnail p-0 border product-img">
                        <span class="font-weight-bold"> <?= $row['name'] ?> </span>
                        <?php if (!empty($is_disabled)): ?>
                            <span class="badge badge-warning ml-2">อยู่ในโปรโมชันอื่น</span>
                        <?php endif; ?>
                        <span class="d-block text-muted small">ราคา: <?= number_format($row['price'], 2) ?> บาท | หมวดหมู่: <?= $row['category_name'] ?></span>
                    </div>
                </label>
            </div>
        <?php endwhile; ?>
    </div>
</form>

<script>
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%'
        });

        function updateSelectedCount() {
            var selectedCount = $('#productList .product-checkbox:checked').length;
            $('#selectedItemsCount').text(selectedCount);
        }

        // --- START: MODIFIED CODE FOR SORTING ---
        // ฟังก์ชันค้นหาและกรองสินค้า (ปรับปรุงใหม่)
        function filterProducts() {
            var searchQuery = $('#productSearch').val().toLowerCase();
            var categoryFilter = $('#categoryFilter').val();
            var promoFilter = $('input[name="product_filter"]:checked').val(); // ดึงค่าจาก radio button

            $('#productList .product-item').each(function() {
                var productText = $(this).find('.product-info').text().toLowerCase();
                var productCategory = $(this).data('category').toString();
                var productPromoStatus = $(this).data('promo-status');

                var isSearchMatch = productText.includes(searchQuery);
                var isCategoryMatch = (categoryFilter === "" || productCategory === categoryFilter);
                var isPromoMatch = (promoFilter === "all" || productPromoStatus === promoFilter);

                if (isSearchMatch && isCategoryMatch && isPromoMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }
        // --- END: MODIFIED CODE FOR SORTING ---

        $('#productSearch').on('input', filterProducts);
        $('#categoryFilter').on('change', filterProducts);
        $('#productList').on('change', '.product-checkbox', updateSelectedCount);

        // --- START: ADDED CODE FOR SORTING ---
        // Event listener สำหรับปุ่มกรองสถานะโปรโมชัน
        $('input[name="product_filter"]').on('change', filterProducts);
        // --- END: ADDED CODE FOR SORTING ---

        $('#selectAllBtn').click(function() {
            $('#productList .product-item:visible .product-checkbox:not(:disabled)').prop('checked', true);
            updateSelectedCount();
        });

        $('#deselectAllBtn').click(function() {
            $('#productList .product-item:visible .product-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        updateSelectedCount();
        filterProducts(); // เรียกใช้ครั้งแรกเพื่อให้แน่ใจว่าการแสดงผลถูกต้อง

        $('#uni_modal').on('click', '.modal-footer .btn-save', function() {
            $('#promotion_product').submit();
        });

        $('#promotion_product').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            alert_toast('กำลังเพิ่มสินค้า...', 'info');
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion_products",
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alert_toast("เกิดข้อผิดพลาด: " + (resp.msg || ''), 'error');
                        end_loader();
                    }
                },
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            });
        });
    });
</script>