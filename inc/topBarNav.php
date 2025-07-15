 <link rel="stylesheet" href="assets/css/navbar.css">
 <style>
   /* Base and Original Styles */
   .sbr {
     font-size: 14px;
     border-radius: 13px;
     max-width: 13rem;
   }

   .fos {
     font-size: 18px;
   }

   .fos:hover {
     color: #ffffff;
     text-shadow: 0 0 5px rgba(255, 255, 255, 0.6), 0 0 10px rgba(255, 255, 255, 0.3);
   }

   .icon-size {
     font-size: 22px;
     margin-left: 10px;
     margin-right: 4px;
   }

   .icon-alert {
     cursor: pointer;
     margin-right: 5px;
   }

   .user-img {
     height: 27px;
     width: 27px;
     object-fit: cover;
     margin-right: 8px;
     vertical-align: middle;
   }

   .user-dd:hover {
     color: #fffa !important
   }

   .user-img-nav {
     height: 32px;
     width: 32px;
     object-fit: cover;
     border-radius: 50%;
     margin-right: 8px;
   }

   .user-dd-toggle {
     background: transparent;
     border: none;
     box-shadow: none;
     color: white;
     padding: 0;
     margin: 0;
     font-size: 1rem;
     display: flex;
     align-items: center;
     cursor: pointer;
     gap: 0.5rem;
   }

   .user-dd-toggle:focus {
     outline: none;
     box-shadow: none;
   }

   .user-name {
     white-space: nowrap;
     max-width: 100px;
     overflow: hidden;
     text-overflow: ellipsis;
   }

   .user-dropdown-menu {
     border-radius: 12px !important;
     min-width: 180px;
   }

   .navbar {
     padding-top: 2.5rem;
     padding-bottom: 2.5rem;
     height: 50px;
     font-size: 14px;
     display: flex !important;
     align-items: center !important;
     /* จัดกึ่งกลางแนวตั้ง */
   }

   .navbar .container-wide {
     display: flex;
     align-items: center;
     height: 100%;
     padding-left: 0;
     padding-right: 0;
   }

   .navbar-toggler {
     margin: 0;
     padding: 0;
   }

   .navbar-brand img {
     margin: 0;
     padding: 0;
     height: 60px;
     width: 60px;
     /* หรือสูงตามต้องการ */
   }

   .navbar-brand,
   .navbar-toggler {
     display: flex;
     align-items: center;
     height: 100%;
     /* เต็มความสูงของ navbar */
   }


   /* --- Navbar Styles --- */
   .navbar-msg {
     background: linear-gradient(135deg, #16542b 0%, #2f6828 40%, #3f7b25 60%, #f57421 95%);
     color: white;
     box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
     /* ทำให้ Navbar อยู่ด้านบนสุดและยึดตำแหน่ง */
     position: fixed;
     top: 0;
     width: 100%;
     z-index: 999;
     /* เพิ่ม transition เพื่อให้การซ่อน/แสดง плавный */
     transition: top 0.3s ease-in-out;
   }

   .navbar-hidden {
     /* ย้าย Navbar ออกไปนอกจอ (ซ่อนด้านบน) */
     top: -100px;
     /* ควรมากกว่าหรือเท่ากับความสูงของ Navbar */
   }

   .bg-foot-msg {
     background: #16542b !important;
   }


   .ndc {
     left: 0;
     right: 0;
     top: 100%;
     border-radius: 13px;
     padding: 1rem 2rem;
     box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
     width: 50vw;
     background: white;
     z-index: 10;
   }

   .ndi {
     white-space: normal;
     break-inside: avoid;
     width: 33.33%;
     padding: 1rem;
   }

   .cart-badge {
     position: absolute;
     top: -5px;
     right: -10px;
     background-color: #f57421;
     color: white;
     font-size: 0.7rem;
     padding: 2px 6px;
     border-radius: 50%;
     font-weight: bold;
   }

   #toTopBtn {
     position: fixed;
     bottom: 30px;
     right: 30px;
     z-index: 999;
     background-color: #f57421;
     color: white;
     border: none;
     outline: none;
     width: 45px;
     height: 45px;
     border-radius: 50%;
     font-size: 20px;
     cursor: pointer;
     box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
     transition: opacity 0.3s ease-in-out;
     opacity: 0;
     visibility: hidden;
   }

   #toTopBtn.show {
     opacity: 1;
     visibility: visible;
   }

   #toTopBtn:hover {
     background-color: #d85f1a;
   }

   html.body-no-scroll,
   body.body-no-scroll {
     overflow: hidden;
     /* position: relative; อาจช่วยในบางกรณี */
   }

   .sidebar-overlay {
     position: fixed;
     top: 0;
     left: 0;
     width: 100%;
     height: 100%;
     background-color: rgba(0, 0, 0, 0.5);
     z-index: 1040;
     opacity: 0;
     visibility: hidden;
     transition: opacity 0.3s ease-in-out, visibility 0.3s ease-in-out;
   }

   .sidebar-overlay.show {
     opacity: 1;
     visibility: visible;
   }

   .mobile-sidebar {
     position: fixed;
     top: 0;
     right: 0;
     height: 100%;
     width: 311px;
     background: #fff;
     z-index: 1050;
     transform: translateX(100%);
     /* <-- เปลี่ยนเป็นค่าบวก */
     transition: transform 0.3s ease-in-out;
     display: flex;
     flex-direction: column;
     box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
   }

   .mobile-sidebar.show {
     transform: translateX(0);
   }

   .sidebar-header {
     padding: 23px 24px;
     display: flex;
     justify-content: space-between;
     align-items: center;
     border-bottom: 1px solid #e9ecef;
     background: linear-gradient(135deg, #16542b 0%, #2f6828 100%);
     color: white;
   }

   .sidebar-header h5 {
     margin: 0;
   }

   .sidebar-close-btn {
     font-size: 2rem;
     color: white;
     background: none;
     border: none;
     cursor: pointer;
     line-height: 1;
   }

   .sidebar-body {
     flex-grow: 1;
     overflow-y: auto;
   }

   .sidebar-body .sidebar-search-form {
     padding: 1rem 1.5rem;
     border-bottom: 1px solid #e9ecef;
   }

   .sidebar-body .sidebar-search-form .form-control {
     border-radius: 13px;
   }

   .sidebar-body .nav-link {
     display: flex;
     align-items: center;
     gap: 1rem;
     padding: 0.75rem 1.5rem;
     color: #333;
     font-size: 1rem;
     text-decoration: none;
     transition: background-color 0.2s;
   }

   .sidebar-body .nav-link i {
     width: 20px;
     text-align: center;
     color: #f57421;
   }

   .sidebar-body .nav-link:hover {
     background-color: #f8f9fa;
   }

   .sidebar-body .dropdown-divider {
     margin: 0.5rem 0;
   }
 </style>

 <nav class="navbar navbar-expand-lg navbar-dark navbar-msg navbar-shown">
   <div class="container container-wide px-0 px-lg-0">

     <a class="navbar-brand" href="./">
       <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="80" height="80" class="d-inline-block align-top" alt="" loading="lazy">
       <?php echo $_settings->info('short_name') ?>
     </a>

     <div class="collapse navbar-collapse" id="navbarSupportedContent">
       <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
         <li class="nav-item"><a class="nav-link text-white fos" href="./?p=products">สินค้าทั้งหมด</a></li>
         <li class="nav-item dropdown position-relative">
           <a class="nav-link dropdown-toggle text-white fos" href="javascript:void(0)" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             หมวดหมู่
           </a>
           <div class="dropdown-menu ndc" aria-labelledby="navbarDropdown" tabindex="-1">
             <div class="container-fluid">
               <?php
                $category_qry = $conn->query("SELECT * FROM `category_list` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `id` ASC");
                while ($row = $category_qry->fetch_assoc()):
                ?>
                 <a class="dropdown-item dfs ndi" href="<?= base_url . "?p=products&cid={$row['id']}" ?>"><?= $row['name'] ?></a>
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
           <form class="form-inline d-flex align-items-center" id="desktop-search-form" method="get" action="./" style="gap: 0.5rem;">
             <input class="form-control sbr mr-sm-2" type="search" placeholder="ค้นหาสินค้า..." name="search" required>
             <button class="btn button1 searchcolor" title="ค้นหาสินค้า" type="submit"><i class="fas fa-search"></i></button>
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

     <button class="navbar-toggler d-lg-none" type="button" id="openSidebarBtn">
       <span class="navbar-toggler-icon"></span>
     </button>

   </div>
 </nav>

 <div class="sidebar-overlay" id="sidebarOverlay"></div>
 <div class="mobile-sidebar" id="mobileSidebar">
   <div class="sidebar-header">
     <h5>เมนู</h5>
     <button class="sidebar-close-btn" id="closeSidebarBtn">&times;</button>
   </div>
   <div class="sidebar-body">
     <div class="sidebar-search-form">
       <form id="sidebar-search-form" method="get" action="./">
         <input class="form-control" type="search" placeholder="ค้นหาสินค้า..." name="search" required>
       </form>
     </div>
     <nav class="nav flex-column pt-3">
       <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
         <a class="nav-link" href="<?= base_url . '?p=user' ?>"><i class="fa fa-user"></i> บัญชีของฉัน</a>
         <a class="nav-link" href="<?= base_url . '?p=cart_list' ?>"><i class="fa fa-shopping-cart"></i> ตะกร้าของฉัน</a>
         <a class="nav-link" href="<?= base_url . '?p=orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
         <div class="dropdown-divider"></div>
       <?php endif; ?>
       <a class="nav-link" href="./"><i class="fa fa-home"></i> หน้าหลัก</a>
       <a class="nav-link" href="./?p=products"><i class="fa fa-box-open"></i> สินค้าทั้งหมด</a>
       <div class="dropdown-divider"></div>
       <h6 class="px-3 mt-2 mb-1 text-muted">หมวดหมู่สินค้า</h6>
       <?php
        $category_qry_side = $conn->query("SELECT * FROM `category_list` WHERE `status` = 1 AND `delete_flag` = 0 ORDER BY `name` ASC");
        while ($row = $category_qry_side->fetch_assoc()):
        ?>
         <a class="nav-link" href="<?= base_url . "?p=products&cid={$row['id']}" ?>"><?= $row['name'] ?></a>
       <?php endwhile; ?>
       <div class="dropdown-divider"></div>
       <a class="nav-link" href="./?p=help"><i class="fa fa-question-circle"></i> ช่วยเหลือ</a>
       <a class="nav-link" href="./?p=about"><i class="fa fa-info-circle"></i> เกี่ยวกับเรา</a>
       <a class="nav-link" href="./?p=contact"><i class="fa fa-envelope"></i> ติดต่อเรา</a>
       <div class="dropdown-divider"></div>
       <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
         <a class="nav-link" href="<?= base_url . '/classes/Login.php?f=logout_customer' ?>"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
       <?php else: ?>
         <a class="nav-link" href="./login.php"><i class="fa fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
         <a class="nav-link" href="./register.php"><i class="fa fa-user-plus"></i> สมัครสมาชิก</a>
       <?php endif; ?>
       <a class="nav-link" href="./admin"><i class="fa fa-user-tie"></i> Admin Panel</a>
     </nav>
   </div>
 </div>

 <button id="toTopBtn" title="กลับขึ้นบน"><i class="fas fa-chevron-up"></i></button>

 <script>
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
   });
 </script>