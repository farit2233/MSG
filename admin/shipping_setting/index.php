<?php
// ตรวจสอบสิทธิ์การใช้งาน
if ($_settings->userdata('type') != 1) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'ไม่มีสิทธิ์ใช้งาน',
                text: 'คุณไม่มีสิทธิ์ใช้งานหน้าเพจนี้',
                confirmButtonText: 'ตกลง',
                allowOutsideClick: false,
            }).then(function() {
                window.location.href = './';
            });
          </script>";
    exit;
}

// กำหนดค่าเริ่มต้นตัวแปร
$id = '';
$price_default = 0.00;
$N = 0.00;
$L = 0.00;
$XL = 0.00;
$rules_size = 1;
$rules_total = 1;

// --- 1. ดึงข้อมูลการตั้งค่าหลัก (shipping_system) ---
$qry = $conn->query("SELECT * FROM shipping_system LIMIT 1");

// ถ้าตารางว่างเปล่า ให้ Insert ข้อมูลเริ่มต้นทันที
if ($qry->num_rows == 0) {
    $conn->query("INSERT INTO `shipping_system` (`id`, `N`, `L`, `XL`, `price_default`, `rules_size`, `rules_total`) VALUES ('1', '40.00', '60.00', '80.00', '80.00', '1', '1')");
    $qry = $conn->query("SELECT * FROM shipping_system LIMIT 1");
}

if ($qry && $qry->num_rows > 0) {
    $row = $qry->fetch_assoc();
    $id = $row['id'];
    $price_default = $row['price_default'];
    $N = $row['N'];
    $L = $row['L'];
    $XL = $row['XL'];
    $rules_size = $row['rules_size'];
    $rules_total = $row['rules_total'];
}

// --- 2. ดึงข้อมูลกฎตามยอดรวม (shipping_total) ---
$total_rules = [];
$qry_total = $conn->query("SELECT * FROM shipping_total ORDER BY min_price ASC");
while ($rt = $qry_total->fetch_assoc()) {
    $total_rules[] = $rt;
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

    /* แก้ไขสีปุ่ม Confirm ของ SweetAlert2 เป็นสีฟ้า */
    .swal2-confirm {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: white !important;
    }
</style>

<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">ตั้งค่าระบบจัดส่ง</div>
        <div class="card-tools">
            <a href="javascript:void(0)" id="reset_btn" class="btn btn-flat btn-dark">
                <span class="fas fa-undo"></span> รีเซ็ตค่าเริ่มต้น
            </a>
        </div>
    </div>

    <form action="" id="shipping-form" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">

        <div class="card-body">

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;"><i class="fa-solid fa-gear"></i> เปิด / ปิดระบบ</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="rules_size" value="0">
                    <div class="d-flex justify-content-between align-items-center border rounded p-3 bg-light mb-2">
                        <div class="pr-3">
                            <div class="font-weight-bold text-dark mb-1">เปิดใช้กฎตามขนาดสินค้า</div>
                            <div class="text-muted small" style="line-height: 1.4;">
                                คิดค่าส่งตามขนาด NORMAL, L, XL
                            </div>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="rules_size" name="rules_size" value="1" <?= isset($rules_size) && $rules_size == 1 ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="rules_size"></label>
                        </div>
                    </div>

                    <input type="hidden" name="rules_total" value="0">
                    <div class="d-flex justify-content-between align-items-center border rounded p-3 bg-light">
                        <div class="pr-3">
                            <div class="font-weight-bold text-dark mb-1">เปิดใช้กฎตามยอดรวม</div>
                            <div class="text-muted small" style="line-height: 1.4;">
                                คิดค่าส่งตามยอดซื้อทั้งหมด (สำหรับสินค้า NORMAL)
                            </div>
                        </div>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="rules_total" name="rules_total" value="1" <?= isset($rules_total) && $rules_total == 1 ? 'checked' : '' ?>>
                            <label class="custom-control-label" for="rules_total"></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ค่าจัดส่งมาตรฐาน (Default) <span class="text-danger">*</span></div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <div class="small text-muted mb-1">ใช้เมื่อไม่ตรงกับเงื่อนไขอื่น ๆ</div>
                                <div class="input-group">
                                    <!-- แก้ไข: แปลงเป็น (float) เพื่อตัด .00 -->
                                    <input type="number" step="0.01" name="price_default" class="form-control" value="<?= isset($price_default) ? (float)$price_default : 0 ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ค่าจัดส่งตามขนาดสินค้า</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row mb-2">
                            <div class="col-md-4">
                                <label>NORMAL (ปกติ)</label>
                                <div class="input-group">
                                    <!-- แก้ไข: แปลงเป็น (float) เพื่อตัด .00 -->
                                    <input type="number" step="0.01" name="N" class="form-control" value="<?= isset($N) ? (float)$N : 0 ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>L (ขนาดใหญ่)</label>
                                <div class="input-group">
                                    <!-- แก้ไข: แปลงเป็น (float) เพื่อตัด .00 -->
                                    <input type="number" step="0.01" name="L" class="form-control" value="<?= isset($L) ? (float)$L : 0 ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>XL (ขนาดพิเศษ)</label>
                                <div class="input-group">
                                    <!-- แก้ไข: แปลงเป็น (float) เพื่อตัด .00 -->
                                    <input type="number" step="0.01" name="XL" class="form-control" value="<?= isset($XL) ? (float)$XL : 0 ?>" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">บาท</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">กฎตามยอดรวม</div>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary btn-sm add-total-price">
                            <i class="fas fa-plus"></i> เพิ่มรายการ
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="form-group">
                        <div class="mb-3">
                            <div class="text-muted small" style="line-height: 1.4;">
                                กำหนดช่วงยอดรวมและค่าจัดส่งที่ต้องการ (ใช้กับสินค้า NORMAL เท่านั้น)
                            </div>
                        </div>

                        <div id="price_total_group">
                            <?php
                            if (!empty($total_rules)):
                                foreach ($total_rules as $rule):
                                    // คำนวณข้อความสำหรับแสดงผลตอนโหลดหน้าเว็บ
                                    $min_show = number_format($rule['min_price'] * 1);
                                    $max_show = ($rule['max_price'] > 0) ? number_format($rule['max_price'] * 1) : '∞';

                                    // Logic แสดงข้อความ
                                    $ship_show = ($rule['shipping_price'] == 0) ? 'ฟรี' : number_format($rule['shipping_price'] * 1) . ' บาท';
                                    $desc_text = "ช่วง: {$min_show} - {$max_show} บาท → ค่าส่ง {$ship_show}";

                                    // Logic Value: คูณ 1 หรือแปลง float เพื่อตัดทศนิยม
                                    $val_min = $rule['min_price'] * 1;
                                    $val_ship = ($rule['shipping_price'] > 0) ? ($rule['shipping_price'] * 1) : '';
                                    $val_max = ($rule['max_price'] > 0) ? ($rule['max_price'] * 1) : '';
                            ?>
                                    <div class="price_total_row border rounded p-3 bg-light mb-3 position-relative">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <label>ยอดขั้นต่ำ (บาท)</label>
                                                <!-- ใช้ $val_min ที่คูณ 1 แล้ว -->
                                                <input type="number" step="0.01" name="min_price[]" class="form-control" value="<?= htmlspecialchars($val_min) ?>" required>
                                            </div>
                                            <div class="col-md-3">
                                                <label>ยอดสูงสุด (บาท)</label>
                                                <input type="number" step="0.01" name="max_price[]" class="form-control" value="<?= $val_max ?>" placeholder="ไม่จำกัด">
                                            </div>
                                            <div class="col-md-3">
                                                <label>ค่าจัดส่ง (บาท)</label>
                                                <input type="number" step="0.01" name="shipping_price[]" class="form-control" value="<?= $val_ship ?>" placeholder="ส่งฟรี">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="d-block text-light" style="user-select: none;">จัดการ</label>
                                                <div class="button-container">
                                                    <button type="button" class="btn btn-danger remove-total-price w-100">
                                                        <i class="fas fa-trash"></i> ลบรายการ
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-2">
                                            <div class="col-12">
                                                <small class="text-muted rule-description"><i class="fas fa-info-circle mr-1"></i> <?= $desc_text ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php
                                endforeach;
                            else:
                                // กรณีไม่มีข้อมูลเลย ให้แสดงแถวแรกแบบว่าง
                                ?>
                                <div class="price_total_row border rounded p-3 bg-light mb-3 position-relative">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <label>ยอดขั้นต่ำ (บาท)</label>
                                            <input type="number" step="0.01" name="min_price[]" class="form-control" value="" placeholder="เช่น 0" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label>ยอดสูงสุด (บาท)</label>
                                            <input type="number" step="0.01" name="max_price[]" class="form-control" value="" placeholder="ไม่จำกัด">
                                        </div>
                                        <div class="col-md-3">
                                            <label>ค่าจัดส่ง (บาท)</label>
                                            <input type="number" step="0.01" name="shipping_price[]" class="form-control" value="" placeholder="ส่งฟรี">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="d-block text-light" style="user-select: none;">จัดการ</label>
                                            <div class="button-container">
                                                <button type="button" class="btn btn-danger remove-total-price w-100">
                                                    <i class="fas fa-trash"></i> ลบรายการ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-2">
                                        <div class="col-12">
                                            <small class="text-muted rule-description"><i class="fas fa-info-circle mr-1"></i> ช่วง: 0 - ∞ บาท → ค่าส่ง ฟรี</small>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
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
        // --- ส่วนที่ 1: ตรวจสอบการแก้ไขฟอร์ม ---
        let formChanged = false;

        $('#shipping-form input, #shipping-form textarea').on('input', function() {
            formChanged = true;
        });

        $('#cancelBtn').click(function() {
            if (formChanged) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) location.reload();
                });
            } else {
                location.reload();
            }
        });

        $('#backBtn').click(function() {
            if (formChanged) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) window.location.href = '<?php echo base_url ?>admin';
                });
            } else {
                window.location.href = '<?php echo base_url ?>admin';
            }
        });

        // --- ส่วนที่ 2: JavaScript UI กฎตามยอดรวม (Updated) ---
        const SQL_MAX_LIMIT = 99999999.99;

        // Template สำหรับแถวใหม่ (ปรับปรุง shipping_price: เอา required ออก, ใส่ placeholder)
        const newRowTemplate = `
        <div class="price_total_row border rounded p-3 bg-light mb-3 position-relative">
            <div class="row">
                <div class="col-md-3">
                    <label>ยอดขั้นต่ำ (บาท)</label>
                    <input type="number" step="0.01" name="min_price[]" class="form-control" placeholder="เช่น 0" required>
                </div>
                <div class="col-md-3">
                    <label>ยอดสูงสุด (บาท)</label>
                    <input type="number" step="0.01" name="max_price[]" class="form-control" placeholder="ไม่จำกัด" max="${SQL_MAX_LIMIT}">
                </div>
                <div class="col-md-3">
                    <label>ค่าจัดส่ง (บาท)</label>
                    <input type="number" step="0.01" name="shipping_price[]" class="form-control" placeholder="ส่งฟรี">
                </div>
                <div class="col-md-3">
                    <label class="d-block text-light" style="user-select: none;">จัดการ</label>
                    <div class="button-container">
                        <button type="button" class="btn btn-danger remove-total-price w-100">
                            <i class="fas fa-trash"></i> ลบรายการ
                        </button>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted rule-description"><i class="fas fa-info-circle mr-1"></i> ช่วง: 0 - ∞ บาท → ค่าส่ง ฟรี</small>
                </div>
            </div>
        </div>`;

        // ฟังก์ชันคำนวณข้อความสรุป และดักค่าเกิน SQL
        function updateRuleText(row) {
            let minVal = parseFloat(row.find('input[name="min_price[]"]').val()) || 0;
            let maxInputObj = row.find('input[name="max_price[]"]');
            let maxVal = parseFloat(maxInputObj.val());

            // --- Logic ดักค่าเกิน SQL ---
            if (maxVal > SQL_MAX_LIMIT) {
                maxInputObj.val('');
                maxVal = NaN;
            }

            // แปลง max: ถ้าว่าง, 0 หรือ NaN ให้เป็น ∞
            let maxText = (isNaN(maxVal) || maxVal === 0 || maxInputObj.val() === '') ? '∞' : maxVal.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            });

            // --- Logic ค่าส่ง (ใหม่) ---
            // ถ้า input เป็นค่าว่าง หรือ 0 ให้ถือว่าเป็น "ฟรี"
            let shipInputVal = row.find('input[name="shipping_price[]"]').val();
            let shipVal = (shipInputVal === '' || isNaN(parseFloat(shipInputVal))) ? 0 : parseFloat(shipInputVal);

            let shipText = (shipVal === 0) ? 'ฟรี' : shipVal.toLocaleString(undefined, {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }) + ' บาท';

            // อัปเดตข้อความ
            let desc = `ช่วง: ${minVal.toLocaleString(undefined, {minimumFractionDigits: 0, maximumFractionDigits: 2})} - ${maxText} บาท → ค่าส่ง ${shipText}`;
            row.find('.rule-description').html(`<i class="fas fa-info-circle mr-1"></i> ${desc}`);
        }

        // Event: เมื่อพิมพ์ในช่อง input (ทำงาน Real-time)
        $(document).on('input', '.price_total_row input', function() {
            let row = $(this).closest('.price_total_row');
            updateRuleText(row);
        });

        // Event: เมื่อเพิ่มแถวใหม่
        $(document).on('click', '.add-total-price', function() {
            $('#price_total_group').append(newRowTemplate);
        });

        // Event: เมื่อลบแถว
        $(document).on('click', '.remove-total-price', function() {
            $(this).closest('.price_total_row').remove();
        });

        // --- ส่วนเสริม: ปุ่มรีเซ็ตค่าเริ่มต้น (Factory Reset) ---
        $('#reset_btn').click(function() {
            Swal.fire({
                title: 'คืนค่าเริ่มต้น?',
                text: "ค่าทั้งหมดจะถูกเปลี่ยนเป็นค่าตั้งต้นและบันทึกทันที", // แจ้ง User ว่าจะบันทึกทันที
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#rules_size').prop('checked', true);
                    $('#rules_total').prop('checked', true);
                    $('input[name="price_default"]').val(80.00);
                    $('input[name="N"]').val(40.00);
                    $('input[name="L"]').val(60.00);
                    $('input[name="XL"]').val(80.00);

                    // รีเซ็ตตารางกฎ (3 ข้อตามที่ขอ)
                    $('#price_total_group').empty();

                    const defaultRules = [{
                            min: 0,
                            max: 499,
                            ship: 50
                        },
                        {
                            min: 500,
                            max: 999,
                            ship: 20
                        },
                        {
                            min: 1000,
                            max: '',
                            ship: ''
                        }
                    ];

                    defaultRules.forEach(rule => {
                        let newRow = $(newRowTemplate);
                        newRow.find('input[name="min_price[]"]').val(rule.min);
                        newRow.find('input[name="max_price[]"]').val(rule.max);
                        newRow.find('input[name="shipping_price[]"]').val(rule.ship);
                        updateRuleText(newRow);
                        $('#price_total_group').append(newRow);
                    });

                    // --- สั่งบันทึกทันที (Auto Save) ---
                    // สั่ง submit form โปรแกรมจะวิ่งไปที่ handler ด้านล่างและทำการ save ajax ให้เลย
                    $('#shipping-form').submit();
                }
            });
        });

        // --- ส่วนที่ 3: ส่งข้อมูล (Submit) ---
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