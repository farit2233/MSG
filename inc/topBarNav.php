<nav class="navbar navbar-expand-lg navbar-dark navbar-msg navbar-shown">
  <div class="container container-wide px-0 px-lg-0">

    <a class="navbar-brand" href="./">
      <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="80" height="80" class="d-inline-block align-top" alt="" loading="lazy">
      <?php echo $_settings->info('short_name') ?>
    </a>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
        <li class="nav-item"><a class="nav-link text-white fos" href="./?p=products">สินค้าทั้งหมด</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle text-white fos" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            หมวดหมู่
          </a>
          <div class="dropdown-menu ndc p-2" aria-labelledby="navbarDropdown">
            <div class="dropdown-columns-wrapper">
              <?php
              $type_qry = $conn->query("SELECT * FROM `product_type` WHERE `status`=1 AND `delete_flag`=0 ORDER BY `date_created` ASC");
              while ($type_row = $type_qry->fetch_assoc()):
                $tid = $type_row['id'];
                $category_qry = $conn->query("SELECT * FROM `category_list` WHERE `status`=1 AND `delete_flag`=0 AND `product_type_id`={$tid} ORDER BY `date_created` ASC");
              ?>
                <div class="dropdown-column">
                  <?php if ($category_qry->num_rows > 0): ?>
                    <div class="submenu-wrapper">
                      <div class="d-flex justify-content-between align-items-center">
                        <a class="dropdown-item flex-grow-1" href="<?= base_url . "?p=products&tid={$tid}" ?>">
                          <?= htmlspecialchars($type_row['name']) ?>
                        </a>
                        <a class="submenu-toggle" href="#" data-toggle="collapse" data-target="#collapse-cat-<?= $tid ?>" role="button" aria-expanded="false" aria-controls="collapse-cat-<?= $tid ?>">
                          &gt;
                        </a>
                      </div>
                      <div class="collapse" id="collapse-cat-<?= $tid ?>">
                        <div class="submenu-items pl-3">
                          <?php while ($cat_row = $category_qry->fetch_assoc()): ?>
                            <a class="dropdown-item sub-item" href="<?= base_url . "?p=products&cid={$cat_row['id']}" ?>">
                              <?= htmlspecialchars($cat_row['name']) ?>
                            </a>
                          <?php endwhile; ?>
                        </div>
                      </div>
                    </div>
                  <?php else: ?>
                    <a class="dropdown-item" href="<?= base_url . "?p=products&tid={$tid}" ?>">
                      <?= htmlspecialchars($type_row['name']) ?>
                    </a>
                  <?php endif; ?>
                </div>
              <?php endwhile; ?>
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
          <?php
          $has_new_notif = false;
          if ($_settings->userdata('id') && $_settings->userdata('login_type') == 2) {
            $check_unseen = $conn->query("SELECT 1 FROM order_list WHERE customer_id = '{$_settings->userdata('id')}' AND is_seen = 0 LIMIT 1");
            $has_new_notif = $check_unseen->num_rows > 0;
          }
          ?>
          <a href="/?p=orders" class="text-white p-0 icon-alert notif-bell" title="แจ้งเตือน">
            <i class="fa fa-bell icon-size position-relative">
              <?php if ($has_new_notif): ?>
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
              <?php endif; ?>
            </i>
          </a>
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
        <li class="nav-item d-flex d-md-none align-items-center">
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
<div class="modal fade" id="mobileSearchModal" tabindex="-1" aria-labelledby="mobileSearchModalLabel" aria-hidden="true">
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


    <nav class="nav flex-column pt-3">
      <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
        <a class="nav-link" href="<?= base_url . '?p=cart_list' ?>"><i class="fa fa-shopping-cart"></i> ตะกร้าของฉัน</a>
        <a class="nav-link" href="<?= base_url . '?p=orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
        <div class="dropdown-divider"></div>
      <?php endif; ?>
      <a class="nav-link" href="./"><i class="fa fa-home"></i> หน้าหลัก</a>
      <a class="nav-link" href="./?p=products"><i class="fa fa-box-open"></i> สินค้าทั้งหมด</a>
      <div class="dropdown-divider"></div>
      <h6 class="px-3 mt-2 mb-1 text-muted">หมวดหมู่สินค้า</h6>
      <?php
      // --- โค้ดสำหรับแสดงผลใน Sidebar ---
      // ใช้ข้อมูล $product_structure ที่ดึงมาแล้วจากด้านบน หรือจะดึงใหม่ก็ได้
      // ในที่นี้จะใช้ข้อมูลเดิมเพื่อประสิทธิภาพ
      foreach ($product_structure as $type_id => $type_data):
        if (!empty($type_data['categories'])):
      ?>
          <div class="sidebar-menu-group">
            <a class="nav-link d-flex justify-content-between align-items-center" data-toggle="collapse" href="#collapse-<?= $type_id ?>" role="button" aria-expanded="false" aria-controls="collapse-<?= $type_id ?>">
              <span><?= htmlspecialchars($type_data['name']) ?></span>
              <i class="fas fa-chevron-down fa-xs"></i>
            </a>
            <div class="collapse" id="collapse-<?= $type_id ?>">
              <div class="sidebar-submenu">
                <?php foreach ($type_data['categories'] as $category): ?>
                  <a class="nav-link" href="<?= base_url . "?p=products&cid={$category['id']}" ?>"><?= htmlspecialchars($category['name']) ?></a>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
      <?php
        endif;
      endforeach;
      ?>
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
</div>

<button id="toTopBtn" title="กลับขึ้นบน"><i class="fas fa-chevron-up"></i></button>

<script>
  function goToUserPage() {
    window.location.href = "<?= base_url . '?p=user' ?>";
  }
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
        mobileSidebar.classList.add('show');
        sidebarOverlay.classList.add('show');
        // เพิ่มคลาสทั้งที่ html และ body
        document.documentElement.classList.add('body-no-scroll'); // <html>
        document.body.classList.add('body-no-scroll');
      });
    }

    const closeSidebar = () => {
      mobileSidebar.classList.remove('show');
      sidebarOverlay.classList.remove('show');
      // ลบคลาสทั้งจาก html และ body
      document.documentElement.classList.remove('body-no-scroll'); // <html>
      document.body.classList.remove('body-no-scroll');
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
    // ตรวจสอบว่ามีฟังก์ชันนี้ก่อนเรียกใช้
    if (typeof update_guest_cart_badge === "function") {
      update_guest_cart_badge();
    }
    $(document).ready(function() {
      // ป้องกัน dropdown หลักปิดเมื่อคลิก submenu-toggle
      $('.submenu-toggle').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var target = $(this).data('target');
        if (target) {
          $(target).collapse('toggle');
        }
      });

      // ป้องกัน dropdown หลักปิดถ้ามี submenu เปิดอยู่
      $('#navbarDropdown').on('hide.bs.dropdown', function(e) {
        if ($('.collapse.show').length) {
          e.preventDefault();
        }
      });
    });

  });
</script>