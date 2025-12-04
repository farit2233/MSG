<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php'); ?>

<body class="hold-transition body-login">
    <?php require_once('inc/topbarnav.php'); ?>
    <script>
        start_loader();
    </script>

    <div class="container d-flex justify-content-center align-items-center pb-5">
        <div class="col-lg-5 col-md-7 col-sm-10 col-12">
            <div class="card card-password card-navy my-3 shadow">
                <div class="card-body bg-color rounded-0 px-3">
                    <div class="fgpw-cart-header-bar text-center">
                        <h3 class="mb-2">ลืมรหัสผ่าน</h3>
                        <p class="text-muted small">กรุณากรอกอีเมลของคุณเพื่อขอรีเซ็ตรหัสผ่าน</p>
                    </div>
                    <form id="forgot-password-form" action="reset_password.php" method="post">
                        <div class="form-group">
                            <label for="email" class="font-weight-bold small text-secondary">อีเมล</label>
                            <input type="email" id="email" class="form-control form-control-lg" name="email" autofocus placeholder="อีเมล" required>
                        </div>
                        <div class="row justify-content-center">
                            <div class="col-md-9 col-12 text-center">
                                <button type="submit" class="btn btn-login btn-block rounded-pill">รีเซ็ตรหัสผ่าน</button>
                            </div>
                        </div>

                        <div class="col-12 text-left mt-3">
                            <p><a href="<?php echo base_url ?>login.php"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าเข้าสู่ระบบ</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            end_loader();
            $('#forgot-password-form').submit(function(e) {
                e.preventDefault();
                var _this = $(this);
                var el = $('<div>');
                el.addClass('alert alert-danger err_msg');
                el.hide();
                $('.err_msg').remove();

                if (_this[0].checkValidity() == false) {
                    _this[0].reportValidity();
                    return false;
                }

                start_loader();
                $.ajax({
                    url: _base_url_ + "classes/Users.php?f=forgot_password", // เรียกฟังก์ชัน forgot_password
                    method: 'POST',
                    data: new FormData($(this)[0]),
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    error: function(err) {
                        console.log(err);
                        alert('ไม่สามารถส่งคำขอรีเซ็ตรหัสผ่านได้');
                        end_loader();
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            // เปลี่ยนเนื้อหาหลังจากส่งคำขอเรียบร้อย
                            $('.card-body').html(`
                               <div class="text-center">
                                    <i class="fa-solid fa-check-circle icon-success pt-4"></i>
                                    <h3 class="text-success head-reset-password">คำขอรีเซ็ตรหัสผ่านของคุณ<br>ถูกส่งเรียบร้อยแล้ว</h3>
                                    <p>กรุณารอการตอบกลับที่อีเมล</p>
                                    <p><a href="<?php echo base_url ?>login.php"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าเข้าสู่ระบบ</a></p>
                                </div>
                            `);
                        } else {
                            el.html(resp.msg);
                            el.show('slow');
                            _this.prepend(el);
                            $('html, body').scrollTop(0);
                        }
                        end_loader();
                    }
                });
            });
        });
    </script>
    <?php require_once('inc/footer_login.php'); ?>
</body>

</html>