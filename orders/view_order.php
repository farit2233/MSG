<?php
require_once('../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
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

// ======================= START: ส่วนที่เพิ่มเข้ามาสำหรับดึงข้อมูลโปรโมชั่นและคูปอง =======================
$promotion_name = '';
if (!empty($promotion_id)) {
    $promo_qry = $conn->query("SELECT name FROM `promotions_list` WHERE id = '{$promotion_id}'");
    if ($promo_qry->num_rows > 0) {
        $promotion_name = $promo_qry->fetch_assoc()['name'];
    } else {
        $promotion_name = "โปรโมชั่น"; // แสดงข้อความทั่วไปหากไม่พบ
    }
}

$coupon_name = '';
if (!empty($coupon_code_id)) {
    $coupon_qry = $conn->query("SELECT coupon_code FROM `coupon_code_list` WHERE id = '{$coupon_code_id}'");
    if ($coupon_qry->num_rows > 0) {
        $coupon_name = $coupon_qry->fetch_assoc()['coupon_code'];
    } else {
        $coupon_name = "คูปองส่วนลด"; // แสดงข้อความทั่วไปหากไม่พบ
    }
}
// ======================= END: ส่วนที่เพิ่มเข้ามาสำหรับดึงข้อมูลโปรโมชั่นและคูปอง =======================

$customer_name = '';
if (!empty($customer_id)) {
    $cus = $conn->query("SELECT CONCAT(firstname, ' ', middlename, ' ', lastname,' ',contact) AS fullname FROM customer_list WHERE id = '{$customer_id}'");
    if ($cus->num_rows > 0) {
        $customer_name = $cus->fetch_assoc()['fullname'];
    }
}

$shipping_methods_name = 'ไม่ระบุขนส่ง';
if (!empty($shipping_methods_id)) {
    $shipping_query = $conn->query("SELECT name FROM shipping_methods WHERE id = '{$shipping_methods_id}'");
    if ($shipping_query->num_rows > 0) {
        $shipping_methods_name = $shipping_query->fetch_assoc()['name'];
    } else {
        $shipping_methods_name = 'ไม่พบข้อมูลขนส่ง';
    }
}

$total_weight = 0;
if (isset($id)) {
    $weight_qry = $conn->query("
        SELECT
            oi.quantity,
            p.product_weight
        FROM order_items oi
        INNER JOIN product_list p ON oi.product_id = p.id
        WHERE oi.order_id = '{$id}'
    ");
    while ($w = $weight_qry->fetch_assoc()) {
        $total_weight += ($w['product_weight'] * $w['quantity']);
    }
}

$shipping_cost = 0.00;
if (!empty($shipping_prices_id)) {
    $cost_qry = $conn->query("SELECT price FROM shipping_prices WHERE id = '{$shipping_prices_id}'");
    if ($cost_qry && $cost_qry->num_rows > 0) {
        $shipping_cost = (float)$cost_qry->fetch_assoc()['price'];
    }
} elseif (!empty($shipping_methods_id)) {
    // Fallback
    $cost_qry = $conn->query("
        SELECT price FROM shipping_prices
        WHERE shipping_methods_id = '{$shipping_methods_id}' AND {$total_weight} BETWEEN min_weight AND max_weight
        LIMIT 1
    ");
    if ($cost_qry && $cost_qry->num_rows > 0) {
        $shipping_cost = (float)$cost_qry->fetch_assoc()['price'];
    }
}

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

<style>
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

    .order-peoduct {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        /* จำนวนบรรทัด */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .order-amount {
        font-size: 14px;
    }

    .order-price {
        font-size: 18px;
        display: block;
    }

    .order-total-detail {
        font-size: 16px;
        text-align: right;
        margin-top: 1rem;
        margin-bottom: 1rem;
        gap: 0.5rem;
    }

    .order-total {
        margin-top: 1.5rem;
        gap: 0.5rem;
    }

    .order-band {
        display: -webkit-box;
        -webkit-line-clamp: 1;
        /* จำนวนบรรทัด */
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media (max-width: 1200px) {
        .order-price {
            font-size: 16px;
            display: block;
        }

        .btn-orders {
            font-size: 12px;
            background-color: #f57421;
            border-radius: 13px;
            color: white;
            transition: all 0.2s ease-in-out;
        }
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
                <div class="pl-4"><?= isset($coupon_code) ? $coupon_code : '' ?></div>
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
                    if (isset($payment_status)) {
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
                    }
                    ?>
                </div>
            </div>

            <div class="mb-3">
                <label class="control-label head-detail">บริษัทขนส่ง :</label>
                <div class="pl-4 ">
                    <?= htmlentities($shipping_methods_name) ?>
                    <br>
                    น้ำหนักรวม: <?= number_format($total_weight, 0) ?> กรัม
                    <br>
                    ค่าส่ง: <?= number_format($shipping_cost, 2) ?> บาท
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
                                <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-logo">
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
                                        <?= format_num($row['quantity'], 0) ?> x</>
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
                        <div class="col-auto text-right">
                            <div class="col-auto text-right">
                                <span class="text-dark mb-0 order-price">
                                    <b><?= format_price_custom($row['price'] * $row['quantity'], 2) ?> บาท</b>
                                </span>
                                <a href=".?p=products/view_product&id=<?= $row['product_id'] ?>" class="btn btn-orders mt-2 d-block">
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
            <div class="order-total">
                <div class="d-flex justify-content-between order-total-detail ">
                    <span>ยอดรวมสินค้า:</span>
                    <span><?= isset($gt) ? format_price_custom($gt, 2) : '0.00' ?> บาท</span>
                </div>

                <div class="d-flex justify-content-between order-total-detail ">
                    <span>ค่าจัดส่ง:</span>
                    <span><?= isset($shipping_cost) ? format_price_custom($shipping_cost, 2) : '0.00' ?> บาท</span>
                </div>

                <?php if (!empty($promotion_name) && isset($promotion_discount) && $promotion_discount > 0): ?>
                    <div class="d-flex justify-content-between text-danger order-total-detail ">
                        <span>โปรโมชั่น (<?= htmlspecialchars($promotion_name) ?>):</span>
                        <span>-<?= format_price_custom($promotion_discount, 2) ?> บาท</span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($coupon_name) && isset($coupon_discount) && $coupon_discount > 0): ?>
                    <div class="d-flex justify-content-between text-danger order-total-detail ">
                        <span>คูปอง (<?= htmlspecialchars($coupon_name) ?>):</span>
                        <span>-<?= format_price_custom($coupon_discount, 2) ?> บาท</span>
                    </div>
                <?php endif; ?>
                <hr style="margin: 1rem 0;">

                <div class="d-flex justify-content-between order-total">
                    <h4><b>ยอดรวมทั้งสิ้น:</b></h4>
                    <h4><b><?= isset($total_amount) ? format_price_custom($total_amount, 2) : '0.00' ?> บาท</b></h4>
                </div>
            </div>
        </div>

    </div>
</div>