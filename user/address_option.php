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

<input type="text" name="id" value="<?= isset($id) ? $id : '' ?>">
<div class="container">
    <h4>ที่อยู่ของคุณ</h4>
    <div class="card">
        <div class="card-body">
            <p><strong>ที่อยู่</strong> <?= isset($address) ? $address : '' ?>
                <strong>ตำบล</strong> <?= isset($sub_district) ? $sub_district : '' ?>
                <strong>อำเภอ</strong> <?= isset($district) ? $district : '' ?>
                <strong>จังหวัด</strong> <?= isset($province) ? $province : '' ?>
                <br><strong>รหัสไปรษณีย์</strong> <?= isset($postal_code) ? $postal_code : '' ?>
            </p>
        </div>
    </div>

    <!-- ปุ่มสำหรับเพิ่มที่อยู่ใหม่ -->
    <button class="btn btn-success mt-3" id="add_address_button">เพิ่มที่อยู่ใหม่</button>
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
        function showChangePasswordForm() {
            $('#change_password_form').show();
            $('#forgot_password_form').hide();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').show();
            $('.modal-footer #btn_forgot_password').hide();
            $('.modal-title').html('เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i>');
        }

        function showForgotPasswordForm() {
            $('#change_password_form').hide();
            $('#forgot_password_form').show();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').hide();
            $('.modal-footer #btn_forgot_password').show();
            $('.modal-title').html('ลืมรหัสผ่าน <i class="fa fa-pencil"></i>');
        }

        // --- Event Handlers ---

        // เมื่อคลิกลิงก์ "ลืมรหัสผ่าน?"
        $('#forgot_password_link').click(function(e) {
            e.preventDefault();
            showForgotPasswordForm();
        });

        // เมื่อคลิกลิงก์ "กลับไปหน้าเปลี่ยนรหัสผ่าน"
        $('#back_to_change_password').click(function(e) {
            e.preventDefault();
            showChangePasswordForm();
        });


        // เมื่อ submit ฟอร์ม "เปลี่ยนรหัสผ่าน"
        $('#change_password_form').submit(function(e) {
            e.preventDefault();

            var newPassword = $('#new_password').val();
            var confirmPassword = $('input[name="confirm_password"]').val();

            if (newPassword !== confirmPassword) {
                Swal.fire('ข้อผิดพลาด', 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน', 'error');
                return;
            }

            start_loader(); // แสดง loader (ถ้ามี)
            $.ajax({
                url: _base_url_ + 'classes/Users.php?f=password&id=' + $('input[name="id"]').val(),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        Swal.fire('สำเร็จ', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader(); // ซ่อน loader (ถ้ามี)
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    end_loader();
                }
            });
        });

        // เมื่อ submit ฟอร์ม "ลืมรหัสผ่าน"
        $('#forgot_password_form').submit(function(e) {
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=forgot_password",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        // ซ่อนฟอร์มและปุ่มทั้งหมด แล้วแสดงข้อความสำเร็จ
                        $('#change_password_form').hide();
                        $('#forgot_password_form').hide();
                        $('#reset_success_message').show();
                        $('.modal-footer #btn_change_password').hide();
                        $('.modal-footer #btn_forgot_password').hide();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถส่งคำขอรีเซ็ตรหัสผ่านได้', 'error');
                    end_loader();
                }
            });
        });
    });
</script>