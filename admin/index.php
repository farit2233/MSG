<?php require_once('../config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>
<style>
  #uni_modal_promotion .modal-dialog {
    max-width: 800px !important;
    z-index: 1050;
    /* เพิ่ม z-index เพื่อให้แสดงผลบนสุด */
  }
</style>

<body class="sidebar-mini layout-fixed control-sidebar-slide-open layout-navbar-fixed  sidebar-mini-xs text-sm" data-new-gr-c-s-check-loaded="14.991.0" data-gr-ext-installed="" style="height: auto;">
  <div class="wrapper">
    <?php require_once('inc/topBarNav.php') ?>
    <?php require_once('inc/navigation.php') ?>
    <?php if ($_settings->chk_flashdata('success')): ?>
      <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
      </script>
    <?php endif; ?>
    <?php $page = isset($_GET['page']) ? $_GET['page'] : 'home';  ?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper  pt-3" style="min-height: 567.854px;">

      <!-- Main content -->
      <section class="content  text-dark">
        <div class="container-fluid">
          <?php
          if (!file_exists($page . ".php") && !is_dir($page)) {
            include '404.html';
          } else {
            if (is_dir($page))
              include $page . '/index.php';
            else
              include $page . '.php';
          }
          ?>
        </div>
      </section>
      <!-- /.content -->
      <div class="modal fade" id="uni_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
          <div class="modal-content rounded-0">
            <div class="modal-header">
              <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">
            </div>
            <div class="modal-footer">
              <!-- ปุ่มยกเลิก -->
              <button type="button" class="btn btn-light border rounded shadow-sm" data-dismiss="modal">ยกเลิก</button>
              <!-- ปุ่มเพิ่มสินค้าที่เลือก -->
              <button type="button" class="btn btn-primary rounded shadow-sm" id="submit" onclick="$('#uni_modal form').submit()">บันทึก</button>
            </div>
          </div>
        </div>
      </div>

      <!-- /.content -->
      <div class="modal fade" id="uni_modal_promotion" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
          <div class="modal-content rounded-0">
            <div class="modal-header">
              <h5 class="modal-title"></h5>
            </div>
            <div class="modal-body">

            </div>
            <div class="modal-footer">
              <!-- ปุ่มยกเลิก -->
              <button type="button" class="btn btn-light border rounded shadow-sm" data-dismiss="modal">ยกเลิก</button>
              <!-- ปุ่มเพิ่มสินค้าที่เลือก -->
              <button type="button" class="btn btn-primary rounded shadow-sm" id="submit" onclick="$('#uni_modal_promotion form').submit()">บันทึก</button>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade" id="uni_modal_right" role='dialog'>
        <div class="modal-dialog modal-full-height  modal-md rounded-0" role="document">
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
      <div class="modal fade" id="confirm_modal" role='dialog'>
        <div class="modal-dialog modal-md modal-dialog-centered rounded-0" role="document">
          <div class="modal-content rounded-0">
            <div class="modal-header">
              <h5 class="modal-title">ยืนยันการลบ</h5>
            </div>
            <div class="modal-body">
              <div id="delete_content"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger rounded-0" id='confirm' onclick="">ดำเนินการต่อ</button>
              <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">ยกเลิก</button>
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
    </div>

  </div>
  <!-- /.content-wrapper -->
  <?php require_once('inc/footer.php') ?>
</body>

</html>