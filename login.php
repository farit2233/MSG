<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>

<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
  <style>
    body {
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size: cover;
      background-repeat: no-repeat;
      backdrop-filter: contrast(1);
    }

    #page-title {
      text-shadow: 6px 4px 7px black;
      font-size: 3.5em;
      color: white !important;
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

    .card {
      border-radius: 1rem;
      /* หรือ 10px, 20px แล้วแต่ต้องการ */
    }

    .padding-top {
      margin-top: 0;
      padding-top: 4rem;
    }

    @media (max-width: 767.98px) {
      .btn-login {
        width: 100%;
      }
    }
  </style>
  <h1 class="text-center text-white px-4 padding-top" id="page-title"><b><?php echo $_settings->info('name') ?></b></h1>
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 60vh;">
    <div class="col-lg-5 col-md-7 col-sm-10 col-12">
      <div class="card card-navy my-3 shadow">
        <div class="card-body bg-color rounded-0 px-3">
          <h4 class="text-center mb-4">เข้าสู่ระบบ</h4>
          <form id="ulogin-form" action="" method="post">
            <div class="input-group mb-3">
              <input type="email" class="form-control" name="email" autofocus placeholder="Email">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-4">
              <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <div class="row justify-content-center">
              <div class="col-md-9 col-12 text-center">
                <button type="submit" class="btn btn-login btn-block  rounded-pill">เข้าสู่ระบบ</button>
              </div>
            </div>
            <div class="col-12 text-center mt-3">
              <p>ยังไม่มีบัญชี?<a href="<?php echo base_url ?>register.php"> สมัครสมาชิก</a></p>

            </div>
            <div class="col-12 text-left mt-3">
              <p><a href="<?php echo base_url ?>./"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าหลัก</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>


  <!-- jQuery -->
  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

  <script>
    $(document).ready(function() {
      end_loader();
      $('#ulogin-form').submit(function(e) {
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