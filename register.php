<?php require_once('./config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">

<head>
  <?php require_once('inc/header.php') ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.12/cropper.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
          <div class="card register-card shadow " style="margin-top: 3rem;">
            <div class="card-body">
              <div class="container-fluid">
                <div class="register-cart-header-bar">
                  <h3 class="mb-0"><i class="fa-solid fa-user-plus"></i> สมัครสมาชิก</h3>
                </div>
                <form id="register-form" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="id">

                  <div class="register-section-title-with-line  mb-4">
                    <h3>โปรไฟล์</h3>
                  </div>
                  <div class="row justify-content-center">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group d-flex justify-content-center mt-2">
                        <img src="uploads/customers/default_user.png" alt="Avatar Preview" id="cimg" class="img-fluid">
                      </div>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/png, image/jpeg">
                        <label class="custom-file-label register-img" for="customFile">เลือกรูปโปรไฟล์</label>
                      </div>
                      <input type="hidden" name="cropped_image" id="cropped_image">
                    </div>
                  </div>

                  <div class="register-section-title-with-line mb-4 mt-4">
                    <h3>ข้อมูลส่วนตัว</h3>
                  </div>
                  <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="firstname" class="control-label">ชื่อ</label>
                        <input type="text" class="form-control form-control-sm" required name="firstname" id="firstname"
                          title="กรุณาใส่ชื่อตามจริง เพื่อที่จะสามารถจัดส่งได้ง่าย">
                      </div>
                      <div class="form-group">
                        <label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
                        <input type="text" class="form-control form-control-sm" name="middlename" id="middlename">
                      </div>
                      <div class="form-group">
                        <label for="lastname" class="control-label">นามสกุล</label>
                        <input type="text" class="form-control form-control-sm" required name="lastname" id="lastname"
                          title="กรุณาใส่นามสกุลตามจริง เพื่อที่จะสามารถจัดส่งได้ง่าย">
                      </div>
                      <div class="form-group">
                        <label for="gender" class="control-label">เพศ</label>
                        <select class="custom-select custom-select-sm" required name="gender" id="gender">
                          <option value="Male">ชาย</option>
                          <option value="Female">หญิง</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                      <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" class="form-control form-control-sm" required name="email" id="email"
                          title="กรุณาใส่อีเมล">
                      </div>
                      <div class="form-group">
                        <label for="contact" class="control-label">เบอร์โทร</label>
                        <input type="text" class="form-control form-control-sm" required name="contact" id="contact"
                          maxlength="15"
                          pattern="\d{10,}"
                          title="กรุณาใส่ตัวเลขอย่างน้อย 10 ตัว">
                      </div>
                      <div class="form-group">
                        <label for="password" class="control-label">รหัสผ่าน</label>
                        <input type="password" class="form-control form-control-sm" required name="password" id="password"
                          title="กรุณาใส่รหัสผ่าน">
                      </div>
                      <div class="form-group">
                        <label for="cpassword" class="control-label">ยืนยัน รหัสผ่าน</label>
                        <input type="password" class="form-control form-control-sm" required id="cpassword"
                          title="ยืนยันรหัสผ่าน">
                      </div>
                    </div>
                  </div>

                  <div class="register-section-title-with-line mb-4">
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
      // ดักทุก input, select ที่มี required
      $('#register-form [required]').each(function() {
        var $input = $(this);

        // เมื่อ input invalid
        $input.on('invalid', function(e) {
          e.preventDefault(); // ป้องกัน default browser
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

      // === ส่วนของ Form Submit และอื่นๆ (เหมือนเดิม) ===
      $('#register-form').submit(function(e) {
        e.preventDefault();
        var _this = $(this);
        var el = $('<div>');
        el.addClass('alert alert-danger err_msg');
        el.hide();
        $('.err_msg').remove();

        if ($('#password').val() != $('#cpassword').val()) {
          el.text('รหัสผ่านไม่ตรงกัน');
          _this.prepend(el);
          el.show('slow');
          $('html, body').scrollTop(0);
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
            alert('An error occurred');
            end_loader();
          },
          success: function(resp) {
            if (resp.status == 'success') {
              location.href = ('./login.php');
            } else if (!!resp.msg) {
              el.html(resp.msg);
              el.show('slow');
              _this.prepend(el);
              $('html, body').scrollTop(0);
            } else {
              alert('An error occurred');
              console.log(resp);
            }
            end_loader();
          }
        });
      });

      // === ส่วนของ Cropper.js (ปรับปรุงใหม่ทั้งหมด) ===
      var $modal = $('#cropModal');
      var image = document.getElementById('image_to_crop');
      var cropper;
      var zoomSlider = document.getElementById('zoom_slider');

      function resizeImage(file, maxWidth, maxHeight, callback) {
        var reader = new FileReader();
        reader.onload = function(event) {
          var img = new Image();
          img.onload = function() {
            var canvas = document.createElement('canvas');
            var ctx = canvas.getContext('2d');
            var width = img.width;
            var height = img.height;

            // คำนวณขนาดใหม่
            if (width > height) {
              if (width > maxWidth) {
                height = Math.round(height * (maxWidth / width));
                width = maxWidth;
              }
            } else {
              if (height > maxHeight) {
                width = Math.round(width * (maxHeight / height));
                height = maxHeight;
              }
            }

            // กำหนดขนาดใหม่ให้กับ canvas
            canvas.width = width;
            canvas.height = height;
            ctx.drawImage(img, 0, 0, width, height);
            callback(canvas.toDataURL('image/jpeg'));
          };
          img.src = event.target.result;
        };
        reader.readAsDataURL(file);
      }

      // 1. เมื่อผู้ใช้เลือกไฟล์รูปภาพ
      $('#customFile').on('change', function(e) {
        var files = e.target.files;
        if (files && files.length > 0) {
          var reader = new FileReader();
          reader.onload = function(event) {
            image.src = event.target.result;
            $modal.modal({
              backdrop: false, // ป้องกันการปิด modal เมื่อคลิกด้านนอก
              keyboard: false, // ป้องกันการปิด modal ด้วยปุ่ม Esc
              show: true
            });
          };
          reader.readAsDataURL(files[0]);
          var fileName = $(this).val().split('\\').pop();
          $(this).next('.custom-file-label').html(fileName);
        }
      });

      // 2. เมื่อ Modal แสดงขึ้นมา
      $modal.on('shown.bs.modal', function() {
        cropper = new Cropper(image, {
          // --- CHANGED: Options for Discord-like cropping ---
          aspectRatio: 1,
          viewMode: 1, // จำกัดให้ cropbox อยู่ในกรอบของรูปเท่านั้น
          dragMode: 'move', // ตั้งค่าเริ่มต้นให้เป็นการ "ลากรูป"

          // --- ADDED: Lock the crop box ---
          cropBoxMovable: false, // ไม่ให้ขยับกรอบ
          cropBoxResizable: false, // ไม่ให้ย่อขยายกรอบ

          // --- ADDED: Disable mouse wheel zoom ---
          wheelZoomRatio: 0, // ปิดการซูมด้วย mouse wheel

          background: false,
          responsive: true,
          autoCropArea: 1, // ให้กรอบ crop เต็มพื้นที่

          // --- ADDED: Event when cropper is ready ---
          ready: function() {
            // ดึงค่า zoom ratio ปัจจุบันหลังจาก cropper จัดรูปให้พอดีแล้ว
            let canvasData = cropper.getCanvasData();
            let initialZoom = canvasData.width / canvasData.naturalWidth;
            // ตั้งค่า min, max, และ value ของ slider
            zoomSlider.min = initialZoom; // ค่าซูมต่ำสุดคือขนาดที่พอดีกับกรอบ
            zoomSlider.max = initialZoom * 3; // อนุญาตให้ซูมได้สูงสุด 3 เท่า
            zoomSlider.value = initialZoom; // ตั้งค่าเริ่มต้นของ slider
          }
        });
      }).on('hidden.bs.modal', function() {
        // 3. เมื่อ Modal ถูกปิด
        cropper.destroy();
        cropper = null;
        // Reset file input to allow selecting the same file again
        $('#customFile').val('');
        $('#customFile').next('.custom-file-label').html('เลือกรูปโปรไฟล์');
      });

      // --- ADDED: Event listener for the zoom slider ---
      zoomSlider.addEventListener('input', function() {
        if (cropper) {
          cropper.zoomTo(this.value);
        }
      });

      // 4. เมื่อผู้ใช้กดปุ่ม "บันทึก"
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