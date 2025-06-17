<?php
$filter = $_GET['filter'] ?? 'all';
$filter_options = [
    'all' => 'ทั้งหมด',
    'pending_payment' => 'ที่ต้องชำระ',
    'shipping_required' => 'ที่ต้องจัดส่ง',
    'in_transit' => 'ที่ต้องได้รับ',
    'completed' => 'สำเร็จ',
    'cancelled' => 'ยกเลิก',
    'returned' => 'คืนเงิน/คืนสินค้า',
];
?>
<style>
    /* Header bar */
    .cart-header-bar {
        border-left: 4px solid #ff6600;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .order-card {
        border-left: 5px solid #ff6600;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
        padding: 1.25rem;
        background-color: #fff;
        transition: all 0.3s ease-in-out;
        cursor: pointer;
    }

    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .order-header {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: center;
        margin-bottom: 1rem;
    }

    .order-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .badge {
        font-size: 0.9rem;
        padding: 0.4em 0.8em;
    }

    .order-filter-link {
        margin-left: 10px;
        color: #333;
        text-decoration: none;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .order-filter-link:hover {
        text-decoration: underline;
        color: #f57421;
    }

    .order-filter-link.active {
        font-weight: bold;
        color: #f57421;
        text-decoration: underline;
    }

    .btn-search-orders {
        background-color: none;
        border: 2px solid #f57421;
        border-radius: 13px;
        color: #f57421;
        font-size: 14px;
    }

    .btn-search-orders:hover {
        background-color: #f57421;
        color: white;
    }

    .btn-orders {
        font-size: 14px;
        background-color: #f57421;
        border-radius: 13px;
        color: white;
        transition: all 0.2s ease-in-out;
    }

    .btn-orders:hover {
        color: white;
        filter: brightness(90%);
    }
</style>
<section class="py-3">
    <div class="container">
        <div class="row mt-n4  justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow" style="margin-top: 3rem;">
                    <div class="card-body">
                        <div class=" container-fluid">
                            <div class="cart-header-bar d-flex justify-content-between align-items-center flex-wrap">
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-basket-shopping mr-2" style="font-size: 30px;"></i>
                                    <h3 class="mb-0">ประวัติการสั่งซื้อ</h3>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($filter_options as $key => $label): ?>
                                        <a href="?p=orders&filter=<?= $key ?>" class="order-filter-link <?= ($filter === $key) ? 'active' : '' ?>">
                                            <?= $label ?>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <form method="get" class="d-flex align-items-center gap-2" style="margin-top: 10px;margin-bottom: 10px;">
                                <input type="hidden" name="p" value="orders">
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
                                    $where .= " AND (payment_status = 3 OR delivery_status = 5)";
                                    break;
                                case 'returned':
                                    $where .= " AND (payment_status = 4 OR delivery_status = 7)";
                                    break;
                                    // 'all' doesn't need condition
                            }
                            $orders = $conn->query("SELECT * FROM `order_list` WHERE {$where} ORDER BY date_created DESC");
                            if ($orders->num_rows > 0):
                                while ($row = $orders->fetch_assoc()):
                                    $payment_status = (int)$row['payment_status'];
                                    $delivery_status = (int)$row['delivery_status'];
                                    $badge_payment = match ($payment_status) {
                                        0 => '<span class="badge bg-secondary">ยังไม่ชำระเงิน</span>',
                                        1 => '<span class="badge bg-warning">รอตรวจสอบ</span>',
                                        2 => '<span class="badge bg-success text-dark">ชำระเงินแล้ว</span>',
                                        3 => '<span class="badge bg-danger">ชำระเงินล้มเหลว</span>',
                                        4 => '<span class="badge bg-dark">คืนเงินสำเร็จ</span>',
                                        default => '<span class="badge bg-light">N/A</span>'
                                    };
                                    $badge_delivery = match ($delivery_status) {
                                        0 => '<span class="badge bg-secondary">ตรวจสอบคำสั่งซื้อ</span>',
                                        1 => '<span class="badge bg-info">กำลังเตรียมของ</span>',
                                        2 => '<span class="badge bg-primary">แพ็คของแล้ว</span>',
                                        3 => '<span class="badge bg-warning text-dark">พัสดุกำลังจัดส่ง</span>',
                                        4 => '<span class="badge bg-success">พัสดุจัดส่งสำเร็จ</span>',
                                        5 => '<span class="badge bg-danger">พัสดุจัดส่งไม่สำเร็จ</span>',
                                        6 => '<span class="badge bg-dark">คืนของระหว่างทาง</span>',
                                        7 => '<span class="badge bg-secondary">คืนของสำเร็จ</span>',
                                        default => '<span class="badge bg-light">N/A</span>'
                                    };
                            ?>
                                    <div class="order-card view-order" data-id="<?= $row['id'] ?>">
                                        <div class="order-header">
                                            <div class="order-info">
                                                <strong>เลขที่คำสั่งซื้อ: </strong> <?= $row['code'] ?><br>
                                                <small class="text-muted">วันที่สั่ง: <?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></small>
                                            </div>
                                            <div>
                                                <button class="btn btn-orders view-order" data-id="<?= $row['id'] ?>">
                                                    <i class="fa fa-eye me-1"></i> ดูรายละเอียด
                                                </button>
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(function() {
        $('.view-order').click(function() {
            const orderId = $(this).data('id');
            uni_modal("รายละเอียดคำสั่งซื้อ", "orders/view_order.php?id=" + orderId, 'modal-lg');
        });
    });
    $('.btn.view-order').click(function(e) {
        e.stopPropagation(); // หยุด event ไม่ให้ไปถึง .order-card
    });
</script>