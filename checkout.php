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
$shipping_qry_all = $conn->query("SELECT id, name, description, cost FROM shipping_methods WHERE is_active = 1 AND delete_flag = 0 ORDER BY id ASC");
$default_shipping_qry = $conn->query("SELECT id, name, description, cost FROM shipping_methods WHERE is_active = 1 AND delete_flag = 0 ORDER BY id ASC LIMIT 1");
$default_shipping_id = 0;
$default_shipping_name = 'เลือกขนส่ง';
$default_shipping_cost = 0.00;

if ($default_shipping_qry && $row = $default_shipping_qry->fetch_assoc()) {
    $default_shipping_id = $row['id'];
    $default_shipping_name = $row['name'];
    $default_shipping_cost = floatval($row['cost']);
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
                $promotion_discount = $default_shipping_cost;
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
$grand_total = ($cart_total - $promotion_discount) + $final_shipping_cost;

?>

<style>
    /* สไตล์สำหรับโปรโมชั่นที่ใช้ไม่ได้ (ตัวจาง) */
    .promo-inactive {
        opacity: 0.5;
        text-decoration: line-through;
    }

    /* สไตล์สำหรับหมายเหตุ */
    .promo-note td {
        border-top: none !important;
        padding-top: 0 !important;
    }
</style>
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
                                                            <em><?= htmlspecialchars($promo['description']) ?></em>
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
                                <input type="hidden" name="total_amount" id="total_amount" value="<?= $grand_total ?>">
                                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
                                <input type="hidden" name="shipping_cost" id="shipping_cost_input" value="<?= $final_shipping_cost ?>">
                                <input type="hidden" name="shipping_methods_id" id="shipping_methods_id" value="<?= $default_shipping_id ?>">
                                <input type="hidden" name="shipping_methods_name" id="shipping_methods_name_input" value="<?= $default_shipping_name ?>">
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
                            onclick="selectShipping('<?= $row['id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', this)">

                            <div>
                                <strong><?= $row['name'] ?></strong>
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
    const cartTotal = parseFloat(<?= json_encode($cart_total) ?>) || 0;
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
        closeShippingModal();
    }
    let selectedShipping = null;

    // ============================
    // ฟังก์ชันกลางสำหรับคำนวณยอดรวมสุทธิ
    // ============================
    function updateGrandTotal(shippingCost) {
        let promoDiscount = 0;
        let finalShippingCost = parseFloat(shippingCost) || 0;
        let originalShippingCost = finalShippingCost; // เก็บค่าส่งดั้งเดิมไว้

        if (appliedPromo && cartTotal >= parseFloat(appliedPromo.minimum_order)) {
            // ถ้ายอดซื้อถึงเกณฑ์ ถึงจะคำนวณส่วนลด
            switch (appliedPromo.type) {
                case 'fixed':
                    promoDiscount = parseFloat(appliedPromo.discount_value);
                    break;
                case 'percent':
                    promoDiscount = cartTotal * (parseFloat(appliedPromo.discount_value) / 100);
                    break;
                case 'free_shipping':
                    // ส่วนลดเท่ากับค่าส่ง และค่าส่งที่จะบวกเพิ่มเป็น 0
                    promoDiscount = originalShippingCost;
                    finalShippingCost = 0;
                    break;
            }
        }
        // ถ้ายอดซื้อไม่ถึง หรือไม่มีโปรโมชั่น promoDiscount จะยังคงเป็น 0

        // คำนวณยอดรวมใหม่
        const grandTotal = (cartTotal - promoDiscount) + finalShippingCost;

        // อัปเดตค่าที่แสดงบนหน้าจอ
        document.getElementById('shipping-cost').innerText = originalShippingCost.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท';
        document.getElementById('shipping_cost_input').value = finalShippingCost;

        document.getElementById('order-total-text').innerText = grandTotal.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        document.getElementById('total_amount').value = grandTotal;
    }


    // ============================
    // ฟังก์ชันเลือกขนส่ง
    // ============================
    function selectShipping(id, name, element) {
        if (!element) return;
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        const totalWeight = parseInt(document.getElementById('total_weight').value) || 0;

        start_loader(); // เริ่ม loader
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=get_shipping_cost',
            method: 'POST',
            data: {
                shipping_methods_id: id,
                total_weight: totalWeight
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.status === 'success') {
                    const newShippingCost = parseFloat(resp.price) || 0;

                    // อัปเดตค่าใน hidden inputs และที่แสดงผล
                    document.getElementById('shipping_methods_id').value = id;
                    document.getElementById('shipping_methods_name_display').innerText = name;
                    document.getElementById('shipping_methods_name_input').value = name;

                    // เรียกใช้ฟังก์ชันคำนวณกลาง
                    updateGrandTotal(newShippingCost);

                    selectedShipping = {
                        id,
                        name,
                        cost: newShippingCost
                    };
                } else {
                    alert('ไม่สามารถคำนวณค่าขนส่งได้');
                }
                end_loader(); // หยุด loader
            },
            error: function() {
                alert('เกิดข้อผิดพลาดขณะคำนวณค่าขนส่ง');
                end_loader(); // หยุด loader
            }
        });
    }


    // ============================
    // โค้ดที่ทำงานเมื่อหน้าเว็บโหลดเสร็จ
    // ============================
    $(document).ready(function() {
        const totalWeight = parseInt(document.getElementById('total_weight').value) || 0;
        const initialShippingId = $('#shipping_methods_id').val();

        if (totalWeight > 0 && initialShippingId) {
            start_loader();
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=get_shipping_cost',
                method: 'POST',
                data: {
                    shipping_methods_id: initialShippingId,
                    total_weight: totalWeight
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        const initialCost = parseFloat(resp.price) || 0;
                        // เรียกใช้ฟังก์ชันคำนวณกลางเมื่อหน้าโหลดเสร็จ
                        updateGrandTotal(initialCost);
                    } else {
                        alert('ไม่สามารถคำนวณค่าขนส่งเริ่มต้นได้');
                        // ถ้าเกิด error ก็ยังต้องคำนวณยอดรวมโดยใช้ค่าส่งเป็น 0
                        updateGrandTotal(0);
                    }
                    end_loader();
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดขณะคำนวณค่าขนส่งเริ่มต้น');
                    updateGrandTotal(0); // คำนวณด้วยค่าส่ง 0
                    end_loader();
                }
            });
        } else {
            // ถ้าไม่มีน้ำหนัก ก็คำนวณด้วยค่าส่งเริ่มต้น (อาจเป็น 0)
            const initialCost = parseFloat(document.getElementById('shipping_cost_input').value) || 0;
            updateGrandTotal(initialCost);
        }
    });
</script>