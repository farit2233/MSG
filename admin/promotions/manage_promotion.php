<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}

$main_category_id = null; // ป้องกัน warning
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }

        // ตั้งค่าหมวดหมู่หลักแยกชัดเจน
        $main_category_id = $category_id;
    }
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

    /* สไตล์สำหรับ Modal 
    .modal-header {
        background-color: #007bff;
        color: white;
    }
    */

    /* จัดให้ฟอร์มค้นหาและ dropdown อยู่ในบรรทัดเดียวกัน */
    .row.mb-3 {
        margin-bottom: 15px;
    }

    /* ขนาดของ input field */
    #productSearch,
    #categoryFilter {
        width: 100%;
    }

    /* รายการสินค้าใน Modal ถ้ามีจำนวนเยอะให้แสดง scroll */
    #productList {
        max-height: 300px;
        overflow-y: auto;
    }

    /* ปรับให้ list-group-item มี padding เพิ่ม */
    #productList .list-group-item {
        align-items: center;
    }

    /* ปรับให้ checkbox อยู่ด้านซ้าย */
    #productList .product-checkbox {
        margin-right: 10px;
    }

    /* จัดปุ่มเลือกทั้งหมดและยกเลิกการเลือกทั้งหมดให้ด้านซ้าย ข้อความเลือกจำนวนให้ด้านขวา */
    #selectedCount .d-flex {
        justify-content: space-between;
    }

    /* ให้กรอบรายการสินค้าและ checkbox มีพื้นที่คลิกได้ */
    #productList .product-item {
        position: relative;
        cursor: pointer;
        /* เปลี่ยนเคอร์เซอร์ให้เป็นมือชี้เมื่อผ่านรายการสินค้า */
        padding: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background-color 0.3s ease, box-shadow 0.3s ease;
    }

    /* เมื่อผู้ใช้คลิกหรือ hover */
    #productList .product-item:hover {
        background-color: #f1f1f1;
        /* เปลี่ยนสีพื้นหลังเมื่อ hover */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        /* เพิ่มเงาให้กรอบ */
    }

    /* เมื่อ checkbox ถูกเลือก */
    #productList .product-item input:checked+.form-check-label {
        background-color: #007bff;
        /* เปลี่ยนพื้นหลังเมื่อเลือก checkbox */
        color: white;
        /* เปลี่ยนสีตัวอักษร */
    }

    /* เพิ่มการจัดการให้ label ครอบทั้งกรอบ */
    #productList .product-item label {
        display: block;
        width: 100%;
        padding-left: 10px;
        padding-right: 10px;
        border-radius: 8px;
        cursor: pointer;
        /* เปลี่ยนเคอร์เซอร์เป็นมือชี้ */
        transition: background-color 0.3s ease;
    }


    /* เพิ่มพื้นที่การคลิกให้สะดวกขึ้น */
    #productList .product-item .form-check-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
    }

    /* เพิ่มช่องว่างระหว่างข้อความและ checkbox */
    #productList .product-item .form-check-label span {
        margin-left: 10px;
    }

    /* ปรับ CSS ให้ข้อความและปุ่มกากบาทอยู่ในแถวเดียวกัน */
    #selectedProductsDisplay .badge {
        max-width: 100px;
        /* กำหนดความกว้างสูงสุด */
        white-space: nowrap;
        /* ไม่ให้ข้อความไปหลายบรรทัด */
        overflow: hidden;
        /* ซ่อนข้อความที่เกิน */
        text-overflow: ellipsis;
        /* แสดง ... เมื่อข้อความยาวเกิน */
        display: inline-flex;
        /* ใช้ flexbox เพื่อจัดการตำแหน่งของปุ่มและข้อความ */
        align-items: center;
        /* จัดแนวแนวนอนให้กับข้อความ */
        margin-right: 10px;
        /* เพิ่มช่องว่างระหว่าง badge */
        padding: 0 10px;
        /* เพิ่ม padding เพื่อให้ดูสวยงาม */
    }

    #selectedProductsDisplay .badge .close {
        margin-left: 5px;
        /* ให้ปุ่มกากบาทห่างจากชื่อสินค้า */
        font-size: 1.2rem;
        /* ปรับขนาดปุ่มกากบาท */
        cursor: pointer;
        /* แสดง cursor เป็น pointer */
    }


    /* ปุ่มใน Modal */
    .btn-secondary {
        margin-right: 10px;
    }

    .form-check-input {
        transform: scale(1.5);
        /* ปรับขนาดของ checkbox */
        margin-right: 10px;
        padding-left: 100px;
        /* เพิ่มระยะห่างระหว่าง checkbox กับชื่อสินค้า */
    }

    .badge {
        position: relative;
        padding-right: 25px;
        /* ปรับเพื่อให้มีพื้นที่สำหรับปุ่มกากบาท */
    }

    .badge .close-btn {
        position: absolute;
        top: 0;
        right: 0;
        font-size: 14px;
        color: #999;
        cursor: pointer;
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขโปรโมชั่น" : "สร้างโปรโมชั่นใหม่" ?></div>
    </div>
    <form action="" id="promotion-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลโปรโมชั่น</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>ชื่อโปรโมชั่น <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_category_id" class="control-label">เลือกประเภทสินค้า</label>
                            <?php
                            $promotion_category_result = $conn->query("SELECT id, name FROM promotion_category WHERE status = 1 ORDER BY id ASC");
                            ?>
                            <select name="promotion_category_id" id="promotion_category_id" class="form-control rounded-0 select2" required>
                                <option value="" disabled <?= !isset($promotion_category_id) ? 'selected' : '' ?>>-- เลือกหมวดหมู่โปรโมชั่น --</option>
                                <?php
                                while ($pc_row = $promotion_category_result->fetch_assoc()) :
                                ?>
                                    <option value="<?= $pc_row['id'] ?>" <?= (isset($promotion_category_id) && $promotion_category_id == $pc_row['id']) ? 'selected' : '' ?>>
                                        <?= $pc_row['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label>โค้ดโปรโมชั่น (ถ้ามี)</label>
                <input type="text" name="promo_code" class="form-control" value="<?= isset($promo_code) ? $promo_code : '' ?>">
            </div>
            <div class="form-group">
                <label for="description">รายละเอียด</label>
                <textarea rows="3" name="description" id="description" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>ประเภทโปรโมชั่น</label>
                <select name="type" class="form-control" required>
                    <option value="fixed" <?= (isset($type) && $type == 'fixed') ? 'selected' : '' ?>>ลดราคาคงที่ (บาท)</option>
                    <option value="percent" <?= (isset($type) && $type == 'percent') ? 'selected' : '' ?>>ลดเป็นเปอร์เซ็นต์ (%)</option>
                    <option value="free_shipping" <?= (isset($type) && $type == 'free_shipping') ? 'selected' : '' ?>>ส่งฟรี</option>
                    <option value="code" <?= (isset($type) && $type == 'code') ? 'selected' : '' ?>>ส่วนลดผ่านโค้ด</option>
                </select>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>มูลค่าส่วนลด</label>
                    <input type="number" name="discount_value" step="0.01" min="0" class="form-control" value="<?= isset($discount_value) ? $discount_value : '' ?>">
                </div>
                <div class="col-md-6">
                    <label>ยอดสั่งซื้อขั้นต่ำ</label>
                    <input type="number" name="minimum_order" step="0.01" min="0" class="form-control" value="<?= isset($minimum_order) ? $minimum_order : '' ?>">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-md-6">
                    <label>เริ่มวันที่</label>
                    <input type="datetime-local" name="start_date" class="form-control" required value="<?= isset($start_date) ? date('Y-m-d\TH:i', strtotime($start_date)) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label>สิ้นสุดวันที่</label>
                    <input type="datetime-local" name="end_date" class="form-control" required value="<?= isset($end_date) ? date('Y-m-d\TH:i', strtotime($end_date)) : '' ?>">
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title">สถานะโปรโมชั่น</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">เปิด / ปิดการใช้งานโปรโมชั่น</label>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer py-1 text-center">
            <button class="btn btn-success btn-sm btn-flat" form="promotion-form"><i class="fa fa-save"></i> บันทึก</button>
            <a class="btn btn-danger btn-sm border btn-flat" href="./?page=promotions"><i class="fa fa-times"></i> ยกเลิก</a>
            <a class="btn btn-light btn-sm border btn-flat" href="./?page=promotions"><i class="fa fa-angle-left"></i> กลับ</a>
        </div>
    </form>

</section>

<script>
    $(document).ready(function() {
        // ใช้ select2 สำหรับหมวดหมู่
        $('.select2').select2({
            width: '100%'
        });

        // เปิด Modal เมื่อกดปุ่ม "เลือกสินค้า"
        $('#selectProductsBtn').click(function() {
            $('#selectProductsModal').modal('show');
        });

        // ฟังก์ชันค้นหาสินค้า และกรองตามหมวดหมู่
        function filterProducts() {
            var searchQuery = $('#productSearch').val().toLowerCase();
            var categoryFilter = $('#categoryFilter').val();

            $('#productList .product-item').each(function() {
                var productName = $(this).text().toLowerCase();
                var productCategory = $(this).data('category');

                // กรองตามคำค้นหาและหมวดหมู่
                if ((productName.indexOf(searchQuery) > -1) &&
                    (categoryFilter === "" || productCategory == categoryFilter)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });

            updateSelectedCount(); // อัปเดตจำนวนที่เลือกหลังจากกรอง
        }

        // ฟังก์ชันนับจำนวนสินค้าที่เลือก
        function updateSelectedCount() {
            var selectedCount = $('#productList .product-checkbox:checked').length;
            $('#selectedItemsCount').text(selectedCount); // อัปเดตจำนวนที่เลือก
        }

        // เมื่อมีการเลือกหรือยกเลิกการเลือก checkbox
        $('#productList').on('change', '.product-checkbox', function() {
            updateSelectedCount(); // อัปเดตจำนวนที่เลือก
        });

        // เมื่อเลือกทั้งหมด
        $('#selectAllBtn').click(function() {
            $('#productList .product-checkbox:visible').prop('checked', true); // เลือกเฉพาะที่แสดง
            updateSelectedCount(); // อัปเดตจำนวนที่เลือก
        });

        // เมื่อยกเลิกการเลือกทั้งหมด
        $('#deselectAllBtn').click(function() {
            $('#productList .product-checkbox:visible').prop('checked', false); // ยกเลิกเฉพาะที่แสดง
            updateSelectedCount(); // อัปเดตจำนวนที่เลือก
        });

        // เมื่อพิมพ์ในช่องค้นหา
        $('#productSearch').keyup(function() {
            filterProducts(); // เรียกฟังก์ชันกรองสินค้า
        });

        // เมื่อเลือกหมวดหมู่
        $('#categoryFilter').change(function() {
            filterProducts(); // เรียกฟังก์ชันกรองสินค้า
        });

        // เมื่อคลิกที่รายการสินค้า, ให้เลือก checkbox
        $('#productList').on('click', '.product-item', function() {
            var checkbox = $(this).find('.product-checkbox'); // หาค่า checkbox ในรายการสินค้า
            checkbox.prop('checked', !checkbox.prop('checked')); // สลับสถานะ checkbox
            updateSelectedCount(); // อัปเดตจำนวนที่เลือก
        });

        // เมื่อคลิกปุ่ม "เพิ่มสินค้าที่เลือก"
        // เมื่อคลิกที่ปุ่ม "เพิ่มสินค้าที่เลือก"
        $('#addSelectedProductsBtn').click(function() {
            var selectedProducts = [];

            // เก็บ ID ของสินค้าที่ถูกเลือก
            $('#productList .product-checkbox:checked').each(function() {
                var productId = $(this).data('product-id');
                var productName = $(this).closest('.product-item').find('label').text().trim();

                // เพิ่มสินค้าเข้าไปใน array selectedProducts
                selectedProducts.push({
                    id: productId,
                    name: productName
                });
            });

            // แสดงสินค้าที่เลือกใน #selectedProductsDisplay
            if (selectedProducts.length > 0) {
                // ซ่อนข้อความ "ยังไม่มีสินค้าที่เลือก"
                $('#noProductsSelected').hide();

                // แสดงสินค้าผ่าน badge
                selectedProducts.forEach(function(product) {
                    $('#selectedProductsDisplay').append(
                        '<span class="badge badge-pill badge-info mr-2 d-inline-flex align-items-center" id="badge-' + product.id + '">' +
                        product.name +
                        ' <button type="button" class="close" aria-label="Close" onclick="removeProduct(' + product.id + ')">' +
                        '<span aria-hidden="true">&times;</span></button>' +
                        '</span>'
                    );
                });

                // อัปเดต input (สามารถใช้ join() เพื่อแสดงเป็นข้อความ)
                var promoCode = selectedProducts.map(function(product) {
                    return product.name;
                }).join(', ');
                $('input[name="promo_code"]').val(promoCode);
            } else {
                alert('กรุณาเลือกสินค้า');
            }

            // ปิด Modal
            $('#selectProductsModal').modal('hide');
        });

        function removeProduct(btn) {
            var badge = btn.closest('.badge');
            badge.remove(); // ลบ badge ที่เลือก

            // ถ้าไม่มีสินค้าที่เลือก ให้แสดงข้อความว่า "ยังไม่มีสินค้าที่เลือก"
            if ($('#selectedProductsDisplay .badge').length === 0) {
                $('#noProductsSelected').show();
            }
        }
        // ฟอร์มบันทึกโปรโมชั่น
        $('#promotion-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion",
                data: new FormData(this),
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