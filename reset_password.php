<?php
require_once('./config.php');

$token = isset($_GET['token']) ? $_GET['token'] : '';
$is_valid_token = false;
$error_message = '';

if (!empty($token)) {
    // ตรวจสอบ Token ในฐานข้อมูล
    $qry = $conn->query("SELECT * FROM `password_resets` WHERE token = '{$conn->real_escape_string($token)}'");
    if ($qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        $expires_at = strtotime($row['expires_at']);
        if ($expires_at > time()) {
            $is_valid_token = true;
        } else {
            $error_message = "ลิงก์รีเซ็ตรหัสผ่านหมดอายุแล้ว กรุณาขอใหม่";
        }
    } else {
        $error_message = "ลิงก์รีเซ็ตรหัสผ่านไม่ถูกต้อง";
    }
} else {
    $error_message = "ไม่พบ Token สำหรับการรีเซ็ตรหัสผ่าน";
}
?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php'); ?>

<body class="hold-transition login-page">
    <script>
        start_loader();
    </script>
    <style>
        /* (ใช้ CSS เดียวกับหน้า forgot_password.php) */
        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            background-attachment: fixed;
            min-height: 100vh;
            width: 100%;
        }

        .fgpw-cart-header-bar {
            padding-top: 16px;
            padding-bottom: 4px;
            padding-left: 16px;
            padding-right: 16px;
            margin-bottom: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .btn-login {
            background: none;
            color: #f57421;
            border: 2px solid #f57421;
            padding: 10px 10px;
            margin-top: 1rem;
            margin-bottom: 1rem;
            width: 100%;
        }

        .btn-login:hover {
            background-color: #f57421;
            color: white;
        }

        .card-password {
            border-radius: 16px;
        }

        #password-requirements .valid {
            color: #28a745;
        }

        #password-requirements .valid .fa-times-circle::before {
            content: "\f058";
            /* fa-check-circle */
        }
    </style>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="col-lg-5 col-md-7 col-sm-10 col-12">
            <div class="card card-password card-navy my-3 shadow">
                <div class="card-body bg-color rounded-0 px-3">
                    <?php if ($is_valid_token) : ?>
                        <div class="fgpw-cart-header-bar text-center">
                            <h3 class="mb-2">ตั้งรหัสผ่านใหม่</h3>
                            <p class="text-muted small">กรุณากรอกรหัสผ่านใหม่ของคุณ</p>
                        </div>
                        <form id="reset-password-form">
                            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                            <div class="form-group">
                                <label for="new_password" class="font-weight-bold small text-secondary">รหัสผ่านใหม่</label>
                                <input type="password" id="new_password" class="form-control form-control-lg" name="new_password" required pattern="(?=.*\d)(?=.*[a-z]).{8,}" title="รหัสผ่านต้องมีอย่างน้อย 8 ตัว, มีตัวพิมพ์เล็ก, และตัวเลข">
                            </div>
                            <div id="password-requirements" class="mb-2" style="display: none;">
                                <small>เงื่อนไขรหัสผ่าน:</small>
                                <ul class="list-unstyled text-danger text-sm">
                                    <li id="length" class="invalid"><i class="fas fa-times-circle"></i> มีความยาวอย่างน้อย 8 ตัวอักษร</li>
                                    <li id="lowercase" class="invalid"><i class="fas fa-times-circle"></i> มีตัวอักษรพิมพ์เล็ก (a-z)</li>
                                    <li id="number" class="invalid"><i class="fas fa-times-circle"></i> มีตัวเลข (0-9)</li>
                                </ul>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password" class="font-weight-bold small text-secondary">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" id="confirm_password" class="form-control form-control-lg" name="confirm_password" required>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-md-9 col-12 text-center">
                                    <button type="submit" class="btn btn-login btn-block rounded-pill">บันทึกรหัสผ่าน</button>
                                </div>
                            </div>
                        </form>
                    <?php else : ?>
                        <div class="text-center py-4">
                            <h4 class="text-danger">เกิดข้อผิดพลาด</h4>
                            <p><?= $error_message ?></p>
                            <a href="<?php echo base_url ?>login.php" class="btn btn-primary">กลับสู่หน้าเข้าสู่ระบบ</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            end_loader();

            // --- Password requirements UI ---
            var passwordInput = $('#new_password');
            var requirementsDiv = $('#password-requirements');
            passwordInput.on('focus', function() {
                requirementsDiv.slideDown('fast');
            });
            passwordInput.on('keyup', function() {
                var pw = $(this).val();
                // length
                if (pw.length >= 8) {
                    $('#length').removeClass('invalid').addClass('valid');
                } else {
                    $('#length').removeClass('valid').addClass('invalid');
                }
                // lowercase
                if (pw.match(/[a-z]/)) {
                    $('#lowercase').removeClass('invalid').addClass('valid');
                } else {
                    $('#lowercase').removeClass('valid').addClass('invalid');
                }
                // number
                if (pw.match(/\d/)) {
                    $('#number').removeClass('invalid').addClass('valid');
                } else {
                    $('#number').removeClass('valid').addClass('invalid');
                }
            });
            passwordInput.on('blur', function() {
                if ($(this).val() === '') {
                    requirementsDiv.slideUp('fast');
                }
            });


            // --- Form Submission ---
            $('#reset-password-form').submit(function(e) {
                e.preventDefault();
                var _this = $(this);

                // --- Client-side validation ---
                if ($('#new_password').val() !== $('#confirm_password').val()) {
                    Swal.fire('ข้อผิดพลาด', 'รหัสผ่านใหม่และการยืนยันรหัสผ่านไม่ตรงกัน', 'error');
                    return false;
                }

                start_loader();
                $.ajax({
                    url: _base_url_ + "classes/Users.php?f=reset_password_with_token",
                    method: 'POST',
                    data: new FormData($(this)[0]),
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    error: function(err) {
                        console.log(err);
                        Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                        end_loader();
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            Swal.fire('สำเร็จ', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success').then(() => {
                                location.href = _base_url_ + 'login.php';
                            });
                        } else {
                            Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                        }
                        end_loader();
                    }
                });
            });
        });
    </script>
</body>

</html>