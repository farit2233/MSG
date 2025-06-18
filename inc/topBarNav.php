 <link rel="stylesheet" href="assets/css/navbar.css">
 <style>
   .navbar {
     padding-top: 0.3rem;
     padding-bottom: 0.3rem;
   }

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
     text-shadow: 0 0 5px rgba(255, 255, 255, 0.6),
       0 0 10px rgba(255, 255, 255, 0.3);
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


   .navbar-msg {
     background: #16542b;
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

   .notif-thumb {
     width: 60px;
     height: 60px;
     object-fit: cover;
     flex-shrink: 0;
     border-radius: 4px;
   }

   .text-truncate {
     display: inline-block;
     max-width: 400px;
     overflow: hidden;
     white-space: nowrap;
     text-overflow: ellipsis;
     vertical-align: middle;
     line-height: 1.2;
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

   @media (max-width: 1200px) {
     .navbar-nav .nav-link {
       font-size: 14px;
       padding: 0.5rem 0.75rem;
     }

     .sbr {
       font-size: 14px;
       border-radius: 13px;
       max-width: 12rem;
     }

     .container-wide {
       max-width: 100%;
       padding-left: 1rem;
       padding-right: 1rem;
     }

     .dropdown-menu {
       width: 100%;
       min-width: unset;
       font-size: 1rem;
       /* เพิ่มขนาดตัวอักษร */
       padding: 1rem;
     }

     .dropdown-menu-notify {
       display: none;
     }

     .user-dropdown-menu {
       width: 12rem;
       right: 0 !important;
       left: auto !important;
     }

     .user-dropdown-wrapper {
       margin-left: auto !important;
     }

     .navbar .d-flex.align-items-center {
       justify-content: flex-end;
       gap: 1rem;
       margin-top: 0.5rem;
     }

     .icon-size {
       font-size: 20px !important;
       margin-top: 0.5rem !important;
       margin-left: 4px;
       margin-right: 0px;
     }

     .cart-badge {
       font-size: 12px;
       margin-left: 1rem;
       margin-top: 0.5rem;
     }
   }
 </style>
 <nav class="navbar navbar-expand-lg navbar-dark navbar-msg fixed-top">
   <div class="container container-wide px-0 px-lg-0">
     <button class="navbar-toggler btn btn-sm" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
     <a class="navbar-brand" href="./">
       <img src="<?php echo validate_image($_settings->info('logo')) ?>" width="80" height="80" class="d-inline-block align-top" alt="" loading="lazy">
       <?php echo $_settings->info('short_name') ?>
     </a>

     <div class="collapse navbar-collapse" id="navbarSupportedContent">
       <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
         <li class="nav-item active">
           <a class="nav-link text-white fos" aria-current="page" href="./">หน้าหลัก</a>
         </li>
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
         <!--li class="nav-item"><a class="nav-link text-white fos" href="./?p=products">สินค้าทั้งหมด</a></li -->
         <li class="nav-item"><a class="nav-link text-white fos" href="./?p=help">ช่วยเหลือ</a></li>
         <li class="nav-item"><a class="nav-link text-white fos" href="./?p=about">เกี่ยวกับเรา</a></li>
         <li class="nav-item"><a class="nav-link text-white fos" href="./?p=contact">ติดต่อเรา</a></li>
       </ul>
       <ul class="navbar-nav ml-auto mb-2 mb-lg-0 ms-lg-4">
         <li class="nav-item d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">
           <form class="form-inline d-flex align-items-center" method="get" action="./" style="gap: 0.5rem;">
             <input class="form-control sbr mr-sm-2" type="search" placeholder="ค้นหาสินค้า..." name="search" required>
             <button class="btn button1 searchcolor" title="ค้นหาสินค้า" type="submit"><i class="fas fa-search"></i></button>
           </form>

           <div class="position-relative">
             <!--CART-->
             <?php
              $cart = '';
              if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
                $cart = $conn->query("SELECT SUM(quantity) FROM `cart_list` where customer_id = '{$_settings->userdata('id')}' ")->fetch_array()[0];
                $cart = $cart > 0 ? format_num($cart) : '';
              }
              ?>
             <a class="nav-link text-white p-0" href="./?p=cart_list" title="ตะกร้าสินค้า">
               <i class="fa fa-basket-shopping icon-size"></i>
               <?php if ($cart > 0): ?>
                 <span class="cart-badge"><?= format_num($cart) ?></span>
               <?php endif; ?>
             </a>
           </div>
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
                        return ['ยังไม่ชำระเงิน', 'รอตรวจสอบ', 'ชำระแล้ว', 'ชำระล้มเหลว', 'คืนเงินแล้ว'][$status] ?? 'N/A';
                      }
                      function get_delivery_text($status)
                      {
                        return ['ตรวจสอบคำสั่งซื้อ', 'เตรียมของ', 'แพ๊กของแล้ว', 'กำลังจัดส่ง', 'จัดส่งสำเร็จ', 'ส่งไม่สำเร็จ', 'คืนของระหว่างทาง', 'คืนของสำเร็จ'][$status] ?? 'N/A';
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
       </ul>
       <div class="dropdown ml-3  d-flex align-items-center user-dropdown-wrapper">
         <?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
           <div class="dropdown">
             <button type="button" class="dropdown-toggle user-dd-toggle" data-toggle="dropdown">
               <img src="<?= validate_image($_settings->userdata('avatar')) ?>" class="user-img-nav" alt="User">
               <span class="user-name"><?= ucwords($_settings->userdata('firstname')) ?></span>
             </button>
             <div class="dropdown-menu user-dropdown-menu">
               <a class="dropdown-item" href="<?= base_url . '?p=user' ?>"><i class="fa fa-user"></i> บัญชีของฉัน</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="<?= base_url . '?p=cart_list' ?>"><i class="fa fa-shopping-cart"></i> ตะกร้าของฉัน</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="<?= base_url . '?p=orders' ?>"><i class="fa fa-truck"></i> ประวัติการสั่งซื้อ</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="<?= base_url . '/classes/Login.php?f=logout_customer' ?>"><i class="fa fa-sign-out-alt"></i> ออกจากระบบ</a>
             </div>
           </div>
         <?php else: ?>
           <div class="dropdown ml-3  d-flex align-items-center user-dropdown-wrapper">
             <button type="button" class="btn btn-rounded dropdown-toggle dropdown-icon text-white" data-toggle="dropdown">
               <i class="fas fa-user-circle icon-acc-size text-white" title="แอคเคานท์"></i>
             </button>
             <div class="dropdown-menu user-dropdown-menu" role="menu">
               <a class="dropdown-item" href="./login.php"><i class="fa fa-sign-in-alt"></i> เข้าสู่ระบบ</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="./register.php"><i class="fa fa-user-plus"></i> สมัครสมาชิก</a>
               <div class="dropdown-divider"></div>
               <a class="dropdown-item" href="./admin"><i class="fa fa-user-tie"></i> Admin Panel</a>
             </div>
           </div>
         <?php endif; ?>

       </div>
     </div>
   </div>
   <button id="toTopBtn" title="กลับขึ้นบน">
     <i class="fas fa-chevron-up"></i>
   </button>
 </nav>
 <script>
   $(function() {
     $('#search_report').click(function() {
       uni_modal("Search Request Report", "report/search.php")
     })
     $('#navbarResponsive').on('show.bs.collapse', function() {
       $('#mainNav').addClass('navbar-shrink')
     })
     $('#navbarResponsive').on('hidden.bs.collapse', function() {
       if ($('body').offset.top == 0)
         $('#mainNav').removeClass('navbar-shrink')
     })
   })

   $('#search-form').submit(function(e) {
     e.preventDefault()
     var sTxt = $('[name="search"]').val()
     if (sTxt != '')
       location.href = './?p=products&search=' + sTxt;
   })

   // เมื่อ scroll เกิน 100px ให้แสดงปุ่ม
   window.addEventListener('scroll', function() {
     const toTopBtn = document.getElementById('toTopBtn');
     if (window.scrollY > 200) {
       toTopBtn.classList.add('show');
     } else {
       toTopBtn.classList.remove('show');
     }
   });

   document.getElementById('toTopBtn').addEventListener('click', function() {
     window.scrollTo({
       top: 0,
       behavior: 'smooth'
     });
   });
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
 </script>