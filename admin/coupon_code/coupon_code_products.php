<?php
require_once('../../config.php');

// ดึงข้อมูลคูปอง (ถ้ามี)
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM coupon_code_list WHERE id = '{$_GET['id']}' AND delete_flag = 0");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}

// ดึงรายการสินค้าที่ถูกเลือกไว้ในคูปองนี้
$selected_products = [];
if (isset($id)) {
    $pp_qry = $conn->query("SELECT product_id FROM `coupon_code_products` WHERE coupon_code_id = {$id}");
    while ($pp_row = $pp_qry->fetch_assoc()) {
        $selected_products[] = $pp_row['product_id'];
    }
}

// ไม่ต้องดึงข้อมูลสินค้าจากคูปองอื่นแล้ว
?>

<form action="" id="coupon_code_products_form">
    <input type="hidden" name="coupon_code_id" value="<?= isset($id) ? $id : '' ?>">
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
                    <input type="radio" name="product_filter" value="available" id="filter_available" autocomplete="off"> ยังไม่ผูกกับคูปองนี้
                </label>
                <label class="btn btn-light btn-sm rounded">
                    <input type="radio" name="product_filter" value="in_promo" id="filter_in_promo" autocomplete="off"> ผูกกับคูปองนี้แล้ว
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
            // 'in_promo' หมายถึง ผูกกับคูปอง "ปัจจุบัน" แล้ว
            $coupon_status = !empty($is_checked) ? 'in_promo' : 'available';
        ?>
            <div class="list-group-item product-item p-3"
                data-category="<?= $row['category_id'] ?>"
                data-coupon-status="<?= $coupon_status ?>">
                <label class="w-100 mb-0 d-flex align-items-center" style="cursor:pointer;">
                    <input type="checkbox" name="product_id[]" class="form-check-input product-checkbox mr-3" value="<?= $row['id'] ?>" <?= $is_checked ?>>
                    <div class="product-info">
                        <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail p-0 border product-img">
                        <span class="font-weight-bold"> <?= $row['name'] ?> </span>
                        <span class="d-block text-muted small">SKU : <?= $row['sku'] ?> | ราคา: <?= number_format($row['price'], 2) ?> บาท | หมวดหมู่: <?= $row['category_name'] ?></span>
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

        function filterProducts() {
            var searchQuery = $('#productSearch').val().toLowerCase();
            var categoryFilter = $('#categoryFilter').val();
            var couponFilter = $('input[name="product_filter"]:checked').val();

            $('#productList .product-item').each(function() {
                var productText = $(this).find('.product-info').text().toLowerCase();
                var productCategory = $(this).data('category').toString();
                var productCouponStatus = $(this).data('coupon-status');

                var isSearchMatch = productText.includes(searchQuery);
                var isCategoryMatch = (categoryFilter === "" || productCategory === categoryFilter);
                var isCouponMatch = (couponFilter === "all" || productCouponStatus === couponFilter);

                if (isSearchMatch && isCategoryMatch && isCouponMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $('#productSearch').on('input', filterProducts);
        $('#categoryFilter').on('change', filterProducts);
        $('#productList').on('change', '.product-checkbox', updateSelectedCount);
        $('input[name="product_filter"]').on('change', filterProducts);

        $('#selectAllBtn').click(function() {
            // ไม่ต้องเช็ค :disabled แล้ว เพราะเอาออกไปแล้ว
            $('#productList .product-item:visible .product-checkbox').prop('checked', true);
            updateSelectedCount();
        });

        $('#deselectAllBtn').click(function() {
            $('#productList .product-item:visible .product-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        updateSelectedCount();
        filterProducts();

        $('#uni_modal').on('click', '.modal-footer .btn-save', function() {
            $('#coupon_code_products_form').submit();
        });

        $('#coupon_code_products_form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_coupon_code_products",
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload();
                    } else {
                        let el = $('<div>').addClass('alert alert-danger err-msg').text(resp.msg || 'เกิดข้อผิดพลาด');
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body, .modal").scrollTop(0);
                    }
                    end_loader();
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