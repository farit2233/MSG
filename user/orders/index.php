<?php
$filter = $_GET['filter'] ?? 'all';
$filter_options = [
    'all' => 'ทั้งหมด',
    'pending_payment' => 'ที่ต้องชำระ',
    'shipping_required' => 'ที่ต้องจัดส่ง',
    'in_transit' => 'ที่ต้องได้รับ',
    'completed' => 'สำเร็จแล้ว',
    'cancelled' => 'ยกเลิกแล้ว',
    'returned' => 'คืนเงิน/คืนสินค้า',
];
$limit = 5; // จำนวนออเดอร์ต่อหน้า
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;
?>
<section class="py-5 profile-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="py-4">
                    <div class="card-body card-address">
                        <div class="container-fluid">
                            <div id="address-list">
                                <div class="profile-section-title-with-line ">
                                    <h4>ประวัติการสั่งซื้อ</h4>
                                    <p>ดูประวัติการสั่งซื้อ และสถานะสินค้า</p>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($filter_options as $key => $label): ?>
                                        <a href="?p=user/orders&filter=<?= $key ?>" class="order-filter-link <?= ($filter === $key) ? 'active' : '' ?>">
                                            <?= $label ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <form method="get" class="d-flex align-items-center gap-2" style="margin-top: 10px;margin-bottom: 10px;">
                                <input type="hidden" name="p" value="user/orders">
                                <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">
                                <input type="text" name="keyword" class="form-control form-control-sm" placeholder="ค้นหาชื่อสินค้า หรือเลขคำสั่งซื้อ" value="<?= htmlspecialchars($_GET['keyword'] ?? '') ?>">
                                <button type="submit" class="btn btn-search-orders">ค้นหา</button>
                            </form>

                            <?php
                            $where = "customer_id = '{$_settings->userdata('id')}'";
                            $keyword = $_GET['keyword'] ?? '';
                            if (!empty($keyword)) {
                                $safe_keyword = $conn->real_escape_string($keyword);
                                $where .= " AND (
                                code LIKE '%{$safe_keyword}%'
                                OR id IN (
                                    SELECT oi.order_id
                                    FROM order_items oi
                                    INNER JOIN product_list p ON p.id = oi.product_id
                                    WHERE p.name LIKE '%{$safe_keyword}%'
                                )
                            )";
                            }
                            switch ($filter) {
                                case 'pending_payment':
                                    $where .= " AND payment_status IN (0, 1)";
                                    break;
                                case 'shipping_required':
                                    $where .= " AND delivery_status IN (1,2)";
                                    break;
                                case 'in_transit':
                                    $where .= " AND delivery_status = 3";
                                    break;
                                case 'completed':
                                    $where .= " AND delivery_status = 4";
                                    break;
                                case 'cancelled':
                                    $where .= " AND (payment_status = 3 OR delivery_status = 6 OR payment_status = 4 OR delivery_status = 10)";
                                    break;
                                case 'returned':
                                    $where .= " AND (payment_status = 5 OR delivery_status = 8 OR payment_status = 6 OR delivery_status = 9)";
                                    break;
                                    // 'all' doesn't need condition
                            }

                            $count_qry = $conn->query("SELECT COUNT(id) as total_orders FROM `order_list` WHERE {$where}");
                            $total_orders = $count_qry->fetch_assoc()['total_orders'];
                            $total_pages = ceil($total_orders / $limit);

                            $orders = $conn->query("SELECT * FROM `order_list` WHERE {$where} ORDER BY date_created DESC LIMIT {$limit} OFFSET {$offset}");
                            if ($orders->num_rows > 0):
                                while ($row = $orders->fetch_assoc()):
                                    $payment_status = (int)$row['payment_status'];
                                    $delivery_status = (int)$row['delivery_status'];
                                    $badge_payment = match ($payment_status) {
                                        0 => '<span class="badge badge-order bg-secondary">ยังไม่ชำระเงิน</span>',
                                        1 => '<span class="badge badge-order bg-warning">รอตรวจสอบ</span>',
                                        2 => '<span class="badge badge-order bg-success text-dark">ชำระเงินแล้ว</span>',
                                        3 => '<span class="badge badge-order bg-danger">ชำระเงินล้มเหลว</span>',
                                        4 => '<span class="badge badge-order bg-secondary">กำลังยกเลิกคำสั่งซื้อ</span>',
                                        5 => '<span class="badge badge-order bg-secondary">กำลังคืนเงิน</span>',
                                        6 => '<span class="badge badge-order bg-dark">คืนเงินแล้ว</span>',
                                        default => '<span class="badge badge-order bg-light">N/A</span>'
                                    };
                                    $badge_delivery = match ($delivery_status) {
                                        0 => '<span class="badge badge-order bg-secondary">ตรวจสอบคำสั่งซื้อ</span>',
                                        1 => '<span class="badge badge-order bg-info">กำลังเตรียมของ</span>',
                                        2 => '<span class="badge badge-order bg-primary">แพ๊กของแล้ว</span>',
                                        3 => '<span class="badge badge-order bg-warning text-dark">กำลังจัดส่ง</span>',
                                        4 => '<span class="badge badge-order bg-success">จัดส่งสำเร็จ</span>',
                                        5 => '<span class="badge badge-order bg-danger">จัดส่งไม่สำเร็จ</span>',
                                        6 => '<span class="badge badge-order bg-secondary">กำลังยกเลิกคำสั่งซื้อ</span>',
                                        7 => '<span class="badge badge-order bg-dark">คืนของระหว่างทาง</span>',
                                        8 => '<span class="badge badge-order bg-secondary">กำลังคืนสินค้า</span>',
                                        9 => '<span class="badge badge-order bg-dark">คืนของสำเร็จ</span>',
                                        10 => '<span class="badge badge-order bg-danger">ยกเลิกแล้ว</span>',
                                        default => '<span class="badge badge-order bg-light">N/A</span>'
                                    };
                            ?>
                                    <div class="order-card" data-id="<?= $row['id'] ?>">
                                        <div class="order-header">
                                            <div class="order-info">
                                                <strong>เลขที่คำสั่งซื้อ</strong> <?= $row['code'] ?><br>
                                                <small class="text-muted">วันที่สั่ง: <?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></small>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <button class="btn btn-orders view-order mb-2" data-id="<?= $row['id'] ?>">
                                                    <i class="fa fa-eye me-1"></i> ดูรายละเอียด
                                                </button>

                                                <?php
                                                if ($payment_status < 2 && $delivery_status < 3) :
                                                ?>
                                                    <button class="btn btn-order-cancel btn-danger cancel-order" data-id="<?= $row['id'] ?>">
                                                        <i class="fa fa-times"></i> ยกเลิกคำสั่งซื้อ
                                                    </button>
                                                <?php endif; ?>

                                                <?php
                                                if ($payment_status == 2 || $delivery_status == 4) :
                                                ?>
                                                    <button class="btn btn-order-cancel btn-danger return-order" data-id="<?= $row['id'] ?>">
                                                        <i class="fa fa-times"></i> ขอคืนเงิน/คืนสินค้า
                                                    </button>
                                                <?php endif; ?>

                                            </div>

                                        </div>
                                        <div class="d-flex flex-wrap gap-3">
                                            <div><strong>ยอดรวม:</strong> <?= format_num($row['total_amount'], 2) ?> บาท</div>
                                            <div><strong> สถานะการชำระเงิน:</strong> <?= $badge_payment ?></div>
                                            <div><strong> สถานะการจัดส่ง:</strong> <?= $badge_delivery ?></div>
                                        </div>
                                    </div>
                                <?php endwhile;
                            else: ?>
                                <div class="alert" style="background-color: #f57421;color:white;">ไม่พบคำสั่งซื้อในระบบ</div>
                            <?php endif; ?>
                            <?php
                            if ($total_pages > 1):
                                $query_params = $_GET;

                                // --- กำหนดค่า ---
                                // จำนวนหน้าที่แสดงผลคงที่ (ช่วง đầu)
                                // เราจะแสดง 1 2 3 4 5 ... เมื่อ $page < 5
                                $num_fixed_pages = 5;
                                // จำนวนหน้าข้างเคียง (สำหรับสถานะกลาง)
                                // (P-2), (P-1), [P], (P+1), (P+2)
                                $adjacents = 2;
                            ?>
                                <div class="d-flex justify-content-center mt-4">
                                    <nav aria-label="Page navigation">
                                        <ul class="pagination">
                                            <li class="page-item <?= ($page == 1) ? 'disabled' : '' ?>">
                                                <?php $query_params['page'] = $page - 1; ?>
                                                <a class="page-link" href="?<?= http_build_query($query_params) ?>" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
                                            </li>

                                            <?php
                                            // --- Logic การแสดงผลตัวเลขหน้า ---

                                            // 1. กรณีที่จำนวนหน้ารวมน้อย (น้อยกว่า 5+1)
                                            // (เช่น 1 2 3 4 5 6) -> ไม่ต้องใช้ ...
                                            if ($total_pages <= ($num_fixed_pages + 1)) {
                                                for ($i = 1; $i <= $total_pages; $i++) {
                                                    $query_params['page'] = $i;
                                                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }
                                            }

                                            // 2. สถานะเริ่มต้น (เมื่ออยู่หน้า 1, 2, 3, 4)
                                            // (1 2 3 [4] 5 ... 10)
                                            // (แก้ไขจาก <= เป็น <)
                                            elseif ($page < $num_fixed_pages) {
                                                // แสดง 1-5
                                                for ($i = 1; $i <= $num_fixed_pages; $i++) {
                                                    $query_params['page'] = $i;
                                                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }
                                                // แสดง ...
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                // แสดงหน้าสุดท้าย
                                                $query_params['page'] = $total_pages;
                                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">' . $total_pages . '</a></li>';
                                            }

                                            // 3. สถานะท้าย (เมื่ออยู่ใกล้หน้าสุดท้าย)
                                            // (1 ... 6 [7] 8 9 10)
                                            elseif ($page >= ($total_pages - ($num_fixed_pages - 2))) {
                                                // แสดง 1
                                                $query_params['page'] = 1;
                                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">1</a></li>';
                                                // แสดง ...
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                // แสดง 5 หน้าสุดท้าย
                                                $start = $total_pages - ($num_fixed_pages - 1);
                                                for ($i = $start; $i <= $total_pages; $i++) {
                                                    $query_params['page'] = $i;
                                                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }
                                            }

                                            // 4. สถานะกลาง (เลื่อนไปเรื่อยๆ)
                                            // (1 ... 3 4 [5] 6 7 ... 10)
                                            // (จะทำงานตั้งแต่ $page = 5 เป็นต้นไป)
                                            else {
                                                // แสดง 1
                                                $query_params['page'] = 1;
                                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">1</a></li>';
                                                // แสดง ...
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';

                                                // แสดงหน้าต่าง (เช่น 3 4 5 6 7)
                                                $start = $page - $adjacents;
                                                $end = $page + $adjacents;
                                                for ($i = $start; $i <= $end; $i++) {
                                                    $query_params['page'] = $i;
                                                    echo '<li class="page-item ' . ($page == $i ? 'active' : '') . '">';
                                                    echo '<a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a>';
                                                    echo '</li>';
                                                }

                                                // แสดง ...
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                                // แสดงหน้าสุดท้าย
                                                $query_params['page'] = $total_pages;
                                                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">' . $total_pages . '</a></li>';
                                            }
                                            ?>

                                            <li class="page-item <?= ($page == $total_pages) ? 'disabled' : '' ?>">
                                                <?php $query_params['page'] = $page + 1; ?>
                                                <a class="page-link" href="?<?= http_build_query($query_params) ?>" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
                                            </li>
                                        </ul>
                                    </nav>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function() {
        // ฟังก์ชันสำหรับเปิด modal ดูรายละเอียด (เหมือนเดิม)
        $('.view-order').click(function() {
            const orderId = $(this).data('id');
            uni_modal_order("รายละเอียดคำสั่งซื้อ", "user/orders/view_order.php?id=" + orderId, 'modal-lg');
        });

        // --- ส่วนที่แก้ไขโดยใช้ SweetAlert2 ---
        $(".cancel-order").click(function() {
            let orderId = $(this).data("id");
            const $this = $(this);
            const originalHtml = $this.html();

            // 1. เปลี่ยนจาก confirm() มาใช้ Swal.fire()
            Swal.fire({
                title: 'ยืนยันการยกเลิกคำสั่งซื้อ',
                html: "คุณต้องการ <b>ยกเลิกคำสั่งซื้อ</b> นี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่',
                cancelButtonText: 'ไม่',
                reverseButtons: true
            }).then((result) => {
                // ตรวจสอบว่าผู้ใช้กดปุ่ม "ยืนยัน" (ใช่, ยกเลิกเลย)
                if (result.isConfirmed) {
                    // เปลี่ยนสถานะปุ่มเป็นกำลังโหลด
                    $this.prop('disabled', true);
                    $this.html('<i class="fa fa-spinner fa-spin"></i> กำลังดำเนินการ...');

                    $.ajax({
                        url: "classes/Master.php?f=cancel_order",
                        method: "POST",
                        data: {
                            order_id: orderId
                        },
                        success: function(resp) {
                            if (resp == 1) {
                                // 2. เปลี่ยน alert() สำเร็จเป็น Swal.fire()
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    html: '<b>ยกเลิกคำสั่งซื้อ</b> เรียบร้อย',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload(); // รีโหลดหน้าหลังจากกด OK
                                });
                            } else {
                                // 3. เปลี่ยน alert() ข้อผิดพลาดเป็น Swal.fire()
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด',
                                    text: resp,
                                    icon: 'error'
                                });
                                // คืนค่าปุ่ม
                                $this.prop('disabled', false);
                                $this.html(originalHtml);
                            }
                        },
                        error: function() {
                            // 4. เปลี่ยน alert() ข้อผิดพลาด Network เป็น Swal.fire()
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                icon: 'error'
                            });
                            // คืนค่าปุ่ม
                            $this.prop('disabled', false);
                            $this.html(originalHtml);
                        }
                    });
                }
            });
        });
        $(".return-order").click(function() {
            let orderId = $(this).data("id");
            const $this = $(this);
            const originalHtml = $this.html();

            // 1. เปลี่ยนจาก confirm() มาใช้ Swal.fire()
            Swal.fire({
                title: 'ยืนยันคำขอ คืนเงิน/คืนสินค้า',
                html: "คุณต้องการยืนยันคำขอ <b>คืนเงิน/คืนสินค้า</b> นี้ใช่หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'ใช่',
                cancelButtonText: 'ไม่',
                reverseButtons: true
            }).then((result) => {
                // ตรวจสอบว่าผู้ใช้กดปุ่ม "ยืนยัน" (ใช่, ยกเลิกเลย)
                if (result.isConfirmed) {
                    // เปลี่ยนสถานะปุ่มเป็นกำลังโหลด
                    $this.prop('disabled', true);
                    $this.html('<i class="fa fa-spinner fa-spin"></i> กำลังดำเนินการ...');

                    $.ajax({
                        url: "classes/Master.php?f=return_order",
                        method: "POST",
                        data: {
                            order_id: orderId
                        },
                        success: function(resp) {
                            if (resp == 1) {
                                // 2. เปลี่ยน alert() สำเร็จเป็น Swal.fire()
                                Swal.fire({
                                    title: 'สำเร็จ!',
                                    html: 'ส่งคำขอ <b>คืนเงิน/คืนสินค้า</b> เรียบร้อย',
                                    icon: 'success'
                                }).then(() => {
                                    location.reload(); // รีโหลดหน้าหลังจากกด OK
                                });
                            } else {
                                // 3. เปลี่ยน alert() ข้อผิดพลาดเป็น Swal.fire()
                                Swal.fire({
                                    title: 'เกิดข้อผิดพลาด',
                                    text: resp,
                                    icon: 'error'
                                });
                                // คืนค่าปุ่ม
                                $this.prop('disabled', false);
                                $this.html(originalHtml);
                            }
                        },
                        error: function() {
                            // 4. เปลี่ยน alert() ข้อผิดพลาด Network เป็น Swal.fire()
                            Swal.fire({
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                                icon: 'error'
                            });
                            // คืนค่าปุ่ม
                            $this.prop('disabled', false);
                            $this.html(originalHtml);
                        }
                    });
                }
            });
        });
    });
</script>