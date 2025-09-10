<?php require_once('./config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php'); ?>

<body class="hold-transition login-page">
    <script>
        start_loader();
    </script>
    <style>
        html,
        body {
            height: 100%;
            /* กำหนดความสูงเต็มหน้าจอ */
            margin: 0;
            /* ลบค่า margin ของทั้ง html และ body */
            padding: 0;
            /* ลบค่า padding */
        }

        body {
            background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
            background-size: cover;
            /* ปรับขนาดภาพให้ครอบคลุมทั้งหน้าจอ */
            background-repeat: no-repeat;
            /* ไม่ให้ภาพพื้นหลังซ้ำกัน */
            background-position: center center;
            /* ตั้งภาพให้อยู่กลางหน้าจอ */
            background-attachment: fixed;
            /* ทำให้ภาพพื้นหลังไม่เลื่อนตามการ scroll */
            min-height: 100vh;
            /* ให้ความสูงของ body เท่ากับความสูงของหน้าจอ */
            width: 100%;
            /* ความกว้างเต็มหน้าจอ */
        }

        #page-title {
            font-size: 32px;
            /* ขนาดฟอนต์ที่ใหญ่ขึ้น */
            font-weight: 700;
            /* เน้นความหนาของฟอนต์ */
            color: white;
            /* ตัวหนังสือสีขาว */
            -webkit-text-stroke: 1px #f57421;
            /* ขอบตัวหนังสือสีส้ม */
            text-transform: uppercase;
            /* ตัวพิมพ์ใหญ่ทั้งหมด */
            letter-spacing: 1px;
            /* การเพิ่มระยะห่างระหว่างตัวอักษร */
            text-align: center;
            /* จัดตำแหน่งข้อความให้อยู่กลาง */
            margin: 20px 0;
            /* กำหนดระยะห่างบนและล่าง */
            transition: all 0.3s ease;
            /* เอฟเฟกต์สำหรับการเคลื่อนไหว */
        }

        #page-title:hover {
            color: #f57421;
            -webkit-text-stroke: 1px #f57421;
        }

        .login-cart-header-bar {
            border-left: 4px solid #ff6600;
            padding: 16px 20px;
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
            display: inline-block;
        }

        .card-login {
            border-radius: 16px;
            /* หรือ 10px, 20px แล้วแต่ต้องการ */
        }

        .padding-top {
            margin-top: 0px;
            padding-top: 2rem;
        }

        /* CSS สำหรับ Logo */
        .logo {
            max-width: 100px;
            /* กำหนดขนาดความกว้างสูงสุด */
            height: auto;
            /* ให้ความสูงปรับตามอัตราส่วน */
            display: block;
            /* ใช้เพื่อให้โลโก้เป็นบล็อก */
            margin: 0 auto;
            /* จัดตำแหน่งให้อยู่กลาง */
        }

        /* สำหรับภาพโลโก้ในกรณีที่ต้องการใช้แบบเต็มหน้าจอ */
        .logo.full-width {
            max-width: 100%;
            height: auto;
        }

        @media (max-width: 767.98px) {
            .btn-login {
                width: 100%;
            }
        }
    </style>
    <div class="container d-flex justify-content-center align-items-center">
        <div class="col-lg-5 col-md-7 col-sm-10 col-12">
            <div class="card card-login card-navy my-3 shadow">
                <div class="card-body bg-color rounded-0 px-3">
                    <!--img src="<?php echo $_settings->info('logo') ?>" class="img-fluid logo">
                    <h1 class="text-center" id="page-title"><b><?php echo $_settings->info('name') ?></b></h1-->
                    <!--h3 class="text-dark mb-2 text-center pt-4">เข้าสู่ระบบ</h3-->
                    <div class="login-cart-header-bar">
                        <h3 class="mb-2"><i class="fa-solid fa-user-plus"></i> เข้าสู่ระบบ</h3>
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
        })
    </script>
</body>

</html>