<?php
// ============================
// PHP: ตรวจสอบสิทธิ์ผู้ใช้ และดึงข้อมูลตะกร้า
// ============================
if ($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2) {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}

$selected_items = isset($_POST['selected_items']) ? explode(',', $_POST['selected_items']) : [];

$cart_total = 0;
$cart_items = [];
$total_weight = 0;

if (!empty($selected_items)) {
    $ids = implode(',', array_map('intval', $selected_items));
    $cart_qry = $conn->query("
        SELECT 
            c.*, 
            p.name as product, 
            p.price,
            p.discount_type,
            p.discount_value,
            p.discounted_price,
            p.product_weight,
            p.image_path
        FROM cart_list c 
        INNER JOIN product_list p ON c.product_id = p.id
        WHERE c.id IN ($ids) AND customer_id = '{$_settings->userdata('id')}'
    ");

    while ($row = $cart_qry->fetch_assoc()) {
        // คำนวณราคาหลังลด
        $original_price = $row['price'];
        if (!is_null($row['discounted_price'])) {
            $final_price = $row['discounted_price'];
        } elseif ($row['discount_type'] === 'amount') {
            $final_price = $original_price - $row['discount_value'];
        } elseif ($row['discount_type'] === 'percent') {
            $final_price = $original_price - ($original_price * $row['discount_value'] / 100);
        } else {
            $final_price = $original_price;
        }

        $row['final_price'] = $final_price;
        $cart_total += $final_price * $row['quantity'];

        // น้ำหนักรวม
        $total_weight += ($row['product_weight'] ?? 0) * $row['quantity'];
        $cart_items[] = $row;
    }
}

// ============================
// PHP: ดึงข้อมูลลูกค้า และสร้างที่อยู่
// ============================
$customer = $conn->query("SELECT * FROM customer_list WHERE id = '{$_settings->userdata('id')}'")->fetch_assoc();
$full_address = "";
if ($customer) {
    $parts = [];
    if (!empty($customer['address'])) $parts[] = $customer['address'];
    if (!empty($customer['sub_district'])) $parts[] = "ต." . $customer['sub_district'];
    if (!empty($customer['district'])) $parts[] = "อ." . $customer['district'];
    if (!empty($customer['province'])) $parts[] = "จ." . $customer['province'];
    if (!empty($customer['postal_code'])) $parts[] = $customer['postal_code'];
    $full_address = implode(", ", $parts);
}

// ============================
// PHP: ดึงข้อมูลขนส่ง สำหรับแสดงใน modal และค่า default
// ============================
// น้ำหนักรวมที่คำนวณไว้แล้ว
$total_weight = $total_weight ?? 0; // มีการคำนวณไว้แล้วในส่วนบน

// เตรียม query เพื่อดึงค่าส่งตามน้ำหนัก
// เราจะ join ตาราง shipping_methods กับ shipping_prices
// และหาช่วงน้ำหนักที่ถูกต้อง
$shipping_query_string = "
    SELECT 
        sm.id, 
        sm.name, 
        sm.description, 
        sp.price as cost  -- ดึงราคาจากตาราง shipping_prices
    FROM 
        shipping_methods sm
    LEFT JOIN 
        shipping_prices sp ON sm.id = sp.shipping_methods_id
    WHERE 
        sm.is_active = 1 
        AND sm.delete_flag = 0
        AND ('{$total_weight}' >= sp.min_weight AND '{$total_weight}' <= sp.max_weight) -- เงื่อนไขสำคัญ
    ORDER BY 
        sm.id ASC
";

$shipping_qry_all = $conn->query($shipping_query_string);

// หาค่า Default (ตัวแรกที่เจอ)
$default_shipping_id = 0;
$default_shipping_name = 'ยังไม่มีขนส่งสำหรับน้ำหนักนี้';
$default_shipping_cost = 0.00;

// ใช้ query เดิม แต่เพิ่ม LIMIT 1
$default_shipping_qry = $conn->query($shipping_query_string . " LIMIT 1");

if ($default_shipping_qry && $row = $default_shipping_qry->fetch_assoc()) {
    $default_shipping_id = $row['id'];
    $default_shipping_name = $row['name'];
    $default_shipping_cost = floatval($row['cost']); // ค่าส่งตามน้ำหนัก
}

// Reset pointer เพื่อให้ loop ใน Modal ทำงานได้ปกติ
if ($shipping_qry_all) {
    $shipping_qry_all->data_seek(0);
}

// ==========================================================
// PHP: ส่วนคำนวณโปรโมชั่นทั้งหมด
// ==========================================================
$cart_promotions = [];
$product_has_promo_status = [];
$promotion_discount = 0; // ตัวแปรเก็บส่วนลดจากโปรโมชั่น
$applied_promo = null; // ตัวแปรเก็บข้อมูลโปรโมชั่นที่ใช้ได้

// --- วนลูปเพื่อรวบรวมข้อมูลโปรโมชั่นทั้งหมดก่อน ---
foreach ($cart_items as $item) {
    $promo_query = "SELECT p.id, p.name, p.description, p.type, p.discount_value, p.minimum_order FROM promotion_products pp
                    JOIN promotions_list p ON pp.promotion_id = p.id
                    WHERE pp.product_id = {$item['product_id']} AND pp.status = 1 AND pp.delete_flag = 0";
    $promo_result = $conn->query($promo_query);

    if ($promo_result && $promo_result->num_rows > 0) {
        $promo_data = $promo_result->fetch_assoc();
        $cart_promotions[$promo_data['id']] = $promo_data;
        $product_has_promo_status[] = $promo_data['id'];
    } else {
        $product_has_promo_status[] = false;
    }
}

// --- วิเคราะห์โปรโมชั่นที่รวบรวมได้ ---
$is_promo_applicable = false;
$unique_promo_ids = array_unique(array_filter($product_has_promo_status));

if (count($unique_promo_ids) === 1 && !in_array(false, $product_has_promo_status, true)) {
    $is_promo_applicable = true;
}

// --- คำนวณส่วนลดถ้าโปรโมชั่นใช้งานได้ และ ตรวจสอบ minimum_order ---
$final_shipping_cost = $default_shipping_cost;
$promo_suggestion_message = null;
$is_discount_applied = false;

if ($is_promo_applicable) {
    $applied_promo = reset($cart_promotions);

    // ตรวจสอบยอดสั่งซื้อขั้นต่ำ
    if ($cart_total >= $applied_promo['minimum_order']) {
        // --- ยอดซื้อถึงเกณฑ์ ---
        $is_discount_applied = true;

        switch ($applied_promo['type']) {
            case 'fixed':
                $promotion_discount = floatval($applied_promo['discount_value']);
                break;
            case 'percent':
                $promotion_discount = $cart_total * (floatval($applied_promo['discount_value']) / 100);
                break;
            case 'free_shipping':
                $final_shipping_cost = 0;
                break;
        }
    } else {
        // --- ยอดซื้อไม่ถึงเกณฑ์ ---
        $is_discount_applied = false;
        $needed_amount = $applied_promo['minimum_order'] - $cart_total;
        $promo_suggestion_message = "ซื้อเพิ่มอีก " . number_format($needed_amount, 2) . " บาท เพื่อรับโปรโมชั่นนี้";
    }
}

// ==========================================================
// PHP: ส่วนคำนวณคูปองทั้งหมด
// ==========================================================
$cart_coupons = [];
$product_has_coupon_status = [];
$coupon_discount = 0; // ตัวแปรเก็บส่วนลดจากคูปอง
$applied_coupon = null; // ตัวแปรเก็บข้อมูลคูปองที่ใช้ได้

// --- ตรวจสอบคูปองในตะกร้า ---
foreach ($cart_items as $item) {
    $coupon_query = "SELECT c.id, c.coupon_code, c.name, c.type, c.discount_value, c.minimum_order 
                     FROM coupon_code_list c 
                     LEFT JOIN coupon_code_products cp ON c.id = cp.coupon_code_id
                     WHERE c.status = 1 
                     AND c.delete_flag = 0
                     AND c.start_date <= NOW() 
                     AND c.end_date >= NOW() 
                     AND (c.all_products_status = 1 OR cp.product_id = {$item['product_id']})"; // ตรวจสอบว่าคูปองสามารถใช้กับสินค้านี้ได้

    $coupon_result = $conn->query($coupon_query);

    if ($coupon_result && $coupon_result->num_rows > 0) {
        while ($coupon_data = $coupon_result->fetch_assoc()) {
            $cart_coupons[$coupon_data['id']] = $coupon_data;
            $product_has_coupon_status[] = $coupon_data['id'];
        }
    } else {
        $product_has_coupon_status[] = false;
    }
}

// --- วิเคราะห์คูปองที่รวบรวมได้ ---
$is_coupon_applicable = false;
$unique_coupon_ids = array_unique(array_filter($product_has_coupon_status));

if (count($unique_coupon_ids) === 1 && !in_array(false, $product_has_coupon_status, true)) {
    $is_coupon_applicable = true;
}

// --- คำนวณส่วนลดถ้าคูปองใช้งานได้ และ ตรวจสอบ minimum_order ---
$final_shipping_cost = $default_shipping_cost;
$coupon_suggestion_message = null;
$is_coupon_applied = false;

if ($is_coupon_applicable) {
    $applied_coupon = reset($cart_coupons);

    // ตรวจสอบยอดสั่งซื้อขั้นต่ำ
    if ($cart_total >= $applied_coupon['minimum_order']) {
        // --- ยอดซื้อถึงเกณฑ์ ---
        $is_coupon_applied = true;

        switch ($applied_coupon['type']) {
            case 'fixed':
                $coupon_discount = floatval($applied_coupon['discount_value']);
                break;
            case 'percent':
                $coupon_discount = $cart_total * (floatval($applied_coupon['discount_value']) / 100);
                break;
            case 'free_shipping':
                $final_shipping_cost = 0;
                break;
        }
    } else {
        // --- ยอดซื้อไม่ถึงเกณฑ์ ---
        $is_coupon_applied = false;
        $needed_amount = $applied_coupon['minimum_order'] - $cart_total;
        $coupon_suggestion_message = "ซื้อเพิ่มอีก " . number_format($needed_amount, 2) . " บาท เพื่อรับคูปองนี้";
    }
}

$grand_total = ($cart_total - $coupon_discount - $promotion_discount) + $final_shipping_cost;

?>
<script>
    // ส่งค่าจาก PHP มาให้ JavaScript
    var cartItems = <?= json_encode(array_values($cart_items)); ?>;
    var initialGrandTotal = <?= $grand_total; ?>;
    var shippingCost = <?= $final_shipping_cost; ?>;
</script>
<section class="py-3">
    <div class="container">
        <div class="row mt-n4 justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow" style="margin-top: 3rem;">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div class="cart-header-bar d-flex align-items-center gap-2">
                                <i class="fa-solid fa-square-check mr-2 text-success" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">ยืนยันคำสั่งซื้อ</h3>
                            </div>

                            <?php if (!empty($cart_items)): ?>
                                <?php if (empty($full_address)): ?>
                                    <div class="alert alert-warning text-center">
                                        <strong>ยังไม่มีที่อยู่จัดส่ง!</strong><br>
                                        กรุณาไปที่หน้า <a href="./?p=user" class="alert-link">บัญชีของฉัน</a> เพื่อกรอกข้อมูลที่อยู่ก่อนทำการสั่งซื้อ
                                    </div>
                                <?php endif; ?>

                                <div class="table-responsive">
                                    <table class="table table-bordered mb-4 small-table">
                                        <thead>
                                            <tr>
                                                <th>สั่งซื้อสินค้าแล้ว</th>
                                                <th class="text-muted text-right" colspan="2">ราคาต่อหน่วย</th>
                                                <th class="text-muted text-right">จำนวน</th>
                                                <th class="text-muted text-right">รายการย่อย</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($cart_items as $item):
                                                $is_discounted = $item['final_price'] < $item['price'];
                                            ?>
                                                <tr class="no-border">
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="col-3 text-center">
                                                                <img src="<?= validate_image($item['image_path']) ?>" class="product-logo" alt="">
                                                            </div>
                                                            <h6 class="my-0 product-name"><?= $item['product'] ?></h6>
                                                        </div>
                                                    </td>
                                                    <td class="text-right" colspan="2">
                                                        <?php if ($is_discounted): ?>
                                                            <span><?= format_num($item['final_price'], 2) ?></span>
                                                        <?php else: ?>
                                                            <?= format_num($item['price'], 2) ?>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="text-right"><?= $item['quantity'] ?></td>
                                                    <td class="text-right"><span><?= format_num($item['final_price'] * $item['quantity'], 2) ?></span></td>
                                                </tr>
                                            <?php endforeach; ?>

                                            <tr class="no-border">
                                                <th></th>
                                            </tr>
                                            <tr class="no-border">
                                                <th></th>
                                            </tr>
                                            <tr class="no-border">
                                                <th></th>
                                            </tr>
                                        </tbody>
                                        <tfoot>
                                            <tr class="no-border">
                                                <th>
                                                    บริการขนส่ง
                                                    <span class="text-danger" style="font-size: 0.8em;">* อิงราคาจากน้ำหนักที่ใหญ่ที่สุดในตระกร้า</span>
                                                </th>
                                                <td class="text-right" colspan="2">
                                                    <span id="shipping_methods_name_display" style="margin-left: 10px;"><?= $default_shipping_name ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="javascript:void(0);" onclick="openShippingModal()">เปลี่ยน</a>
                                                </td>
                                                <td class="text-right">
                                                    <label id="shipping-cost"><?= number_format($default_shipping_cost, 2) ?> บาท</label>
                                                </td>
                                            </tr>

                                            <?php
                                            if (!empty($cart_promotions)) :
                                                $promo_class = ($is_promo_applicable && $is_discount_applied) ? 'promo-active' : 'promo-inactive';
                                                foreach ($cart_promotions as $promo) :
                                            ?>
                                                    <tr class="no-border <?= $promo_class ?>">
                                                        <th>
                                                            โปรโมชั่น
                                                            <span class="text-danger" style="font-size: 0.9em; display: block; font-weight: normal;">
                                                                <?= htmlspecialchars($promo['name']) ?>
                                                            </span>
                                                        </th>
                                                        <td colspan="3">
                                                            <!--em><?= htmlspecialchars($promo['description']) ?></em-->
                                                        </td>
                                                        <td class="text-right">
                                                            <strong>
                                                                <?php
                                                                // แสดงรายละเอียดส่วนลด
                                                                if ($promo['type'] == 'fixed') {
                                                                    echo "- " . number_format($promo['discount_value'], 2) . " บาท";
                                                                } elseif ($promo['type'] == 'percent') {
                                                                    echo "- " . number_format($promo['discount_value'], 2) . "%";
                                                                } elseif ($promo['type'] == 'free_shipping') {
                                                                    echo "ฟรีค่าจัดส่ง";
                                                                }
                                                                ?>
                                                            </strong>
                                                        </td>
                                                    </tr>
                                                <?php
                                                endforeach;

                                                // --- แสดงหมายเหตุ ถ้าโปรโมชั่นใช้ไม่ได้ (เพราะเลือกของไม่ครบ) ---
                                                if (!$is_promo_applicable && !empty($cart_promotions)) :
                                                ?>
                                                    <tr class="promo-note">
                                                        <td colspan="5" class="text-danger text-center" style="font-size: 0.9em;">
                                                            * กรุณาเลือกสินค้าทั้งหมดที่อยู่ในโปรโมชั่นเดียวกันเพื่อรับส่วนลด
                                                        </td>
                                                    </tr>
                                                <?php
                                                endif;

                                                // แสดงข้อความแนะนำให้ซื้อเพิ่ม
                                                if (isset($promo_suggestion_message)):
                                                ?>
                                                    <tr class="promo-note">
                                                        <td colspan="5" class="text-info text-center" style="font-size: 0.9em; font-weight: bold;">
                                                            <i class="fa fa-info-circle"></i> <?= $promo_suggestion_message ?>
                                                        </td>
                                                    </tr>
                                            <?php
                                                endif;
                                            endif;
                                            ?>
                                            <tr class="no-border">
                                                <th>
                                                    <div class="input-group" style="display: flex; align-items: center;">
                                                        คูปองส่วนลด
                                                        <input type="text" id="coupon_code_input" placeholder="กรอกรหัสคูปองส่วนลด" class="ml-3">
                                                        <div class="input-group-append">
                                                            <button type="button" id="apply_coupon_button" class="btn btn-primary btn-sm">ใช้</button>
                                                        </div>
                                                    </div>
                                                    <small id="coupon_error_message" class="text-danger" style="display: inline-block;"></small>
                                                </th>
                                                <td class="text-right">
                                                    <p id="discount_type"></p>
                                                </td>
                                                <td colspan="3" class="text-right">
                                                    <strong id="discount_value"></strong>
                                                </td>
                                            </tr>

                                            <tr>
                                                <th><strong>รวม</strong></th>
                                                <td colspan="5">
                                                    <h5 class="text-bold text-right">
                                                        <span id="order-total-text"><?= format_num($grand_total, 2) ?></span> บาท
                                                    </h5>
                                                </td>
                                            </tr>
                                        </tfoot>

                                    </table>

                                    <table class="table table-bordered mb-4 small-table">
                                        <thead>
                                            <tr>
                                                <th colspan="2">
                                                    <h5 class="text-bold">ที่อยู่จัดส่ง</h5>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>ชื่อ</th>
                                                <td><?= htmlentities($customer['firstname'] . ' ' . $customer['middlename'] . ' ' . $customer['lastname']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>เบอร์โทร</th>
                                                <td><?= htmlentities($customer['contact']) ?></td>
                                            </tr>
                                            <tr>
                                                <th>ที่อยู่</th>
                                                <td>
                                                    <?= htmlentities($customer['address']) ?><br>
                                                    <?= !empty($customer['sub_district']) ? 'ต.' . htmlentities($customer['sub_district']) . ' ' : '' ?>
                                                    <?= !empty($customer['district']) ? 'อ.' . htmlentities($customer['district']) . ' ' : '' ?>
                                                    <?= !empty($customer['province']) ? 'จ.' . htmlentities($customer['province']) : '' ?><br>
                                                    <?= htmlentities($customer['postal_code']) ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            <?php else: ?>
                                <h5 class="text-center text-muted">ไม่มีรายการที่เลือกสำหรับการชำระเงิน</h5>
                            <?php endif; ?>

                        </div>

                        <div class="container-fluid">
                            <div class="cart-header-bar d-flex align-items-center gap-2">
                                <i class="fa-solid fa-money-bill-wave mr-2 text-success" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">รูปแบบการชำระเงิน</h3>
                            </div>
                            <form action="" id="order-form">

                                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
                                <input type="hidden" name="shipping_methods_id" id="shipping_methods_id" value="<?= $default_shipping_id ?>">
                                <input type="hidden" name="delivery_address" value="<?= htmlentities($full_address) ?>">
                                <input type="hidden" id="total_weight" value="<?= $total_weight ?>">
                                <input type="hidden" name="promotion_id" value="<?= ($is_discount_applied && isset($applied_promo['id'])) ? $applied_promo['id'] : '0' ?>">

                                <div class="py-1 text-center">
                                    <button class="btn addcart rounded-pill" <?= empty($full_address) ? 'disabled' : '' ?>>
                                        ยืนยันคำสั่งซื้อ
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="shippingModal" class="modal-backdrop-custom" style="display:none;">
        <div class="shipping-modal-content">
            <div class="shipping-modal-header">เลือก ตัวเลือกการจัดส่ง</div>
            <div class="shipping-modal-body">
                <?php if ($shipping_qry_all && $shipping_qry_all->num_rows > 0): ?>
                    <?php while ($row = $shipping_qry_all->fetch_assoc()):
                        $cost = floatval($row['cost']);
                    ?>
                        <div class="shipping-option"
                            data-id="<?= $row['id'] ?>"
                            data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                            data-cost="<?= $cost ?>"
                            onclick="selectShipping(this)">

                            <div>
                                <strong><?= $row['name'] ?></strong>
                                <span style="float:right;"><?= number_format($cost, 2) ?> บาท</span>
                                <span class="checkmark">&#10003;</span>
                            </div>
                            <div class="desc text-muted" style="font-size: 0.9em;"><?= htmlspecialchars($row['description']) ?></div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>ไม่มีข้อมูลขนส่ง</p>
                <?php endif; ?>
            </div>
            <div class="shipping-modal-footer">
                <button class="btn-cancel" onclick="closeShippingModal()">ยกเลิก</button>
                <button class="btn-confirm" onclick="confirmShipping()">ยืนยัน</button>
            </div>
        </div>
    </div>
</section>

<script>
    let appliedCoupon = {
        amount: 0,
        type: null
    }; // ✨ ตัวแปรใหม่สำหรับจำคูปองที่ใช้
    const cartTotal = parseFloat(<?= json_encode($cart_total) ?>) || 0;
    const appliedPromo = <?= json_encode($applied_promo) ?>;
    const initialShippingCost = <?= $final_shipping_cost; ?>; // ✨ เก็บค่าส่งเริ่มต้น

    // ============================
    // JS: จัดการฟอร์มสั่งซื้อ
    // ============================
    $('#order-form').submit(function(e) {
        e.preventDefault();
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=place_order',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            error: err => {
                console.log(err);
                alert_toast("An error occurred", "error");
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.replace('./');
                } else {
                    alert_toast(resp.msg || "เกิดข้อผิดพลาดตอนสั่งซื้อ", 'error');
                }
                end_loader();
            }
        });
    });

    // ============================
    // JS: จัดการ Modal
    // ============================
    function openShippingModal() {
        document.getElementById('shippingModal').style.display = 'flex';
    }

    function closeShippingModal() {
        document.getElementById('shippingModal').style.display = 'none';
    }

    function confirmShipping() {
        if (!selectedShipping) return;

        document.getElementById('shipping_methods_id').value = selectedShipping.id;
        document.getElementById('shipping_methods_name_display').innerText = selectedShipping.name;

        updateGrandTotal(selectedShipping.cost);
        closeShippingModal();
    }
    let selectedShipping = null;

    // ============================
    // ฟังก์ชันกลางสำหรับคำนวณยอดรวมสุทธิ
    // ============================
    function updateGrandTotal(shippingCost) {
        let promoDiscount = 0;
        let finalShippingCost = parseFloat(shippingCost) || 0;

        // --- ส่วนที่ 1: คำนวณส่วนลดโปรโมชั่น (โค้ดเดิมของคุณ) ---
        if (appliedPromo && cartTotal >= parseFloat(appliedPromo.minimum_order)) {
            switch (appliedPromo.type) {
                case 'fixed':
                    promoDiscount = parseFloat(appliedPromo.discount_value);
                    break;
                case 'percent':
                    promoDiscount = cartTotal * (parseFloat(appliedPromo.discount_value) / 100);
                    break;
                case 'free_shipping':
                    finalShippingCost = 0; // โปรโมชั่นส่งฟรี ทำให้ค่าส่งเป็น 0
                    break;
            }
        }

        // --- ส่วนที่ 2: ✨ ตรวจสอบส่วนลดจาก "คูปอง" ---
        let couponDiscount = 0;

        if (appliedCoupon.type === 'free_shipping') {
            finalShippingCost = 0; // คูปองส่งฟรี ก็ทำให้ค่าส่งเป็น 0
            // ไม่ต้องกำหนด couponDiscount เพราะเราจัดการผ่าน finalShippingCost แล้ว
        } else {
            couponDiscount = appliedCoupon.amount; // ถ้าเป็นคูปองลดราคาปกติ
        }


        // --- ส่วนที่ 3: คำนวณยอดรวมสุดท้าย ---
        // ยอดรวม = (ราคาสินค้า - ส่วนลดโปรโมชั่น - ส่วนลดคูปอง) + ค่าส่งสุดท้าย
        const grandTotal = (cartTotal - promoDiscount - couponDiscount) + finalShippingCost;


        // อัปเดตยอดรวมสุทธิ (ใช้ toLocaleString เหมือนเดิมได้)
        document.getElementById('order-total-text').innerText = grandTotal.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // ============================
    // ฟังก์ชันเลือกขนส่ง
    // ============================
    function selectShipping(element) {
        if (!element) return;
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        selectedShipping = {
            id: element.dataset.id,
            name: element.dataset.name,
            cost: parseFloat(element.dataset.cost) || 0
        };
    }


    // ============================
    // โค้ดที่ทำงานเมื่อหน้าเว็บโหลดเสร็จ
    // ============================
    $(document).ready(function() {
        const initialCost = parseFloat(<?= json_encode($default_shipping_cost) ?>) || 0;
        updateGrandTotal(initialCost);
        let timeout;

        $('#apply_coupon_button').on('click', function() {
            var coupon_code = $('#coupon_code_input').val().trim();
            var errorMessage = $('#coupon_error_message');

            if (coupon_code === '') {
                errorMessage.text('กรุณากรอกรหัสคูปอง');
                return;
            }

            // เราต้องรู้ค่าส่งปัจจุบันเพื่อส่งไปให้ PHP คำนวณ 'free_shipping'
            // โดยดึงค่าจาก hidden input ที่เก็บ ID ขนส่ง
            const currentShippingId = $('#shipping_methods_id').val();
            let currentShippingCost = initialShippingCost; // ใช้ค่าเริ่มต้นไว้ก่อน

            // หาค่าส่งปัจจุบันจริงๆ จาก element ที่เราเลือกไว้ (ถ้ามี)
            const selectedOption = document.querySelector(`.shipping-option[data-id="${currentShippingId}"]`);
            if (selectedOption) {
                currentShippingCost = parseFloat(selectedOption.dataset.cost) || initialShippingCost;
            }


            $.ajax({
                url: 'check_coupon.php',
                method: 'POST',
                data: {
                    coupon_code: coupon_code,
                    cart_items: <?= json_encode(array_values($cart_items)); ?>,
                    cart_total: cartTotal,
                    shipping_cost: currentShippingCost // ส่งค่าส่งปัจจุบัน
                },
                dataType: 'json',
                success: function(response) {
                    errorMessage.text('');

                    if (response.success) {
                        // 1. อัปเดตตัวแปรกลาง appliedCoupon
                        appliedCoupon.amount = parseFloat(response.discount_amount);
                        appliedCoupon.type = response.type;

                        // 2. อัปเดต UI ที่เกี่ยวกับคูปองโดยเฉพาะ
                        $('#discount_type').text(response.message);

                        // เช็คว่า type เป็น 'free_shipping' หรือไม่
                        if (appliedCoupon.type === 'free_shipping') {
                            $('#discount_value').html('<strong>ส่งฟรี</strong>');
                        } else {
                            $('#discount_value').html('<strong>-' + appliedCoupon.amount.toFixed(2) + '</strong>');
                        }

                    } else {
                        errorMessage.text(response.error);
                        // 3. ถ้า error ให้รีเซ็ตค่าคูปอง
                        appliedCoupon = {
                            amount: 0,
                            type: null
                        };
                        $('#discount_type').text('');
                        $('#discount_value').html('');
                    }

                    // 4. ✨ เรียกใช้ฟังก์ชันคำนวณกลางเสมอ!
                    updateGrandTotal(currentShippingCost);
                },

                error: function() {
                    errorMessage.text('เกิดข้อผิดพลาดในการสื่อสารกับ Server');
                }
            });
        });
    });
</script>