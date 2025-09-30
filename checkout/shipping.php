<?php
require_once('../config.php');
// ตรวจสอบว่าเรามีการส่ง ID มาไหม (สำหรับฟังก์ชันเปลี่ยนรหัสผ่าน)
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) {
                $$k = $v;
            }
        }
    } else {
        echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
}


$selected_items = isset($_POST['selected_items']) ? explode(',', $_POST['selected_items']) : [];

$cart_total = 0;
$cart_total_before_vat = 0; // << เพิ่มบรรทัดนี้
$cart_items = [];
$total_weight = 0;

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
            p.product_weight,
            p.image_path
        FROM cart_list c 
        INNER JOIN product_list p ON c.product_id = p.id
        WHERE c.id IN ($ids) AND customer_id = '{$_settings->userdata('id')}'
    ");

    while ($row = $cart_qry->fetch_assoc()) {
        // คำนวณราคาหลังลด
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

        // น้ำหนักรวม
        $total_weight += ($row['product_weight'] ?? 0) * $row['quantity'];
        $cart_items[] = $row;
    }
}

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
        sm.status = 1 
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
<style>
    .text-size-input {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }

    .password input,
    .password select {
        border-radius: 13px;
        font-size: 16px;
    }

    .icon-success {
        color: #28a745;
        /* สีเขียวสำหรับติ๊กถูก */
        font-size: 60px;
        /* ขนาดไอคอนใหญ่ */
        margin-bottom: 20px;
        /* เว้นระยะห่างจากข้อความ */
    }
</style>
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
                <span style="float:right;"><?= format_price_custom($cost, 2) ?> บาท</span>
                <span class="checkmark">&#10003;</span>
            </div>
            <div class="desc text-muted" style="font-size: 0.9em;"><?= htmlspecialchars($row['description']) ?></div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>ไม่มีข้อมูลขนส่ง</p>
<?php endif; ?>
<script>
    let appliedCoupon = {
        id: 0,
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

        // --- [ บรรทัดที่ต้องเพิ่ม ] ---
        // อัปเดตข้อความแสดงราคาค่าส่งให้ตรงกับที่เลือก
        document.getElementById('shipping-cost').innerText = formatPrice(selectedShipping.cost) + ' บาท';

        updateGrandTotal(selectedShipping.cost);
        closeShippingModal();
    }

    let selectedShipping = null;

    function formatPrice(value) {
        if (isNaN(value)) return value;

        // แปลงเป็นจำนวน float
        let num = parseFloat(value);

        // ถ้าจำนวนเต็ม → ไม่แสดงทศนิยม
        if (num % 1 === 0) {
            return num.toLocaleString('th-TH', {
                maximumFractionDigits: 0
            });
        } else {
            // ถ้ามีทศนิยม → แสดง 2 หลัก
            return num.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // ============================
    // ฟังก์ชันกลางสำหรับคำนวณยอดรวมสุทธิ
    // ============================
    function updateGrandTotal(shippingCost) {
        let promoDiscount = 0;
        let finalShippingCost = parseFloat(shippingCost) || 0;

        // --- ส่วนที่ 1: คำนวณส่วนลดโปรโมชัน (โค้ดเดิมของคุณ) ---
        if (appliedPromo && cartTotal >= parseFloat(appliedPromo.minimum_order)) {
            switch (appliedPromo.type) {
                case 'fixed':
                    promoDiscount = parseFloat(appliedPromo.discount_value);
                    break;
                case 'percent':
                    promoDiscount = cartTotal * (parseFloat(appliedPromo.discount_value) / 100);
                    break;
                case 'free_shipping':
                    finalShippingCost = 0; // โปรโมชันส่งฟรี ทำให้ค่าส่งเป็น 0
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
        // ยอดรวม = (ราคาสินค้า - ส่วนลดโปรโมชัน - ส่วนลดคูปอง) + ค่าส่งสุดท้าย
        const grandTotal = (cartTotal - promoDiscount - couponDiscount) + finalShippingCost;


        // อัปเดตยอดรวมสุทธิ (ใช้ toLocaleString เหมือนเดิมได้)
        // ฟังก์ชันอัปเดตยอดรวม
        function updateOrderTotal(grandTotal) {
            let formattedTotal;

            if (grandTotal % 1 === 0) {
                // จำนวนเต็ม → ไม่แสดงทศนิยม
                formattedTotal = grandTotal.toLocaleString('th-TH', {
                    maximumFractionDigits: 0
                });
            } else {
                // มีทศนิยม → แสดง 2 หลัก
                formattedTotal = grandTotal.toLocaleString('th-TH', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            document.getElementById('order-total-text').innerText = formattedTotal;
            document.getElementById('order-vat-total').innerText = formattedTotal;
        }

        // ตัวอย่างการเรียกใช้งาน
        updateOrderTotal(grandTotal);

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

    $(document).ready(function() {
        const initialCost = parseFloat(<?= json_encode($default_shipping_cost) ?>) || 0;
        updateGrandTotal(initialCost);
        let timeout;

        end_loader();
        $('#address_option_modal').click(function() {
            modal_confirm('สมุดที่อยู่ <i class="fa fa-pencil"></i>', 'checkout/address.php?pid=<?= isset($id) ? $id : '' ?>')
        })
        $('#shipping_option_modal').click(function() {
            modal_confirm('เลือกขนส่ง <i class="fa fa-truck"></i>', 'checkout/address.php?pid=<?= isset($id) ? $id : '' ?>')
        })

        $('#apply_coupon_button').on('click', function() {
            var coupon_code = $('#coupon_code_input').val().trim();
            var error_el = $('#coupon_error_message');
            var discount_val_el = $('#discount_value');
            var discount_type_el = $('#discount_type');
            var quantity_warning_message_el = $('#quantity_warning_message'); // เพิ่มตัวแปรสำหรับแสดงข้อความเตือน

            if (coupon_code === '') {
                error_el.text('กรุณากรอกรหัสคูปอง');
                return;
            }

            // เริ่ม loader (ถ้ามี)
            start_loader();
            error_el.text('');

            // ส่งข้อมูลไปตรวจสอบที่หลังบ้าน
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=apply_coupon',
                method: 'POST',
                data: {
                    coupon_code: coupon_code,
                    cart_items: cartItems, // ตัวแปรนี้ต้องมีข้อมูลสินค้าในตะกร้า
                    cart_total: cartTotal // ตัวแปรนี้คือยอดรวมราคาสินค้า
                },
                dataType: 'json',
                error: function(err) {
                    console.error(err);
                    alert_toast("เกิดข้อผิดพลาดในการตรวจสอบคูปอง", "error");
                    end_loader();
                },
                success: function(resp) {
                    if (resp.success) {
                        // === ✅ จุดสำคัญที่สุด ===
                        // นำ ID ของคูปองมาใส่ใน hidden input
                        $('#applied_coupon_id').val(resp.coupon_code_id);
                        // ======================

                        // อัปเดตตัวแปร appliedCoupon เพื่อใช้ในการคำนวณราคาสุทธิ
                        appliedCoupon.id = resp.coupon_code_id;
                        appliedCoupon.amount = resp.discount_amount;
                        appliedCoupon.type = resp.type;

                        // แสดงผลลัพธ์ให้ผู้ใช้เห็น
                        discount_type_el.text(resp.message);

                        // แสดงข้อความเตือนจำนวนคูปองที่เหลือ
                        if (resp.quantity_warning_message) {
                            quantity_warning_message_el.text(resp.quantity_warning_message).show(); // แสดงข้อความ
                        } else {
                            quantity_warning_message_el.hide(); // ซ่อนข้อความถ้าไม่มี
                        }

                        // 👇 เพิ่มส่วนนี้เข้ามา 👇
                        if (resp.type === 'free_shipping') {
                            discount_val_el.html('<strong class="text-danger">ส่งฟรี</strong>');
                        } else {
                            discount_val_el.html('<strong class="text-danger">- ' + formatPrice(resp.discount_amount) + ' บาท</strong>');
                        }
                        // 👆 เพิ่มส่วนนี้เข้ามา 👆

                        error_el.text('');
                        alert_toast("ใช้คูปองสำเร็จ!", "success");
                    } else {
                        // --- กรณีใช้คูปองไม่สำเร็จ ---
                        // ล้างค่า ID ใน hidden input
                        $('#applied_coupon_id').val(0);

                        // ล้างค่าในตัวแปร
                        appliedCoupon.id = 0;
                        appliedCoupon.amount = 0;
                        appliedCoupon.type = null;

                        // แสดงข้อผิดพลาด
                        error_el.text(resp.error);
                        discount_type_el.text('');
                        discount_val_el.text('');
                        quantity_warning_message_el.hide(); // ซ่อนข้อความเตือนถ้าใช้คูปองไม่สำเร็จ
                        alert_toast(resp.error, "error");
                    }

                    // เรียกฟังก์ชันคำนวณยอดรวมใหม่อีกครั้ง
                    updateGrandTotal(initialShippingCost); // ใช้ค่าส่งเริ่มต้น
                    end_loader();
                }
            });
        });
    });
</script>
<script>
    $(document).ready(function() {
        var passwordInput = $('#new_password');
        var requirementsDiv = $('#password-requirements');
        var lengthReq = $('#length');
        var lowerReq = $('#lowercase');
        var numReq = $('#number');

        // เมื่อเริ่มพิมพ์ในช่องรหัสผ่าน
        passwordInput.on('focus', function() {
            requirementsDiv.slideDown('fast');
        });

        passwordInput.on('keyup', function() {
            var password = $(this).val();

            // ตรวจสอบความยาว
            if (password.length >= 8) {
                lengthReq.removeClass('invalid').addClass('valid');
            } else {
                lengthReq.removeClass('valid').addClass('invalid');
            }

            // ตรวจสอบตัวพิมพ์เล็ก
            if (password.match(/[a-z]/)) {
                lowerReq.removeClass('invalid').addClass('valid');
            } else {
                lowerReq.removeClass('valid').addClass('invalid');
            }

            // ตรวจสอบตัวเลข
            if (password.match(/\d/)) {
                numReq.removeClass('invalid').addClass('valid');
            } else {
                numReq.removeClass('valid').addClass('invalid');
            }
        });

        // ซ่อนเมื่อไม่ได้โฟกัสและช่องว่าง
        passwordInput.on('blur', function() {
            if ($(this).val() === '') {
                requirementsDiv.slideUp('fast');
            }
        });


        // --- ฟังก์ชันสำหรับสลับฟอร์ม ---
        function showChangePasswordForm() {
            $('#change_password_form').show();
            $('#forgot_password_form').hide();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').show();
            $('.modal-footer #btn_forgot_password').hide();
            $('.modal-title').html('เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i>');
        }

        function showForgotPasswordForm() {
            $('#change_password_form').hide();
            $('#forgot_password_form').show();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_change_password').hide();
            $('.modal-footer #btn_forgot_password').show();
            $('.modal-title').html('ลืมรหัสผ่าน <i class="fa fa-pencil"></i>');
        }

        // --- Event Handlers ---

        // เมื่อคลิกลิงก์ "ลืมรหัสผ่าน?"
        $('#forgot_password_link').click(function(e) {
            e.preventDefault();
            showForgotPasswordForm();
        });

        // เมื่อคลิกลิงก์ "กลับไปหน้าเปลี่ยนรหัสผ่าน"
        $('#back_to_change_password').click(function(e) {
            e.preventDefault();
            showChangePasswordForm();
        });


        // เมื่อ submit ฟอร์ม "เปลี่ยนรหัสผ่าน"
        $('#change_password_form').submit(function(e) {
            e.preventDefault();

            var newPassword = $('#new_password').val();
            var confirmPassword = $('input[name="confirm_password"]').val();

            if (newPassword !== confirmPassword) {
                Swal.fire('ข้อผิดพลาด', 'รหัสผ่านใหม่และยืนยันรหัสผ่านไม่ตรงกัน', 'error');
                return;
            }

            start_loader(); // แสดง loader (ถ้ามี)
            $.ajax({
                url: _base_url_ + 'classes/Users.php?f=password&id=' + $('input[name="id"]').val(),
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        Swal.fire('สำเร็จ', 'เปลี่ยนรหัสผ่านเรียบร้อยแล้ว', 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader(); // ซ่อน loader (ถ้ามี)
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้', 'error');
                    end_loader();
                }
            });
        });

        // เมื่อ submit ฟอร์ม "ลืมรหัสผ่าน"
        $('#forgot_password_form').submit(function(e) {
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=forgot_password",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        // ซ่อนฟอร์มและปุ่มทั้งหมด แล้วแสดงข้อความสำเร็จ
                        $('#change_password_form').hide();
                        $('#forgot_password_form').hide();
                        $('#reset_success_message').show();
                        $('.modal-footer #btn_change_password').hide();
                        $('.modal-footer #btn_forgot_password').hide();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถส่งคำขอรีเซ็ตรหัสผ่านได้', 'error');
                    end_loader();
                }
            });
        });
    });
</script>