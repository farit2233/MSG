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
            <div class="card card-login card-navy my-3 shadow">
                <div class="card-body bg-color rounded-0 px-3">
                    <!--img src="<?php echo $_settings->info('logo') ?>" class="img-fluid logo">
                    <h1 class="text-center" id="page-title"><b><?php echo $_settings->info('name') ?></b></h1-->
                    <!--h3 class="text-dark mb-2 text-center pt-4">เข้าสู่ระบบ</h3-->
                    <div class="login-cart-header-bar text-center">
                        <h3 class="mb-2">เข้าสู่ระบบ</h3>
                        <p class="text-muted small">กรุณาเข้าสู่ระบบบัญชีของคุณเพื่อดำเนินการต่อ</p>
                    </div>
                    <form id="login-form" action="" method="post">

                        <div class="form-group">
                            <label for="email" class="font-weight-bold small text-secondary">อีเมล</label>
                            <input type="email" id="email" class="form-control form-control-lg" name="email" autofocus placeholder="อีเมล" required>
                        </div>
                        <div class="form-group">
                            <label for="login-password" class="font-weight-bold small text-secondary">รหัสผ่าน</label>
                            <input type="password" class="form-control form-control-lg" name="password" placeholder="รหัสผ่าน">
                            <div class="text-right">
                                <small><a href="<?php echo base_url ?>forgot_password.php">ลืมรหัสผ่าน?</a></small>
                            </div>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-9 col-12 text-center">
                                <button type="submit" class="btn btn-login btn-block rounded-pill">เข้าสู่ระบบ</button>
                            </div>
                        </div>
                        <div class="col-12 text-center mt-3">
                            <p>ยังไม่มีบัญชี?<a href="<?php echo base_url ?>register.php"> สมัครสมาชิกเลย</a></p>

                        </div>
                        <div class="col-12 text-left mt-3">
                            <p><a href="<?php echo base_url ?>./"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าหลัก</a></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            end_loader();
            $('#login-form').submit(function(e) {
                e.preventDefault()
                var _this = $(this)
                var el = $('<div>')
                el.addClass('alert alert-danger err_msg')
                el.hide()
                $('.err_msg').remove()
                if ($('#password').val() != $('#cpassword').val()) {
                    el.text('รหัสผ่านไม่ถูกต้อง')
                    _this.prepend(el)
                    el.show('slow')
                    $('html, body').scrollTop(0)
                    return false;
                }
                if (_this[0].checkValidity() == false) {
                    _this[0].reportValidity();
                    return false;
                }
                start_loader()
                $.ajax({
                    url: _base_url_ + "classes/Login.php?f=login_customer",
                    method: 'POST',
                    type: 'POST',
                    data: new FormData($(this)[0]),
                    dataType: 'json',
                    cache: false,
                    processData: false,
                    contentType: false,
                    error: err => {
                        console.log(err)
                        alert('เข้าสู่ระบบไม่สำเร็จ')
                        end_loader()
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            location.href = ('./')
                        } else if (!!resp.msg) {
                            el.html(resp.msg)
                            el.show('slow')
                            _this.prepend(el)
                            $('html, body').scrollTop(0)
                        } else {
                            alert('เข้าสู่ระบบไม่สำเร็จ')
                            console.log(resp)
                        }
                        end_loader()
                    }
                })
            })
        });
    </script>
    <?php require_once('inc/footer_login.php'); ?>
</body>

</html>