<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>
<script>
  start_loader()
</script>
<style>
  body {
    background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
    background-size: cover;
    background-repeat: no-repeat;
    backdrop-filter: contrast(1);
    overflow-x: hidden;
  }

  .section-title-with-line h3 {
    position: relative;
    border-bottom: 2px solid #f57421 !important;
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
  }

  .section-title-with-line h3::after {
    content: none !important;
  }

  .cart-header-bar {
    border-left: 4px solid #ff6600;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 24px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  }

  .card {
    border-radius: 13px;
    max-width: 767.98px;
    margin: auto;
    width: 100%;
  }

  #page-title {
    text-shadow: 6px 4px 7px black;
    font-size: 3.5em;
    color: #fff4f4 !important;
    background: #8080801c;
  }

  img#cimg {
    height: 10em;
    width: 10em;
    object-fit: cover;
    border-radius: 100%;
  }

  label {
    font-size: 18px;
  }

  input.form-control {
    border-radius: 13px;
    font-size: 16px;
  }

  .bg-color-Regis {
    background-color: #16542b;
  }

  .custom-input {
    border-radius: 13px;
    font-size: 16px;
  }

  .btn-regis {
    background: none;
    color: #f57421;
    border: 2px solid #f57421;
    padding: 8px 30px;
    margin-top: 1rem;
    margin-bottom: 1rem;
    font-size: 14px;
  }

  .btn-regis:hover {
    background-color: #f57421;
    color: white;
    display: inline-block;
  }

  @media (max-width: 767.98px) {
    .btn-regis {
      width: 100%;
    }
  }
</style>

<body>
  <section class="py-3">
    <div class="container">
      <div class="row mt-n4 justify-content-center align-items-center flex-column">
        <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
          <div class="card shadow" style="margin-top: 3rem;">
            <div class="card-body">
              <div class="container-fluid">
                <div class="cart-header-bar">
                  <h3 class="mb-0"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิก</h3>
                </div>
                <form id="register-form" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id">

                  <div class="section-title-with-line mb-4">
                    <h3>โปรไฟล์</h3>
                  </div>
                  <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group d-flex justify-content-center mt-2">
                        <img src="<?php echo validate_image('') ?>" alt="" id="cimg"
                          class="img-fluid img-thumbnail" style="max-width: 200px;">
                      </div>
                      <div class="custom-file custom-input">
                        <input type="file" class="custom-file-input custom-input" id="customFile" name="img"
                          onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
                        <label class="custom-file-label custom-input" for="customFile">เลือกรูปจากไฟล์ในเครื่อง</label>
                      </div>
                    </div>
                  </div>

                  <div class="section-title-with-line mb-4">
                    <h3>ข้อมูลส่วนตัว</h3>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="firstname" class="control-label">ชื่อ</label>
                        <input type="text" class="form-control form-control-sm" required name="firstname" id="firstname">
                      </div>
                      <div class="form-group">
                        <label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
                        <input type="text" class="form-control form-control-sm" name="middlename" id="middlename">
                      </div>
                      <div class="form-group">
                        <label for="lastname" class="control-label">นามสกุล</label>
                        <input type="text" class="form-control form-control-sm" required name="lastname" id="lastname">
                      </div>
                      <div class="form-group">
                        <label for="gender" class="control-label">เพศ</label>
                        <select class="custom-select custom-select-sm custom-input" required name="gender" id="gender">
                          <option value="Male">ชาย</option>
                          <option value="Female">หญิง</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" class="form-control form-control-sm" required name="email" id="email">
                      </div>
                      <div class="form-group">
                        <label for="contact" class="control-label">เบอร์โทร</label>
                        <input type="text" class="form-control form-control-sm" required name="contact" id="contact">
                      </div>
                      <div class="form-group">
                        <label for="password" class="control-label">รหัสผ่าน</label>
                        <input type="password" class="form-control form-control-sm" required name="password" id="password">
                      </div>
                      <div class="form-group">
                        <label for="cpassword" class="control-label">ยืนยัน รหัสผ่าน</label>
                        <input type="password" class="form-control form-control-sm" required id="cpassword">
                      </div>
                    </div>
                  </div>

                  <div class="section-title-with-line mb-4">
                    <h3>ที่อยู่จัดส่ง</h3>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="address" class="control-label">บ้านเลขที่ ถนน</label>
                        <input type="text" class="form-control form-control-sm" name="address" id="address">
                      </div>
                      <div class="form-group">
                        <label for="sub_district" class="control-label">ตำบล</label>
                        <input type="text" class="form-control form-control-sm" name="sub_district" id="sub_district">
                      </div>
                      <div class="form-group">
                        <label for="district" class="control-label">อำเภอ</label>
                        <input type="text" class="form-control form-control-sm" name="district" id="district">
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="province" class="control-label">จังหวัด</label>
                        <input type="text" class="form-control form-control-sm" name="province" id="province">
                      </div>
                      <div class="form-group">
                        <label for="postal_code" class="control-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control form-control-sm" name="postal_code" id="postal_code">
                      </div>
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-md-8 col-12 text-md-left text-center mb-2 mb-md-0">
                      <p>มีบัญชีอยู่แล้ว? <a href="./login.php">เข้าสู่ระบบที่นี่</a></p>
                    </div>
                    <div class="col-md-4 col-12 text-md-right text-center">
                      <button type="submit" class="btn btn-regis btn-block rounded-pill">สร้างบัญชี</button>
                    </div>
                  </div>
                </form>
              </div>
            </div> <!-- /.card-body -->
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- jQuery -->
  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

  <script>
    function displayImg(input, _this) {
      if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
          $('#cimg').attr('src', e.target.result);
        }

        reader.readAsDataURL(input.files[0]);
      } else {
        $('#cimg').attr('src', "<?php echo validate_image('') ?>");
      }
    }
    $(document).ready(function() {
      end_loader();
      $('.pass_view').click(function() {
        var input = $(this).siblings('input')
        var type = input.attr('type')
        if (type == 'password') {
          $(this).html('<i class="fa fa-eye"></i>')
          input.attr('type', 'text').focus()
        } else {
          $(this).html('<i class="fa fa-eye-slash"></i>')
          input.attr('type', 'password').focus()
        }
      })
      $('#register-form').submit(function(e) {
        e.preventDefault()
        var _this = $(this)
        var el = $('<div>')
        el.addClass('alert alert-dark err_msg')
        el.hide()
        $('.err_msg').remove()
        if ($('#password').val() != $('#cpassword').val()) {
          el.text('Password does not match')
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
          url: _base_url_ + "classes/Users.php?f=registration",
          method: 'POST',
          type: 'POST',
          data: new FormData($(this)[0]),
          dataType: 'json',
          cache: false,
          processData: false,
          contentType: false,
          error: err => {
            console.log(err)
            alert('An error occurred')
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
              alert('An error occurred')
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