<?php
require_once('../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    // ดึงข้อมูล Order (รวมถึง shipping_cost และ grand_total ที่เราบันทึกไว้แล้ว)
    $qry = $conn->query("SELECT * FROM `order_list` WHERE id = '{$_GET['id']}' AND customer_id = '{$_settings->userdata('id')}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    } else {
        echo "<script>alert('You dont have access for this page'); location.replace('./?page=my_orders');</script>";
    }
} else {
    echo "<script>alert('You dont have access for this page'); location.replace('./?page=my_orders');</script>";
}

// ======================= START: ข้อมูลโปรโมชันและคูปอง =======================
$promotion_name = '';
$promotion_type = '';
if (!empty($promotion_id)) {
    $promo_qry = $conn->query("SELECT name, type FROM `promotions_list` WHERE id = '{$promotion_id}'");
    if ($promo_qry->num_rows > 0) {
        $promo_data = $promo_qry->fetch_assoc();
        $promotion_name = $promo_data['name'];
        $promotion_type = $promo_data['type'];
    } else {
        $promotion_name = "โปรโมชัน";
    }
}

$coupon_name = '';
$coupon_type = '';
if (!empty($coupon_code_id)) {
    $coupon_qry = $conn->query("SELECT coupon_code, type FROM `coupon_code_list` WHERE id = '{$coupon_code_id}'");
    if ($coupon_qry->num_rows > 0) {
        $coupon_data = $coupon_qry->fetch_assoc();
        $coupon_name = $coupon_data['coupon_code'];
        $coupon_type = $coupon_data['type'];
    } else {
        $coupon_name = "คูปองส่วนลด";
    }
}
// ======================= END: ข้อมูลโปรโมชันและคูปอง =======================

$shipping_providers = 'ไม่ระบุขนส่ง';
if (!empty($provider_id)) {
    $shipping_query = $conn->query("SELECT name FROM shipping_providers WHERE id = '{$provider_id}'");
    if ($shipping_query->num_rows > 0) {
        $shipping_data = $shipping_query->fetch_assoc();
        $shipping_providers = $shipping_data['name'];
    }
}

// Helper Function
if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = format_num($price, 2);
        if (substr($formatted_price, -3) == '.00') {
            return format_num($price, 0);
        }
        return $formatted_price;
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
                <div class="pl-4"><?= !empty($name) ? htmlentities($name) : 'ไม่พบข้อมูลลูกค้า' ?>, <br><?= !empty($contact) ? htmlentities($contact) : 'ไม่พบเบอร์โทรศัพท์' ?></div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">ที่อยู่จัดส่ง:</label>
                <div class="pl-4">
                    <?= !empty($delivery_address) ? nl2br(htmlentities(str_replace([" จ.", " จังหวัด"], ["\nจ.", "\nจังหวัด"], $delivery_address))) : 'ไม่พบที่อยู่' ?>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="mb-3">
                <label for="" class="control-label">สถานะการชำระเงิน:</label>
                <div class="pl-4">
                    <?php
                    if (isset($payment_status)) {
                        switch ((int)$payment_status) {
                            case 0:
                                echo '<span class="badge bg-secondary">ยังไม่ชำระเงิน</span>';
                                break;
                            case 1:
                                echo '<span class="badge bg-warning text-dark">รอตรวจสอบ</span>';
                                break;
                            case 2:
                                echo '<span class="badge bg-success">ชำระเงินแล้ว</span>';
                                break;
                            case 3:
                                echo '<span class="badge bg-danger">ชำระเงินล้มเหลว</span>';
                                break;
                            case 4:
                                echo '<span class="badge bg-secondary">กำลังยกเลิกคำสั่งซื้อ</span>';
                                break;
                            case 5:
                                echo '<span class="badge bg-secondary">กำลังคืนเงิน</span>';
                                break;
                            case 6:
                                echo '<span class="badge bg-dark">คืนเงินแล้ว</span>';
                                break;
                            default:
                                echo '<span class="badge bg-light">N/A</span>';
                                break;
                        }
                    }
                    ?>
                </div>
            </div>
            <div class="mb-3">
                <label for="" class="control-label">สถานะการจัดส่ง:</label>
                <div class="pl-4">
                    <?php
                    if (isset($delivery_status)) {
                        switch ((int)$delivery_status) {
                            case 0:
                                echo '<span class="badge bg-secondary">ตรวจสอบคำสั่งซื้อ</span>';
                                break;
                            case 1:
                                echo '<span class="badge bg-info">กำลังเตรียมของ</span>';
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
                                echo '<span class="badge bg-danger">จัดส่งไม่สำเร็จ</span>';
                                break;
                            case 6:
                                echo '<span class="badge bg-secondary">กำลังยกเลิกคำสั่งซื้อ</span>';
                                break;
                            case 7:
                                echo '<span class="badge bg-dark">คืนระหว่างทาง</span>';
                                break;
                            case 8:
                                echo '<span class="badge bg-secondary">กำลังคืนสินค้า</span>';
                                break;
                            case 9:
                                echo '<span class="badge bg-dark">คืนของสำเร็จ</span>';
                                break;
                            case 10:
                                echo '<span class="badge bg-danger">ยกเลิกแล้ว</span>';
                                break;
                            default:
                                echo '<span class="badge bg-light">N/A</span>';
                                break;
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="control-label head-detail">บริษัทขนส่ง :</label>
                <div class="pl-4 ">
                    <?= htmlentities($tracking_id ?: 'ไม่พบเลขขนส่ง') ?>
                    <br>
                    <?= htmlentities($shipping_providers) ?>
                    <br>
                    ค่าส่ง:
                    <?php if ($shipping_cost == 0): ?>
                        <span class="text-success fw-bold">ส่งฟรี</span>
                    <?php else: ?>
                        <?= number_format($shipping_cost, 2) ?> บาท
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div id="item_list" class="list-group">
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
                                <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-order img-fluid">
                            </a>
                        </div>
                        <div class="col-auto flex-shrink-1 flex-grow-1">
                            <div style="line-height:1.2em">
                                <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class=" product-link">
                                    <span class='mb-1 fw-bold order-peoduct'><?= $row['product'] ?></span>
                                </a>
                                <small class="text-muted order-band"><?= $row['brand'] ?> | <?= $row['category'] ?></small>
                                <div class="text-muted d-flex align-items-center gap-1 mt-1">
                                    <small class="text-muted order-band">
                                        <?= format_num($row['quantity'], 0) ?> x
                                    </small>
                                    <?php if ($row['price'] < $row['product_price']): ?>
                                        <small class="text-muted ml-1 order-amount" style="text-decoration: line-through;">
                                            <?= format_price_custom($row['product_price'], 2) ?>
                                        </small>
                                        <small class="text-danger fw-bold ml-1 order-amount">
                                            <?= format_price_custom($row['price'], 2) ?>
                                        </small>
                                    <?php else: ?>
                                        <span class="ml-1 order-amount"><?= format_price_custom($row['price'], 2) ?></span>
                                    <?php endif; ?>
                                    <span class=" ml-1 order-amount">บาท</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto text-right order-item-actions">
                            <span class="text-dark mb-0 order-price">
                                <b><?= format_price_custom($row['price'] * $row['quantity'], 2) ?> บาท</b>
                            </span>
                            <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class="btn btn-orders mt-2">
                                <i class="fa fa-basket-shopping me-1"></i> ซื้ออีกครั้ง
                            </a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php if ($order_items->num_rows <= 0): ?>
            <h5 class="text-center text-muted">ไม่พบรายการการสั่งซื้อ</h5>
        <?php endif; ?>

        <div class="d-flex justify-content-end py-3">
            <div class="order-total">
                <div class="d-flex justify-content-between order-total-detail ">
                    <span>ยอดรวมสินค้า:</span>
                    <span><?= isset($gt) ? format_price_custom($gt, 2) : '0.00' ?> บาท</span>
                </div>

                <div class="d-flex justify-content-between order-total-detail ">
                    <span>ค่าจัดส่ง:</span>
                    <?php if (isset($shipping_cost) && $shipping_cost == 0): ?>
                        <span class="text-success fw-bold">ส่งฟรี</span>
                    <?php else: ?>
                        <span><?= isset($shipping_cost) ? format_price_custom($shipping_cost, 2) : '0.00' ?> บาท</span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($promotion_name) && ((isset($promotion_discount) && $promotion_discount > 0) || $promotion_type == 'free_shipping')): ?>
                    <div class="d-flex justify-content-between text-danger order-total-detail ">
                        <span>โปรโมชัน (<?= htmlspecialchars($promotion_name) ?>):</span>
                        <?php if ($promotion_type == 'free_shipping'): ?>
                            <span>ส่งฟรี</span>
                        <?php else: ?>
                            <span>-<?= format_price_custom($promotion_discount, 2) ?> บาท</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($coupon_name) && ((isset($coupon_discount) && $coupon_discount > 0) || $coupon_type == 'free_shipping')): ?>
                    <div class="d-flex justify-content-between text-danger order-total-detail ">
                        <span>คูปอง (<?= htmlspecialchars($coupon_name) ?>):</span>
                        <?php if ($coupon_type == 'free_shipping'): ?>
                            <span>ส่งฟรี</span>
                        <?php else: ?>
                            <span>-<?= format_price_custom($coupon_discount, 2) ?> บาท</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <hr style="margin: 1rem 0;">

                <div class="d-flex justify-content-between order-total">
                    <h4><b>ยอดรวมทั้งสิ้น:</b></h4>
                    <h4><b><?= isset($grand_total) ? format_price_custom($grand_total, 2) : '0.00' ?> บาท</b></h4>
                </div>
            </div>
        </div>

    </div>
</div>