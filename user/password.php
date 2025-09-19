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
    <form id="change_password_form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="current_password" class="control-label">รหัสผ่านเดิม</label>
            <input type="password" class="form-control" name="current_password" required>
            <div class="text-right">
                <small><a href="#" id="forgot_password_link">ลืมรหัสผ่าน?</a></small>
            </div>
        </div>
        <div class="form-group">
            <label for="new_password" class="control-label">รหัสผ่านใหม่</label>
            <input type="password" class="form-control" name="new_password" required id="new_password"
                pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                title="รหัสผ่านต้องมีอย่างน้อย 8 ตัว, มีตัวพิมพ์เล็ก, และตัวเลข">
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" class="form-control" name="confirm_password" required id="confirm_password">
        </div>
        <div id="password-requirements" class="mb-2" style="display: none;">
            <small>เงื่อนไขรหัสผ่าน:</small>
            <ul class="list-unstyled text-danger text-sm">
                <li id="length" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีความยาวอย่างน้อย 8 ตัวอักษร</li>
                <li id="lowercase" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีตัวอักษรพิมพ์เล็ก (a-z)</li>
                <li id="number" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีตัวเลข (0-9)</li>
            </ul>
        </div>
        <style>
            #password-requirements .valid {
                color: #28a745;
            }

            #password-requirements .valid .fa-times-circle::before {
                content: "\f058";
                /* fa-check-circle */
            }
        </style>
    </form>

    <form id="forgot_password_form" style="display: none;">
        <div class="form-group">
            <label for="email" class="control-label">อีเมล</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="text-right">
            <small><a href="#" id="back_to_change_password">กลับไปหน้าเปลี่ยนรหัสผ่าน</a></small>
        </div>
    </form>

    <div id="reset_success_message" style="display: none;" class="text-center">
        <i class="fa-solid fa-check-circle icon-success pt-4"></i>
        <h4 class="text-success">คำขอรีเซ็ตรหัสผ่านของคุณ <br>ถูกส่งเรียบร้อยแล้ว</h4>
        <p>กรุณารอการตอบกลับที่อีเมล</p>
    </div>
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