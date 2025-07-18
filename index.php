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
<link rel="stylesheet" href="assets/css/navbar.css">

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

</body>

</html>