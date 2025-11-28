<?php
// ============================
// PHP: ตรวจสอบสิทธิ์ผู้ใช้ และดึงข้อมูลตะกร้า
// ============================
if ($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2) {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}

$selected_items = isset($_POST['selected_items']) ? explode(',', $_POST['selected_items']) : [];

// ตัวแปรเดิมของคุณ
$cart_total = 0;
$cart_total_before_vat = 0;
$cart_items = [];
$total_weight = 0;

// [เพิ่ม] ตัวแปรสำหรับแสดงสรุป (ราคาเต็ม และ จำนวนชิ้น)
$total_full_price = 0;
$total_items_count = 0;

if (!empty($selected_items)) {
    $ids = implode(',', array_map('intval', $selected_items));
    $cart_qry = $conn->query("
        SELECT 
            c.*, 
            p.name as product, 
            p.price,
            p.vat_price,
            p.discount_type,
            p.discount_value,
            p.discounted_price,
            p.product_size,
            p.product_weight,
            p.image_path
        FROM cart_list c 
        INNER JOIN product_list p ON c.product_id = p.id
        WHERE c.id IN ($ids) AND customer_id = '{$_settings->userdata('id')}'
    ");

    while ($row = $cart_qry->fetch_assoc()) {
        // --- Logic เดิมของคุณ (รักษาไว้ 100%) ---
        $original_price = $row['vat_price'];

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
        $cart_total_before_vat += $row['price'] * $row['quantity'];
        $total_weight += ($row['product_weight'] ?? 0) * $row['quantity'];

        // --- [ส่วนที่เพิ่ม] เพื่อใช้แสดงผล ---
        // คำนวณราคาเต็มรวม (vat_price * จำนวน)
        $total_full_price += $row['vat_price'] * $row['quantity'];
        // นับจำนวนชิ้นรวม
        $total_items_count += $row['quantity'];

        $cart_items[] = $row;
    }
}

// คำนวณส่วนลดรวม (ราคาเต็ม - ราคาขายจริง)
$total_discount = $total_full_price - $cart_total;

// ============================
// PHP: ดึงข้อมูลลูกค้า และสร้างที่อยู่
// ============================

// ============================
// PHP: ดึงข้อมูลลูกค้า และสร้างที่อยู่ (✨ UPDATED SECTION)
// ============================

$customer = $conn->query("SELECT * FROM customer_list WHERE id = '{$_settings->userdata('id')}'")->fetch_assoc();
$address = $conn->query("SELECT * FROM customer_addresses WHERE customer_id = '{$_settings->userdata('id')}' AND is_primary = 1")->fetch_assoc();

// --- เพิ่มการตรวจสอบความสมบูรณ์ของที่อยู่ ---
$is_address_complete = false;
if (
    $address &&
    !empty($address['name']) &&
    !empty($address['contact']) &&
    !empty($address['address']) &&
    !empty($address['sub_district']) &&
    !empty($address['district']) &&
    !empty($address['province']) &&
    !empty($address['postal_code'])
) {
    $is_address_complete = true;
}
// --- สิ้นสุดการตรวจสอบ ---

$full_address = "";
if ($address) { // ตรวจสอบจาก $address โดยตรง
    $parts = [];
    // ส่วนนี้เหมือนเดิม: สร้าง string ที่อยู่เต็ม
    if (!empty($address['name'])) $parts[] = $address['name'];
    if (!empty($address['contact'])) $parts[] = $address['contact'];
    if (!empty($address['address'])) $parts[] = $address['address'];
    if (!empty($address['sub_district'])) $parts[] = "ต." . $address['sub_district'];
    if (!empty($address['district'])) $parts[] = "อ." . $address['district'];
    if (!empty($address['province'])) $parts[] = "จ." . $address['province'];
    if (!empty($address['postal_code'])) $parts[] = $address['postal_code'];

    $full_address = implode(", ", $parts);
}


// ============================
// PHP: ดึงข้อมูลขนส่ง สำหรับแสดงใน modal และค่า default
// ============================
// น้ำหนักรวมที่คำนวณไว้แล้ว
$total_weight = $total_weight ?? 0; // มีการคำนวณไว้แล้วในส่วนบน

// ============================
// [NEW] PHP: ระบบคำนวณค่าส่งอัตโนมัติ (Size-based & Total-based)
// ============================

// 1. ดึงการตั้งค่าระบบขนส่ง
$sys_qry = $conn->query("SELECT * FROM shipping_system LIMIT 1");
$shipping_system = $sys_qry->num_rows > 0 ? $sys_qry->fetch_assoc() : [
    'N' => 40,
    'L' => 60,
    'XL' => 80,
    'price_default' => 80,
    'rules_size' => 1,
    'rules_total' => 1
];

// 2. ตรวจสอบขนาดสินค้าในตะกร้า (ต้องเพิ่ม p.product_size ใน query หลักก่อน ดูข้อ 2 ด้านล่าง)
$has_size_xl = false;
$has_size_l  = false;
// (ตัวแปร $cart_items ถูกวนลูปเก็บไว้ด้านบนแล้ว เราจะวนเช็คอีกรอบ หรือแก้ใน loop แรกก็ได้ แนะนำ loop เช็คตรงนี้เพื่อความชัวร์)
foreach ($cart_items as $item) {
    // สมมติว่า field ใน database ชื่อ product_size
    $size = isset($item['product_size']) ? strtoupper(trim($item['product_size'])) : 'N';
    if ($size == 'XL') $has_size_xl = true;
    if ($size == 'L')  $has_size_l  = true;
}

// 3. เริ่มคำนวณค่าส่งตามเงื่อนไข 3 ข้อ
$calc_shipping_cost = 0;
$shipping_rule_name = "";

// กฏข้อ 1: กฎตามขนาดสินค้า (Size-based) -> ถ้ามี L หรือ XL
if (($has_size_xl || $has_size_l) && $shipping_system['rules_size'] == 1) {
    if ($has_size_xl) {
        $calc_shipping_cost = $shipping_system['XL'];
        $shipping_rule_name = "ค่าจัดส่ง (สินค้าขนาด XL)";
    } else {
        $calc_shipping_cost = $shipping_system['L'];
        $shipping_rule_name = "ค่าจัดส่ง (สินค้าขนาด L)";
    }
}
// กฏข้อ 2: กฎตามยอดรวม (Total-based) -> ถ้ามีแต่ Normal (ไม่มี L, XL)
elseif (!$has_size_xl && !$has_size_l && $shipping_system['rules_total'] == 1) {
    // ดึงเรทราคาตามยอดรวมจากตาราง shipping_total
    // หาระหว่าง min_price และ max_price (หรือ max_price เป็น NULL คือมากกว่าขึ้นไป)
    $total_qry = $conn->query("
    SELECT * FROM shipping_total 
    WHERE min_price <= '$cart_total' 
    AND (max_price >= '$cart_total' OR max_price IS NULL)
    ORDER BY min_price DESC LIMIT 1
");

    if ($total_qry->num_rows > 0) {
        $rule = $total_qry->fetch_assoc();
        if ($rule['free_shipping'] == 1) {
            $calc_shipping_cost = 0;
            $shipping_rule_name = "ค่าจัดส่ง (โปรส่งฟรีตามยอดซื้อ)";
        } else {
            $calc_shipping_cost = $rule['shipping_price'];
            $shipping_rule_name = "ค่าจัดส่ง (ตามยอดซื้อ)";
        }
    } else {
        // กรณีไม่เข้าช่วงราคาใดๆ เลย (อาจใช้ default)
        $calc_shipping_cost = $shipping_system['price_default'];
        $shipping_rule_name = "ค่าจัดส่งมาตรฐาน";
    }
}
// กฏข้อ 3: ค่าส่งมาตรฐาน (Default) -> ถ้าไม่เข้าเงื่อนไขบน
else {
    $calc_shipping_cost = $shipping_system['price_default'];
    $shipping_rule_name = "ค่าจัดส่งมาตรฐาน";
}

// กำหนดตัวแปรสำหรับนำไปใช้ต่อ (แทนที่ตัวแปรระบบเก่า)
$default_shipping_cost = floatval($calc_shipping_cost);
$default_shipping_name = $shipping_rule_name;
$default_shipping_id   = 0; // ระบบใหม่ไม่มี ID ขนส่งแยกย่อย


// ==========================================================
// PHP: ส่วนคำนวณโปรโมชันทั้งหมด
// ==========================================================
$cart_promotions = [];
$product_has_promo_status = [];
$promotion_discount = 0; // ตัวแปรเก็บส่วนลดจากโปรโมชัน
$applied_promo = null; // ตัวแปรเก็บข้อมูลโปรโมชันที่ใช้ได้

// --- วนลูปเพื่อรวบรวมข้อมูลโปรโมชันทั้งหมดก่อน ---
foreach ($cart_items as $item) {
    $promo_query = "
        SELECT p.id, p.name, p.description, p.type, p.discount_value, p.minimum_order, p.start_date, p.end_date
        FROM promotion_products pp
        JOIN promotions_list p ON pp.promotion_id = p.id
        WHERE pp.product_id = {$item['product_id']}
        AND pp.status = 1
        AND pp.delete_flag = 0
        AND p.status = 1
        AND p.delete_flag = 0
        AND p.start_date <= NOW()
        AND p.end_date >= NOW()
    ";

    $promo_result = $conn->query($promo_query);

    if ($promo_result && $promo_result->num_rows > 0) {
        $promo_data = $promo_result->fetch_assoc();
        $cart_promotions[$promo_data['id']] = $promo_data;
        $product_has_promo_status[] = $promo_data['id'];
    } else {
        $product_has_promo_status[] = false;
    }
}

// --- วิเคราะห์โปรโมชันที่รวบรวมได้ ---
$is_promo_applicable = false;
$unique_promo_ids = array_unique(array_filter($product_has_promo_status));

if (count($unique_promo_ids) === 1 && !in_array(false, $product_has_promo_status, true)) {
    $is_promo_applicable = true;
}

function formatPrice($value)
{
    if (floor($value) == $value) {
        // จำนวนเต็ม → ไม่แสดงทศนิยม
        return number_format($value, 0);
    } else {
        // มีทศนิยม → แสดง 2 หลัก
        return number_format($value, 2);
    }
}
// --- คำนวณส่วนลดถ้าโปรโมชันใช้งานได้ และ ตรวจสอบ minimum_order ---
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
        $promo_suggestion_message = "ซื้อเพิ่มอีก " . formatPrice($needed_amount, 2) . " บาท เพื่อรับโปรโมชันนี้";
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
        $coupon_suggestion_message = "ซื้อเพิ่มอีก " . formatPrice($needed_amount, 2) . " บาท เพื่อรับคูปองนี้";
    }
}

$grand_total = ($cart_total - $coupon_discount - $promotion_discount) + $final_shipping_cost;
$vat_total = ($cart_total - $coupon_discount - $promotion_discount) + $final_shipping_cost;

if (!function_exists('format_price_custom')) {
    function format_price_custom($vat_price)
    {
        $formatted_price = format_num($vat_price, 2);
        if (substr($formatted_price, -3) == '.00') {
            return format_num($vat_price, 0);
        }
        return $formatted_price;
    }
}
?>
<script>
    // ส่งค่าจาก PHP มาให้ JavaScript
    var cartItems = <?= json_encode(array_values($cart_items)); ?>;
    var initialGrandTotal = <?= $grand_total; ?>;
    var initialVatTotal = <?= $vat_total; ?>;
    var shippingCost = <?= $final_shipping_cost; ?>;
</script>

<section class="py-5 container">
    <div class="checkout-header-bar d-flex align-items-center gap-2">
        <i class="fa-solid fa-square-check mr-2 text-success" style="font-size: 30px;"></i>
        <h3 class="d-inline mb-0">ยืนยันคำสั่งซื้อ</h3>
    </div>

    <div class="row">
        <div class="col-lg-8">

            <div class="checkout-card">
                <div class="checkout-card-header">
                    <i class="fa-solid fa-location-dot text-danger"></i>
                    <div class="checkout-card-title flex-grow-1">ที่อยู่สำหรับจัดส่ง</div>
                    <a href="javascript:void(0);" id="address_option_modal" class="clickable-text-btn text-primary" style="font-size: 0.9rem;">เปลี่ยน</a>
                </div>
                <div class="checkout-card-body">
                    <?php if (!$is_address_complete): ?>
                        <div class="alert alert-warning mb-0">
                            <i class="fa fa-exclamation-triangle"></i> <strong>ข้อมูลไม่ครบ!</strong>
                            กรุณา <a href="./?p=user" class="alert-link">เพิ่มที่อยู่</a> ให้ครบถ้วน
                        </div>
                    <?php else: ?>
                        <h6 class="font-weight-bold mb-1">
                            <?= !empty($address['name']) ? htmlentities($address['name']) : '' ?>
                            <span class="text-muted font-weight-normal ml-2"><?= !empty($address['contact']) ? htmlentities($address['contact']) : '' ?></span>
                        </h6>
                        <p class="text-muted mb-0" style="line-height: 1.6;">
                            <?php
                            // ใช้ Logic ตัดคำจังหวัดขึ้นบรรทัดใหม่ตามที่คุณต้องการ
                            if (!empty($address)) {
                                $addr_str = "";
                                if (!empty($address['address'])) $addr_str .= htmlentities($address['address']) . " ";
                                if (!empty($address['sub_district'])) $addr_str .= "ต." . htmlentities($address['sub_district']) . " ";
                                if (!empty($address['district'])) $addr_str .= "อ." . htmlentities($address['district']) . " ";
                                if (!empty($address['province'])) $addr_str .= " จ." . htmlentities($address['province']); // เว้นวรรคหน้า จ.
                                if (!empty($address['postal_code'])) $addr_str .= " " . htmlentities($address['postal_code']);

                                echo nl2br(str_replace([" จ.", " จังหวัด"], ["\nจ.", "\nจังหวัด"], $addr_str));
                            } else {
                                echo "ไม่พบข้อมูลที่อยู่";
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="checkout-card">
                <div class="checkout-card-header">
                    <i class="fa-solid fa-box text-warning"></i>
                    <span class="checkout-card-title">รายการสินค้า</span>
                    <span class="ml-auto text-muted small"><?= count($cart_items) ?> รายการ</span>
                </div>
                <div class="checkout-card-body">
                    <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $item):
                            $is_discounted = $item['final_price'] < $item['vat_price'];
                        ?>
                            <div class="item-row">
                                <div class="item-image-container">
                                    <img src="<?= validate_image($item['image_path']) ?>" class="item-image img-fluid" alt="<?= htmlspecialchars($item['product']) ?>">
                                </div>
                                <div class="item-details">
                                    <div class="item-name"><?= $item['product'] ?></div>
                                    <div class="item-meta mt-1">
                                    </div>
                                </div>
                                <div class="item-price-qty">
                                    <?php if ($is_discounted): ?>
                                        <span class="old-price"><?= format_price_custom($item['vat_price'], 2) ?></span>
                                        <span class="current-price text-danger"><?= format_price_custom($item['final_price'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="current-price"><?= format_price_custom($item['vat_price'], 2) ?></span>
                                    <?php endif; ?>
                                    <div class="text-muted small mt-1">x <?= $item['quantity'] ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted my-4">ไม่มีสินค้าในรายการ</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="checkout-card">
                <div class="checkout-card-header">
                    <i class="fa-solid fa-truck-fast text-info"></i>
                    <div class="checkout-card-title flex-grow-1">ข้อมูลการจัดส่ง</div>
                </div>
                <div class="checkout-card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 font-weight-bold"><?= $default_shipping_name ?></h6>
                            <small class="text-muted">คำนวณอัตโนมัติจากเงื่อนไขสินค้า</small>
                        </div>
                        <div class="font-weight-bold">
                            <span id="shipping-cost"><?= ($default_shipping_cost == 0) ? 'ฟรี' : format_price_custom($default_shipping_cost, 2) . ' บาท' ?></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-lg-4">
            <div class="sticky-sidebar">

                <div class="checkout-payment">
                    <div class="checkout-card-header">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                        <span class="checkout-card-title">สรุปคำสั่งซื้อ</span>
                    </div>
                    <div class="checkout-card-body">

                        <div class="mb-4">
                            <label class="small text-muted mb-1">โค้ดส่วนลด / คูปอง</label>
                            <div class="coupon-box">
                                <input type="text" id="coupon_code_input" placeholder="กรอกโค้ดที่นี่">
                                <button type="button" id="apply_coupon_button">ใช้</button>
                            </div>
                            <small id="coupon_error_message" class="text-danger"></small>
                            <small id="discount_type" class="text-success d-block"></small>
                        </div>

                        <div class="summary-row">
                            <span>ยอดรวมสินค้า (<?= number_format($total_items_count) ?> ชิ้น)</span>
                            <span><?= format_price_custom($total_full_price, 2) ?> บาท</span>
                        </div>

                        <?php if ($total_discount > 0.01): ?>
                            <div class="summary-row text-danger">
                                <span>ส่วนลดสินค้า</span>
                                <span>-<?= format_price_custom($total_discount, 2) ?> บาท</span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <span>ค่าจัดส่ง</span>
                            <span id="shipping-cost-summary"><?= format_price_custom($default_shipping_cost, 2) ?> บาท</span>
                        </div>

                        <?php if ($is_promo_applicable && $is_discount_applied): ?>
                            <div class="summary-row text-danger">
                                <span>โปรโมชัน (<?= htmlspecialchars($applied_promo['name']) ?>)</span>
                                <span>
                                    <?php
                                    if ($applied_promo['type'] == 'free_shipping') echo "ส่งฟรี";
                                    else echo "- " . format_price_custom($promotion_discount, 2);
                                    ?>
                                </span>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row text-danger" id="coupon-row" style="display:none;">
                            <span>ส่วนลดคูปอง</span>
                            <span id="discount_value"></span>
                        </div>

                        <div class="summary-row">
                            <span>ราคารวม <small class="text-muted">รวม VAT</small></span>
                            <span><span id="order-vat-total"><?= format_price_custom($cart_total, 2) ?></span> บาท</span>
                        </div>

                        <hr>

                        <div class="summary-row total">
                            <span>ยอดรวมทั้งสิ้น</span>
                            <span><span id="order-total-text"><?= format_price_custom($cart_total + $default_shipping_cost, 2) ?></span> บาท</span>
                        </div>

                        <form action="" id="order-form" class="mt-4">
                            <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
                            <input type="hidden" name="delivery_address" value="<?= htmlentities($full_address) ?>">
                            <input type="hidden" id="total_weight" value="<?= $total_weight ?>">
                            <input type="hidden" name="promotion_id" value="<?= ($is_discount_applied && isset($applied_promo['id'])) ? $applied_promo['id'] : '0' ?>">
                            <input type="hidden" name="coupon_code_id" id="applied_coupon_id" value="0">

                            <button type="submit" class="btn btn-checkout btn-block btn-lg w-100 shadow-sm" style="background-color: #f57421; border-color: #f57421;" <?= !$is_address_complete ? 'disabled' : '' ?>>
                                สั่งซื้อสินค้า
                            </button>
                        </form>

                    </div>
                </div>

                <div class="text-center text-muted small mt-2">
                    <i class="fa-solid fa-shield-halved mr-1"></i> ข้อมูลปลอดภัยด้วยการเข้ารหัส SSL
                </div>

            </div>
        </div>
    </div>

</section>
<script>
    var cartItems = <?= json_encode(array_values($cart_items)); ?>;
    var cartTotal = parseFloat(<?= json_encode($cart_total) ?>) || 0;
    var initialGrandTotal = <?= $grand_total; ?>; // ระวัง! ต้องคำนวณ grand_total ใน php ให้รวมค่าส่งใหม่ก่อนส่งมานะ

    // [แก้ไข] รับค่าที่คำนวณจาก PHP มาตรงๆ เลย
    var currentShippingCost = parseFloat(<?= $default_shipping_cost ?>);

    let appliedCoupon = {
        id: 0,
        amount: 0,
        type: null
    };
    const appliedPromo = <?= json_encode($applied_promo) ?>;

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


    function formatPrice(value) {
        if (isNaN(value)) return value;
        let num = parseFloat(value);
        if (num % 1 === 0) {
            return num.toLocaleString('th-TH', {
                maximumFractionDigits: 0
            });
        } else {
            return num.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    function updateGrandTotal(shippingCost) {
        let promoDiscount = 0;
        let finalShippingCost = parseFloat(shippingCost) || 0;

        if (appliedPromo && cartTotal >= parseFloat(appliedPromo.minimum_order)) {
            switch (appliedPromo.type) {
                case 'fixed':
                    promoDiscount = parseFloat(appliedPromo.discount_value);
                    break;
                case 'percent':
                    promoDiscount = cartTotal * (parseFloat(appliedPromo.discount_value) / 100);
                    break;
                case 'free_shipping':
                    finalShippingCost = 0;
                    break;
            }
        }

        let couponDiscount = 0;
        if (appliedCoupon.type === 'free_shipping') {
            finalShippingCost = 0;
        } else {
            couponDiscount = appliedCoupon.amount;
        }

        const grandTotal = (cartTotal - promoDiscount - couponDiscount) + finalShippingCost;
        let formattedTotal = grandTotal.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        if (grandTotal % 1 === 0) formattedTotal = grandTotal.toLocaleString('th-TH', {
            maximumFractionDigits: 0
        });

        document.getElementById('order-total-text').innerText = formattedTotal;
        document.getElementById('order-vat-total').innerText = formattedTotal;

        // Update Shipping Display ในส่วนสรุป (ขวามือ) ถ้ามีการเปลี่ยนแปลงจากคูปอง
        let shipText = (finalShippingCost === 0) ? "ฟรี" : formatPrice(finalShippingCost) + " บาท";
        document.getElementById('shipping-cost-summary').innerText = shipText;

        function updateOrderTotal(grandTotal) {
            let formattedTotal;
            if (grandTotal % 1 === 0) {
                formattedTotal = grandTotal.toLocaleString('th-TH', {
                    maximumFractionDigits: 0
                });
            } else {
                formattedTotal = grandTotal.toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
            document.getElementById('order-total-text').innerText = formattedTotal;
            document.getElementById('order-vat-total').innerText = formattedTotal;
        }
        updateOrderTotal(grandTotal);
    }

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

    $(document).ready(function() {
        const initialCost = parseFloat(<?= json_encode($default_shipping_cost) ?>) || 0;
        updateGrandTotal(initialCost);
        end_loader();

        $('#address_option_modal').click(function() {
            modal_confirm('สมุดที่อยู่ <i class="fa fa-pencil"></i>', 'checkout/address.php?pid=<?= isset($id) ? $id : '' ?>')
        });

        $('#apply_coupon_button').on('click', function() {
            var coupon_code = $('#coupon_code_input').val().trim();
            var error_el = $('#coupon_error_message');
            var discount_val_el = $('#discount_value');
            var discount_type_el = $('#discount_type');
            var coupon_row = $('#coupon-row'); // แถวแสดงส่วนลดคูปองใน summary

            if (coupon_code === '') {
                error_el.text('กรุณากรอกรหัสคูปอง');
                return;
            }
            start_loader();
            error_el.text('');

            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=apply_coupon',
                method: 'POST',
                data: {
                    coupon_code: coupon_code,
                    cart_items: cartItems,
                    cart_total: cartTotal
                },
                dataType: 'json',
                error: function(err) {
                    console.error(err);
                    alert_toast("เกิดข้อผิดพลาด", "error");
                    end_loader();
                },
                success: function(resp) {
                    if (resp.success) {
                        $('#applied_coupon_id').val(resp.coupon_code_id);
                        appliedCoupon.id = resp.coupon_code_id;
                        appliedCoupon.amount = resp.discount_amount;
                        appliedCoupon.type = resp.type;

                        discount_type_el.text(resp.message);
                        coupon_row.show(); // แสดงแถวคูปอง

                        if (resp.type === 'free_shipping') {
                            discount_val_el.html('<strong class="text-success">ส่งฟรี</strong>');
                        } else {
                            discount_val_el.html('<strong class="text-danger">- ' + formatPrice(resp.discount_amount) + ' บาท</strong>');
                        }
                        error_el.text('');
                        alert_toast("ใช้คูปองสำเร็จ!", "success");
                    } else {
                        $('#applied_coupon_id').val(0);
                        appliedCoupon.id = 0;
                        appliedCoupon.amount = 0;
                        appliedCoupon.type = null;

                        error_el.text(resp.error);
                        discount_type_el.text('');
                        coupon_row.hide(); // ซ่อนแถวคูปอง
                        alert_toast(resp.error, "error");
                    }
                    updateGrandTotal(currentShippingCost);
                    end_loader();
                }
            });
        });
    });
</script>