<?php
require_once('../config.php');
// ตรวจสอบว่าเรามีการส่ง ID มาไหม (สำหรับฟังก์ชันเปลี่ยนรหัสผ่าน)
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) {
                $$k = $v;
            }
        }
    } else {
        echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
}
?>
<style>
    .text-size-input {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }

    .password input,
    .password select {
        border-radius: 13px;
        font-size: 16px;
    }

    .icon-success {
        color: #28a745;
        /* สีเขียวสำหรับติ๊กถูก */
        font-size: 60px;
        /* ขนาดไอคอนใหญ่ */
        margin-bottom: 20px;
        /* เว้นระยะห่างจากข้อความ */
    }
</style>
<div class="container-fluid password">
    <form id="confirm_form_1">
        <?php
        $addresses = $conn->query("SELECT * FROM customer_addresses WHERE customer_id = '{$_settings->userdata('id')}' ORDER BY is_primary DESC, id ASC");
        while ($row = $addresses->fetch_assoc()):
        ?>
            <div class="address-option d-flex justify-content-between align-items-center" data-id="<?= $row['id'] ?>" onclick="selectAddress(this)">
                <div class="flex-grow-1 d-flex align-items-center">
                    <input type="radio" name="address_id" value="<?= $row['id'] ?>" class="address-radio me-3">
                    <div>
                        <h6 class="mb-0">
                            ที่อยู่ <?= ($row['is_primary'] == 1) ? 'หลัก' : 'เพิ่มเติม' ?>
                        </h6>
                        <p class="mb-0 text-muted small">
                            <?= !empty($row['name']) ? htmlspecialchars($row['name']) : 'ไม่พบชื่อ' ?><br>
                            <?= !empty($row['contact']) ? htmlspecialchars($row['contact']) : 'ไม่พบเบอร์โทรศัพท์' ?><br>
                            <?= !empty($row['address']) ? 'ที่อยู่ ' . htmlspecialchars($row['address']) . ',' : ', ไม่พบที่อยู่,' ?>
                            <?= !empty($row['sub_district']) ? 'ต.' . htmlspecialchars($row['sub_district']) . ',' : '' ?>
                            <?= !empty($row['district']) ? 'อ.' . htmlspecialchars($row['district']) . ',' : '' ?>
                            <?= !empty($row['province']) ? 'จ.' . htmlspecialchars($row['province']) . ',' : '' ?>
                            <?= !empty($row['postal_code']) ? htmlspecialchars($row['postal_code']) : '' ?>
                        </p>
                    </div>
                </div>
                <div class="ms-3 d-flex flex-column align-items-end">
                    <a href="#" class="edit-address mb-1 text-sm"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['name']) ?>"
                        data-contact="<?= htmlspecialchars($row['contact']) ?>"
                        data-address="<?= htmlspecialchars($row['address']) ?>"
                        data-sub_district="<?= htmlspecialchars($row['sub_district']) ?>"
                        data-district="<?= htmlspecialchars($row['district']) ?>"
                        data-province="<?= htmlspecialchars($row['province']) ?>"
                        data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>"
                        style="text-decoration: none;" id="editAddress">
                        <i class="fa-solid fa-pencil-alt"></i> แก้ไข
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

    </form>
    <form id="confirm_form_2" style="display: none;">
        <input type="hidden" name="address_id" id="address_id">
        <input type="hidden" name="customer_id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="address" class="control-label">ชื่อ นามสกุล<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="address" class="control-label">บ้านเลขที่ ถนน <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="address" id="address" required>
        </div>
        <div class="form-group">
            <label for="sub_district" class="control-label">ตำบล <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="sub_district" id="sub_district" required>
        </div>
        <div class="form-group">
            <label for="district" class="control-label">อำเภอ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="district" id="district" required>
        </div>
        <div class="form-group">
            <label for="province" class="control-label">จังหวัด <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="province" id="province" required>
        </div>
        <div class="form-group">
            <label for="postal_code" class="control-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="postal_code" id="postal_code" required>
        </div>
        <div class="text-right">
            <small><a href="#" id="back_to_change_address">กลับไปหน้าสมุดบัญชี</a></small>
        </div>
    </form>

</div>
<script>
    $(document).ready(function() {
        var passwordInput = $('#new_password');
        var requirementsDiv = $('#password-requirements');
        var lengthReq = $('#length');
        var lowerReq = $('#lowercase');
        var numReq = $('#number');

        // เมื่อเริ่มพิมพ์ในช่องรหัสผ่าน
        passwordInput.on('focus', function() {
            requirementsDiv.slideDown('fast');
        });

        passwordInput.on('keyup', function() {
            var password = $(this).val();

            // ตรวจสอบความยาว
            if (password.length >= 8) {
                lengthReq.removeClass('invalid').addClass('valid');
            } else {
                lengthReq.removeClass('valid').addClass('invalid');
            }

            // ตรวจสอบตัวพิมพ์เล็ก
            if (password.match(/[a-z]/)) {
                lowerReq.removeClass('invalid').addClass('valid');
            } else {
                lowerReq.removeClass('valid').addClass('invalid');
            }

            // ตรวจสอบตัวเลข
            if (password.match(/\d/)) {
                numReq.removeClass('invalid').addClass('valid');
            } else {
                numReq.removeClass('valid').addClass('invalid');
            }
        });

        // ซ่อนเมื่อไม่ได้โฟกัสและช่องว่าง
        passwordInput.on('blur', function() {
            if ($(this).val() === '') {
                requirementsDiv.slideUp('fast');
            }
        });


        // --- ฟังก์ชันสำหรับสลับฟอร์ม ---
        function showAddressOptionForm() {
            $('#confirm_form_1').show();
            $('#confirm_form_2').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').show();
        }

        function showeditAddressForm() {
            $('#confirm_form_1').hide();
            $('#confirm_form_2').show();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').show();
        }

        // --- Event Handlers ---
        $('#editAddress').click(function(e) {
            e.preventDefault();
            showeditAddressForm();

            var _this = $(this); // อ้างอิงถึงปุ่ม 'แก้ไข' ที่ถูกคลิก

            // 1. เปลี่ยนหัวข้อฟอร์ม
            $('#form-title').text('แก้ไขที่อยู่');

            // 2. ดึงข้อมูลจาก data attributes ของปุ่มมาใส่ในฟอร์ม
            $('#address_id').val(_this.data('id'));
            $('#name').val(_this.data('name'));
            $('#contact').val(_this.data('contact'));
            $('#address').val(_this.data('address'));
            $('#sub_district').val(_this.data('sub_district'));
            $('#district').val(_this.data('district'));
            $('#province').val(_this.data('province'));
            $('#postal_code').val(_this.data('postal_code'));

            // 3. ซ่อนรายการที่อยู่และแสดงฟอร์ม
            $('#address-list').hide();
            $('#address-form').show();
        });

        // เมื่อคลิกลิงก์ "กลับไปหน้าเปลี่ยนรหัสผ่าน"
        $('#back_to_change_address').click(function(e) {
            e.preventDefault();
            showAddressOptionForm();
        });


        $('#confirm_form_1').click(function(e) {
            e.preventDefault();
            var _this = $(this);

            // ถ้าเป็นที่อยู่หลักอยู่แล้ว จะไม่ให้คลิก
            if (_this.hasClass('disabled')) return;

            var address_id = _this.data('id');

            // ใช้ SweetAlert เพื่อยืนยันก่อนตั้งเป็นที่อยู่หลัก
            Swal.fire({
                title: 'ยืนยันการตั้งเป็นที่อยู่หลัก?',
                text: "คุณต้องการตั้งที่อยู่นี้เป็นที่อยู่หลักหรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // ถ้าผู้ใช้ยืนยัน ให้ส่งคำขอไปที่เซิร์ฟเวอร์
                    $.ajax({
                        url: _base_url_ + "classes/Users.php?f=save_address",
                        method: 'POST',
                        data: {
                            address_id: address_id,
                            is_primary: 1
                        },
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.status == 'success') {
                                location.reload(); // รีโหลดหน้าเมื่อสำเร็จ
                            } else {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    resp.msg || 'เกิดข้อผิดพลาดในการตั้งที่อยู่หลัก.',
                                    'error'
                                );
                            }
                        },
                        error: function(err) {
                            console.log(err);
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'เกิดข้อผิดพลาดในการติดต่อกับเซิร์ฟเวอร์.',
                                'error'
                            );
                        }
                    });
                } else {
                    Swal.fire(
                        'ยกเลิกการตั้งที่อยู่หลัก',
                        'คุณได้ยกเลิกการตั้งที่อยู่หลักแล้ว.',
                        'info'
                    );
                }
            });
        });
    });
</script>