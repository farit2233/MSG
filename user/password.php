<?php
require_once('../config.php');
// ตรวจสอบว่าเรามีการส่ง ID มาไหม (สำหรับฟังก์ชันเปลี่ยนรหัสผ่าน)
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $id = $row['id'];
        // เราไม่จำเป็นต้องใช้ $email ในหน้านี้ แต่ดึงมาเผื่อไว้ได้
        // $email = $row['email'];
    } else {
        echo "<script>
            alert('ไม่พบผู้ใช้');
            location.replace('./');
        </script>";
        exit;
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
            <div class="text-right">
                <small><a href="#" id="forgot_password_link">ลืมรหัสผ่าน?</a></small>
            </div>
        </div>
        <div class="form-group">
            <label for="new_password" class="control-label">รหัสผ่านใหม่</label>
            <input type="password" class="form-control" name="new_password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password" class="control-label">ยืนยันรหัสผ่านใหม่</label>
            <input type="password" class="form-control" name="confirm_password" required>
        </div>
    </form>

    <form id="forgot_password_form" style="display: none;">
        <div class="form-group">
            <label for="email" class="control-label">อีเมล</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">ชื่อ (ที่ใช้สมัคร)</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="text-right">
            <small><a href="#" id="back_to_change_password">กลับไปหน้าเปลี่ยนรหัสผ่าน</a></small>
        </div>
    </form>

    <div id="reset_success_message" style="display: none;" class="text-center">
        <h4 class="text-success">ส่งคำขอสำเร็จ</h4>
        <p>ระบบได้ส่งลิงก์สำหรับตั้งรหัสผ่านใหม่ไปยังอีเมลของคุณแล้ว กรุณาตรวจสอบ</p>
    </div>
</div>

<script>
    $(document).ready(function() {

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
                url: _base_url_ + 'classes/Users.php?f=password',
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