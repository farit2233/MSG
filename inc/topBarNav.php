<?php
$product_structure = [];

$type_qry = $conn->query("SELECT * FROM `product_type` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `date_created` ASC");
$product_structure = [];
$other_types = [];
while ($type_row = $type_qry->fetch_assoc()) {
  $tid = $type_row['id'];
  $is_other = $type_row['other']; // ตรวจสอบค่า other
  $product_structure[$tid] = [
    'name' => $type_row['name'],
    'categories' => []
  ];

  // ดึงข้อมูล category_list
  $cat_qry = $conn->query("SELECT * FROM `category_list` WHERE `status` = 1 AND `delete_flag` = 0 AND `product_type_id` = {$tid} ORDER BY `date_created` ASC");
  while ($cat_row = $cat_qry->fetch_assoc()) {
    $product_structure[$tid]['categories'][] = [
      'id' => $cat_row['id'],
      'name' => $cat_row['name']
    ];
  }

  // ถ้าเป็น product_type ที่มี other = 1 ให้เก็บไว้ใน $other_types
  if ($is_other == 1) {
    $other_types[$tid] = $product_structure[$tid];
    unset($product_structure[$tid]);
  }
}

// รวม product_type ที่มี other = 1 ไว้ท้ายสุด
$product_structure = $product_structure + $other_types;
$promotion_structure = [];

// Query เพื่อดึงข้อมูลของหมวดหมู่โปรโมชัน
$type_qry = $conn->query("SELECT * FROM `promotion_category` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `date_created` ASC");
while ($type_row = $type_qry->fetch_assoc()) {
  $pcid = $type_row['id'];

  // ตรวจสอบวันหมดอายุของโปรโมชัน
  $current_time = time(); // เวลาปัจจุบัน
  $pro_qry = $conn->query("SELECT * FROM `promotions_list` 
                           WHERE `status` = 1 AND `delete_flag` = 0 AND `promotion_category_id` = {$pcid} 
                           ORDER BY `date_created` ASC");

  $has_active_promotions = false; // ตัวแปรเพื่อตรวจสอบว่ามีโปรโมชันที่ยังคงใช้งานได้หรือไม่

  while ($pro_row = $pro_qry->fetch_assoc()) {
    // ตรวจสอบวันหมดอายุของแต่ละโปรโมชัน
    $end_date = strtotime($pro_row['end_date']); // วันหมดอายุ
    if ($end_date >= $current_time) {
      // ถ้าโปรโมชันยังไม่หมดอายุ ให้เพิ่มลงใน array
      $promotion_structure[$pcid]['categories'][] = [
        'id' => $pro_row['id'],
        'name' => $pro_row['name'],
        'discount_value' => $pro_row['discount_value'],
        'type' => $pro_row['type']
      ];
      $has_active_promotions = true; // ตั้งค่าถ้ามีโปรโมชันที่ยังใช้งานได้
    }
  }

  // ถ้ามีโปรโมชันที่ยังใช้งานได้ จะเพิ่มหมวดหมู่
  if ($has_active_promotions) {
    $promotion_structure[$pcid]['name'] = $type_row['name'];
  }
}

?>
<nav class="navbar navbar-expand-lg navbar-msg">
  <div class="container container-wide px-0 px-lg-0">

    <a class="navbar-brand" href="./">
      <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="80" height="80" class="d-inline-block align-top" alt="" loading="lazy">
    </a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">

        <li class="nav-item nav-item-hoverable">
          <a class="nav-link text-white fos" href="#">ใหม่และโดดเด่น</a>
          <div class="mega-menu-content">
            <div class="mega-box">
              <div class="content">
                <div class="row">
                  <?php foreach ($promotion_structure as $pid => $type_data): ?>
                    <?php if (!empty($type_data['categories'])): ?>
                      <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <ul>
                          <div onclick="return false;" class="text-decoration-none">
                            <h5 class="list-header"><?= htmlspecialchars($type_data['name']) ?></h5>
                          </div>
                          <hr class="mt-1 mb-2">
                          <?php foreach ($type_data['categories'] as $cat_row): ?>
                            <li>
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

        <li class="nav-item nav-item-hoverable">
          <a class="nav-link text-white fos" href="#">ประเภทสินค้า</a>
          <div class="mega-menu-content">
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

        <li class="nav-item"><a class="nav-link text-white fos" href="./?p=promotions">โปรโมชัน</a></li>
        <li class="nav-item"><a class="nav-link text-white fos" href="./?p=contact">ติดต่อเรา</a></li>
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

              <a href="?p=user/orders" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน" data-toggle="dropdown" id="notifDropdown">
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
                      <p>กรุณาเข้าสู่ระบบเพื่อดูแจ้งเตือน</p>
                      <a href="../login.php">เข้าสู่ระบบ</a>
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
                      return ['ยังไม่ชำระเงิน', 'รอตรวจสอบ', 'ชำระเงินแล้ว', 'ชำระเงินล้มเหลว', 'กำลังยกเลิกคำสั่งซื้อ', 'กำลังคืนเงิน', 'คืนเงินแล้ว'][$status] ?? 'N/A';
                    }
                    function get_delivery_text($status)
                    {
                      return ['ตรวจสอบคำสั่งซื้อ', 'กำลังเตรียมของ', 'แพ็กของแล้ว', 'กำลังจัดส่ง', 'จัดส่งสำเร็จ', 'จัดส่งไม่สำเร็จ', 'กำลังยกเลิกคำสั่งซื้อ', 'คืนของระหว่างทาง', 'กำลังคืนสินค้า', 'คืนของสำเร็จ', 'ยกเลิกแล้ว'][$status] ?? 'N/A';
                    }
                    while ($notif = $notif_qry->fetch_assoc()): ?>

                      <a class="dropdown-item d-flex align-items-start gap-2" href=" ./?p=user/orders">
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
            <div class="profile-menu">

              <button type="button" id="profileMenuToggle" class="profile-menu_toggle">
                <img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="profile-menu_avatar" alt="User">
                <span class="profile-menu_username d-none d-lg-inline"><?= ucwords($_settings->userdata('firstname')) ?></span>
                <span class="profile-menu_caret fas fa-caret-down"></span>
              </button>

              <div id="profileMenuContent" class="profile-menu__content">
                <a class="profile-menu_item" href="<?= base_url . '?p=user' ?>">
                  <i class="fa-solid fa-user-circle"></i>
                  <span>บัญชีของฉัน</span>
                </a>
                <a class="profile-menu_item" href="<?= base_url . '?p=cart_list' ?>">
                  <i class="fa fa-basket-shopping"></i>
                  <span>ตะกร้าของฉัน</span>
                </a>
                <a class="profile-menu_item" href="<?= base_url . '?p=user/slip_payment' ?>">
                  <i class="fa-solid fa-receipt"></i>
                  <span>แจ้งยอดชำระเงิน</span>
                </a>
                <a class="profile-menu_item" href="<?= base_url . '?p=user/orders' ?>">
                  <i class="fa fa-truck"></i>
                  <span>ประวัติการสั่งซื้อ</span>
                </a>
                <div class="profile-menu_divider"></div>
                <a class="profile-menu_item" href="<?= base_url . '/classes/Login.php?f=logout_customer' ?>">
                  <i class="fa fa-sign-out-alt"></i>
                  <span>ออกจากระบบ</span>
                </a>
              </div>
            </div>
          <?php else: ?>
            <div class="account-menu">
              <button type="button" id="accountMenuToggle" class="account-menu_toggle">
                <i class="fas fa-user-circle icon-acc-size" title="แอคเคานท์"></i>
                <span class="account-menu_caret fas fa-caret-down"></span>
              </button>

              <div id="accountMenuContent" class="account-menu_content" role="menu">
                <a class="account-menu_item" href="./login.php">
                  <i class="fa fa-sign-in-alt"></i>
                  <span>เข้าสู่ระบบ</span>
                </a>
                <div class="account-menu_divider"></div>
                <a class="account-menu_item" href="./register.php">
                  <i class="fa fa-user-plus"></i>
                  <span>สมัครสมาชิก</span>
                </a>
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
          <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
            <a href="./?p=user/orders" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน">
              <i class="fa fa-bell icon-size position-relative">
                <?php if ($has_new_notif): ?>
                  <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                <?php endif; ?>
              </i>
            </a>
          <?php else: ?>
            <a href="./login.php" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน">
              <i class="fa fa-bell icon-size position-relative">
                <?php if ($has_new_notif): ?>
                  <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                <?php endif; ?>
              </i>
            </a>
          <?php endif; ?>
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
      <button class="hamburger-button" type="button" id="openSidebarBtn" aria-label="Toggle Menu">
        <span class="hamburger-box">
          <span class="hamburger-inner"></span>
        </span>
      </button>
    </div>
  </div>
</nav>

<!-- Modal ช่องค้นหา -->
<div class="modal fade" id="mobileSearchModal" tabindex="-5" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-search modal-md mt-5">
    <div class="modal-content modal-content-search minimalist-search-modal">
      <div class="modal-body">
        <form method="get" action="./" class="search-input-minimal">
          <input type="text" name="search" class="form-control " placeholder="ค้นหา..." required autofocus>
          <button type="submit" title="ค้นหา">
            <i class="fa fa-search search-icon"></i>
          </button>

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
            <a class="nav-link" href="<?= base_url . '?p=cart_list' ?>"> <i class="fa fa-basket-shopping"></i> ตะกร้าของฉัน</a>
            <a class="nav-link" href="<?= base_url . '?p=user/slip_payment' ?>"><i class="fa-solid fa-receipt"></i> แจ้งยอดชำระเงิน</a>
            <a class="nav-link" href="<?= base_url . '?p=user/orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
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
          <a class="nav-link" href="./?p=promotions"><i class="fa-solid fa-ticket"></i> โปรโมชัน</a>
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
      if (window.innerWidth <= 1023) {
        e.preventDefault(); // กัน dropdown เด้ง
        window.location.href = "../login.php";
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



    const accountMenu = document.querySelector('.account-menu');
    const toggleButton = document.getElementById('accountMenuToggle');
    const menuContent = document.getElementById('accountMenuContent');

    // ตรวจสอบว่ามีปุ่มนี้ในหน้าเว็บจริง
    if (toggleButton) {

      // เมื่อ "คลิก" ที่ปุ่ม
      toggleButton.addEventListener('click', function(event) {
        // หยุดไม่ให้ event การคลิกกระจายไปที่อื่น
        event.stopPropagation();

        // สลับการใช้งานคลาส 'is-active' ที่กล่องครอบ
        accountMenu.classList.toggle('is-active');
      });

      // เมื่อ "คลิก" ที่ส่วนใดๆ ของหน้าเว็บ
      document.addEventListener('click', function(event) {
        // ถ้าพื้นที่ที่คลิกไม่ได้อยู่ในเมนู และเมนูกำลังเปิดอยู่
        if (!accountMenu.contains(event.target) && accountMenu.classList.contains('is-active')) {
          // ให้เอาคลาส 'is-active' ออก (เพื่อปิดเมนู)
          accountMenu.classList.remove('is-active');
        }
      });
    }

    // --- Sidebar Controls ---
    const openSidebarBtn = document.getElementById('openSidebarBtn');
    const closeSidebarBtn = document.getElementById('closeSidebarBtn');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileSidebar = document.getElementById('mobileSidebar');

    if (openSidebarBtn) {
      openSidebarBtn.addEventListener('click', () => {
        // 1. บันทึกตำแหน่ง scroll ปัจจุบัน
        scrollPosition = window.pageYOffset || document.documentElement.scrollTop;

        mobileSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');

        // 2. ล็อก Body และใช้ `top` ติดลบเพื่อคงตำแหน่งเดิมไว้
        document.body.classList.add('body-no-scroll');
        document.body.style.top = `-${scrollPosition}px`;
      });
    }

    const closeSidebar = () => {
      mobileSidebar.classList.remove('show');
      sidebarOverlay.classList.remove('show');

      // 3. ปลดล็อก Body และล้างค่า `top`
      document.body.classList.remove('body-no-scroll');
      document.body.style.top = '';

      // 4. สั่งให้เลื่อนกลับไปยังตำแหน่งเดิมที่บันทึกไว้
      window.scrollTo(0, scrollPosition);
    };

    if (closeSidebarBtn) closeSidebarBtn.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    const profileMenu = document.querySelector('.profile-menu');
    const profileMenuToggle = document.getElementById('profileMenuToggle');

    // ตรวจสอบว่ามีเมนูนี้ในหน้าเว็บจริง (เผื่อเป็นหน้า guest)
    if (profileMenu && profileMenuToggle) {

      // เมื่อ "คลิก" ที่ปุ่ม
      profileMenuToggle.addEventListener('click', function(event) {
        event.stopPropagation(); // หยุดไม่ให้ event กระจายไปที่ document
        profileMenu.classList.toggle('is-active');
      });

      // เมื่อ "คลิก" ที่ส่วนใดๆ ของหน้าเว็บ
      document.addEventListener('click', function(event) {
        // ถ้าเมนูกำลังเปิดอยู่ และไม่ได้คลิกภายในตัวเมนู
        if (profileMenu.classList.contains('is-active') && !profileMenu.contains(event.target)) {
          profileMenu.classList.remove('is-active'); // ให้ปิดเมนู
        }
      });
    }

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
<script>
  $(document).ready(function() {
    // เช็คว่าเป็น Guest หรือไม่ (ถ้าไม่มี id สมาชิก)
    var isGuest = '<?= !isset($_settings) || empty($_settings->userdata('id')) ? 1 : 0 ?>';

    if (isGuest == '1') {
      // ถ้าเป็น Guest ให้ดึงยอดจาก LocalStorage มาแสดง
      update_guest_cart_count();
    }
  });

  function update_guest_cart_count() {
    // ดึงข้อมูลตะกร้าจาก LocalStorage
    var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];

    // รวมจำนวนสินค้า (qty) ทั้งหมด
    var totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty), 0);

    // จุดที่แสดงตัวเลข (รองรับทั้ง Mobile และ PC)
    // ต้องมั่นใจว่าใน HTML มี id="guest_cart_count_mobile" หรือ class="cart-count" อยู่
    var cartSelectors = '#guest_cart_count_mobile, #guest_cart_count, .cart-count';

    // อัปเดตตัวเลข
    $(cartSelectors).text(totalQty);

    // ซ่อน/แสดง Badge
    if (totalQty > 0) {
      $(cartSelectors).removeClass('d-none');
    } else {
      $(cartSelectors).addClass('d-none');
    }
  }
</script>