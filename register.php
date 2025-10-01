<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">

<head>
  <?php require_once('inc/header.php') ?>
</head>

<script>
  start_loader()
</script>
<style>
  .register {
    background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
  }
</style>

<body class="register">
  <section class="pb-5">
    <div class="container">
      <div class="row mt-n4 justify-content-center align-items-center flex-column">
        <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
          <div class="card card-register shadow " style="margin-top: 3rem;">
            <div class="card-body">
              <div class="container-fluid">
                <div class="register-cart-header-bar">
                  <h3 class="mb-0"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิก</h3>
                </div>
                <form id="register-form" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id">

                  <div class="register-section-title-with-line  mb-4">
                    <h4>โปรไฟล์</h4>
                  </div>
                  <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group d-flex justify-content-center mt-2">
                        <img src="uploads/customers/default_user.png" alt="Avatar Preview" id="cimg" class="img-fluid">
                      </div>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/png, image/jpeg">
                        <label class="custom-file-label register-img" for="customFile">เลือกรูปจากเครื่อง</label>
                      </div>
                      <input type="hidden" name="cropped_image" id="cropped_image">
                    </div>
                  </div>

                  <div class="register-section-title-with-line mb-4 mt-4">
                    <h4>ข้อมูลส่วนตัว</h4>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="firstname" class="control-label">ชื่อ</label>
                        <input type="text" class="form-control" required name="firstname" id="firstname"
                          title="กรุณาใส่ชื่อผู้ใช้งาน">
                      </div>
                      <div class="form-group">
                        <label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
                        <input type="text" class="form-control" name="middlename" id="middlename">
                      </div>
                      <div class="form-group">
                        <label for="lastname" class="control-label">นามสกุล</label>
                        <input type="text" class="form-control" required name="lastname" id="lastname"
                          title="กรุณาใส่นามสกุลผู้ใช้งาน">
                      </div>
                      <div class="form-group">
                        <label for="gender" class="control-label">เพศ</label>
                        <select class="custom-select custom-select" required name="gender" id="gender">
                          <option value="Male">ชาย</option>
                          <option value="Female">หญิง</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="contact" class="control-label">เบอร์โทร</label>
                        <input type="text" class="form-control" required name="contact" id="contact"
                          maxlength="15"
                          pattern="\d{10,}"
                          title="กรุณาใส่ตัวเลขอย่างน้อย 10 ตัว">
                      </div>
                      <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" class="form-control" required name="email" id="email"
                          title="กรุณาใส่อีเมล">
                      </div>
                      <div class="form-group">
                        <label for="password" class="control-label">รหัสผ่าน</label>
                        <input type="password" class="form-control" required name="password" id="password"
                          pattern="(?=.*\d)(?=.*[a-z]).{8,}"
                          title="รหัสผ่านต้องมีอย่างน้อย 8 ตัว, มีตัวพิมพ์เล็ก, และตัวเลข">
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
                        <label for="cpassword" class="control-label">ยืนยันรหัสผ่าน</label>
                        <input type="password" class="form-control" required id="cpassword"
                          title="ยืนยันรหัสผ่าน">
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
                    </div>
                  </div>

                  <!--div class="register-section-title-with-line mb-4">
                    <h4>ที่อยู่จัดส่ง</h4>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                      <div class="form-group">
                        <label for="address" class="control-label">บ้านเลขที่ ถนน</label>
                        <input type="text" class="form-control" name="address" id="address">
                      </div>

                      <div class="form-group">
                        <label for="district" class="control-label">อำเภอ</label>
                        <input type="text" class="form-control" name="district" id="district">
                      </div>
                      <div class="form-group">
                        <label for="postal_code" class="control-label">รหัสไปรษณีย์</label>
                        <input type="text" class="form-control" name="postal_code" id="postal_code">
                      </div>


                    </div>

                  <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">

                    <div class="form-group">
                      <label for="sub_district" class="control-label">ตำบล/เขต</label>
                      <input type="text" class="form-control" name="sub_district" id="sub_district">
                    </div>
                    <div class="form-group">
                      <label for="province" class="control-label">จังหวัด</label>
                      <input type="text" class="form-control" name="province" id="province">
                    </div>

                  </div-->

              </div>

              <div class="row justify-content-center">
                <div class="col-md-5 col-12 text-center">
                  <button type="submit" class="btn btn-regis btn-block rounded-pill">สร้างบัญชี</button>
                </div>
              </div>
              <div class="col-12 text-center mt-3">
                <p>มีบัญชีอยู่แล้ว? <a href="./login.php">เข้าสู่ระบบที่นี่</a></p>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>

    <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-dialog-register" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalLabel"><i class="fas fa-crop-alt"></i> ปรับแต่งรูปโปรไฟล์</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <i class="fa fa-times"></i>
            </button>
          </div>
          <div class="modal-body">
            <div class="img-container">
              <img id="image_to_crop" src="">
            </div>
            <div class="zoom-controls">
              <i class="fas fa-search-minus"></i>
              <input type="range" class="form-control-range" id="zoom_slider" min="0.1" max="2" step="0.01">
              <i class="fas fa-search-plus"></i>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
            <button type="button" class="btn btn-primary" id="crop_button">บันทึก</button>
          </div>
        </div>
      </div>
    </div>
  </section>

  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>
  <script src="<?php echo base_url ?>plugins/cropper.js/cropper.min.js"></script>

  <script>
    $(document).ready(function() {

      var passwordInput = $('#password');
      var requirementsDiv = $('#password-requirements');

      var lengthReq = $('#length');
      var lowerReq = $('#lowercase');
      var numReq = $('#number');

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
      });

      // ซ่อนเมื่อไม่ได้โฟกัสและช่องว่าง
      passwordInput.on('blur', function() {
        if ($(this).val() === '') {
          requirementsDiv.slideUp('fast');
        }
      });

      // ดักทุก input, select ที่มี required
      $('#register-form [required]').each(function() {
        var $input = $(this);

        // เมื่อ input invalid
        $input.on('invalid', function(e) {
          e.preventDefault(); // ป้องกัน default browser
          $input.next('.err-msg').remove(); // ลบ error เก่า (ถ้ามี)
          // ถ้ายังไม่มี error message ให้สร้าง
          if ($input.next('.err-msg').length === 0) {
            $input.after('<div class="err-msg text-danger mt-1">' + $input.attr('title') + '</div>');
          }
        });

        // เมื่อกรอกหรือแก้ไข input ลบข้อความ error
        $input.on('input change', function() {
          $input.next('.err-msg').remove();
        });
      });
    });

    const contactInput = document.getElementById('contact');
    contactInput.addEventListener('input', function() {
      this.value = this.value.replace(/\D/g, ''); // ลบทุกตัวที่ไม่ใช่เลข
    });

    $(document).ready(function() {
      end_loader();

      $('#register-form').submit(function(e) {
        e.preventDefault();
        var _this = $(this);

        $('.err-msg').remove();

        if ($('#password').val() != $('#cpassword').val()) {
          $('#cpassword').after('<div class="err-msg text-danger mt-1">รหัสผ่านไม่ตรงกัน</div>');
          $('html, body').animate({
            scrollTop: $('#cpassword').offset().top - 100
          }, 'slow');
          return false;
        }

        if (_this[0].checkValidity() == false) {
          _this[0].reportValidity();
          return false;
        }


        start_loader();
        $.ajax({
          url: _base_url_ + "classes/Users.php?f=registration",
          method: 'POST',
          data: new FormData($(this)[0]),
          dataType: 'json',
          cache: false,
          contentType: false,
          processData: false,
          error: err => {
            console.log(err);
            end_loader();
          },
          success: function(resp) {
            if (resp.status == 'success') {
              location.href = ('./index.php');
              // --- START: แก้ไขส่วนนี้ ---
            } else if (!!resp.msg) {
              // หาก Server ตอบกลับมาว่ามี Error (ซึ่งในฟอร์มนี้คืออีเมลซ้ำ)
              // ให้แสดงข้อความใต้ช่องอีเมลทันที โดยไม่ใช้ alert
              $('#email').after('<div class="err-msg text-danger mt-1">อีเมลนี้ถูกใช้แล้ว</div>');
              $('html, body').animate({
                scrollTop: $('#email').offset().top - 100
              }, 'slow');
            } else {
              // กรณีเกิด Error อื่นๆ ที่ไม่คาดคิด ให้แสดงใน console แทน alert
              console.error("An unknown registration error occurred.", resp);
            }
            // --- END: แก้ไขส่วนนี้ ---
            end_loader();
          }
        });
      });

      // === ส่วนของ Cropper.js (เหมือนเดิม) ===
      var $modal = $('#cropModal');
      var image = document.getElementById('image_to_crop');
      var cropper;
      var zoomSlider = document.getElementById('zoom_slider');

      $('#customFile').on('change', function(e) {
        var files = e.target.files;
        if (files && files.length > 0) {
          var reader = new FileReader();
          reader.onload = function(event) {
            image.src = event.target.result;
            $modal.modal({
              backdrop: false,
              keyboard: false,
              show: true
            });
          };
          reader.readAsDataURL(files[0]);
          var fileName = $(this).val().split('\\').pop();
          $(this).next('.custom-file-label').html(fileName);
        }
      });

      $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
          aspectRatio: 1,
          viewMode: 1,
          dragMode: 'move',
          cropBoxMovable: false,
          cropBoxResizable: false,
          wheelZoomRatio: 0,
          background: false,
          responsive: true,
          autoCropArea: 1,
          ready: function() {
            let canvasData = cropper.getCanvasData();
            let initialZoom = canvasData.width / canvasData.naturalWidth;
            zoomSlider.min = initialZoom;
            zoomSlider.max = initialZoom * 3;
            zoomSlider.value = initialZoom;
          }
        });
      }).on('hidden.bs.modal', function() {
        cropper.destroy();
        cropper = null;
        $('#customFile').val('');
        $('#customFile').next('.custom-file-label').html('เลือกรูปจากเครื่อง');
      });

      zoomSlider.addEventListener('input', function() {
        if (cropper) {
          cropper.zoomTo(this.value);
        }
      });

      $('#crop_button').on('click', function() {
        var canvas = cropper.getCroppedCanvas({
          width: 400,
          height: 400,
          imageSmoothingQuality: 'high',
        });
        var base64data = canvas.toDataURL('image/jpeg');
        $('#cimg').attr('src', base64data);
        $('#cropped_image').val(base64data);
        $modal.modal('hide');
      });
    });
  </script>
</body>

</html>