  <script>
    $(document).ready(function() {
      $('#p_use').click(function() {
        uni_modal("Privacy Policy", "policy.php", "mid-large")
      })

      $('#fab-main-button').click(function(e) {
        // ให้สลับ (toggle) คลาส 'active' ที่ตัวครอบ (ID: fab-contact-menu)
        $('#fab-contact-menu').toggleClass('active');
      });

      function isMobileDevice() {
        // ใช้ regex แบบง่ายๆ ตรวจสอบ User Agent
        return /Mobi|Android|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
      }

      // 2. ตรวจสอบและเปลี่ยนลิงก์
      if (isMobileDevice()) {
        var phoneButton = $('#fab-phone-button'); // หาปุ่มที่เราตั้ง id ไว้
        var telLink = phoneButton.data('tel'); // ดึงเบอร์โทรจาก data-tel

        if (telLink) {
          phoneButton.attr('href', telLink); // 
          phoneButton.attr('title', 'โทร'); // (Optional) เปลี่ยน title กลับเป็น "โทร"
        }
      }

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

      window.viewer_modal = function($src = '') {
        start_loader()
        var t = $src.split('.')
        t = t[1]
        if (t == 'mp4') {
          var view = $("<video src='" + $src + "' controls autoplay></video>")
        } else {
          var view = $("<img src='" + $src + "' />")
        }
        $('#viewer_modal .modal-content video,#viewer_modal .modal-content img').remove()
        $('#viewer_modal .modal-content').append(view)
        $('#viewer_modal').modal({
          show: true,
          backdrop: 'static',
          keyboard: false,
          focus: true
        })
        end_loader()

      }
      window.uni_modal = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#uni_modal .modal-title').html($title)
              $('#uni_modal .modal-body').html(resp)
              if ($size != '') {
                $('#uni_modal .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#uni_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
              }
              $('#uni_modal').modal({
                show: true,
                backdrop: 'static',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window.uni_modal_conditions = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#uni_modal_conditions .modal-title').html($title)
              $('#uni_modal_conditions .modal-body').html(resp)
              if ($size != '') {
                $('#uni_modal_conditions .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#uni_modal_conditions .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md")
              }
              $('#uni_modal_conditions').modal({
                show: true,
                backdrop: 'true',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window.uni_modal_order = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#uni_modal_order .modal-title').html($title)
              $('#uni_modal_order .modal-body').html(resp)
              if ($size != '') {
                $('#uni_modal_order .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#uni_modal_order .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
              }
              $('#uni_modal_order').modal({
                show: true,
                backdrop: 'true',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window.modal_confirm = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#modal_confirm .modal-title').html($title)
              $('#modal_confirm .modal-body').html(resp)
              if ($size != '') {
                $('#modal_confirm .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#modal_confirm .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
              }
              $('#modal_confirm').modal({
                show: true,
                backdrop: 'true',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window.address_option_modal = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#address_option_modal .modal-title').html($title)
              $('#address_option_modal .modal-body').html(resp)
              if ($size != '') {
                $('#address_option_modal .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#address_option_modal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
              }
              $('#address_option_modal').modal({
                show: true,
                backdrop: 'true',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window.cropModal = function($title = '', $url = '', $size = "") {
        start_loader()
        $.ajax({
          url: $url,
          error: err => {
            console.log()
            alert("An error occured")
          },
          success: function(resp) {
            if (resp) {
              $('#cropModal .modal-title').html($title)
              $('#cropModal .modal-body').html(resp)
              if ($size != '') {
                $('#cropModal .modal-dialog').addClass($size + '  modal-dialog-centered')
              } else {
                $('#cropModal .modal-dialog').removeAttr("class").addClass("modal-dialog modal-md modal-dialog-centered")
              }
              $('#cropModal').modal({
                show: true,
                backdrop: 'true',
                keyboard: false,
                focus: true
              })
              end_loader()
            }
          }
        })
      }
      window._conf = function($msg = '', $func = '', $params = []) {
        $('#confirm_modal #confirm').attr('onclick', $func + "(" + $params.join(',') + ")")
        $('#confirm_modal .modal-body').html($msg)
        $('#confirm_modal').modal('show')
      }
    })
  </script>


  <div class="fab-container" id="fab-contact-menu">

    <div class="fab-main" id="fab-main-button">
      <i class="fas fa-plus"></i>
    </div>
    <a href="mailto:<?php echo $_settings->info('email') ?>" target="_blank" class="fab-option email" title="อีเมล">
      <i class="fas fa-envelope"></i>
    </a>

    <a href="<?php echo $_settings->info('Facebook') ?>" target="_blank" class="fab-option facebook" title="Facebook">
      <i class="fab fa-facebook-f"></i>
    </a>

    <a href="https://line.me/ti/p/~<?php echo $_settings->info('Line') ?>" target="_blank" class="fab-option line" title="Line">
      <i class="fab fa-line"></i>
    </a>

    <a href="./?p=contact"
      id="fab-phone-button"
      class="fab-option phone"
      title="ดูข้อมูลติดต่อ"
      data-tel="tel:<?php echo str_replace(['-', ' '], '', $_settings->info('mobile')) ?>">

      <i class="fas fa-phone"></i>
    </a>


    <button id="toTopBtn" title="กลับขึ้นบน"><i class="fas fa-chevron-up"></i></button>

  </div>


  <!-- Footer-->
  <?php if ($_settings->userdata('id') == '' && $_settings->userdata('login_type') != 2): ?>
    <hr class="my-4" style="border-top: 1px solid #ccc; margin: 2rem 0;">
    <div class="text-center">
      <div class="mb-2">
        <a href="./login.php" class="btn btn-not-login rounded-pill">เข้าสู่ระบบ</a>
      </div>
      <div class="mb-2">
        <p>ยังไม่มีบัญชี? <a href="./register.php" class="text-decoration-underline">สมัครสมาชิกที่นี่</a></p>
      </div>
    <?php endif; ?>
    </div>



    <footer class="py-5 text-white bg-foot-msg">
      <div class="container">
        <div class="row">
          <!-- เกี่ยวกับเรา -->
          <div class="col-md-3 mb-4">
            <h5><?php echo $_settings->info('name') ?></h5>
            <p>เราคัดสรรของเล่นเสริมพัฒนาการ <br>เพื่อให้ทุกวันของลูกคุณเปี่ยมไปด้วยคุณค่า</p>
          </div>

          <!-- บริการลูกค้า -->
          <div class="col-md-3 mb-4">
            <h6>ประเภทสินค้า</h6>
            <ul class="list-unstyled">
              <?php
              // --- ส่วนที่ 1: การเชื่อมต่อฐานข้อมูล ---
              // สมมติว่าคุณมี $conn ที่ใช้เชื่อมต่อฐานข้อมูลแล้ว (เช่น มาจากไฟล์ include)
              // ถ้ายังไม่มี คุณต้องเชื่อมต่อก่อน
              // $conn = new mysqli($servername, $username, $password, $dbname);
              // mysqli_set_charset($conn, "utf8"); // ตั้งค่า encoding (แนะนำ)


              // --- ส่วนที่ 2: การดึงข้อมูล (Query) ---
              // ดึงข้อมูลประเภทสินค้า 5 รายการ (ตามที่โจทย์ระบุ)
              // ถ้าต้องการแสดง "ทุก" ประเภท ให้ลบ "LIMIT 5" ออก
              $sql = "SELECT id, name FROM product_type LIMIT 5";
              $result = $conn->query($sql);

              // --- ส่วนที่ 3: การวนลูป (Loop) เพื่อแสดงผล ---
              if ($result && $result->num_rows > 0) {
                // วนลูปข้อมูลที่ได้มาทีละแถว
                while ($row = $result->fetch_assoc()) {

                  // สร้างลิงก์ตาม path ที่คุณต้องการ
                  $link = "./?p=products&tid=" . $row['id'];

                  // แสดงผล (echo) HTML ที่เป็น <li> ออกมา
                  echo '<li><a href="' . $link . '" class="text-white">' . htmlspecialchars($row['name']) . '</a></li>';
                }
              } else {
                // กรณีไม่พบข้อมูลในตาราง
                echo '<li><span class="text-white">ไม่พบข้อมูล</span></li>';
              }

              // $conn->close(); // ปิดการเชื่อมต่อถ้าจำเป็น
              ?>
            </ul>
          </div>

          <!-- นโยบายและข้อกำหนด -->
          <div class="col-md-3 mb-4">
            <h6>เกี่ยวกับเรา</h6>
            <ul class="list-unstyled">
              <li><a href="./" class="text-white">หน้าแรก</a></li>
              <li><a href="./?p=about" class="text-white">เกี่ยวกับบริษัท</a></li>
              <li><a href="./?p=contact" class="text-white">ติดต่อเรา</a></li>
              <li><a href="./?p=promotions" class="text-white">โปรโมชัน</a></li>
            </ul>
          </div>

          <!-- ช่องทางการติดต่อ -->
          <div class="col-md-3 mb-4">
            <h6>ติดต่อเรา</h6>
            <p class="mb-1"><i class="fas fa-location-dot text-white"></i> <?php echo $_settings->info('address') ?> </p>
            <p class="mb-1"><i class="fas fa-phone text-white"></i> <?php echo $_settings->info('mobile') ?></p>
            <p class="mb-1"><i class="fab fa-line text-white"></i> <?php echo $_settings->info('Line') ?></p>
            <p class="mb-0"><i class="fab fa-facebook text-white"></i><a href="<?php echo $_settings->info('Facebook') ?>" target="_blank" class="text-white"> Facebook </a></p>
            <p class="mb-0"><i class="fas fa-envelope text-white"></i><a href="mailto:<?php echo $_settings->info('email') ?>" target="blank" target="_blank" class="text-white"> <?php echo $_settings->info('email') ?> </a></p>
          </div>
        </div>

        <hr class="bg-light">
        <p class="text-center mb-0">&copy; <?php echo date('Y') ?> <?php echo $_settings->info('name') ?>. สงวนลิขสิทธิ์.</p>
      </div>
    </footer>

    <!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
    <script>
      $.widget.bridge('uibutton', $.ui.button)
    </script>
    <!-- Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo base_url ?>plugins/chart.js/Chart.min.js"></script>
    <!-- Sparkline -->
    <script src="<?php echo base_url ?>plugins/sparklines/sparkline.js"></script>
    <!-- Select2 -->
    <script src="<?php echo base_url ?>plugins/select2/js/select2.full.min.js"></script>
    <!-- JQVMap -->
    <script src="<?php echo base_url ?>plugins/jqvmap/jquery.vmap.min.js"></script>
    <script src="<?php echo base_url ?>plugins/jqvmap/maps/jquery.vmap.usa.js"></script>
    <!-- jQuery Knob Chart -->
    <script src="<?php echo base_url ?>plugins/jquery-knob/jquery.knob.min.js"></script>
    <!-- daterangepicker -->
    <script src="<?php echo base_url ?>plugins/moment/moment.min.js"></script>
    <script src="<?php echo base_url ?>plugins/daterangepicker/daterangepicker.js"></script>
    <!-- Tempusdominus Bootstrap 4 -->
    <script src="<?php echo base_url ?>plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
    <!-- Summernote -->
    <script src="<?php echo base_url ?>plugins/summernote/summernote-bs4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo base_url ?>plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <!-- overlayScrollbars -->
    <!-- <script src="<?php echo base_url ?>plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script> -->
    <!-- AdminLTE App -->
    <script src="<?php echo base_url ?>dist/js/adminlte.js"></script>