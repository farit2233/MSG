<style>
  a {
    color: inherit;
    text-decoration: none;
  }

  a:hover {
    color: inherit;
    text-decoration: none;
  }

  .info-box {
    transition: transform 0.5s ease, box-shadow 0.5s ease;
  }

  .info-box:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
  }
</style>

<h1>ยินดีต้อนรับ, <?php echo $_settings->userdata('firstname') . " " . $_settings->userdata('lastname') ?>!</h1>
<hr>
<h4 class="mt-4">สรุปข้อมูลระบบ</h4>
<div class="row">
  <?php
  $boxes = [
    ['link' => '?page=categories', 'icon' => 'fas fa-th-list', 'label' => 'หมวดหมู่สินค้าทั้งหมด', 'query' => "SELECT * FROM category_list where delete_flag = 0", 'bg' => 'bg-primary'],
    ['link' => '?page=products', 'icon' => 'fas fa-boxes', 'label' => 'สินค้าทั้งหมด', 'query' => "SELECT id FROM product_list where `status` = 1", 'bg' => 'bg-info'],
    ['link' => '?page=orders&status=0', 'icon' => 'fas fa-clock', 'label' => 'คำสั่งซื้อที่รอดำเนินการ', 'query' => "SELECT id FROM order_list where `status` = 0", 'bg' => 'bg-warning text-dark'],
    ['link' => '?page=orders&status=1', 'icon' => 'fas fa-box-open', 'label' => 'คำสั่งซื้อที่แพ็กแล้ว', 'query' => "SELECT id FROM order_list where `status` = 1", 'bg' => 'bg-primary'],
    ['link' => '?page=orders&status=2', 'icon' => 'fas fa-truck', 'label' => 'กำลังจัดส่ง', 'query' => "SELECT id FROM order_list where `status` = 2", 'bg' => 'bg-info'],
    ['link' => '?page=orders&status=3', 'icon' => 'fas fa-check-circle', 'label' => 'คำสั่งซื้อสำเร็จแล้ว', 'query' => "SELECT id FROM order_list where `status` = 3", 'bg' => 'bg-success'],
  ];

  foreach ($boxes as $box):
    $result = $conn->query($box['query']);
    $count = $result ? format_num($result->num_rows) : '0';
    $bg = $box['bg'] ?? 'bg-light';
  ?>
    <div class="col-12 col-sm-4 col-md-4">
      <a href="<?= $box['link'] ?>">
        <div class="info-box <?= $bg ?> text-white">
          <span class="info-box-icon"><i class="<?= $box['icon'] ?>"></i></span>
          <div class="info-box-content">
            <span class="info-box-text text-bold"><?= $box['label'] ?></span>
            <span class="info-box-number text-right h5"><?= $count ?></span>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php
$payment_statuses = [
  0 => ['label' => 'ยังไม่ชำระเงิน', 'bg' => 'bg-secondary', 'icon' => 'fas fa-wallet'],
  1 => ['label' => 'รอตรวจสอบ', 'bg' => 'bg-warning text-dark', 'icon' => 'fas fa-search-dollar'],
  2 => ['label' => 'ชำระเงินแล้ว', 'bg' => 'bg-success', 'icon' => 'fas fa-check-circle'],
];
?>
<h4 class="mt-4">สถานะการชำระเงิน</h4>
<div class="row">
  <?php foreach ($payment_statuses as $status => $data):
    $count = $conn->query("SELECT id FROM order_list WHERE payment_status = $status")->num_rows;
  ?>
    <div class="col-12 col-sm-6 col-md-4 mb-3">
      <a href="?page=orders&payment_status=<?= $status ?>" class="text-reset text-decoration-none">
        <div class="info-box <?= $data['bg'] ?> text-white">
          <span class="info-box-icon"><i class="<?= $data['icon'] ?>"></i></span>
          <div class="info-box-content">
            <span class="info-box-text"><?= $data['label'] ?></span>
            <span class="info-box-number h5"><?= format_num($count) ?></span>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<?php
$delivery_statuses = [
  1 => ['label' => 'กำลังเตรียมของ', 'bg' => 'bg-info', 'icon' => 'fas fa-box'],
  2 => ['label' => 'แพ็คของแล้ว', 'bg' => 'bg-primary', 'icon' => 'fas fa-truck-loading'],
  3 => ['label' => 'กำลังจัดส่ง', 'bg' => 'bg-warning text-dark', 'icon' => 'fas fa-truck'],
  4 => ['label' => 'จัดส่งสำเร็จ', 'bg' => 'bg-success', 'icon' => 'fas fa-check'],
];
?>
<h4 class="mt-4">สถานะการจัดส่ง</h4>
<div class="row">
  <?php foreach ($delivery_statuses as $status => $data):
    $count = $conn->query("SELECT id FROM order_list WHERE delivery_status = $status")->num_rows;
  ?>
    <div class="col-12 col-sm-6 col-md-4 mb-3">
      <a href="?page=orders&delivery_status=<?= $status ?>" class="text-reset text-decoration-none">
        <div class="info-box <?= $data['bg'] ?> text-white">
          <span class="info-box-icon"><i class="<?= $data['icon'] ?>"></i></span>
          <div class="info-box-content">
            <span class="info-box-text"><?= $data['label'] ?></span>
            <span class="info-box-number h5"><?= format_num($count) ?></span>
          </div>
        </div>
      </a>
    </div>
  <?php endforeach; ?>
</div>

<div class="container">
  <?php
  $files = array();
  $fopen = scandir(base_app . 'uploads/banner');
  foreach ($fopen as $fname) {
    if (!in_array($fname, ['.', '..'])) {
      $files[] = validate_image('uploads/banner/' . $fname);
    }
  }
  ?>
  <div id="tourCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
    <div class="carousel-inner h-100">
      <?php foreach ($files as $k => $img): ?>
        <div class="carousel-item h-100 <?= $k == 0 ? 'active' : '' ?>">
          <img class="d-block w-100 h-100" style="object-fit:contain" src="<?= $img ?>" alt="">
        </div>
      <?php endforeach; ?>
    </div>
    <a class="carousel-control-prev" href="#tourCarousel" role="button" data-slide="prev">
      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="carousel-control-next" href="#tourCarousel" role="button" data-slide="next">
      <span class="carousel-control-next-icon" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
</div>