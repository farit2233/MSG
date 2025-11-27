<?php
if ($_settings->userdata('type') != 1) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์ใช้งาน',
                text: 'คุณไม่มีสิทธิ์ใช้งานหน้าเพจนี้',
                confirmButtonText: 'ตกลง',
                allowOutsideClick: false,
            }).then(function() {
                window.location.href = './';  // กำหนดให้เด้งกลับหน้าแรก
            });
          </script>";
    exit;
}

$provider_id = null;
$display_name = '';
$description = '';
$shipping_type = 'fixed';
$cost = 0.00;
$cod_enabled = 0;
$status = 1;
$id = '';


// ตรวจสอบว่า id มีอยู่ใน URL หรือไม่
if (isset($_GET['id']) && $_GET['id'] > 0) {

    // ดึงข้อมูลการจัดส่ง
    $qry = $conn->query("SELECT * FROM shipping_methods WHERE id = '{$_GET['id']}' ");
    if ($qry && $qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        // ทำการเก็บค่าที่ดึงมาไว้ในตัวแปร
        foreach ($row as $k => $v) {
            $$k = $v; // map ค่าใน $row ให้ตรงกับตัวแปร
        }
        $display_name = $name; // mapping display_name
    }

    // ดึงข้อมูลราคาตามน้ำหนัก
    $weight_ranges = [];

    // *** แก้ไข: เพิ่ม AND status = 1 และ ORDER BY min_weight ASC ***
    $price_qry = $conn->query("SELECT * FROM shipping_prices 
                              WHERE shipping_methods_id = '{$id}' AND status = 1 
                              ORDER BY min_weight ASC");

    while ($price_row = $price_qry->fetch_assoc()) {
        $weight_ranges[] = $price_row; // เก็บข้อมูลในอาร์เรย์
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

    .swal2-confirm {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }

    .swal2-confirm:hover {
        background-color: #218838 !important;
        border-color: #1e7e34 !important;
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?php echo isset($id) && $id > 0 ? 'แก้ไขข้อมูลการจัดส่ง' : 'เพิ่มข้อมูลการจัดส่ง'; ?></div>
    </div>
    <form action="" id="shipping-form" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลการจัดส่ง</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">ชื่อการจัดส่งสำหรับลูกค้า <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="display_name" class="form-control" placeholder="ชื่อแสดงในหน้าสั่งซื้อสินค้าของลูกค้า" value="<?= isset($display_name) ? htmlspecialchars($display_name) : '' ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="รายละเอียดการจัดส่ง เช่น ระยะเวลา รอบส่ง"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ประเภทค่าจัดส่ง</div>
                </div>
                <div class=" card-body">
                    <div class="form-group" id="fixed_cost_group">
                        <label for="cost">ค่าจัดส่งคงที่ <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">฿</span>
                            </div>
                            <input type="number" step="0.01" name="cost" class="form-control" value="<?= isset($cost) ? $cost : 0 ?>" required>
                        </div>
                    </div>

                    <hr>

                    <div class="form-group" id="weight_cost_group">
                        <label>ค่าจัดส่งตามน้ำหนัก <span class="text-danger">*</span></label>
                        <div id="weight-price-group">

                            <?php if (isset($weight_ranges) && !empty($weight_ranges)): ?>
                                <?php foreach ($weight_ranges as $range): ?>
                                    <div class="row weight-price-row mb-2">
                                        <div class="col-md-3">
                                            <label>ราคา</label>
                                            <input type="number" step="0.01" name="price[]" class="form-control" value="<?= htmlspecialchars($range['price']) ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>น้ำหนักเริ่มต้น (กรัม.)</label>
                                            <input type="number" step="1" name="weight_from[]" class="form-control" value="<?= htmlspecialchars($range['min_weight']) ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>น้ำหนักสูงสุด (กรัม.)</label>
                                            <input type="number" step="1" name="weight_to[]" class="form-control" value="<?= htmlspecialchars($range['max_weight']) ?>" required>
                                        </div>
                                        <div class="col-md-3 d-flex align-items-end button-container">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">เก็บเงินปลายทาง</div>
                </div>
                <div class=" card-body">
                    <input type="hidden" name="cod_enabled" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="cod_enabled" name="cod_enabled" value="1" <?= isset($cod_enabled) && $cod_enabled == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="cod_enabled">ตั้งค่าให้การจัดส่งนี้เป็นแบบเก็บเงินปลายทาง</label>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">สถานะการแสดงผล</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= isset($status) && $status == 1 ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">ปิดเพื่อซ่อนการจัดส่งจากหน้าร้าน แต่ร้านค้ายังสามารถจัดการต่อได้</label>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer py-1 text-center">
            <a class="btn btn-light btn-sm border btn-flat" href="javascript:void(0)" id="backBtn"><i class="fa fa-angle-left"></i> กลับ</a>
            <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
            <button class="btn btn-success btn-sm btn-flat" form="shipping-form"><i class="fa fa-save"></i> บันทึก</button>
        </div>
    </form>
</section>

<script>
    $(document).ready(function() {
        let formChanged = false;

        // ตรวจสอบการเปลี่ยนแปลงของฟอร์ม
        $('#shipping-form input, #shipping-form textarea').on('input', function() {
            formChanged = true;
        });

        // เมื่อกดปุ่ม "ยกเลิก"
        $('#cancelBtn').click(function() {
            if (formChanged) {
                // ถ้ามีการเปลี่ยนแปลงข้อมูล
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // รีเฟรชหน้า
                        location.reload();
                    }
                });
            } else {
                // ถ้าไม่มีการเปลี่ยนแปลงก็รีเฟรชหน้า
                location.reload();
            }
        });

        // เมื่อกดปุ่ม "กลับ"
        $('#backBtn').click(function() {
            if (formChanged) {
                // ถ้ามีการเปลี่ยนแปลงข้อมูล
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // กลับไปหน้าหมวดหมู่โปรโมชัน
                        window.location.href = './?page=shipping_setting';
                    }
                });
            } else {
                // ถ้าไม่มีการเปลี่ยนแปลงก็กลับไปหน้าหมวดหมู่โปรโมชัน
                window.location.href = './?page=shipping_setting';
            }
        });
    });
    $(function() {

        // --- START: โค้ดส่วน JavaScript ---

        // HTML สำหรับสร้างแถวใหม่
        const newRowTemplate = `
        <div class="row weight-price-row mb-2">
            <div class="col-md-3">
                <label>ราคา</label>
                <input type="number" step="0.01" name="price[]" class="form-control" placeholder="เช่น 40" required>
            </div>
            <div class="col-md-3">
                <label>น้ำหนักเริ่มต้น (กรัม.)</label>
                <input type="number" step="1" name="weight_from[]" class="form-control" placeholder="เช่น 0" required>
            </div>
            <div class="col-md-3">
                <label>น้ำหนักสูงสุด (กรัม.)</label>
                <input type="number" step="1" name="weight_to[]" class="form-control" placeholder="เช่น 1000" required>
            </div>
            <div class="col-md-3 d-flex align-items-end button-container">
                </div>
        </div>`;

        // ฟังก์ชันสำหรับอัปเดตปุ่ม + และ -
        function updateWeightPriceButtons() {
            const rows = $('#weight-price-group .weight-price-row');

            // *** [สำคัญ] แก้ไขจุดนี้: ถ้าไม่มีแถวเลย (เช่น ตอน "เพิ่มใหม่") ให้เพิ่ม 1 แถว ***
            if (rows.length === 0) {
                $('#weight-price-group').append(newRowTemplate);
                updateWeightPriceButtons(); // เรียกใช้ฟังก์ชันอีกครั้งเพื่อใส่ปุ่ม
                return;
            }

            rows.each(function(index) {
                const buttonContainer = $(this).find('.button-container');
                const removeButtonHtml = '<button type="button" class="btn btn-danger remove-weight-price ml-2"><i class="fas fa-trash"></i></button>';

                // ถ้าเป็นแถวสุดท้าย ให้แสดงปุ่ม + และ -
                if (index === rows.length - 1) {
                    const addButtonHtml = '<button type="button" class="btn btn-primary add-weight-price ml-2"><i class="fas fa-plus"></i></button>';
                    // สลับตำแหน่งปุ่ม + มาก่อน -
                    buttonContainer.html(addButtonHtml + ' ' + removeButtonHtml);
                } else {
                    // สำหรับแถวอื่นๆ ให้แสดงเฉพาะปุ่ม -
                    buttonContainer.html(removeButtonHtml);
                }
            });
        }

        // Event Listener สำหรับกดปุ่ม + (Add)
        $('#weight-price-group').on('click', '.add-weight-price', function() {
            $('#weight-price-group').append(newRowTemplate);
            updateWeightPriceButtons(); // อัปเดตปุ่มทั้งหมด
        });

        // Event Listener สำหรับกดปุ่ม - (Remove)
        $('#weight-price-group').on('click', '.remove-weight-price', function() {
            $(this).closest('.weight-price-row').remove();
            updateWeightPriceButtons(); // อัปเดตปุ่มทั้งหมด
        });

        // เรียกใช้ฟังก์ชันเพื่อตั้งค่าปุ่มเมื่อหน้าเว็บโหลดเสร็จ
        // (นี่จะเป็นตัวสร้างแถวแรกในหน้า "เพิ่มใหม่" ด้วย)
        updateWeightPriceButtons();

        // --- END: โค้ดส่วน JavaScript ---


        // ส่งข้อมูลฟอร์ม (โค้ดเดิม)
        $('#shipping-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_shipping_setting",
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        alert_toast("บันทึกข้อมูลเรียบร้อยแล้ว", 'success');
                        setTimeout(() => {
                            location.href = "./?page=shipping_setting";
                        }, 1000);
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div class="alert alert-danger err-msg">').text(resp.msg);
                        _this.prepend(el);
                        $("html, body").scrollTop(0);
                        end_loader();
                    } else {
                        alert_toast("ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>