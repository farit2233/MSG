<?php
require_once('./../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM `order_list` WHERE id = '{$_GET['id']}' AND customer_id = '{$_settings->userdata('id')}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    } else {
        echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}

// ตรวจสอบว่ามีค่าของ shipping_methods_id หรือไม่
if (!empty($shipping_methods_id)) {
    $shipping_query = $conn->query("SELECT name, cost FROM shipping_methods WHERE id = '{$shipping_methods_id}'");
    if ($shipping_query->num_rows > 0) {
        $shipping_data = $shipping_query->fetch_assoc();
        $shipping_methods_name = $shipping_data['name']; // เก็บชื่อขนส่ง
        $shipping_methods_cost = $shipping_data['cost']; // เก็บราคาขนส่ง
        $shipping_methods_name .= ' (' . number_format($shipping_methods_cost, 2) . ' บาท)';
    } else {
        $shipping_methods_name = 'ไม่พบข้อมูลขนส่ง';
    }
} else {
    $shipping_methods_name = 'ไม่ระบุขนส่ง';
}

// ตรวจสอบค่าของ shipping_methods_name ก่อนแสดงผล
if (empty($shipping_methods_name)) {
    $shipping_methods_name = 'ไม่พบคำสั่งซื้อ';
}
?>

<style>
    #uni_modal .modal-footer {
        display: none !important;
    }

    .product-link {
        color: inherit !important;
    }

    .product-link:hover {
        background-color: #f8f9fa !important;
        text-decoration: underline !important;
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
<?php
$customer_name = '';
if (!empty($customer_id)) {
    $cus = $conn->query("SELECT CONCAT(firstname, ' ', middlename, ' ', lastname,' ',contact) AS fullname FROM customer_list WHERE id = '{$customer_id}'");
    if ($cus->num_rows > 0) {
        $customer_name = $cus->fetch_assoc()['fullname'];
    }
}
?>
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">เลขที่คำสั่งซื้อ: </label>
                <div class="pl-4"><?= isset($code) ? $code : '' ?></div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">ชื่อ และเบอร์โทรผู้รับสินค้า:</label>
                <div class="pl-4"><?= !empty($customer_name) ? htmlentities($customer_name) : 'ไม่พบข้อมูลลูกค้า' ?></div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">ที่อยู่จัดส่ง:</label>
                <div class="pl-4"><?= !empty($delivery_address) ? nl2br(htmlentities($delivery_address)) : 'ไม่พบที่อยู่' ?></div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">สถานะการชำระเงิน:</label>
                <div class="pl-4">
                    <?php
                    switch ((int)$payment_status) {
                        case 0:
                            echo '<span class="badge bg-secondary">ยังไม่ชำระเงิน</span>';
                            break;
                        case 1:
                            echo '<span class="badge bg-warning text-dark">รอตรวจสอบ</span>';
                            break;
                        case 2:
                            echo '<span class="badge bg-success">ชำระแล้ว</span>';
                            break;
                        case 3:
                            echo '<span class="badge bg-danger">ล้มเหลว</span>';
                            break;
                        case 4:
                            echo '<span class="badge bg-dark">คืนเงินแล้ว</span>';
                            break;
                        default:
                            echo '<span class="badge bg-light">N/A</span>';
                            break;
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">สถานะการจัดส่ง:</label>
                <div class="pl-4">
                    <?php
                    switch ((int)$delivery_status) {
                        case 0:
                            echo '<span class="badge bg-secondary">ตรวจสอบคำสั่งซื้อ</span>';
                            break;
                        case 1:
                            echo '<span class="badge bg-info">เตรียมของ</span>';
                            break;
                        case 2:
                            echo '<span class="badge bg-primary">แพ๊กของแล้ว</span>';
                            break;
                        case 3:
                            echo '<span class="badge bg-warning text-dark">กำลังจัดส่ง</span>';
                            break;
                        case 4:
                            echo '<span class="badge bg-success">จัดส่งสำเร็จ</span>';
                            break;
                        case 5:
                            echo '<span class="badge bg-danger">ส่งไม่สำเร็จ</span>';
                            break;
                        case 6:
                            echo '<span class="badge bg-dark">คืนของระหว่างทาง</span>';
                            break;
                        case 7:
                            echo '<span class="badge bg-secondary">คืนของสำเร็จ</span>';
                            break;
                        default:
                            echo '<span class="badge bg-light">N/A</span>';
                            break;
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">ขนส่ง</label>
                <div class="pl-4"><?= !empty($shipping_methods_name) ? nl2br(htmlentities($shipping_methods_name)) : 'ไม่พบข้อมูลขนส่ง' ?></div>
            </div>
        </div>
    </div>
    <div id="item_list" class="list-group">
        <h4>รายการสินค้า</h4>
        <?php
        $gt = 0;
        $order_items = $conn->query("
    SELECT 
        o.*, 
        p.name as product, 
        p.brand as brand, 
        p.price as product_price,
        cc.name as category, 
        p.image_path,
        p.id as product_id,
        COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = p.id ), 0) as available
    FROM `order_items` o 
    INNER JOIN product_list p ON o.product_id = p.id 
    INNER JOIN category_list cc ON p.category_id = cc.id 
    WHERE order_id = '{$id}'
    ");
        while ($row = $order_items->fetch_assoc()):
            $gt += $row['price'] * $row['quantity'];
        ?>

            <div class="list-group-item cart-item" data-id='<?= $row['id'] ?>' data-max='<?= format_num($row['available'], 0) ?>'>
                <div class="d-flex w-100 align-items-center">
                    <div class="col-2 text-center">
                        <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class=" product-link">
                            <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-logo">
                        </a>
                    </div>
                    <div class="col-auto flex-shrink-1 flex-grow-1">
                        <div style="line-height:1.2em">
                            <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class=" product-link">
                                <h5 class='mb-1 fw-bold'><?= $row['product'] ?></h5>
                            </a>
                            <small class="text-muted"><?= $row['brand'] ?> | <?= $row['category'] ?></small>
                            <div class="text-muted d-flex align-items-center gap-1 mt-1">
                                <?= format_num($row['quantity'], 0) ?> x
                                <?php if ($row['price'] < $row['product_price']): ?>
                                    <span class="text-muted" style="text-decoration: line-through; font-size: 0.9em;">
                                        <?= format_num($row['product_price'], 2) ?>
                                    </span>
                                    <span class="text-danger fw-bold" style="font-size: 1em;">
                                        <?= format_num($row['price'], 2) ?>
                                    </span>
                                <?php else: ?>
                                    <span><?= format_num($row['price'], 2) ?></span>
                                <?php endif; ?>
                                <span class="ms-1">บาท</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-auto text-end">
                        <div class="col-auto text-end">
                            <h5 class="text-dark mb-0"><b><?= format_num($row['price'] * $row['quantity'], 2) ?> บาท</b></h5>
                            <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class="btn btn-orders mt-2">
                                <i class="fa fa-shopping-cart me-1"></i> ซื้ออีกครั้ง
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
    <?php if ($order_items->num_rows <= 0): ?>
        <h5 class="text-center text-muted">ไม่พบรายการการสั่งซื้อ</h5>
    <?php endif; ?>

    <div class="d-flex justify-content-end py-3">
        <div class="col-auto">
            <h3><b>รวมทั้งหมด: <?= format_num($gt, 2) ?> บาท</b></h3>
        </div>
    </div>
</div>
<hr class="px-n5">
<div class="py-1 text-right">
    <button class="btn btn-sm btn-light bg-gradient-light border btn-flat" type="button" data-dismiss="modal"><i class="fa fa-times"></i> ปิด</button>
</div>