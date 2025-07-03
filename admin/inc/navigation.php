<style>
  [class*="sidebar-light-"] .nav-treeview>.nav-item>.nav-link.active,
  [class*="sidebar-light-"] .nav-treeview>.nav-item>.nav-link.active:hover {
    color: #ffffff !important;
  }
</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-light-dark navbar-light elevation-4 sidebar-no-expand">
  <!-- Brand Logo -->
  <a href="<?php echo base_url ?>admin" class="brand-link bg-dark text-sm">
    <img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="Store Logo" class="brand-image img-circle elevation-3" style="opacity: .8;width: 1.5rem;height: 1.5rem;max-height: unset">
    <span class="brand-text font-weight-light"><?php echo $_settings->info('short_name') ?></span>
  </a>
  <!-- Sidebar -->
  <div class="sidebar os-host os-theme-light os-host-overflow os-host-overflow-y os-host-resize-disabled os-host-transition os-host-scrollbar-horizontal-hidden">
    <div class="os-resize-observer-host observed">
      <div class="os-resize-observer" style="left: 0px; right: auto;"></div>
    </div>
    <div class="os-size-auto-observer observed" style="height: calc(100% + 1px); float: left;">
      <div class="os-resize-observer"></div>
    </div>
    <div class="os-content-glue" style="margin: 0px -8px; width: 249px; height: 646px;"></div>
    <div class="os-padding">
      <div class="os-viewport os-viewport-native-scrollbars-invisible" style="overflow-y: scroll;">
        <div class="os-content" style="padding: 0px 8px; height: 100%; width: 100%;">
          <!-- Sidebar user panel (optional) -->
          <div class="clearfix"></div>
          <!-- Sidebar Menu -->
          <nav class="mt-0">
            <ul class="nav nav-pills nav-sidebar flex-column text-sm nav-compact nav-flat nav-child-indent nav-collapse-hide-child" data-widget="treeview" role="menu" data-accordion="false">
              <li class="nav-item dropdown">
                <a href="./" class="nav-link nav-home">
                  <i class="nav-icon fas fa-tachometer-alt"></i>
                  <p>
                    หน้าแดชบอร์ด
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-list"></i>
                  <p>
                    หมวดหมู่สินค้า
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="./?page=categories" class="nav-link nav-categories">
                      <i class="nav-icon fas fa-th-list"></i>
                      <p>
                        หมวดหมู่สินค้าทั้งหมด
                      </p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=category_hierarchy" class="nav-link nav-category_hierarchy">
                      <i class="nav-icon fas fa-th-list"></i>
                      <p>
                        หมวดหมู่ย่อย
                      </p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a href="./?page=products" class="nav-link nav-products">
                  <i class="nav-icon fas fa-boxes"></i>
                  <p>
                    สินค้าทั้งหมด
                  </p>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a href="./?page=inventory" class="nav-link nav-inventory">
                  <i class="nav-icon fas fa-warehouse"></i>
                  <p>
                    สต๊อกสินค้า
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a href="#" class="nav-link">
                  <i class="nav-icon fas fa-table"></i>
                  <p>
                    รายการคำสั่งซื้อ
                    <i class="right fas fa-angle-left"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-item">
                    <a href="./?page=orders" class="nav-link tree-item nav-orders">
                      <i class="far fa-circle nav-icon"></i>
                      <p>รายการคำสั่งซื้อทั้งหมด</p>
                    </a>
                  </li>
                  <li class="nav-header text-xs pl-3 pt-2 text-muted">สถานะการชำระเงิน</li>
                  <li class="nav-item">
                    <a href="./?page=orders&payment_status=0" class="nav-link tree-item nav-payment_status_0">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ยังไม่ชำระเงิน</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&payment_status=1" class="nav-link tree-item nav-payment_status_1">
                      <i class="far fa-circle nav-icon"></i>
                      <p>รอตรวจสอบ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&payment_status=2" class="nav-link tree-item nav-payment_status_2">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ชำระเงินแล้ว</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&payment_status=3" class="nav-link tree-item nav-payment_status_3">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ชำระล้มเหลว</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&payment_status=4" class="nav-link tree-item nav-payment_status_4">
                      <i class="far fa-circle nav-icon"></i>
                      <p>คืนเงินแล้ว</p>
                    </a>
                  </li>
                </ul>
                <ul class="nav nav-treeview" style="display: none;">
                  <li class="nav-header text-xs pl-3 pt-2 text-muted">สถานะการจัดส่ง</li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=0" class="nav-link tree-item nav-delivery_status_0">
                      <i class="far fa-circle nav-icon"></i>
                      <p>ตรวจสอบคำสั่งซื้อ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=1" class="nav-link tree-item nav-delivery_status_1">
                      <i class="far fa-circle nav-icon"></i>
                      <p>กำลังเตรียมของ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=2" class="nav-link tree-item nav-delivery_status_2">
                      <i class="far fa-circle nav-icon"></i>
                      <p>แพ็กของแล้ว</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=3" class="nav-link tree-item nav-delivery_status_3">
                      <i class="far fa-circle nav-icon"></i>
                      <p>กำลังจัดส่ง</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=4" class="nav-link tree-item nav-delivery_status_4">
                      <i class="far fa-circle nav-icon"></i>
                      <p>จัดส่งสำเร็จ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=5" class="nav-link tree-item nav-delivery_status_5">
                      <i class="far fa-circle nav-icon"></i>
                      <p>จัดส่งไม่สำเร็จ</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=6" class="nav-link tree-item nav-delivery_status_6">
                      <i class="far fa-circle nav-icon"></i>
                      <p>คืนของระหว่างทาง</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="./?page=orders&delivery_status=7" class="nav-link tree-item nav-delivery_status_7">
                      <i class="far fa-circle nav-icon"></i>
                      <p>คืนของสำเร็จ</p>
                    </a>
                  </li>
                </ul>
              </li>
              <li class="nav-item dropdown">
                <a href="./?page=customers" class="nav-link nav-customers">
                  <i class="nav-icon fas fa-user-friends"></i>
                  <p>
                    รายชื่อลูกค้า
                  </p>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a href="./?page=shipping_setting" class="nav-link nav-shipping_setting">
                  <i class="nav-icon fas fa-truck"></i>
                  <p>
                    ตั้งค่าขนส่ง
                  </p>
                </a>
              </li>
              <?php if ($_settings->userdata('type') == 1): ?>
                <li class="nav-header">Maintenance</li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=reports" class="nav-link nav-reports">
                    <i class="nav-icon far fa-circle"></i>
                    <p>
                      รายงานยอดขายประจำวัน
                    </p>
                  </a>
                </li>
                <li class="nav-header">Maintenance</li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=user/list" class="nav-link nav-user_list">
                    <i class="nav-icon fas fa-users-cog"></i>
                    <p>
                      รายชื่อผู้ดูแลระบบ
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=system_info/help_info" class="nav-link nav-system_info_help_info">
                    <i class="nav-icon fas fa-question"></i>
                    <p>
                      หน้าช่วยเหลือ
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=system_info/contact_info" class="nav-link nav-system_info_contact_info">
                    <i class="nav-icon fas fa-phone-square-alt"></i>
                    <p>
                      หน้าติดต่อ
                    </p>
                  </a>
                </li>
                <li class="nav-item dropdown">
                  <a href="<?php echo base_url ?>admin/?page=system_info" class="nav-link nav-system_info">
                    <i class="nav-icon fas fa-tools"></i>
                    <p>
                      ตั้งค่าหน้าเว็บ
                    </p>
                  </a>
                </li>
              <?php endif; ?>
            </ul>
          </nav>
          <!-- /.sidebar-menu -->
        </div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-horizontal os-scrollbar-unusable os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="width: 100%; transform: translate(0px, 0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar os-scrollbar-vertical os-scrollbar-auto-hidden">
      <div class="os-scrollbar-track">
        <div class="os-scrollbar-handle" style="height: 55.017%; transform: translate(0px, 0px);"></div>
      </div>
    </div>
    <div class="os-scrollbar-corner"></div>
  </div>
  <!-- /.sidebar -->
</aside>
<script>
  $(document).ready(function() {
    var page = '<?php echo isset($_GET['page']) ? $_GET['page'] : 'home' ?>';
    var status = '';
    var statusType = '';

    <?php if (isset($_GET['payment_status'])): ?>
      status = '<?php echo $_GET['payment_status'] ?>';
      statusType = 'payment_status';
    <?php elseif (isset($_GET['delivery_status'])): ?>
      status = '<?php echo $_GET['delivery_status'] ?>';
      statusType = 'delivery_status';
    <?php endif; ?>

    page = page.replace(/\//g, '_');
    page = (status != '' && statusType != '') ? statusType + "_" + status : page;

    var selector = '.nav-link.nav-' + page;
    if ($(selector).length > 0) {
      $(selector).addClass('active');
      if ($(selector).hasClass('tree-item')) {
        $(selector).closest('.nav-treeview').parent().addClass('menu-open');
      }
      if ($(selector).hasClass('nav-is-tree')) {
        $(selector).parent().addClass('menu-open');
      }
    }

    $('.nav-link.active').addClass('bg-dark');
  });
</script>