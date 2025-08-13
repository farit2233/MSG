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
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขโปรโมชั่น" : "สร้างโปรโมชั่นใหม่" ?></div>
    </div>
    <form action="" id="coupon-code-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลโปรโมชั่น</div>
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
                                <option value="" disabled <?= !isset($type) ? 'selected' : '' ?>>-- เลือกหมวดหมู่โปรโมชั่น --</option>
                                <option value="fixed" <?= (isset($type) && $type == 'fixed') ? 'selected' : '' ?>>ลดราคาคงที่ (บาท)</option>
                                <option value="percent" <?= (isset($type) && $type == 'percent') ? 'selected' : '' ?>>ลดเป็นเปอร์เซ็นต์ (%)</option>
                                <option value="free_shipping" <?= (isset($type) && $type == 'free_shipping') ? 'selected' : '' ?>>ส่งฟรี</option>
                                <!--option value="code" <?= (isset($type) && $type == 'code') ? 'selected' : '' ?>>ส่วนลดผ่านโค้ด</option-->
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label>เลือกใช่ร่วมกับโปรโมชั่นประเภทอื่นที่ไม่ใช่คูปองได้ <span class="text-danger">*</span></label>
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
                            <input type="number" name="limit_coupon" step="0.01" min="0" class="form-control" placeholder="กรุณากำหนดจำนวนสิทธิการใช้คูปอง" value="<?= isset($limit_coupon) ? $limit_coupon : '' ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label>กำหนดจำนวนคูปอง <span class="text-danger">*</span></label>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <input type="number" name="coupon_amount" step="0.01" min="0" class="form-control" placeholder="กรุณากำหนดจำนวนคูปอง" value="<?= isset($coupon_amount) ? $coupon_amount : '' ?>" id="coupon_amount">
                                </div>
                                <div class="col-md-6">
                                    <div class="radio-group">
                                        <label>
                                            <input type="radio" id="unl_coupon" name="unl_coupon" value="1" <?= (isset($unl_coupon) && $unl_coupon == 1) ? 'checked' : '' ?> required>
                                            ไม่จำกัดจำนวนคูปอง
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>เริ่มวันที่ <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="start_date" class="form-control" required value="<?= isset($start_date) ? date('Y-m-d\TH:i', strtotime($start_date)) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label>สิ้นสุดวันที่ <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="end_date" class="form-control" required value="<?= isset($end_date) ? date('Y-m-d\TH:i', strtotime($end_date)) : '' ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">สถานะโปรโมชั่น</div>
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
            <button class="btn btn-success btn-sm btn-flat" form="coupon-code-form"><i class="fa fa-save"></i> บันทึก</button>
            <a class="btn btn-danger btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
            <a class="btn btn-light btn-sm border btn-flat" href="javascript:void(0)" id="backBtn"><i class="fa fa-angle-left"></i> กลับ</a>
        </div>
    </form>

</section>
<script>
    $(document).ready(function() {

        let formChanged = false;

        // ตรวจสอบการเปลี่ยนแปลงของฟอร์ม
        $('#coupon-code-form input, #coupon-code-form textarea').on('input', function() {
            formChanged = true;
        });

        // เมื่อกดปุ่ม "ยกเลิก"
        $('#cancelBtn').click(function() {
            if (formChanged) {
                // ถ้ามีการเปลี่ยนแปลงข้อมูล
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมดและหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
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
                    text: "การเปลี่ยนแปลงจะหายไปและคุณจะกลับไปหน้าหมวดหมู่โปรโมชั่น",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // กลับไปหน้าหมวดหมู่โปรโมชั่น
                        window.location.href = './?page=coupon_code';
                    }
                });
            } else {
                // ถ้าไม่มีการเปลี่ยนแปลงก็กลับไปหน้าหมวดหมู่โปรโมชั่น
                window.location.href = './?page=coupon_code';
            }
        });

        document.getElementById('coupon_amount').addEventListener('input', function() {
            var discountValue = document.getElementById('coupon_amount').value;
            var noLimitRadio = document.getElementById('unl_coupon');
            var discountInput = document.getElementById('coupon_amount');

            // หากกรอกตัวเลข ระบบจะยกเลิกการเลือก radio "ไม่จำกัดจำนวนคูปอง" และเปลี่ยน placeholder กลับ
            if (discountValue > 0) {
                noLimitRadio.checked = false; // ยกเลิกการเลือก radio "ไม่จำกัดจำนวนคูปอง"
                discountInput.placeholder = "จำนวนสิทธิการใช้คูปอง"; // เปลี่ยน placeholder กลับ
                discountInput.setAttribute('required', 'required'); // ตั้งค่า required กลับ
            }
        });

        document.getElementById('unl_coupon').addEventListener('change', function() {
            var noLimitRadio = document.getElementById('unl_coupon');
            var discountValue = document.getElementById('coupon_amount');

            // หากเลือก "ไม่จำกัดจำนวนคูปอง" ให้ลบค่าในช่องกรอกและเปลี่ยน placeholder
            if (noLimitRadio.checked) {
                discountValue.value = ''; // ลบค่าที่กรอกในช่อง
                discountValue.removeAttribute('required'); // ลบ attribute required
                discountValue.placeholder = "ไม่จำกัดจำนวนคูปอง"; // เปลี่ยน placeholder เป็น "ไม่จำกัดจำนวนคูปอง"
            } else {
                discountValue.setAttribute('required', 'required'); // ใส่ attribute required กลับ
            }
        });

        // ฟังก์ชัน submit
        $('#coupon-code-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();

            // ตรวจสอบให้แน่ใจว่า coupon_amount มีค่า
            var couponAmount = document.getElementById('coupon_amount').value;
            if (!couponAmount && !document.getElementById('unl_coupon').checked) {
                alert('กรุณากรอกจำนวนคูปองหรือเลือก "ไม่จำกัดจำนวนคูปอง"');
                end_loader();
                return;
            }

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
                        location.replace('./?page=coupon_code/manage_coupon_code&id=' + resp.cid);
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