<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}' and delete_flag = 0 ");
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
    .product-img {
        width: 4em;
        height: 4em;
        object-fit: cover;
        object-position: center center;
    }

    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    section {
        font-size: 16px;
    }

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

    .form-check-input {
        transform: scale(1.5);
        /* ปรับขนาดของ checkbox */
        margin-right: 10px;
        padding-left: 100px;
        /* เพิ่มระยะห่างระหว่าง checkbox กับชื่อสินค้า */
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">เพิ่มสินค้าในโปรโมชั่นของ <?= isset($name) ? $name : "" ?></div>
    </div>
    <form action="" id="promotion_product" method="POST">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">เลือกรายการสินค้า</div>
                    <div class="card-tools">
                        <button type="button" id="selectProductsBtn" class="btn btn-flat btn-dark">
                            <i class="fas fa-plus"></i> เลือกสินค้า
                        </button>
                    </div>
                </div>

                <div class="container-fluid py-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="list">
                            <colgroup>
                                <col width="5%">
                                <col width="15%">
                                <col width="25%">
                                <col width="25%">
                                <col width="10%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            </colgroup>
                            <thead class="text-center">
                                <tr>
                                    <th>ที่</th>
                                    <th>รูปภาพสินค้า</th>
                                    <th>แบรนด์</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา</th>
                                    <th>สถานะ</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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

    <!-- Modal สำหรับเลือกสินค้า -->
    <div class="modal fade" id="selectProductsModal" tabindex="-1" role="dialog" aria-labelledby="selectProductsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
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
                            <label>หมวดหมู่สินค้า</label>
                            <select id="categoryFilter" name="category_id" class="form-control select2">
                                <option value="">-- เลือกหมวดหมู่ --</option>
                                <?php
                                $cat_q = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
                                while ($cat = $cat_q->fetch_assoc()):
                                ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($main_category_id == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
                                <?php endwhile; ?>
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
                                <button type="button" id="selectAllBtn" class="btn btn-light btn-sm rounded mr-2">เลือกทั้งหมด</button>
                                <button type="button" id="deselectAllBtn" class="btn btn-light btn-sm rounded mr-2">ยกเลิกการเลือกทั้งหมด</button>
                            </div>
                        </div>
                    </div>
                    <!-- รายการสินค้า (Scroll if needed) -->
                    <div id="productList" class="list-group border rounded p-3" style="max-height: 400px; overflow-y: auto;">
                        <?php
                        $qry = $conn->query("SELECT p.*, c.name AS category_name FROM product_list p INNER JOIN category_list c ON p.category_id = c.id WHERE p.delete_flag = 0 ORDER BY p.brand ASC, p.name ASC");
                        while ($row = $qry->fetch_assoc()):
                        ?>
                            <div class="list-group-item product-item p-4"
                                data-id="<?= $row['id'] ?>"
                                data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                data-brand="<?= htmlspecialchars($row['brand'], ENT_QUOTES) ?>"
                                data-dose="<?= htmlspecialchars($row['dose'], ENT_QUOTES) ?>"
                                data-price="<?= $row['price'] ?>"
                                data-image_path="<?= validate_image($row['image_path']) ?>"
                                data-status="<?= $row['status'] ?>"
                                data-category="<?= $row['category_id'] ?>"> <label class="w-100 mb-0" style="cursor:pointer;">
                                    <input type="checkbox" class="form-check-input product-checkbox" value="<?= $row['id'] ?>">
                                    <div>
                                        <span class="font-weight-bold"><?= $row['name'] ?></span>
                                        <span class="d-block text-muted small">SKU : <?= $row['sku'] ?> | ราคา: <?= number_format($row['price'], 2) ?> บาท | หมวดหมู่: <?= $row['category_name'] ?></span>
                                    </div>
                                </label>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <!-- ปุ่มยกเลิก -->
                    <button type="button" class="btn btn-light border rounded shadow-sm" data-dismiss="modal">ยกเลิก</button>
                    <!-- ปุ่มเพิ่มสินค้าที่เลือก -->
                    <button type="button" id="addSelectedProductsBtn" class="btn btn-primary rounded shadow-sm">เพิ่มสินค้าที่เลือก</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {

        // --- ฟังก์ชัน Helper และการตั้งค่าเริ่มต้น ---

        // ใช้ select2 สำหรับหมวดหมู่
        $('.select2').select2({
            width: '100%'
        });

        // เพิ่ม class เพื่อจัดสไตล์ให้ตาราง (จากสคริปเก่า)
        // หมายเหตุ: การใช้ dataTable จะถูกนำออกไปก่อน เพราะตารางหลักเป็นแบบไดนามิก
        $('#list td, #list th').addClass('py-1 px-2 align-middle');

        // ฟังก์ชันสำหรับอัปเดตเลขลำดับในตารางหลัก
        function updateRowNumbers() {
            $('#list tbody tr').each(function(index) {
                // คอลัมน์ที่ 1 คือลำดับที่
                $(this).find('td:nth-child(1)').text(index + 1);
            });
        }

        // ฟังก์ชันนับจำนวนสินค้าที่เลือกใน Modal (จากสคริปเก่า)
        function updateSelectedCount() {
            var selectedCount = $('#productList .product-checkbox:checked').length;
            $('#selectedItemsCount').text(selectedCount);
        }
        // ฟังก์ชันค้นหาสินค้า และกรองตามหมวดหมู่ใน Modal (จากสคริปเก่า)
        function filterProducts() {
            var searchQuery = $('#productSearch').val().toLowerCase();
            var categoryFilter = $('#categoryFilter').val(); // ค่า string ของ category_id หรือ ""

            $('#productList .product-item').each(function() {
                var productName = $(this).text().toLowerCase();
                var productCategory = $(this).data('category').toString(); // ดึงค่า data-category ที่เพิ่มไป

                var isSearchMatch = productName.indexOf(searchQuery) > -1;
                // ตรวจสอบว่า filter เป็นค่าว่าง หรือตรงกับหมวดหมู่สินค้า
                var isCategoryMatch = (categoryFilter === "" || productCategory === categoryFilter);

                if (isSearchMatch && isCategoryMatch) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
            updateSelectedCount(); // อัปเดตจำนวนสินค้าที่เลือกทุกครั้งที่มีการกรอง
        }
        // --- Event Handlers สำหรับ Modal ---

        // เมื่อคลิกปุ่ม "เลือกสินค้า" เพื่อเปิด Modal
        $('#selectProductsBtn').on('click', function() {
            // 1. ดึง ID ของสินค้าที่มีอยู่ในตารางหลักแล้ว
            let existingIds = [];
            $('#list tbody tr').each(function() {
                existingIds.push($(this).data('id').toString());
            });

            // 2. อัปเดตสถานะ checkbox ใน modal ให้ตรงกับตารางหลัก
            $('#selectProductsModal .product-checkbox').each(function() {
                $(this).prop('checked', existingIds.includes($(this).val()));
            });

            // 3. อัปเดตจำนวนที่เลือกและแสดง Modal
            updateSelectedCount();
            $('#selectProductsModal').modal('show');
        });

        // เมื่อคลิก "เพิ่มสินค้าที่เลือก" ใน Modal
        $('#addSelectedProductsBtn').on('click', function() {
            const tableBody = $('#list tbody');

            // สร้าง Map ของสินค้าที่ถูกเลือกเพื่อการเข้าถึงที่รวดเร็ว
            const selectedProductsMap = new Map();
            $('#selectProductsModal .product-checkbox:checked').each(function() {
                const productItem = $(this).closest('.product-item');
                const productId = productItem.data('id');
                if (!selectedProductsMap.has(productId)) {
                    selectedProductsMap.set(productId, {
                        id: productId,
                        name: productItem.data('name'),
                        brand: productItem.data('brand'),
                        dose: productItem.data('dose'),
                        price: parseFloat(productItem.data('price')).toFixed(2),
                        image_path: productItem.data('image_path'),
                        status: productItem.data('status')
                    });
                }
            });

            // ล้างตารางและสร้างใหม่จาก Map
            tableBody.empty();
            selectedProductsMap.forEach(function(product) {
                const newRow = `
                <tr data-id="${product.id}">
                    <td class="text-center">
                        <input type="hidden" name="product_id[]" value="${product.id}">
                    </td>
                    <td class="text-center">
                        <img src="${product.image_path}" alt="" class="img-thumbnail p-0 border product-img">
                    </td>
                    <td>${product.brand}</td>
                    <td>
                        <div style="line-height:1em">
                            <div>${product.name}</div>
                            <div><small class="text-muted">${product.dose}</small></div>
                        </div>
                    </td>
                    <td class="text-right">${new Intl.NumberFormat().format(product.price)} ฿</td>
                    <td class="text-center">
                        ${product.status == 1 
                            ? '<span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>' 
                            : '<span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>'}
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-flat remove-product-btn" title="ลบรายการนี้">
                            <i class="fa fa-trash text-danger"></i>
                        </button>
                    </td>
                </tr>
            `;
                tableBody.append(newRow);
            });

            updateRowNumbers();
            $('#selectProductsModal').modal('hide');
        });

        // Event Handlers อื่นๆ ใน Modal (จากสคริปเก่า)
        $('#productSearch').on('input', filterProducts);
        $('#categoryFilter').on('change', filterProducts);
        $('#productList').on('change', '.product-checkbox', updateSelectedCount);

        $('#selectAllBtn').click(function() {
            $('#productList .product-item:visible .product-checkbox').prop('checked', true);
            updateSelectedCount();
        });

        $('#deselectAllBtn').click(function() {
            $('#productList .product-item:visible .product-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        // --- Event Handlers สำหรับตารางหลัก ---

        // ปุ่มลบสินค้าในแต่ละแถว
        $('#list tbody').on('click', '.remove-product-btn', function() {
            if (confirm('คุณต้องการลบสินค้านี้ออกจากโปรโมชั่นใช่หรือไม่?')) {
                $(this).closest('tr').remove();
                updateRowNumbers();
            }
        });

        // --- การส่งฟอร์ม (จากสคริปเก่า) ---

        $('#promotion_product').submit(function(e) {
            e.preventDefault();
            // เช็คว่ามีสินค้าถูกเลือกอย่างน้อย 1 รายการหรือไม่
            if ($('input[name="product_id[]"]').length === 0) {
                alert_toast("กรุณาเลือกสินค้าอย่างน้อย 1 รายการ", 'warning');
                return false;
            }
            var _this = $(this);
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion_product",
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
                        location.replace('./?page=promotions/view_promotion&id=' + resp.id);
                    } else {
                        alert_toast(resp.msg || "ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });

    });
</script>