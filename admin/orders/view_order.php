<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    // สมมติว่าตาราง order_list มีคอลัมน์ promotion_id
    $qry = $conn->query("SELECT ol.*, ol.promotion_id FROM `order_list` ol where ol.id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}

// ชื่อลูกค้า
$customer_name = '';
if (!empty($customer_id)) {
    $cus = $conn->query("SELECT CONCAT(firstname, ' ', middlename, ' ', lastname,' ',contact) AS fullname FROM customer_list WHERE id = '{$customer_id}'");
    if ($cus->num_rows > 0) {
        $customer_name = $cus->fetch_assoc()['fullname'];
    }
}

// ข้อมูลขนส่ง
$shipping_methods_name = 'ไม่ระบุขนส่ง';
if (!empty($shipping_methods_id)) {
    $shipping_query = $conn->query("SELECT name, cost FROM shipping_methods WHERE id = '{$shipping_methods_id}'");
    if ($shipping_query->num_rows > 0) {
        $shipping_data = $shipping_query->fetch_assoc();
        $shipping_methods_name = $shipping_data['name'];
    } else {
        $shipping_methods_name = 'ไม่พบข้อมูลขนส่ง';
    }
}

$total_weight = 0;
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

$shipping_cost = 0.00;
if (!empty($shipping_methods_id)) {
    $cost_qry = $conn->query("
        SELECT price FROM shipping_prices
        WHERE
            shipping_methods_id = '{$shipping_methods_id}'
            AND {$total_weight} BETWEEN min_weight AND max_weight
        LIMIT 1
    ");
    if ($cost_qry && $cost_qry->num_rows > 0) {
        $shipping_cost = (float)$cost_qry->fetch_assoc()['price'];
    }
}
?>

<style>
    #order-logo {
        max-width: 100%;
        max-height: 20em;
        object-fit: scale-down;
        object-position: center center;
    }

    .product-logo {
        width: 7em;
        object-fit: cover;
        object-position: center center;
    }

    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    .head-detail {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">รายละเอียดคำสั่งซื้อ</div>
    </div>
    <div class="card-body">
        <div class="flex-column  justify-content-center align-items-center">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="card-title m-0 flex-grow-1" style="font-size: 18px !important;">รายละเอียดคำสั่งซื้อหมายเลข : <?= isset($code) ? $code : '' ?></div>
                    <div class="card-tools text-end">
                        <?php if (isset($status) && $status < 4): ?>
                            <button class="btn btn-info btn-sm bg-gradient-info rounded-0 mb-1" type="button" id="update_status">
                                อัปเดตสถานะ
                            </button>
                        <?php endif; ?>
                        <button class="btn btn-navy btn-sm bg-gradient-navy rounded-0 mb-1" type="button" id="print">
                            <i class="fa fa-print"></i> พิมพ์
                        </button>
                        <button class="btn btn-danger btn-sm bg-gradient-danger rounded-0 mb-1" type="button" id="delete_data">
                            <i class="fa fa-trash"></i> ลบ
                        </button>
                        <a class="btn btn-light btn-sm bg-gradient-light border rounded-0 mb-1" href="./?page=orders">
                            <i class="fa fa-angle-left"></i> กลับ
                        </a>
                    </div>
                </div>

                <div class="card-body ">
                    <div class="container-fluid">
                        <div class=" printout">
                            <div class="row mb-3">
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12 ">
                                    <div class="mb-3">
                                        <label for="" class="control-label head-detail">หมายเลขคำสั่งซื้อ :</label>
                                        <div class="pl-4"><?= isset($code) ? $code : '' ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="control-label head-detail">ที่อยู่จัดส่ง :</label>
                                        <div class="pl-4"><?= isset($delivery_address) ? str_replace(["\r\n", "\r", "\n"], "<br>", $delivery_address) : '' ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="control-label head-detail">ชื่อผู้รับ :</label>
                                        <div class="pl-4"><?= !empty($customer_name) ? htmlentities($customer_name) : 'ไม่พบข้อมูลลูกค้า' ?></div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                    <div class="mb-3">
                                        <label for="" class="control-label head-detail">สถานะการชำระเงิน :</label>
                                        <div class="pl-4 ">
                                            <?php
                                            switch ((int)$payment_status) {
                                                case 0:
                                                    echo '<span>ยังไม่ชำระเงิน</span>';
                                                    break;
                                                case 1:
                                                    echo '<span>รอตรวจสอบ</span>';
                                                    break;
                                                case 2:
                                                    echo '<span>ชำระแล้ว</span>';
                                                    break;
                                                case 3:
                                                    echo '<span>ล้มเหลว</span>';
                                                    break;
                                                case 4:
                                                    echo '<span>คืนเงินแล้ว</span>';
                                                    break;
                                                default:
                                                    echo '<span>N/A</span>';
                                                    break;
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="" class="control-label head-detail">สถานะการจัดส่ง :</label>
                                        <div class="pl-4 ">
                                            <?php
                                            switch ((int)$delivery_status) {
                                                case 0:
                                                    echo '<span>ตรวจสอบคำสั่งซื้อ</span>';
                                                    break;
                                                case 1:
                                                    echo '<span>เตรียมของ</span>';
                                                    break;
                                                case 2:
                                                    echo '<span>แพ๊กของแล้ว</span>';
                                                    break;
                                                case 3:
                                                    echo '<span>กำลังจัดส่ง</span>';
                                                    break;
                                                case 4:
                                                    echo '<span>จัดส่งสำเร็จ</span>';
                                                    break;
                                                case 5:
                                                    echo '<span>ส่งไม่สำเร็จ</span>';
                                                    break;
                                                case 6:
                                                    echo '<span>คืนของระหว่างทาง</span>';
                                                    break;
                                                case 7:
                                                    echo '<span>คืนของสำเร็จ</span>';
                                                    break;
                                                default:
                                                    echo '<span>N/A</span>';
                                                    break;
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
                                <?php
                                $gt = 0;
                                // ### MODIFIED QUERY: Added p.discounted_price to select statement ###
                                $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, p.discounted_price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
                                while ($row = $order_items->fetch_assoc()):
                                    // ### START: LOGIC FOR DISCOUNTED PRICE ###
                                    $price = $row['price'];
                                    $discounted_price = $row['discounted_price'];
                                    $has_discount = isset($discounted_price) && $discounted_price > 0;

                                    // Use discounted price if available, otherwise use regular price
                                    $effective_price = $has_discount ? $discounted_price : $price;
                                    $item_total = $effective_price * $row['quantity'];
                                    $gt += $item_total;
                                    // ### END: LOGIC FOR DISCOUNTED PRICE ###
                                ?>
                                    <div class="list-group-item cart-item" data-id='<?= $row['id'] ?>' data-max='<?= format_num($row['available'], 0) ?>'>
                                        <div class="d-flex w-100 align-items-center">
                                            <div class="col-2 text-center">
                                                <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail border p-0 product-logo">
                                            </div>
                                            <div class="col-auto flex-shrink-1 flex-grow-1">
                                                <div style="line-height:1em">
                                                    <div class='mb-0'><?= $row['product'] ?></div>
                                                    <div class="text-muted"><?= $row['brand'] ?></div>
                                                    <div class="text-muted"><?= $row['category'] ?></div>
                                                    <div class="text-muted d-flex w-100">
                                                        <?= format_num($row['quantity'], 0) ?> x
                                                        <?php if ($has_discount): ?>
                                                            <span class="text-danger px-2"><del><?= format_num($price, 2) ?></del></span> <b><?= format_num($effective_price, 2) ?></b>
                                                        <?php else: ?>
                                                            <span class="px-2"><b><?= format_num($effective_price, 2) ?></b></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-auto">
                                                <h5 class="text-bold"><?= format_num($item_total, 2) ?></h5>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <?php if ($order_items->num_rows <= 0): ?>
                                <h5 class="text-center text-muted">Order Items is empty.</h5>
                            <?php endif; ?>

                            <div id="item_list" class="list-group">
                                <?php
                                $gt = 0;
                                $order_items = $conn->query("SELECT o.*, p.name as product, p.brand as brand, p.price, p.discounted_price, cc.name as category, p.image_path, COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) as `available` FROM `order_items` o inner join product_list p on o.product_id = p.id inner join category_list cc on p.category_id = cc.id where order_id = '{$id}' ");
                                while ($row = $order_items->fetch_assoc()):
                                    $price = $row['price'];
                                    $discounted_price = $row['discounted_price'];
                                    $has_discount = isset($discounted_price) && $discounted_price > 0;
                                    $effective_price = $has_discount ? $discounted_price : $price;
                                    $item_total = $effective_price * $row['quantity'];
                                    $gt += $item_total; // $gt คือยอดรวมราคาสินค้าทั้งหมด (Subtotal)
                                ?>
                                <?php endwhile; ?>
                            </div>
                            <?php if ($order_items->num_rows <= 0): ?>
                                <h5 class="text-center text-muted">Order Items is empty.</h5>
                            <?php endif; ?>
                            <?php
                            $discount_amount = 0;
                            $shipping_discount = 0; // เพิ่มตัวแปรสำหรับส่วนลดค่าส่ง
                            $promotion_name = '';
                            $original_shipping_cost = $shipping_cost;

                            // ตรวจสอบว่ามี promotion_id หรือไม่
                            if (!empty($promotion_id)) {
                                $promo_qry = $conn->query("SELECT * FROM `promotions_list` WHERE id = '{$promotion_id}' AND status = 1");
                                if ($promo_qry->num_rows > 0) {
                                    $promo_data = $promo_qry->fetch_assoc();

                                    // ตรวจสอบว่ายอดสั่งซื้อถึงขั้นต่ำหรือไม่
                                    if ($gt >= $promo_data['minimum_order']) {
                                        $promotion_name = $promo_data['name'];
                                        switch ($promo_data['type']) {
                                            case 'percent':
                                                $discount_amount = ($gt * $promo_data['discount_value']) / 100;
                                                break;
                                            case 'fixed':
                                                $discount_amount = $promo_data['discount_value'];
                                                break;
                                            case 'free_shipping':
                                                // แก้ไข: ไม่ได้หักจาก $gt แต่ให้เป็นส่วนลดค่าส่ง
                                                $shipping_discount = $shipping_cost; // ส่วนลดค่าส่งจะเท่ากับค่าส่งทั้งหมด
                                                $shipping_cost = 0; // ตั้งค่าส่งเป็น 0
                                                break;
                                                // สามารถเพิ่ม case 'code' ได้ถ้ามีเงื่อนไขเพิ่มเติม
                                        }
                                    }
                                }
                            }

                            // คำนวณส่วนลดทั้งหมด
                            $total_discount = $discount_amount + $shipping_discount;

                            // คำนวณยอดรวมสุทธิใหม่
                            // ยอดรวม = (ยอดรวมราคาสินค้า - ส่วนลดราคาสินค้า) + ค่าส่งที่ลดแล้ว
                            $grand_total = ($gt - $discount_amount) + $shipping_cost;
                            ?>

                            <div class="d-flex justify-content-end py-3">
                                <div class="col-auto">
                                    <div class="text-right">
                                        <h5>ยอดรวม: <?= format_num($gt, 2) ?> บาท</h5>

                                        <?php if ($shipping_discount > 0): ?>
                                            <h5>ค่าจัดส่ง: <span><?= format_num($original_shipping_cost, 2) ?></span> บาท</h5>
                                            <h5>
                                                ส่วนลด : <?= htmlspecialchars($promotion_name) ?> (-<?= format_num($shipping_discount, 2) ?> บาท)
                                            </h5>
                                        <?php else: ?>
                                            <h5>ค่าจัดส่ง: <?= format_num($original_shipping_cost, 2) ?> บาท</h5>
                                        <?php endif; ?>

                                        <?php if ($discount_amount > 0): ?>
                                            <h5>
                                                ส่วนลด : <?= htmlspecialchars($promotion_name) ?> (-<?= format_num($discount_amount, 2) ?> บาท)
                                            </h5>
                                        <?php endif; ?>

                                        <hr>
                                        <h4><b>รวมทั้งสิ้น : <?= format_num($grand_total, 2) ?> บาท</b></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>

    <noscript id="print-header">
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold">
                        <large><?= $_settings->info('name') ?></large>
                    </div>
                    <div class="text-center font-weight-bold">
                        <large>รายละเอียดคำสั่งซื้อ</large>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    </noscript>
</section>
<script>
    function print_t() {
        var h = $('head').clone()
        var el = ""
        $('.printout').map(function() {
            var p = $(this).clone()
            p.find('.btn').remove()
            p.find('.card').addClass('border')
            p.removeClass('col-lg-8 col-md-10 col-sm-12 col-xs-12')
            p.addClass('col-12')
            el += p[0].outerHTML
        })
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("รายละเอียดคำสั่งซื้อ - มุมมองการพิพม์")
        var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
        nw.document.querySelector('head').innerHTML = h.html()
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML
        nw.document.querySelector('body').innerHTML += el
        nw.document.close()
        start_loader()
        setTimeout(() => {
            nw.print()
            setTimeout(() => {
                nw.close()
                end_loader()
            }, 200);
        }, 300);
    }
    $(function() {
        $('#print').click(function() {
            print_t()
        })
        $('#assign_team').click(function() {
            uni_modal("Assign a Team", 'orders/assign_team.php?id=<?= isset($id) ? $id : '' ?>')
        })
        $('#delete_data').click(function() {
            _conf("คุณแน่ใจหรือไม่ที่จะลบคำสั่งซื้อนี้?", "delete_order", ["<?= isset($id) ? $id : '' ?>"])
        })
        $('#update_status').click(function() {
            uni_modal("อัปเดตสถานะ", "orders/update_status.php?id=<?= isset($id) ? $id : '' ?>")
        })
    })

    function delete_order($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_order",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.replace("./?page=orders");
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>