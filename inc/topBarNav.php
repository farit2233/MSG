<?php
$product_structure = [];

$type_qry = $conn->query("SELECT * FROM `product_type` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `date_created` ASC");
while ($type_row = $type_qry->fetch_assoc()) {
  $tid = $type_row['id'];
  $product_structure[$tid] = [
    'name' => $type_row['name'],
    'categories' => []
  ];

  $cat_qry = $conn->query("SELECT * FROM `category_list` WHERE `status` = 1 AND `delete_flag` = 0 AND `product_type_id` = {$tid} ORDER BY `date_created` ASC");
  while ($cat_row = $cat_qry->fetch_assoc()) {
    $product_structure[$tid]['categories'][] = [
      'id' => $cat_row['id'],
      'name' => $cat_row['name']
    ];
  }
}

$promotion_structure = [];

// Query เพื่อดึงข้อมูลของหมวดหมู่โปรโมชั่น
$type_qry = $conn->query("SELECT * FROM `promotion_category` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `date_created` ASC");
while ($type_row = $type_qry->fetch_assoc()) {
  $pcid = $type_row['id'];

  // ตรวจสอบวันหมดอายุของโปรโมชั่น
  $current_time = time(); // เวลาปัจจุบัน
  $pro_qry = $conn->query("SELECT * FROM `promotions_list` 
                           WHERE `status` = 1 AND `delete_flag` = 0 AND `promotion_category_id` = {$pcid} 
                           ORDER BY `date_created` ASC");

  $has_active_promotions = false; // ตัวแปรเพื่อตรวจสอบว่ามีโปรโมชั่นที่ยังคงใช้งานได้หรือไม่

  while ($pro_row = $pro_qry->fetch_assoc()) {
    // ตรวจสอบวันหมดอายุของแต่ละโปรโมชั่น
    $end_date = strtotime($pro_row['end_date']); // วันหมดอายุ
    if ($end_date >= $current_time) {
      // ถ้าโปรโมชั่นยังไม่หมดอายุ ให้เพิ่มลงใน array
      $promotion_structure[$pcid]['categories'][] = [
        'id' => $pro_row['id'],
        'name' => $pro_row['name'],
        'discount_value' => $pro_row['discount_value'],
        'type' => $pro_row['type']
      ];
      $has_active_promotions = true; // ตั้งค่าถ้ามีโปรโมชั่นที่ยังใช้งานได้
    }
  }

  // ถ้ามีโปรโมชั่นที่ยังใช้งานได้ จะเพิ่มหมวดหมู่
  if ($has_active_promotions) {
    $promotion_structure[$pcid]['name'] = $type_row['name'];
  }
}

?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-msg">
  <div class="container container-wide px-0 px-lg-0">

    <a class="navbar-brand" href="./">
      <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="80" height="80" class="d-inline-block align-top" alt="" loading="lazy">
      <?php echo $_settings->info('short_name') ?>
    </a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <!--li class="nav-item"><a class="nav-link text-white fos" href="./?p=products">สินค้าทั้งหมด</a></li-->

        <!-- HTML Navbar ที่แสดงโปรโมชั่น -->
        <li class="nav-item dropdown position-static">
          <a class="nav-link dropdown-toggle text-white fos" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            โปรโมชั่น
          </a>
          <div class="dropdown-menu megamenu w-100" aria-labelledby="navbarDropdown">
            <div class="mega-box">
              <div class="content">
                <div class="row">
                  <?php foreach ($promotion_structure as $pid => $type_data): ?>
                    <?php if (!empty($type_data['categories'])): ?>
                      <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <ul>
                          <a href="#" onclick="return false;" class="text-decoration-none">
                            <h5 class="list-header"><?= htmlspecialchars($type_data['name']) ?></h5>
                          </a>
                          <hr class="mt-1 mb-2">
                          <?php foreach ($type_data['categories'] as $cat_row): ?>
                            <li>
                              <!-- ลิงก์ไปยังหน้ารายการสินค้าตาม promotion_id -->
                              <a href="<?= base_url . "?p=products&pid=" . $cat_row['id'] ?>" class="text-decoration-none">
                                <?= htmlspecialchars($cat_row['name']) ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </li>

        <li class="nav-item dropdown position-static">
          <a class="nav-link dropdown-toggle text-white fos" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            ประเภทสินค้า
          </a>
          <div class="dropdown-menu megamenu w-100" aria-labelledby="navbarDropdown">
            <div class="mega-box">
              <div class="content">
                <div class="row">
                  <?php foreach ($product_structure as $tid => $type_data): ?>
                    <?php if (!empty($type_data['categories'])): ?>
                      <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <ul>
                          <a href="<?= base_url . "?p=products&tid={$tid}" ?>" class="text-decoration-none">
                            <h5 class="list-header"><?= htmlspecialchars($type_data['name']) ?></h5>
                          </a>
                          <hr class="mt-1 mb-2">
                          <?php foreach ($type_data['categories'] as $cat_row): ?>
                            <li>
                              <a href="<?= base_url . "?p=products&cid=" . $cat_row['id'] ?>" class="text-decoration-none">

                                <?= htmlspecialchars($cat_row['name']) ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>
                  <?php endforeach; ?>
                </div>
              </div>
            </div>
          </div>
        </li>
        <li class="nav-item"><a class="nav-link text-white fos" href="./?p=help">ช่วยเหลือ</a></li>
        <li class="nav-item"><a class="nav-link text-white fos" href="./?p=about">เกี่ยวกับเรา</a></li>
      </ul>
    </div>

    <div class="d-none d-lg-flex align-items-center ms-auto">
      <ul class="navbar-nav flex-row align-items-center">
        <li class="nav-item">
          <form class="expanding-search" method="get" action="./">
            <input type="text" name="search" placeholder="ค้นหาสินค้า..." />
            <button type="submit" title="ค้นหา">
              <i class="fa fa-search"></i>
            </button>
          </form>
        </li>
        <li class="nav-item position-relative mx-2">
          <a class="nav-link text-white p-0" href="./?p=cart_list" title="ตะกร้าสินค้า">
            <i class="fa fa-basket-shopping icon-size"></i>
            <?php if ($_settings->userdata('id') && $_settings->userdata('login_type') == 2): ?>
              <?php
              $cart_count = $conn->query("SELECT SUM(quantity) FROM `cart_list` WHERE customer_id = '{$_settings->userdata('id')}'")->fetch_array()[0];
              $cart_count = $cart_count > 0 ? format_num($cart_count) : '';
              ?>
              <?php if ($cart_count): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?>
            <?php else: ?>
              <span class="cart-badge d-none" id="guest_cart_count"></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item position-relative me-2">
          <!--alert-->
          <?php
          $is_logged_in = $_settings->userdata('id') && $_settings->userdata('login_type') == 2;
          $customer_id = $_settings->userdata('id');

          // เตรียม query แจ้งเตือน (หากล็อกอินแล้ว)
          if ($is_logged_in) {
            $notif_qry = $conn->query("
              SELECT 
                o.code, o.id, o.date_updated, o.payment_status, o.delivery_status,
                
                -- สินค้าชิ้นที่ 1
                (SELECT p.name 
                FROM order_items oi1 
                INNER JOIN product_list p ON p.id = oi1.product_id 
                WHERE oi1.order_id = o.id 
                ORDER BY oi1.product_id ASC 
                LIMIT 1 OFFSET 0) AS product_name,

                -- สินค้าชิ้นที่ 2 (ถ้ามี)
                (SELECT p.name 
                FROM order_items oi2 
                INNER JOIN product_list p ON p.id = oi2.product_id 
                WHERE oi2.order_id = o.id 
                ORDER BY oi2.product_id ASC 
                LIMIT 1 OFFSET 1) AS more_product_name,

                -- รูปภาพจากสินค้าชิ้นแรก
                (SELECT p.image_path 
                FROM order_items oi3 
                INNER JOIN product_list p ON p.id = oi3.product_id 
                WHERE oi3.order_id = o.id 
                ORDER BY oi3.product_id ASC 
                LIMIT 1 OFFSET 0) AS image_path

              FROM order_list o
              WHERE o.customer_id = '{$customer_id}'
              ORDER BY o.date_updated DESC
              LIMIT 5
            ");
          }
          ?>
          <div class="position-relative">
            <div class="dropdown">
              <?php
              // เช็กว่ามีแจ้งเตือนที่ยังไม่อ่าน
              $has_new_notif = false;
              if ($is_logged_in) {
                $check_unseen = $conn->query("SELECT 1 FROM order_list WHERE customer_id = '{$customer_id}' AND is_seen = 0 LIMIT 1");
                $has_new_notif = $check_unseen->num_rows > 0;
              }
              ?>
              <a href="/?p=orders" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน" data-toggle="dropdown" id="notifDropdown">
                <i class="fa fa-bell icon-size position-relative">
                  <?php if ($has_new_notif): ?>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                    </span>
                  <?php endif; ?>
                </i>
              </a>
              <div class="dropdown-menu-notify">
                <div class="dropdown-menu dropdown-menu-right ">
                  <?php if (!$is_logged_in): ?>
                    <div class="dropdown-item text-muted text-center small">
                      กรุณาเข้าสู่ระบบเพื่อดูแจ้งเตือน
                    </div>

                  <?php elseif ($notif_qry->num_rows == 0): ?>
                    <div class="dropdown-item text-muted text-center small">
                      คุณยังไม่ได้สั่งสินค้า<br>
                      <a href="./?p=products" class="text-decoration-underline">ไปสั่งซื้อเลย!</a>
                    </div>

                  <?php else: ?>
                    <?php
                    function get_payment_text($status)
                    {
                      return ['ยังไม่ชำระ', 'รอตรวจสอบ', 'ชำระแล้ว', 'ชำระล้มเหลว', 'คืนเงินแล้ว'][$status] ?? 'N/A';
                    }
                    function get_delivery_text($status)
                    {
                      return ['ตรวจสอบคำสั่งซื้อ', 'เตรียมของ', 'แพ็คของแล้ว', 'กำลังจัดส่ง', 'จัดส่งสำเร็จ', 'ส่งไม่สำเร็จ', 'คืนของระหว่างทาง', 'คืนของสำเร็จ'][$status] ?? 'N/A';
                    }
                    while ($notif = $notif_qry->fetch_assoc()): ?>

                      <a class="dropdown-item d-flex align-items-start gap-2" href=" ./?p=orders">
                        <div class="d-flex align-items-center gap-2">
                          <img src="<?= validate_image($notif['image_path']) ?>" class="notif-thumb" alt="product">
                          <div class="">
                            <h6 class="mb-0">เลขที่คำสั่งซื้อ: <?= $notif['code'] ?></h6>
                            <small class="text-truncate">
                              <?= htmlentities($notif['product_name']) ?>
                              <?php if (!empty($notif['more_product_name'])): ?>
                                , <?= htmlentities($notif['more_product_name']) ?>
                              <?php endif; ?>
                            </small><br>
                            <small class="text-muted">
                              สถานะการชำระเงิน: <b><?= get_payment_text($notif['payment_status']) ?></b> |
                              สถานะการจัดส่ง: <b><?= get_delivery_text($notif['delivery_status']) ?></b>
                            </small>
                          </div>
                        </div>
                      </a>
                      <div class="dropdown-divider"></div>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
          <!-- end alert-->
        </li>
        <li class="nav-item dropdown">
          <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
            <div class="dropdown">
              <button type="button" class="dropdown-toggle user-dd-toggle" data-toggle="dropdown">
                <img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="user-img-nav" alt="User">
                <span class="user-name d-none d-lg-inline"><?= ucwords($_settings->userdata('firstname')) ?></span>
              </button>
              <div class="dropdown-menu user-dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="<?= base_url . '?p=user' ?>"><i class="fa fa-user"></i> บัญชีของฉัน</a>
                <a class="dropdown-item" href="<?= base_url . '?p=cart_list' ?>"><i class="fa fa-shopping-cart"></i> ตะกร้าของฉัน</a>
                <a class="dropdown-item" href="<?= base_url . '?p=orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="<?= base_url . '/classes/Login.php?f=logout_customer' ?>"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
              </div>
            </div>
          <?php else: ?>
            <div class="dropdown">
              <button type="button" class="btn btn-rounded dropdown-toggle dropdown-icon text-white" data-toggle="dropdown">
                <i class="fas fa-user-circle icon-acc-size text-white" title="แอคเคานท์"></i>
              </button>
              <div class="dropdown-menu user-dropdown-menu dropdown-menu-right" role="menu">
                <a class="dropdown-item" href="./login.php"><i class="fa fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="./register.php"><i class="fa fa-user-plus"></i> สมัครสมาชิก</a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="./admin"><i class="fa fa-user-tie"></i> Admin Panel</a>
              </div>
            </div>
          <?php endif; ?>
        </li>
      </ul>
    </div>

    <div class="d-flex d-lg-none align-items-center justify-content-end flex-nowrap">
      <ul class="navbar-nav flex-row align-items-center mb-0" style="gap: 0.8rem;">
        <!-- icons -->
        <li class="nav-item d-flex d-none align-items-center">
          <a class="nav-link text-white p-0" href="#" data-toggle="modal" data-target="#mobileSearchModal" title="ค้นหาสินค้า">
            <i class="fas fa-search icon-size"></i>
          </a>
        </li>
        <li class="nav-item position-relative">
          <a class="nav-link text-white p-0" href="./?p=cart_list" title="ตะกร้าสินค้า">
            <i class="fa fa-basket-shopping icon-size"></i>
            <?php if ($_settings->userdata('id') && $_settings->userdata('login_type') == 2): ?>
              <?php if ($cart_count): ?><span class="cart-badge"><?= $cart_count ?></span><?php endif; ?>
            <?php else: ?>
              <span class="cart-badge d-none" id="guest_cart_count_mobile"></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item position-relative">
          <a href="/?p=orders" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน">
            <i class="fa fa-bell icon-size position-relative">
              <?php if ($has_new_notif): ?>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
              <?php endif; ?>
            </i>
          </a>
        </li>
        <li class="nav-item d-flex align-items-center">
          <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
            <a href="<?= base_url . '?p=user' ?>" title="บัญชีของฉัน" class="d-flex align-items-center">
              <img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="user-img-nav" alt="User Avatar" />
            </a>
          <?php else: ?>
            <a href="./login.php" title="เข้าสู่ระบบ" class="d-flex align-items-center">
              <i class="fas fa-user-circle icon-acc-size text-white"></i>
            </a>
          <?php endif; ?>
        </li>

      </ul>
      <button class="navbar-toggler ms-2" type="button" id="openSidebarBtn">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>

  </div>
</nav>

<!-- Modal ช่องค้นหา -->
<div class="modal fade" id="mobileSearchModal" tabindex="-5" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm mt-5">
    <div class="modal-content minimalist-search-modal">
      <div class="modal-body">
        <form method="get" action="./" class="search-form-wrapper">
          <i class="fa fa-search search-icon"></i>
          <input type="search" name="search" class="form-control search-input" placeholder="ค้นหา..." required autofocus>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>
<div class="mobile-sidebar" id="mobileSidebar">
  <div class="sidebar-header">
    <h5>เมนู</h5>
    <button class="sidebar-close-btn" id="closeSidebarBtn">&times;</button>
  </div>
  <div class="sidebar-body">
    <div class="sidebar-search-form">
      <div class="d-flex align-items-center user-dropdown-wrapper w-100">
        <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
          <div class="d-flex align-items-center user-mobile-btn w-100 py-2 px-3" role="button" tabindex="0" onclick="goToUserPage()">
            <img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="user-img-mobile me-2" alt="User">
            <div class="user-name text-start">
              <div class="user-display-name"><?= ucwords($_settings->userdata('firstname')) ?></div>
              <small class="text-muted">บัญชีของฉัน</small>
            </div>
          </div>
        <?php else: ?>
          <div class="d-flex align-items-center py-2">
            <a class="nav-link p-0 mr-3" href="./login.php">
              <i class="fa fa-sign-in-alt mr-1"></i> เข้าสู่ระบบ
            </a>
            <a class="nav-link p-0" href="./register.php">
              <i class="fa fa-user-plus mr-1"></i> สมัครสมาชิก
            </a>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="sidebar-panels-wrapper">
      <div class="sidebar-panel sidebar-main-panel">
        <nav class="nav flex-column pt-3">
          <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
            <a class="nav-link" href="<?= base_url . '?p=cart_list' ?>"><i class="fa fa-shopping-cart"></i> ตะกร้าของฉัน</a>
            <a class="nav-link" href="<?= base_url . '?p=orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
            <div class="dropdown-divider"></div>
          <?php endif; ?>
          <h6 class="px-3 mt-2 mb-1 text-muted">ประเภทสินค้า</h6>
          <?php foreach ($product_structure as $type_id => $type_data): ?>
            <?php if (!empty($type_data['categories'])): ?>
              <a href="#" class="nav-link d-flex justify-content-between align-items-center type-item" data-type-id="<?= $type_id ?>">
                <span><?= htmlspecialchars($type_data['name']) ?></span>
                <i class=" fas fa-chevron-right fa-xs"></i>
              </a>
            <?php endif; ?>
          <?php endforeach; ?>

          <div class="dropdown-divider"></div>
          <a class="nav-link" href="./?p=help"><i class="fa fa-question-circle"></i> ช่วยเหลือ</a>
          <a class="nav-link" href="./?p=about"><i class="fa fa-info-circle"></i> เกี่ยวกับเรา</a>
          <a class="nav-link" href="./?p=contact"><i class="fa fa-envelope"></i> ติดต่อเรา</a>
          <div class="dropdown-divider"></div>
          <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
            <a class="nav-link" href="<?= base_url . '/classes/Login.php?f=logout_customer' ?>">
              <i class="fa fa-sign-out-alt"></i> ออกจากระบบ
            </a>
          <?php endif; ?>
        </nav>
      </div>

      <div class="sidebar-panel sidebar-submenu-panel">
        <nav class="nav flex-column pt-3">
          <a href="#" class="nav-link back-to-main-menu"><i class="fas fa-arrow-left me-2"></i> กลับไปเมนูหลัก</a>
          <div class="dropdown-divider"></div>
          <h6 class="px-3 mt-2 mb-1 text-muted submenu-title"></h6>
          <div class="submenu-list">
          </div>
        </nav>
      </div>
    </div>
  </div>
</div>
<script id="product-data-json" type="application/json">
  <?= json_encode($product_structure); ?>
</script>
<button id="toTopBtn" title="กลับขึ้นบน"><i class="fas fa-chevron-up"></i></button>

<script>
  let scrollPosition = 0;

  function goToUserPage() {
    window.location.href = "<?= base_url . '?p=user' ?>";
  }
  $(function() {
    $('#notifDropdown').on('click', function() {
      $.ajax({
        url: './ajax/mark_notifications_seen.php',
        method: 'POST',
        success: function(resp) {
          console.log("Marked as seen");
          $('.fa-bell .position-absolute').remove();
        }
      });
    });

    // ถ้าเป็นมือถือ → redirect ไป orders ทันที
    document.querySelector(".notif-bell").addEventListener("click", function(e) {
      if (window.innerWidth <= 768) {
        e.preventDefault(); // กัน dropdown เด้ง
        window.location.href = "./?p=orders";
      }
    });
  });
  document.addEventListener('DOMContentLoaded', function() {

    // --- Navbar Scroll Behavior ---
    let lastScrollTop = 0;
    const navbar = document.querySelector('.navbar-msg');
    const navbarHeight = navbar.offsetHeight; // ความสูงของ Navbar

    window.addEventListener('scroll', function() {
      const currentScroll = window.pageYOffset || document.documentElement.scrollTop;

      // ถ้าเลื่อนลงและตำแหน่ง scroll มากกว่าความสูงของ Navbar
      if (currentScroll > lastScrollTop && currentScroll > navbarHeight) {
        // เพิ่ม class เพื่อซ่อน Navbar
        navbar.classList.add('navbar-hidden');
      } else {
        // ถ้าเลื่อนขึ้น
        // ลบ class เพื่อแสดง Navbar
        navbar.classList.remove('navbar-hidden');
      }

      // อัปเดตตำแหน่ง scroll ล่าสุด
      lastScrollTop = currentScroll <= 0 ? 0 : currentScroll;
    }, false);


    // --- To Top Button ---
    const toTopBtn = document.getElementById('toTopBtn');
    window.addEventListener('scroll', function() {
      if (window.scrollY > 200) {
        toTopBtn.classList.add('show');
      } else {
        toTopBtn.classList.remove('show');
      }
    });
    toTopBtn.addEventListener('click', function() {
      window.scrollTo({
        top: 0,
        behavior: 'smooth'
      });
    });

    // --- Sidebar Controls ---
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileSidebar = document.getElementById('mobileSidebar');

    if (openSidebarBtn) {
      openSidebarBtn.addEventListener('click', () => {
        scrollPosition = window.pageYOffset || document.documentElement.scrollTop;
        mobileSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        // เพิ่มคลาสทั้งที่ html และ body
        document.body.classList.add('body-no-scroll');
        document.body.style.top = `-${scrollPosition}px`;
      });
    }

    const closeSidebar = () => {
      mobileSidebar.classList.remove('show');
      sidebarOverlay.classList.remove('show');
      // ลบคลาสทั้งจาก html และ body
      document.body.classList.remove('body-no-scroll');
      document.body.style.top = '';
      window.scrollTo(0, scrollPosition);
    };

    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    // --- Search Form Submission ---
    function handleSearch(event) {
      event.preventDefault();
      const searchInput = event.target.querySelector('[name="search"]');
      if (searchInput && searchInput.value.trim() !== '') {
        location.href = './?p=products&search=' + encodeURIComponent(searchInput.value.trim());
      }
    }
    const desktopSearchForm = document.getElementById('desktop-search-form');
    const sidebarSearchForm = document.getElementById('sidebar-search-form');
    if (desktopSearchForm) desktopSearchForm.addEventListener('submit', handleSearch);
    if (sidebarSearchForm) sidebarSearchForm.addEventListener('submit', handleSearch);

    // --- Guest Cart Badge Update (ถ้ามี) ---
    function update_guest_cart_badge() {
      const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
      const totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty, 10), 0);
      const badge = document.getElementById('guest_cart_count');
      if (badge) {
        if (totalQty > 0) {
          badge.textContent = totalQty;
          badge.classList.remove('d-none');
        } else {
          badge.classList.add('d-none');
        }
      }
    }

    if (typeof update_guest_cart_badge === "function") {
      update_guest_cart_badge();
    }

    const wrapper = document.querySelector('.dropdown-columns-wrapper');
    if (wrapper) {
      const columns = wrapper.querySelectorAll('.dropdown-column');
      if (columns.length > 5) {
        wrapper.classList.add('multi-columns');
      }
    }


    $(document).ready(function() {
      $('.submenu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var target = $(this).data('target');
        if (target) {
          $(target).collapse('toggle');
        }
      });

      $('#navbarDropdown').on('hide.bs.dropdown', function(e) {
        if ($('.collapse.show').length) {
          e.preventDefault();
        }
      });
    });

    const productData = JSON.parse(document.getElementById('product-data-json').textContent);

    // 2. ดึง Element ที่เกี่ยวข้อง
    const panelsWrapper = document.querySelector('.sidebar-panels-wrapper');
    const typeItems = document.querySelectorAll('.type-item');
    const backToMainMenuBtn = document.querySelector('.back-to-main-menu');
    const submenuTitle = document.querySelector('.submenu-title');
    const submenuList = document.querySelector('.submenu-list');

    // 3. สร้าง Event Listener ให้กับทุก "ประเภทสินค้า"
    typeItems.forEach(item => {
      item.addEventListener('click', function(e) {
        e.preventDefault();

        // หา ID ของประเภทที่ถูกคลิก
        const typeId = this.dataset.typeId;
        const typeData = productData[typeId];

        if (typeData && typeData.categories) {
          // อัปเดตชื่อหัวข้อของ Submenu
          submenuTitle.innerHTML = `<a href="${_base_url_}?p=products&tid=${typeId}" class="text-dark font-weight-bold text-decoration-none d-block" style ="font-size : 20px">${typeData.name}</a>`;


          // เคลียร์รายการหมวดหมู่เดิมทิ้ง
          submenuList.innerHTML = '';

          // สร้างรายการหมวดหมู่ใหม่
          typeData.categories.forEach(category => {
            const link = document.createElement('a');
            link.href = `${_base_url_}?p=products&cid=${category.id}`;
            link.className = 'nav-link';
            link.textContent = category.name;
            submenuList.appendChild(link);
          });

          // สั่งให้ panel สไลด์โดยการเพิ่ม class
          panelsWrapper.classList.add('show-submenu');
        }
      });
    });

    // 4. สร้าง Event Listener ให้กับปุ่ม "กลับ"
    backToMainMenuBtn.addEventListener('click', function(e) {
      e.preventDefault();
      // สั่งให้ panel สไลด์กลับโดยการลบ class
      panelsWrapper.classList.remove('show-submenu');
    });
  });
</script>