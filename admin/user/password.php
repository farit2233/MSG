<?php
require_once('../../config.php');
// ตรวจสอบว่าเรามีการส่ง ID มาไหม (สำหรับฟังก์ชันเปลี่ยนรหัสผ่าน)
if (isset($_GET['id'])) {
    $user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}' ");
    foreach ($user->fetch_array() as $k => $v) {
        if (!is_numeric($k))
            $$k = $v;
    }
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
</style>

<div class="container-fluid password">
    <form id="change_password_form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="current_password" class="control-label">รหัสผ่านเดิม</label>
            <input type="password" class="form-control" name="current_password" required>
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
                <li id="uppercase" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีตัวอักษรพิมพ์ใหญ่ (A-Z)</li>
                <li id="number" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีตัวเลข (0-9)</li>
                <li id="special" class="invalid text-sm"><i class="fas fa-times-circle"></i> มีสัญลักษณ์พิเศษ (เช่น @, #, $, %)</li>
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
</div>

<script>
    $(document).ready(function() {
        var passwordInput = $('#new_password');
        var requirementsDiv = $('#password-requirements');
        var lengthReq = $('#length');
        var lowerReq = $('#lowercase');
        var numReq = $('#number');
        var upperReq = $('#uppercase');
        var specialReq = $('#special');

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

            // ตรวจสอบตัวพิมพ์ใหญ่
            if (password.match(/[A-Z]/)) {
                upperReq.removeClass('invalid').addClass('valid');
            } else {
                upperReq.removeClass('valid').addClass('invalid');
            }

            // ตรวจสอบสัญลักษณ์พิเศษ
            if (password.match(/[\W_]/)) {
                specialReq.removeClass('invalid').addClass('valid');
            } else {
                specialReq.removeClass('valid').addClass('invalid');
            }
        });

        // ซ่อนเมื่อไม่ได้โฟกัสและช่องว่าง
        passwordInput.on('blur', function() {
            if ($(this).val() === '') {
                requirementsDiv.slideUp('fast');
            }
        });

        // เมื่อ submit ฟอร์ม "เปลี่ยนรหัสผ่าน"
        $('#change_password_form').submit(function(e) {
            e.preventDefault();

            var newPassword = $('input[name="new_password"]').val();
            var confirmPassword = $('input[name="confirm_password"]').val();

            if (newPassword !== confirmPassword) {
                // เปลี่ยนจากการแสดง alert เป็นข้อความใต้ฟอร์ม
                Swal.fire('ข้อผิดพลาด', 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน', 'error');
                return;
            }

            start_loader();
            $.ajax({
                url: _base_url_ + 'classes/Users.php?f=user_manage_password&id=' + $('input[name="id"]').val(),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        Swal.fire('สำเร็จ', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        // แสดงข้อผิดพลาดในรูปแบบที่ต้องการ
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    end_loader();
                }
            });
        });
    });
</script>