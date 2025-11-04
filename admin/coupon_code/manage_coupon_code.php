<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM coupon_code_list WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    .radio-group {
        display: flex;
        margin-top: 6px;
    }

    .radio-group input[type="radio"] {
        transform: scale(1.25);
        /* ปรับขนาด radio button */
    }

    .radio-group label {
        margin-right: 20px;
        /* ช่องว่างระหว่าง radio */
    }

    .radio-group label:last-child {
        margin-left: 3.5rem;
        /* ปรับให้ radio ตัวขวาอยู่ใกล้ตัวแรก */
    }

    .swal2-confirm {
        background-color: #28a745 !important;
        /* สีเขียว */
        border-color: #28a745 !important;
        /* สีเขียว */
        color: white !important;
        /* สีตัวอักษรเป็นขาว */
    }

    .swal2-confirm:hover {
        background-color: #218838 !important;
        /* สีเขียวเข้ม */
        border-color: #1e7e34 !important;
        /* สีเขียวเข้ม */
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขโปรโมชัน" : "สร้างโปรโมชันใหม่" ?></div>
    </div>
    <form action="" id="coupon-code-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลโปรโมชัน</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>ชื่อคูปองส่วนลด <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="กรุณาใส่ชื่อคูปอง" value=" <?php echo isset($name) ? $name : ''; ?>" required />
                        </div>
                        <div class="col-md-6">
                            <label>รหัสคูปอง <span class="text-danger">*</span></label>
                            <input type="text" name="coupon_code" id="coupon_code" class="form-control" placeholder="กรุณากรอกรหัสคูปอง" value="<?php echo isset($coupon_code) ? $coupon_code : ''; ?>" required />
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">รายละเอียด</label>
                        <textarea rows="3" name="description" id="description" class="form-control" placeholder="รายละเอียดคูปอง"><?php echo isset($description) ? $description : ''; ?></textarea>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>ประเภทส่วนลด <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" required>
                                <option value="" disabled <?= !isset($type) ? 'selected' : '' ?>>-- เลือกหมวดหมู่โปรโมชัน --</option>
                                <option value="fixed" <?= (isset($type) && $type == 'fixed') ? 'selected' : '' ?>>ลดราคาคงที่ (บาท)</option>
                                <option value="percent" <?= (isset($type) && $type == 'percent') ? 'selected' : '' ?>>ลดเป็นเปอร์เซ็นต์ (%)</option>
                                <option value="free_shipping" <?= (isset($type) && $type == 'free_shipping') ? 'selected' : '' ?>>ส่งฟรี</option>
                                <!--option value="code" <?= (isset($type) && $type == 'code') ? 'selected' : '' ?>>ส่วนลดผ่านโค้ด</option-->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>เลือกใช่ร่วมกับโปรโมชันประเภทอื่นที่ไม่ใช่คูปองได้ <span class="text-danger">*</span></label>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" id="cpromo_1" name="cpromo" value="1" <?= (isset($cpromo) && $cpromo == 1) ? 'checked' : '' ?> required>
                                    ได้
                                </label>
                                <label>
                                    <input type="radio" id="cpromo_0" name="cpromo" value="0" <?= (isset($cpromo) && $cpromo == 0) ? 'checked' : '' ?> required>
                                    ไม่
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>มูลค่าส่วนลด</label>
                            <input type="number" name="discount_value" step="0.01" min="0" class="form-control" placeholder="กรอกมูลค่าส่วนลด" value="<?= isset($discount_value) ? $discount_value : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label>ยอดสั่งซื้อขั้นต่ำ</label>
                            <input type="number" name="minimum_order" step="0.01" min="0" class="form-control" placeholder="กรอกยอดสั่งซื้อขั้นต่ำ" value="<?= isset($minimum_order) ? $minimum_order : '' ?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>จำกัดสิทธิการใช้คูปอง จำนวน ครั้ง / 1 ลูกค้า <span class="text-danger">*</span></label>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="number" name="limit_coupon" step="1" min="0" class="form-control" placeholder="กรอกจำนวน" value="<?= isset($limit_coupon) ? $limit_coupon : '' ?>" id="limit_coupon">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="unl_coupon" name="unl_coupon" value="1" <?= (isset($unl_coupon) && $unl_coupon == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="unl_coupon">
                                            ไม่จำกัดจำนวนครั้ง
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>กำหนดจำนวนคูปอง <span class="text-danger">*</span></label>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="number" name="coupon_amount" step="1" min="0" class="form-control" placeholder="กรอกจำนวน" value="<?= isset($coupon_amount) ? $coupon_amount : '' ?>" id="coupon_amount">
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="unl_amount" name="unl_amount" value="1" <?= (isset($unl_amount) && $unl_amount == 1) ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="unl_amount">
                                            ไม่จำกัดจำนวน
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>เริ่มวันที่ <span class="text-danger">*</span></label>
                            <div class="input-group flatpickr-thai">
                                <input type="text" name="start_date" class="form-control" placeholder="เลือกวัน-เวลาเริ่มต้น..."
                                    value="<?= isset($start_date) ? date('Y-m-d H:i', strtotime($start_date)) : '' ?>" required data-input>

                                <div class="input-group-append">
                                    <a class="input-group-text" title="เปิด/ปิดปฏิทิน" data-toggle>
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label>สิ้นสุดวันที่ <span class="text-danger">*</span></label>
                            <div class="input-group flatpickr-thai">
                                <input type="text" name="end_date" class="form-control" placeholder="เลือกวัน-เวลาสิ้นสุด..."
                                    value="<?= isset($end_date) ? date('Y-m-d H:i', strtotime($end_date)) : '' ?>" required data-input>

                                <div class="input-group-append">
                                    <a class="input-group-text" title="เปิด/ปิดปฏิทิน" data-toggle>
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">การใช้คูปองกับสินค้า</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="all_products_status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="all_products_status" name="all_products_status" value="1" <?= (isset($all_products_status) && $all_products_status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="all_products_status">เปิด / ปิดการใช้คูปองกับสินค้าได้ทุกชนิด</label>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">สถานะโปรโมชัน</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">เปิด / ปิดการใช้งานโปรโมชัน</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer py-1 text-center">
            <a class="btn btn-light btn-sm border btn-flat" href="javascript:void(0)" id="backBtn"><i class="fa fa-angle-left"></i> กลับ</a>
            <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
            <button class="btn btn-success btn-sm btn-flat" form="coupon-code-form"><i class="fa fa-save"></i> บันทึก</button>
        </div>
    </form>

</section>
<script>
    $(document).ready(function() {

        flatpickr(".flatpickr-thai", {
            wrap: true, // สำหรับการใช้งานไอคอน (input-group)
            enableTime: true, // เปิดให้เลือกเวลา
            time_24hr: true, // ใช้เวลารูปแบบ 24 ชั่วโมง
            locale: "th", // ใช้งานภาษาไทย (ต้อง import ไฟล์ th.js)

            // --- ส่วนสำคัญ ---
            altInput: true, // สร้าง input อีกอันไว้แสดงผล
            altFormat: "j F พ.ศ. Y (H:i น.)", // รูปแบบที่แสดงให้ผู้ใช้เห็น
            dateFormat: "Y-m-d H:i", // รูปแบบข้อมูลที่จะส่งไปให้ Server
        });

        let formChanged = false;

        // ตรวจสอบการเปลี่ยนแปลงของฟอร์ม
        $('#coupon-code-form input, #coupon-code-form textarea, #coupon-code-form select, #coupon-code-form input[type="radio"], #coupon-code-form input[type="checkbox"]').on('change input', function() {
            formChanged = true;
        });


        // เมื่อกดปุ่ม "ยกเลิก"
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
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                location.reload();
            }
        });

        // เมื่อกดปุ่ม "กลับ"
        $('#backBtn').click(function() {
            if (formChanged) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และจะกลับไปหน้าหลัก",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = './?page=coupon_code';
                    }
                });
            } else {
                window.location.href = './?page=coupon_code';
            }
        });


        /**
         * ฟังก์ชันสำหรับจัดการ Logic การบังคับกรอกข้อมูลระหว่าง Input กับ Radio
         * @param {string} inputId - ID ของช่อง input type="number"
         * @param {string} radioId - ID ของช่อง input type="radio"
         */
        const setupValidationLogic = (inputId, radioId) => {
            const numberInput = document.getElementById(inputId);
            const unlimitedRadio = document.getElementById(radioId);

            // ฟังก์ชันสำหรับอัปเดตสถานะ required
            const updateValidationState = () => {
                // ถ้าช่องตัวเลขว่าง และ Radio ไม่ได้ถูกติ๊ก -> ให้บังคับกรอก (required)
                if (numberInput.value === '' && !unlimitedRadio.checked) {
                    numberInput.required = true;
                } else {
                    // มิเช่นนั้น ไม่ต้องบังคับกรอก
                    numberInput.required = false;
                }
            };

            // เมื่อพิมพ์ในช่องตัวเลข
            numberInput.addEventListener('input', function() {
                if (this.value !== '') {
                    // ถ้ามีค่า ให้เอาติ๊กออกจาก Radio
                    unlimitedRadio.checked = false;
                }
                // อัปเดตสถานะ validation
                updateValidationState();
            });

            // เมื่อติ๊กที่ Radio
            unlimitedRadio.addEventListener('change', function() {
                if (this.checked) {
                    // ถ้าติ๊ก ให้ลบค่าในช่องตัวเลข
                    numberInput.value = '';
                }
                // อัปเดตสถานะ validation
                updateValidationState();
            });

            // เรียกใช้ฟังก์ชันครั้งแรกเมื่อโหลดหน้าเว็บ เพื่อกำหนดสถานะให้ถูกต้อง
            updateValidationState();
        };

        // เรียกใช้งานฟังก์ชันกับคู่ฟอร์มทั้งสอง
        setupValidationLogic('limit_coupon', 'unl_coupon');
        setupValidationLogic('coupon_amount', 'unl_amount');


        // ฟังก์ชัน submit
        $('#coupon-code-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();

            // ตรวจสอบ validation อีกครั้งก่อนส่ง
            if (document.getElementById('limit_coupon').hasAttribute('required') && document.getElementById('limit_coupon').value === '' ||
                document.getElementById('coupon_amount').hasAttribute('required') && document.getElementById('coupon_amount').value === '') {
                // ไม่ต้องทำอะไร ปล่อยให้ HTML5 validation จัดการ
                // หรือจะแสดง alert เองก็ได้
                return false;
            }

            start_loader();

            // ส่งข้อมูลฟอร์มด้วย Ajax
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_coupon_code",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.replace('./?page=coupon_code');
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>');
                        el.addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").scrollTop(0);
                        end_loader();
                    } else {
                        alert_toast("An error occurred", 'error');
                        end_loader();
                        console.log(resp);
                    }
                }
            });
        });

    });
</script>