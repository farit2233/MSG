<?php require_once('config.php'); ?>
<!DOCTYPE html>
<html lang="en">
<?php require_once('inc/header.php') ?>
<?php if ($_settings->chk_flashdata('success')): ?>
  <script>
    $(function() {
      alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    })
  </script>
<?php endif; ?>


<body class="d-flex flex-column min-vh-100">
  <?php require_once('inc/topBarNav.php') ?>
  <?php
  // ตรวจสอบว่าเป็นการค้นหาหรือไม่
  if (isset($_GET['search']) && trim($_GET['search']) !== '') {
    $page = 'search'; // ให้ใช้ไฟล์ search.php
  } else {
    $page = isset($_GET['p']) ? $_GET['p'] : 'home';
  }

  // ตรวจสอบว่าไฟล์หรือโฟลเดอร์มีอยู่ไหม
  if (!file_exists($page . ".php") && !is_dir($page)) {
    include '404.html';
  } else {
    if (is_dir($page))
      include $page . '/index.php';
    else
      include $page . '.php';
  }
  ?>
  <?php require_once('inc/footer.php') ?>
  <style>
    @media (max-width: 932px) {
      .modal-dialog-order {
        width: 100% !important;
      }
    }

    #uni_modal_conditions .modal-dialog {
      margin-top: 8% !important;
      margin-bottom: auto !important;
    }
  </style>
  <div class="modal fade" id="uni_modal" role='dialog'>
    <div class="modal-dialog   rounded-0 modal-md modal-dialog-centered" role="document">
      <div class="modal-content  rounded-0">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
        </div>
        <div class="modal-body">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='submit' onclick="$('#uni_modal form').submit()">Save</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="uni_modal_right" role='dialog'>
    <div class="modal-dialog  rounded-0 modal-full-height  modal-md" role="document">
      <div class="modal-content rounded-0">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span class="fa fa-arrow-right"></span>
          </button>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="viewer_modal" role='dialog'>
    <div class="modal-dialog modal-md" role="document">
      <div class="modal-content">
        <button type="button" class="btn-close" data-dismiss="modal"><span class="fa fa-times"></span></button>
        <img src="" alt="">
      </div>
    </div>
  </div>
  <div class="modal fade" id="confirm_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Confirmation</h5>
        </div>
        <div class="modal-body">
          <div id="delete_content"></div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id='confirm' onclick="">Continue</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade modal-conditions" id="uni_modal_conditions" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close position-absolute" style="right: 10px; top: 10px; z-index: 10;" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uni_modal_order" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered rounded-0 modal-dialog-order" role="document">
      <div class="modal-content rounded-0">
        <div class="modal-header">
          <h5 class="modal-title"></h5>
          <button type="button" class="close position-absolute" style="right: 10px; top: 10px; z-index: 10;" data-dismiss="modal" aria-label="Close">
            <i class="fa fa-times"></i>
          </button>
        </div>
        <div class="modal-body">

        </div>
        <button type="button" class="btn btn-light btn-sm border btn-flat" data-dismiss="modal" aria-label="Close">
          <i class="fa fa-times"></i> ปิด
        </button>
      </div>
    </div>
  </div>

  <div class="modal fade" id="password_modal" role='dialog'>
    <div class="modal-dialog modal-md modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">เปลี่ยนรหัสผ่าน</h5>
        </div>
        <div class="modal-body">

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary" id="btn_change_password" form="change_password_form">ยืนยัน</button>
          <button type="submit" class="btn btn-primary" id="btn_forgot_password" form="forgot_password_form" style="display:none;">ส่งคำขอ</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="address_option_modal" tabindex="-1" aria-labelledby="addressOptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addressOptionModalLabel">เปลี่ยนที่อยู่หลัก / เพิ่มที่อยู่ใหม่</h5>
        </div>
        <div class="modal-body">
          <h6>ที่อยู่หลักของคุณ:</h6>
          <div id="main-address">

          </div>
          <h6>ที่อยู่เพิ่มเติม:</h6>
          <div id="additional-addresses">

          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
          <button type="submit" class="btn btn-primary" id="confirm_address_selection" form="address_option">เลือกที่อยู่นี้</button>
        </div>
      </div>
    </div>
  </div>


  <div class="modal fade" id="cropModal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-user " role="document">
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
</body>

</html>