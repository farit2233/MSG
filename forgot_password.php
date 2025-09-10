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

        #page-title {
            font-size: 32px;
            font-weight: 700;
            color: white;
            -webkit-text-stroke: 1px #f57421;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-align: center;
            margin: 20px 0;
            transition: all 0.3s ease;
        }

        #page-title:hover {
            color: #f57421;
            -webkit-text-stroke: 1px #f57421;
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

        .card-password {
            border-radius: 16px;
        }

        .padding-top {
            margin-top: 0;
            padding-top: 2rem;
        }

        .icon-success {
            color: #28a745;
            /* สีเขียวสำหรับติ๊กถูก */
            font-size: 60px;
            /* ขนาดไอคอนใหญ่ */
            margin-bottom: 20px;
            /* เว้นระยะห่างจากข้อความ */
        }

        .head-reset-password {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
    </style>

    <div class="container d-flex justify-content-center align-items-center">
        <div class="col-lg-5 col-md-7 col-sm-10 col-12">
            <div class="card card-password card-navy my-3 shadow">
                <div class="card-body bg-color rounded-0 px-3">
                    <h3 class="text-dark mb-2 text-center pt-4">ลืมรหัสผ่าน</h3>
                    <p class="text-muted small text-center">กรุณากรอกอีเมล และชื่อของคุณเพื่อขอรีเซ็ตรหัสผ่าน</p>
                    <hr>
                    <form id="forgot-password-form" action="reset_password.php" method="post">
                        <div class="form-group">
                            <label for="email" class="font-weight-bold small text-secondary">อีเมล</label>
                            <input type="email" id="email" class="form-control form-control-lg" name="email" autofocus placeholder="อีเมล" required>
                        </div>

                        <div class="form-group">
                            <label for="name" class="font-weight-bold small text-secondary">ชื่อ (ที่ใช้สมัคร)</label>
                            <input type="text" id="name" class="form-control form-control-lg" name="name" placeholder="กรุณากรอกชื่อ" required>
                        </div>

                        <div class="row justify-content-center">
                            <div class="col-md-9 col-12 text-center">
                                <button type="submit" class="btn btn-login btn-block rounded-pill">ส่งคำขอรีเซ็ตรหัสผ่าน</button>
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
                                    <p>กรุณารอการตอบกลับจากทีมงาน</p>
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
</body>

</html>