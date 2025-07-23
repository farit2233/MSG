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

  section {
    font-size: 16px;
  }
</style>
<section>
  <h1>ยินดีต้อนรับ, <?php echo $_settings->userdata('firstname') . " " . $_settings->userdata('lastname') ?>!</h1>
  <hr>
  <h4 class="mt-4">สรุปข้อมูลระบบ</h4>
  <div class="row">
    <?php
    //$boxes = [
    //  ['link' => '?page=categories', 'label' => 'หมวดหมู่สินค้าทั้งหมด', 'bg' => 'bg-primary', 'icon' => 'fas fa-th-list', 'query' => "SELECT * FROM category_list where delete_flag = 0"],
    //  ['link' => '?page=products', 'label' => 'สินค้าทั้งหมด', 'bg' => 'bg-info', 'icon' => 'fas fa-boxes', 'query' => "SELECT id FROM product_list where `status` = 1"],
    //  ['link' => '?page=inventory', 'label' => 'สต๊อกสินค้า', 'bg' => 'bg-secondary', 'icon' => 'fas fa-warehouse'],
    //];
    $boxes = [
      ['link' => '?page=product_type', 'label' => 'ประเภทสินค้าทั้งหมด', 'bg' => 'bg-white', 'icon' => 'fas fa-layer-group', 'query' => "SELECT * FROM product_type where delete_flag = 0"],
      ['link' => '?page=categories', 'label' => 'หมวดหมู่สินค้าทั้งหมด', 'bg' => 'bg-white', 'icon' => 'fas fa-th-list', 'query' => "SELECT * FROM category_list where delete_flag = 0"],
      ['link' => '?page=products', 'label' => 'สินค้าทั้งหมด', 'bg' => 'bg-white', 'icon' => 'fas fa-boxes', 'query' => "SELECT id FROM product_list where `status` = 1"],
      ['link' => '?page=inventory', 'label' => 'สต๊อกสินค้า', 'bg' => 'bg-white', 'icon' => 'fas fa-warehouse'],
    ];

    foreach ($boxes as $box):
      $bg = $box['bg'] ?? 'bg-light';
    ?>
      <div class="col-12 col-sm-4 col-md-4">
        <a href="<?= $box['link'] ?>">
          <div class="info-box <?= $bg ?> text-white">
            <span class="info-box-icon"><i class="<?= $box['icon'] ?>"></i></span>
            <div class="info-box-content">
              <span class="info-box-text text-bold"><?= $box['label'] ?></span>
              <?php if (isset($box['query'])): ?>
                <?php $result = $conn->query($box['query']); ?>
                <span class="info-box-number text-right h5"><?= format_num($result->num_rows ?? 0) ?></span>
              <?php endif; ?>
            </div>
          </div>
        </a>
      </div>
    <?php endforeach; ?>

  </div>

  <?php
  //$payment_status = [
  //  0 => ['label' => 'ยังไม่ชำระเงิน', 'bg' => 'bg-secondary', 'icon' => 'fas fa-wallet'],
  //  1 => ['label' => 'รอตรวจสอบ', 'bg' => 'bg-warning text-dark', 'icon' => 'fas fa-search-dollar'],
  //  2 => ['label' => 'ชำระเงินแล้ว', 'bg' => 'bg-success', 'icon' => 'fas fa-check-circle'],
  //  3 => ['label' => 'ชำระเงินล้มเหลว', 'bg' => 'bg-danger', 'icon' => 'fa fa-exclamation'],
  //  4 => ['label' => 'คืนเงินแล้ว', 'bg' => 'bg-info', 'icon' => 'fas fa-undo-alt'],
  //];
  $payment_status = [
    0 => ['label' => 'ยังไม่ชำระเงิน', 'bg' => 'bg-white', 'icon' => 'fas fa-wallet'],
    1 => ['label' => 'รอตรวจสอบ', 'bg' => 'bg-white', 'icon' => 'fas fa-search-dollar'],
    2 => ['label' => 'ชำระเงินแล้ว', 'bg' => 'bg-white', 'icon' => 'fas fa-check-circle'],
    3 => ['label' => 'ชำระเงินล้มเหลว', 'bg' => 'bg-white', 'icon' => 'fa fa-exclamation'],
    4 => ['label' => 'คืนเงินแล้ว', 'bg' => 'bg-white', 'icon' => 'fas fa-undo-alt'],
  ];
  ?>
  <h4 class="mt-4">สถานะการชำระเงิน</h4>
  <div class="row">
    <?php foreach ($payment_status as $status => $data):
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
  //$delivery_status = [
  //  0 => ['label' => 'ตรวจสอบคำสั่งซื้อ', 'bg' => 'bg-secondary', 'icon' => 'fas fa-file-invoice'],
  //  1 => ['label' => 'กำลังเตรียมของ', 'bg' => 'bg-info', 'icon' => 'fas fa-box'],
  //  2 => ['label' => 'แพ๊กของแล้ว', 'bg' => 'bg-primary', 'icon' => 'fas fa-truck-loading'],
  //  3 => ['label' => 'กำลังจัดส่ง', 'bg' => 'bg-info', 'icon' => 'fas fa-truck'],
  //  4 => ['label' => 'จัดส่งสำเร็จ', 'bg' => 'bg-success', 'icon' => 'fas fa-check-circle'],
  //  5 => ['label' => 'จัดส่งไม่สำเร็จ', 'bg' => 'bg-danger', 'icon' => 'fas fa-exclamation-triangle'],
  //  6 => ['label' => 'คืนของระหว่างทาง', 'bg' => 'bg-warning text-dark', 'icon' => 'fas fa-undo-alt'],
  //  7 => ['label' => 'คืนของสำเร็จ', 'bg' => 'bg-success', 'icon' => 'fas fa-box-open'],
  //];
  $delivery_status = [
    0 => ['label' => 'ตรวจสอบคำสั่งซื้อ', 'bg' => 'bg-white', 'icon' => 'fas fa-file-invoice'],
    1 => ['label' => 'กำลังเตรียมของ', 'bg' => 'bg-white', 'icon' => 'fas fa-box'],
    2 => ['label' => 'แพ๊กของแล้ว', 'bg' => 'bg-white', 'icon' => 'fas fa-truck-loading'],
    3 => ['label' => 'กำลังจัดส่ง', 'bg' => 'bg-white', 'icon' => 'fas fa-truck'],
    4 => ['label' => 'จัดส่งสำเร็จ', 'bg' => 'bg-white', 'icon' => 'fas fa-check-circle'],
    5 => ['label' => 'จัดส่งไม่สำเร็จ', 'bg' => 'bg-white', 'icon' => 'fas fa-exclamation-triangle'],
    6 => ['label' => 'คืนของระหว่างทาง', 'bg' => 'bg-white', 'icon' => 'fas fa-undo-alt'],
    7 => ['label' => 'คืนของสำเร็จ', 'bg' => 'bg-white', 'icon' => 'fas fa-box-open'],
  ];
  ?>
  <h4 class="mt-4">สถานะการจัดส่ง</h4>
  <div class="row">
    <?php foreach ($delivery_status as $status => $data):
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
</section>