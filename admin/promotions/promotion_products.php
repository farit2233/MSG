<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
        // ตรวจสอบและแปลง promotion_categories_by_products หากมีข้อมูลเก่า
        $promotion_categories_by_products = [];
        if (isset($category_ids_products) && !empty($category_ids_products)) {
            $promotion_categories_by_products = json_decode($category_ids_products, true);
            if (!is_array($promotion_categories_by_products)) {
                $promotion_categories_by_products = [];
            }
        }
        // ตรวจสอบและแปลง promotion_categories_by_categories หากมีข้อมูลเก่า
        $promotion_categories_by_categories = [];
        if (isset($category_ids_categories) && !empty($category_ids_categories)) {
            $promotion_categories_by_categories = json_decode($category_ids_categories, true);
            if (!is_array($promotion_categories_by_categories)) {
                $promotion_categories_by_categories = [];
            }
        }

        // กำหนดสถานะของ switch toggles ตามข้อมูลที่มี
        $is_by_product_enabled = !empty($promotion_categories_by_products);
        $is_by_category_enabled = !empty($promotion_categories_by_categories);

        // หากมีทั้งสองตัวเลือกเปิดอยู่ (ซึ่งไม่ควรเกิดขึ้นหากต้องการให้เลือกได้ตัวเดียว)
        // อาจจะต้องกำหนดค่าเริ่มต้น หรือให้ตัวที่ถูกเลือกมาทีหลังเป็นหลัก
        // ในที่นี้ เราจะให้ By Product เป็นตัวเลือกหลัก หากมีข้อมูลทั้งสอง
        if ($is_by_product_enabled) {
            $selected_promotion_type = 'by_product';
        } elseif ($is_by_category_enabled) {
            $selected_promotion_type = 'by_category';
        } else {
            $selected_promotion_type = ''; // ไม่มีโปรโมชั่นที่ถูกเลือก
        }
    } else {
        // หากไม่มี ID หรือ ID ไม่ถูกต้อง ให้ตั้งค่าเริ่มต้น
        $promotion_categories_by_products = [];
        $promotion_categories_by_categories = [];
        $selected_promotion_type = '';
    }
} else {
    // หากไม่มี ID เลย (สำหรับฟอร์มสร้างใหม่)
    $promotion_categories_by_products = [];
    $promotion_categories_by_categories = [];
    $selected_promotion_type = '';
}


?>
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    section {
        font-size: 16px;
    }

    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: calc(2.25rem + 2px);
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">เพิ่มสินค้าในโปรโมชั่น</div>
    </div>
    <form action="" id="promotion_product" method="POST">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">โปรโมชั่นสำหรับสินค้า</div>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input promotion-toggle" id="promotion_by_product_toggle"
                            data-target="promotion_by_product_section"
                            <?= ($selected_promotion_type == 'by_product') ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="promotion_by_product_toggle">เปิดใช้งานโปรโมชั่นสำหรับสินค้า</label>
                    </div>
                    <?php // ดึงรายการสินค้าทั้งหมดสำหรับ dropdown
                    $all_products = [];
                    $product_q = $conn->query("SELECT id, name FROM product_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
                    while ($product = $product_q->fetch_assoc()) {
                        $all_products[] = $product;
                    } ?>
                    <div id="promotion_by_product_section" class="p-3">
                        <div class="form-group" id="product_category_selection_group">
                            <label>เลือกสินค้าที่ร่วมโปรโมชั่น <span class="text-danger">*</span></label>
                            <div id="product-category-dropdown-group">
                                <?php if ($selected_promotion_type == 'by_product' && !empty($promotion_categories_by_products)): ?>
                                    <?php foreach ($promotion_categories_by_products as $product_id_selected): ?>
                                        <div class="row category-row mb-2">
                                            <div class="col-md-9">
                                                <select name="category_ids_products[]" class="form-control select2" required>
                                                    <option value="">-- เลือกสินค้า --</option>
                                                    <?php foreach ($all_products as $product): ?>
                                                        <option value="<?= $product['id'] ?>" <?= ($product_id_selected == $product['id']) ? 'selected' : '' ?>><?= htmlspecialchars($product['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end button-container">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="row category-row mb-2">
                                        <div class="col-md-9">
                                            <select name="category_ids_products[]" class="form-control select2" required>
                                                <option value="">-- เลือกสินค้า --</option>
                                                <?php foreach ($all_products as $product): ?>
                                                    <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end button-container">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php // ดึงรายการหมวดหมู่ทั้งหมดสำหรับ dropdown
            $all_categories = [];
            $cat_q = $conn->query("SELECT id, name FROM category_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
            while ($cat = $cat_q->fetch_assoc()) {
                $all_categories[] = $cat;
            } ?>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">โปรโมชั่นสำหรับหมวดหมู่</div>
                </div>
                <div class="card-body">
                    <div class="custom-control custom-switch mb-3">
                        <input type="checkbox" class="custom-control-input promotion-toggle" id="promotion_by_category_toggle"
                            data-target="promotion_by_category_section"
                            <?= ($selected_promotion_type == 'by_category') ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="promotion_by_category_toggle">เปิดใช้งานโปรโมชั่นสำหรับหมวดหมู่</label>
                    </div>

                    <div id="promotion_by_category_section" class="p-3">
                        <div class="form-group" id="category_selection_for_category_group">
                            <label>เลือกหมวดหมู่ที่ร่วมโปรโมชั่น <span class="text-danger">*</span></label>
                            <div id="category-for-category-dropdown-group">
                                <?php if ($selected_promotion_type == 'by_category' && !empty($promotion_categories_by_categories)): ?>
                                    <?php foreach ($promotion_categories_by_categories as $cat_id_selected): ?>
                                        <div class="row category-row mb-2">
                                            <div class="col-md-9">
                                                <select name="category_ids_categories[]" class="form-control select2" required>
                                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                                    <?php foreach ($all_categories as $cat): ?>
                                                        <option value="<?= $cat['id'] ?>" <?= ($cat_id_selected == $cat['id']) ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-3 d-flex align-items-end button-container">
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="row category-row mb-2">
                                        <div class="col-md-9">
                                            <select name="category_ids_categories[]" class="form-control select2" required>
                                                <option value="">-- เลือกหมวดหมู่ --</option>
                                                <?php foreach ($all_categories as $cat): ?>
                                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end button-container">
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer py-1 text-center">
            <?php if (isset($_GET['id']) && $_GET['id'] > 0): ?>
                <button class="btn btn-success btn-sm btn-flat" form="promotion_product">
                    <i class="fa fa-save"></i> บันทึก
                </button>
                <a class="btn btn-danger btn-sm border btn-flat" href="./?page=promotions/view_promotion&id=<?= $_GET['id'] ?>">
                    <i class="fa fa-times"></i> ยกเลิก
                </a>
                <a class="btn btn-light btn-sm border btn-flat" href="./?page=promotions/view_promotion&id=<?= $_GET['id'] ?>">
                    <i class="fa fa-angle-left"></i> กลับ
                </a>
            <?php endif; ?>
        </div>

    </form>
</section>

<script>
    $(document).ready(function() {
        // ฟังก์ชันเริ่มต้น Select2 สำหรับ dropdown ใหม่
        function initializeSelect2(element, placeholderText) {
            $(element).select2({
                width: '100%',
                placeholder: placeholderText,
                allowClear: true // อนุญาตให้ล้างค่าได้
            });
        }

        // Initialize Select2 for existing dropdowns
        $('.select2').each(function() {
            // ตรวจสอบว่าเป็น dropdown ของสินค้าหรือหมวดหมู่ เพื่อกำหนด placeholder ที่ถูกต้อง
            const isProductDropdown = $(this).attr('name') === 'category_ids_products[]';
            const placeholder = isProductDropdown ? "-- เลือกสินค้า --" : "-- เลือกหมวดหมู่ --";
            initializeSelect2(this, placeholder);
        });

        // ----------------------------------------------------
        // Logic สำหรับ Switch Toggles และการแสดงผล Section
        // ----------------------------------------------------

        // เก็บสถานะที่เลือกไว้ตอนโหลดหน้า
        const initialSelectedPromotionType = '<?= $selected_promotion_type ?>';

        // ฟังก์ชันเปิด/ปิด Section และ Disable/Enable Input/Select
        function togglePromotionSection(sectionId, enabled) {
            const $section = $('#' + sectionId);
            $section.toggle(enabled);
            $section.find('input, select').prop('disabled', !enabled);
        }

        // ตั้งค่าเริ่มต้นเมื่อโหลดหน้า
        togglePromotionSection('promotion_by_product_section', initialSelectedPromotionType === 'by_product');
        togglePromotionSection('promotion_by_category_section', initialSelectedPromotionType === 'by_category');


        // Event Listener สำหรับ Switch Toggles
        $('.promotion-toggle').on('change', function() {
            const $thisToggle = $(this);
            const targetSectionId = $thisToggle.data('target');
            const isChecked = $thisToggle.is(':checked');

            // ถ้า toggle ที่เพิ่งถูกเลือกเป็น "เปิด"
            if (isChecked) {
                // วนลูปปิด toggle อื่นๆ และซ่อน/disable section ที่เกี่ยวข้อง
                $('.promotion-toggle').not($thisToggle).each(function() {
                    const otherToggle = $(this);
                    if (otherToggle.is(':checked')) {
                        otherToggle.prop('checked', false);
                        togglePromotionSection(otherToggle.data('target'), false);
                    }
                });
                // เปิด section ของ toggle ที่เพิ่งถูกเลือก
                togglePromotionSection(targetSectionId, true);
            } else {
                // ถ้า toggle ที่เพิ่งถูกเลือกเป็น "ปิด" ให้ซ่อน/disable section นั้น
                togglePromotionSection(targetSectionId, false);
            }
        });


        // ----------------------------------------------------
        // Logic สำหรับการจัดการ Dropdown (เพิ่ม/ลบ)
        // ----------------------------------------------------

        // HTML สำหรับสร้างแถว dropdown สินค้าใหม่ (สำหรับโปรโมชั่นสินค้า)
        const newProductRowTemplate = `
        <div class="row category-row mb-2">
            <div class="col-md-9">
                <select name="category_ids_products[]" class="form-control select2-new" required>
                    <option value="">-- เลือกสินค้า --</option>
                    <?php foreach ($all_products as $product): ?>
                        <option value="<?= $product['id'] ?>"><?= htmlspecialchars($product['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end button-container">
            </div>
        </div>`;

        // HTML สำหรับสร้างแถว dropdown หมวดหมู่ใหม่ (สำหรับโปรโมชั่นหมวดหมู่)
        const newCategoryForCategoryRowTemplate = `
        <div class="row category-row mb-2">
            <div class="col-md-9">
                <select name="category_ids_categories[]" class="form-control select2-new" required>
                    <option value="">-- เลือกหมวดหมู่ --</option>
                    <?php foreach ($all_categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end button-container">
            </div>
        </div>`;


        // ฟังก์ชันสำหรับอัปเดตปุ่ม + และ - ของแต่ละแถว dropdown (สำหรับ By Product)
        function updateProductButtons() {
            const rows = $('#product-category-dropdown-group .category-row');
            if (rows.length === 0) {
                // ถ้าไม่มี dropdown เลย ให้เพิ่ม dropdown แรก
                $('#product-category-dropdown-group').append(newProductRowTemplate);
                initializeSelect2($('#product-category-dropdown-group .select2-new').last(), "-- เลือกสินค้า --");
                updateProductButtons(); // เรียกซ้ำเพื่อให้ปุ่มถูกต้อง
                return;
            }
            rows.each(function(index) {
                const buttonContainer = $(this).find('.button-container');
                buttonContainer.empty();
                if (index === rows.length - 1) {
                    buttonContainer.append('<button type="button" class="btn btn-primary add-product-row ml-2"><i class="fas fa-plus"></i></button> ');
                }
                buttonContainer.append('<button type="button" class="btn btn-danger remove-product-row ml-2"><i class="fas fa-trash"></i></button>');
            });
        }

        // ฟังก์ชันสำหรับอัปเดตปุ่ม + และ - ของแต่ละแถว dropdown (สำหรับ By Category)
        function updateCategoryForCategoryButtons() {
            const rows = $('#category-for-category-dropdown-group .category-row');
            if (rows.length === 0) {
                // ถ้าไม่มี dropdown เลย ให้เพิ่ม dropdown แรก
                $('#category-for-category-dropdown-group').append(newCategoryForCategoryRowTemplate);
                initializeSelect2($('#category-for-category-dropdown-group .select2-new').last(), "-- เลือกหมวดหมู่ --");
                updateCategoryForCategoryButtons(); // เรียกซ้ำเพื่อให้ปุ่มถูกต้อง
                return;
            }
            rows.each(function(index) {
                const buttonContainer = $(this).find('.button-container');
                buttonContainer.empty();
                if (index === rows.length - 1) {
                    buttonContainer.append('<button type="button" class="btn btn-primary add-category-for-category-row ml-2"><i class="fas fa-plus"></i></button> ');
                }
                buttonContainer.append('<button type="button" class="btn btn-danger remove-category-for-category-row ml-2"><i class="fas fa-trash"></i></button>');
            });
        }


        // Event Listeners สำหรับโปรโมชั่น "สินค้า"
        $('#product-category-dropdown-group').on('click', '.add-product-row', function() {
            $('#product-category-dropdown-group').append(newProductRowTemplate);
            initializeSelect2($('#product-category-dropdown-group .select2-new').last(), "-- เลือกสินค้า --");
            updateProductButtons();
        });
        $('#product-category-dropdown-group').on('click', '.remove-product-row', function() {
            $(this).closest('.category-row').remove();
            updateProductButtons();
        });

        // Event Listeners สำหรับโปรโมชั่น "หมวดหมู่"
        $('#category-for-category-dropdown-group').on('click', '.add-category-for-category-row', function() {
            $('#category-for-category-dropdown-group').append(newCategoryForCategoryRowTemplate);
            initializeSelect2($('#category-for-category-dropdown-group .select2-new').last(), "-- เลือกหมวดหมู่ --");
            updateCategoryForCategoryButtons();
        });
        $('#category-for-category-dropdown-group').on('click', '.remove-category-for-category-row', function() {
            $(this).closest('.category-row').remove();
            updateCategoryForCategoryButtons();
        });

        // เรียกใช้ฟังก์ชันเพื่อตั้งค่าปุ่มเมื่อหน้าเว็บโหลดเสร็จ
        updateProductButtons();
        updateCategoryForCategoryButtons();


        // ----------------------------------------------------
        // Submit Form Logic
        // ----------------------------------------------------
        $('#promotion_product').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            start_loader();

            let category_ids_products_arr = [];
            let category_ids_categories_arr = [];
            let selected_promotion_type_to_save = '';

            // ตรวจสอบว่า toggle ไหนถูกเปิด
            if ($('#promotion_by_product_toggle').is(':checked')) {
                selected_promotion_type_to_save = 'by_product';
                _this.find('#product-category-dropdown-group select[name="category_ids_products[]"]').each(function() {
                    if ($(this).val()) {
                        category_ids_products_arr.push($(this).val());
                    }
                });

                // ตรวจสอบความซ้ำซ้อนสำหรับ By Product
                var unique_product_category_ids = new Set(category_ids_products_arr);
                if (unique_product_category_ids.size !== category_ids_products_arr.length) {
                    alert_toast("ไม่สามารถบันทึกได้: มีสินค้าซ้ำกันในโปรโมชั่นสำหรับสินค้า", 'error'); // เปลี่ยนข้อความเตือน
                    end_loader();
                    return;
                }

            } else if ($('#promotion_by_category_toggle').is(':checked')) {
                selected_promotion_type_to_save = 'by_category';
                _this.find('#category-for-category-dropdown-group select[name="category_ids_categories[]"]').each(function() {
                    if ($(this).val()) {
                        category_ids_categories_arr.push($(this).val());
                    }
                });

                // ตรวจสอบความซ้ำซ้อนสำหรับ By Category
                var unique_category_for_category_ids = new Set(category_ids_categories_arr);
                if (unique_category_for_category_ids.size !== category_ids_categories_arr.length) {
                    alert_toast("ไม่สามารถบันทึกได้: มีหมวดหมู่ซ้ำกันในโปรโมชั่นสำหรับหมวดหมู่", 'error');
                    end_loader();
                    return;
                }
            }


            // สร้าง FormData
            var formData = new FormData(this);

            // ลบค่าเก่าที่อาจจะมาจาก HTML (ป้องกันข้อมูลซ้ำซ้อน)
            formData.delete('category_ids_products[]');
            formData.delete('category_ids_categories[]');

            // เพิ่มประเภทโปรโมชั่นที่เลือก
            formData.append('promotion_type', selected_promotion_type_to_save);

            // เพิ่ม category_ids ของประเภทที่ถูกเลือกเป็น JSON string
            if (selected_promotion_type_to_save === 'by_product') {
                formData.append('category_ids_products', JSON.stringify(category_ids_products_arr));
                formData.append('category_ids_categories', JSON.stringify([])); // ส่งค่าว่างไปให้ส่วนที่ไม่ได้เลือก
            } else if (selected_promotion_type_to_save === 'by_category') {
                formData.append('category_ids_categories', JSON.stringify(category_ids_categories_arr));
                formData.append('category_ids_products', JSON.stringify([])); // ส่งค่าว่างไปให้ส่วนที่ไม่ได้เลือก
            } else {
                // กรณีไม่มี toggle ไหนถูกเลือก
                formData.append('category_ids_products', JSON.stringify([]));
                formData.append('category_ids_categories', JSON.stringify([]));
            }


            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion",
                data: formData,
                method: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.replace('./?page=promotions/manage_promotion&id=' + resp.id);
                    } else {
                        alert_toast(resp.msg || "ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>