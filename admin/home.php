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
<div class="row">
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=categories" id="categories">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-th-list"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">หมวดหมู่สินค้าทั้งหมด</span>
          <span class="info-box-number text-right h5">
            <?php
            $category = $conn->query("SELECT * FROM category_list where delete_flag = 0")->num_rows;
            echo format_num($category);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=products" id="product">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-file-invoice"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">สินค้าทั้งหมด</span>
          <span class="info-box-number text-right h5">
            <?php
            $products = $conn->query("SELECT id FROM product_list where `status` = 1")->num_rows;
            echo format_num($products);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=orders&status=0">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-file-invoice"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">รายการคำสั่งซื้อที่รอดำเนินการ</span>
          <span class="info-box-number text-right h5">
            <?php
            $order = $conn->query("SELECT id FROM order_list where `status` = 0")->num_rows;
            echo format_num($order);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=orders&status=1">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-file-invoice"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">รายการคำสั่งซื้อที่แพ็กของแล้ว</span>
          <span class="info-box-number text-right h5">
            <?php
            $order = $conn->query("SELECT id FROM order_list where `status` = 1")->num_rows;
            echo format_num($order);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=orders&status=2">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-file-invoice"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">กำลังจัดส่ง</span>
          <span class="info-box-number text-right h5">
            <?php
            $order = $conn->query("SELECT id FROM order_list where `status` = 2")->num_rows;
            echo format_num($order);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-12 col-sm-4 col-md-4">
    <a href="?page=orders&status=3">
      <div class="info-box">
        <span class="info-box-icon bg-gradient-teal elevation-1"><i class="fas fa-file-invoice"></i></span>
        <div class="info-box-content">
          <span class="info-box-text text-bold">รายการคำสั่งซื้อที่สำเร็จแล้ว</span>
          <span class="info-box-number text-right h5">
            <?php
            $order = $conn->query("SELECT id FROM order_list where `status` = 3")->num_rows;
            echo format_num($order);
            ?>
            <?php ?>
          </span>
        </div>
        <!-- /.info-box-content -->
      </div>
    </a>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>
<div class="container">
  <?php
  $files = array();
  $fopen = scandir(base_app . 'uploads/banner');
  foreach ($fopen as $fname) {
    if (in_array($fname, array('.', '..')))
      continue;
    $files[] = validate_image('uploads/banner/' . $fname);
  }
  ?>
  <div id="tourCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
    <div class="carousel-inner h-100">
      <?php foreach ($files as $k => $img): ?>
        <div class="carousel-item  h-100 <?php echo $k == 0 ? 'active' : '' ?>">
          <img class="d-block w-100  h-100" style="object-fit:contain" src="<?php echo $img ?>" alt="">
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