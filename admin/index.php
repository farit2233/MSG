<?php require_once('../config.php'); ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
<?php require_once('inc/header.php') ?>

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
              <button type="button" class="btn btn-dark rounded-0" id='submit' onclick="$('#uni_modal form').submit()">บันทึก</button>
              <button type="button" class="btn btn-secondary rounded-0" data-dismiss="modal">ยกเลิก</button>
            </div>
          </div>
        </div>
      </div>

    </div>
    <!-- /.content-wrapper -->
    <?php require_once('inc/footer.php') ?>
</body>

</html>