<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('../inc/header.php') ?>

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
          <form id="login-frm" action="" method="post">
            <div class="input-group mb-3">
              <input type="text" class="form-control" name="username" autofocus placeholder="Username">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-user"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control" name="password" placeholder="Password">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>
            <!-- /.col -->
            <div class="row justify-content-center">
              <div class="col-md-9 col-12 text-center">
                <button type="submit" class="btn btn-login btn-block  rounded-pill">เข้าสู่ระบบ</button>
              </div>
            </div>
            <div class="col-12 text-left mt-3">
              <p><a href="<?php echo base_url ?>./"><i class="fa-solid fa-arrow-left"></i> กลับสู่หน้าหลัก</a></p>
            </div>
            <!-- /.col -->
        </div>
        </form>
        <!-- /.social-auth-links -->

        <!-- <p class="mb-1">
        <a href="forgot-password.html">I forgot my password</a>
      </p> -->

      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

  <script>
    $(document).ready(function() {
      end_loader();
    })
  </script>
</body>

</html>