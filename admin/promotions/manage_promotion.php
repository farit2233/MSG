<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
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
        display: flex;
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

    /* ปุ่มใน Modal */
    .btn-secondary {
        margin-right: 10px;
    }

    .modal-dialog {
        margin-top: 140px;
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
                    <div class="form-group">
                        <label>ชื่อโปรโมชั่น <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
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
                        <label>สินค้าที่ร่วมรายการ</label>
                        <input type="text" name="promo_code" class="form-control" value="<?= isset($promo_code) ? $promo_code : '' ?>" disabled>
                        <button type="button" id="selectProductsBtn" class="btn btn-primary">
                            เลือกสินค้า
                        </button>
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
    <!-- ปุ่มเปิด Modal -->
    <div class="form-group">
        <label>สินค้าที่ร่วมรายการ</label>
        <input type="text" name="promo_code" class="form-control" value="<?= isset($promo_code) ? $promo_code : '' ?>" disabled>
        <button type="button" id="selectProductsBtn" class="btn btn-primary">
            เลือกสินค้า
        </button>
    </div>

    <!-- Modal สำหรับเลือกสินค้า -->
    <div class="modal fade" id="selectProductsModal" tabindex="-1" role="dialog" aria-labelledby="selectProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="selectProductsModalLabel">เลือกสินค้าที่ร่วมโปรโมชั่น</h5>
                    <!-- ปุ่มปิด Modal สำหรับ Bootstrap 4 -->
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- ฟอร์มค้นหาสินค้าและกรองหมวดหมู่ -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="productSearch" class="form-label">ค้นหาสินค้า</label>
                            <input type="text" id="productSearch" class="form-control" placeholder="ค้นหาสินค้า...">
                        </div>
                        <div class="col-md-6">
                            <label for="categoryFilter" class="form-label">เลือกหมวดหมู่</label>
                            <select id="categoryFilter" class="form-control">
                                <option value="">เลือกหมวดหมู่</option>
                                <option value="category1">หมวดหมู่ 1</option>
                                <option value="category2">หมวดหมู่ 2</option>
                                <option value="category3">หมวดหมู่ 3</option>
                            </select>
                        </div>
                    </div>
                    <!-- ข้อความแสดงจำนวนสินค้าที่เลือก -->
                    <div id="selectedCount" class="mb-3">
                        <div class="d-flex justify-content-between">
                            <div>
                                <strong>เลือกแล้ว: </strong><span id="selectedItemsCount">0</span> ชิ้น
                            </div>
                            <div>
                                <button type="button" id="selectAllBtn" class="btn btn-secondary">เลือกทั้งหมด</button>
                                <button type="button" id="deselectAllBtn" class="btn btn-secondary">ยกเลิกการเลือกทั้งหมด</button>
                            </div>
                        </div>
                    </div>

                    <!-- รายการสินค้า (Scroll if needed) -->
                    <div id="productList" class="list-group" style="max-height: 300px; overflow-y: auto;">
                        <!-- ตัวอย่างสินค้า -->
                        <div class="list-group-item">
                            <input type="checkbox" class="product-checkbox" data-product-id="1">
                            สินค้า 1
                        </div>
                        <div class="list-group-item">
                            <input type="checkbox" class="product-checkbox" data-product-id="2">
                            สินค้า 2
                        </div>
                        <div class="list-group-item">
                            <input type="checkbox" class="product-checkbox" data-product-id="3">
                            สินค้า 3
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- ปุ่มยกเลิก -->
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <!-- ปุ่มเพิ่มสินค้าที่เลือก -->
                    <button type="button" id="addSelectedProductsBtn" class="btn btn-primary">เพิ่มสินค้าที่เลือก</button>
                </div>
            </div>
        </div>
    </div>


</section>

<script>
    $(document).ready(function() {
        // เปิด Modal เมื่อกดปุ่ม "เลือกสินค้า"
        $('#selectProductsBtn').click(function() {
            $('#selectProductsModal').modal('show');
        });

        // ฟังก์ชันค้นหาสินค้า
        $('#productSearch').keyup(function() {
            var searchQuery = $(this).val().toLowerCase();
            $('#productList .list-group-item').each(function() {
                var itemText = $(this).text().toLowerCase();
                if (itemText.indexOf(searchQuery) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // กรองสินค้าตามหมวดหมู่
        $('#categoryFilter').change(function() {
            var selectedCategory = $(this).val();
            $('#productList .list-group-item').each(function() {
                var category = $(this).data('category');
                if (selectedCategory === "" || category === selectedCategory) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // เลือกทั้งหมด
        $('#selectAllBtn').click(function() {
            $('#productList .product-checkbox').prop('checked', true);
        });

        // ยกเลิกการเลือกทั้งหมด
        $('#deselectAllBtn').click(function() {
            $('#productList .product-checkbox').prop('checked', false);
        });

        // เพิ่มสินค้าที่เลือก
        $('#addSelectedProductsBtn').click(function() {
            var selectedProducts = [];
            $('#productList .product-checkbox:checked').each(function() {
                selectedProducts.push($(this).data('product-id'));
            });

            if (selectedProducts.length > 0) {
                // ทำสิ่งที่ต้องการกับสินค้าที่เลือก เช่น ส่งไปที่ฟอร์ม หรือบันทึกในฐานข้อมูล
                console.log('สินค้าที่เลือก:', selectedProducts);
                $('#selectProductsModal').modal('hide');
            } else {
                alert('กรุณาเลือกสินค้าก่อน');
            }
        });


        $('#promotion-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
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
        z
    });
</script>