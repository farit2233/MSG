<?php
// ==========================================
// 1. Logic PHP: Setup & Auth
// ==========================================
if ($_settings->userdata('id') == '') {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); location.replace('./');</script>";
    exit;
}

$selected_items = $_POST['selected_items'] ?? '';
$delivery_address = $_POST['delivery_address'] ?? '';
$total_weight = $_POST['total_weight'] ?? 0;

// รับค่า ID ของโปรโมชันและคูปอง
$promotion_id = isset($_POST['promotion_id']) ? intval($_POST['promotion_id']) : 0;
$coupon_code_id = isset($_POST['coupon_code_id']) ? intval($_POST['coupon_code_id']) : 0;

$bank_id = $_POST['bank_id'] ?? '';
// รับค่าส่งตั้งต้น (ก่อนหัก)
$base_shipping_cost = isset($_POST['shipping_cost']) ? floatval($_POST['shipping_cost']) : 0;

if (empty($selected_items) || empty($bank_id)) {
    echo "<script>location.replace('./?p=cart_list');</script>";
    exit;
}

// ==========================================
// 2. ดึงข้อมูลสินค้า & คำนวณ Subtotal
// ==========================================
$subtotal = 0;
$total_items_count = 0;
$order_items = [];

if (!empty($selected_items)) {
    $ids = implode(',', array_map('intval', explode(',', $selected_items)));
    $qry = $conn->query("
        SELECT c.*, p.name as product_name, p.image_path, p.vat_price, p.product_size
        FROM cart_list c 
        INNER JOIN product_list p ON c.product_id = p.id
        WHERE c.id IN ($ids)
    ");
    while ($row = $qry->fetch_assoc()) {
        $order_items[] = $row;
        $subtotal += ($row['vat_price'] * $row['quantity']);
        $total_items_count += $row['quantity'];
    }
}

// ==========================================
// 3. Logic PHP: คำนวณส่วนลด (Promotion & Coupon)
// ==========================================

$discount_promotion = 0;
$discount_coupon = 0;
$shipping_cost = $base_shipping_cost;
$promo_name = "";
$coupon_name = "";
$promo_type = "";   // เก็บประเภทโปรโมชัน
$coupon_type = "";  // เก็บประเภทคูปอง

// --- 3.1 ตรวจสอบ Promotion ---
if ($promotion_id > 0) {
    $promo_qry = $conn->query("SELECT * FROM promotions_list WHERE id = '{$promotion_id}' AND status = 1 AND delete_flag = 0 AND start_date <= NOW() AND end_date >= NOW()");
    if ($promo_qry->num_rows > 0) {
        $promo = $promo_qry->fetch_assoc();

        if ($subtotal >= $promo['minimum_order']) {
            $promo_name = $promo['name'];
            $promo_type = $promo['type']; // เก็บ type ไว้เช็กตอนแสดงผล

            if ($promo['type'] == 'fixed') {
                $discount_promotion = floatval($promo['discount_value']);
            } elseif ($promo['type'] == 'percent') {
                $discount_promotion = $subtotal * (floatval($promo['discount_value']) / 100);
            } elseif ($promo['type'] == 'free_shipping') {
                $shipping_cost = 0;
                // ไม่บวกค่า discount_promotion เป็นตัวเลข แต่จะไปแสดงผลว่า "ส่งฟรี"
            }
        }
    }
}

// --- 3.2 ตรวจสอบ Coupon ---
if ($coupon_code_id > 0) {
    $coupon_qry = $conn->query("SELECT * FROM coupon_code_list WHERE id = '{$coupon_code_id}' AND status = 1 AND delete_flag = 0 AND start_date <= NOW() AND end_date >= NOW()");
    if ($coupon_qry->num_rows > 0) {
        $coupon = $coupon_qry->fetch_assoc();

        if ($subtotal >= $coupon['minimum_order']) {
            $coupon_name = $coupon['coupon_code'];
            $coupon_type = $coupon['type']; // เก็บ type ไว้เช็กตอนแสดงผล

            if ($coupon['type'] == 'fixed') {
                $discount_coupon = floatval($coupon['discount_value']);
            } elseif ($coupon['type'] == 'percent') {
                $discount_coupon = $subtotal * (floatval($coupon['discount_value']) / 100);
            } elseif ($coupon['type'] == 'free_shipping') {
                $shipping_cost = 0;
            }
        }
    }
}

// ==========================================
// 4. คำนวณยอดสุทธิ (Grand Total)
// ==========================================
$final_total = ($subtotal - $discount_promotion - $discount_coupon) + $shipping_cost;
if ($final_total < 0) $final_total = 0;


// ==========================================
// 5. ดึงข้อมูลธนาคาร & Helper Function
// ==========================================
$bank_info = [];
if (!empty($bank_id)) {
    $bank_qry = $conn->query("SELECT * FROM bank_system WHERE id = '{$bank_id}'");
    if ($bank_qry->num_rows > 0) {
        $bank_info = $bank_qry->fetch_assoc();
    }
}

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
    .bank-logo-compact {
        width: 60px;
        height: 60px;
        object-fit: cover;
    }

    .account-num-compact {
        font-size: 1.5rem;
        font-weight: 700;
        color: #333;
        letter-spacing: 0.5px;
    }

    .price-compact {
        font-size: 1.8rem;
        font-weight: bold;
        color: #dc3545;
    }

    .copy-btn-small {
        font-size: 0.8rem;
        padding: 4px 10px;
        border-radius: 15px;
        background-color: #f1f3f5;
        color: #555;
        cursor: pointer;
        transition: 0.2s;
    }

    .copy-btn-small:hover {
        background-color: #e2e6ea;
        color: #000;
    }
</style>

<div class="container my-5">

    <div class="checkout-header-bar d-flex align-items-center gap-2">
        <i class="fa-solid fa-money-bill-transfer mr-2 text-success" style="font-size: 30px;"></i>
        <h3 class="d-inline mb-0">แจ้งชำระเงิน</h3>
    </div>

    <form id="place-order-form">
        <input type="hidden" name="selected_items" value="<?= htmlspecialchars($selected_items) ?>">
        <input type="hidden" name="delivery_address" value="<?= htmlspecialchars($delivery_address) ?>">
        <input type="hidden" name="total_weight" value="<?= $total_weight ?>">
        <input type="hidden" name="promotion_id" value="<?= $promotion_id ?>">
        <input type="hidden" name="coupon_code_id" value="<?= $coupon_code_id ?>">
        <input type="hidden" name="bank_id" value="<?= $bank_id ?>">
        <input type="hidden" name="shipping_cost" value="<?= $shipping_cost ?>">
        <input type="hidden" name="final_total" value="<?= $final_total ?>">

        <div class="row">

            <div class="col-md-8 mb-4">
                <div class="checkout-card h-100">
                    <div class="checkout-card-header">
                        <h5 class="checkout-card-title">
                            <i class="fa-solid fa-wallet mr-2 text-warning"></i> ช่องทางการชำระเงิน
                        </h5>
                    </div>

                    <div class="checkout-card-body d-flex flex-column align-items-center pt-4">
                        <?php if (!empty($bank_info)): ?>
                            <div class="d-flex align-items-center justify-content-center mb-4">
                                <img src="<?= validate_image($bank_info['image_path']) ?>" class="bank-logo-compact mr-3">
                                <div class="text-left">
                                    <h5 class="font-weight-bold mb-0 text-dark"><?= $bank_info['bank_name'] ?></h5>
                                    <small class="text-muted"><?= $bank_info['bank_company'] ?></small>
                                </div>
                            </div>
                            <div class="bg-light rounded p-3 mb-3 text-center border w-100" style="max-width: 450px;">
                                <div class="text-muted small mb-1">เลขที่บัญชี</div>
                                <div class="d-flex align-items-center justify-content-center">
                                    <span class="account-num-compact mr-3" id="accNum"><?= $bank_info['bank_number'] ?></span>
                                    <span class="copy-btn-small" onclick="copyToClipboard('#accNum')" title="คัดลอก">
                                        <i class="fa fa-copy"></i> คัดลอก
                                    </span>
                                </div>
                            </div>
                            <div class="mb-4 text-center">
                                <span class="text-muted small">ยอดโอนสุทธิ</span>
                                <div class="price-compact"><?= format_price_custom($final_total) ?> บาท</div>
                            </div>
                            <div class="w-100 px-lg-5" style="max-width: 500px;">
                                <button type="submit" class="btn btn-checkout btn-lg btn-block shadow-sm" id="btn-submit-order">
                                    <i class="fa-solid fa-check-circle mr-2"></i> แจ้งชำระเงิน
                                </button>
                                <p class="text-center text-muted mt-2 small">* กรุณาโอนเงินให้เรียบร้อยก่อนกดยืนยัน</p>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger w-100">ไม่พบข้อมูลธนาคาร</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="sticky-sidebar">
                    <div class="checkout-card">
                        <div class="checkout-card-header bg-white border-bottom py-3 px-3">
                            <h5 class="checkout-card-title mb-0 font-weight-bold">
                                <i class="fa-solid fa-list mr-2"></i>สรุปคำสั่งซื้อ
                            </h5>
                        </div>

                        <div class="checkout-card-body p-0">
                            <div style="max-height: 300px; overflow-y: auto;">
                                <?php foreach ($order_items as $item): ?>
                                    <div class="d-flex p-3 border-bottom">
                                        <img src="<?= validate_image($item['image_path']) ?>" class="product-logo mr-3 rounded">
                                        <div class="flex-grow-1" style="min-width: 0;">
                                            <h6 class="product-name font-weight-bold text-dark mb-1" style="font-size: 0.9rem; width: 100%;">
                                                <?= $item['product_name'] ?>
                                            </h6>
                                            <small class="text-muted d-block">Size: <?= $item['product_size'] ?> | x<?= $item['quantity'] ?></small>
                                        </div>
                                        <div class="text-right ml-2 check-out-summary-row" style="white-space: nowrap;">
                                            <span>
                                                <?= format_price_custom($item['vat_price'] * $item['quantity']) ?> บาท
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="p-3">
                                <div class="d-flex justify-content-between mb-2 small check-out-summary-row">
                                    <span>ราคารวม <small class="text-muted">รวม VAT</small></span>
                                    <span><span id="order-vat-total"><?= format_price_custom($subtotal) ?></span> บาท</span>
                                </div>

                                <div class="d-flex justify-content-between mb-2 small check-out-summary-row">
                                    <span>ค่าจัดส่ง</span>
                                    <span>
                                        <?= ($shipping_cost == 0) ? 'ฟรี' : format_price_custom($shipping_cost) . ' บาท' ?>
                                    </span>
                                </div>

                                <?php if ($discount_promotion > 0 || $promo_type == 'free_shipping'): ?>
                                    <div class="d-flex justify-content-between mb-2 small check-out-summary-row text-danger">
                                        <span>โปรโมชัน <?= $promo_name ?></span>
                                        <span>
                                            <?php if ($promo_type == 'free_shipping'): ?>
                                                ส่งฟรี
                                            <?php else: ?>
                                                -<?= format_price_custom($discount_promotion) ?> บาท
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <?php if ($discount_coupon > 0 || $coupon_type == 'free_shipping'): ?>
                                    <div class="d-flex justify-content-between mb-2 small text-danger check-out-summary-row">
                                        <span>คูปอง <?= $coupon_name ?></span>
                                        <span>
                                            <?php if ($coupon_type == 'free_shipping'): ?>
                                                ส่งฟรี
                                            <?php else: ?>
                                                -<?= format_price_custom($discount_coupon) ?> บาท
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="d-flex justify-content-between align-items-center mt-2 summary-row total">
                                    <span class="h6 mb-0 price-total">ยอดสุทธิ</span>
                                    <span class="h5 mb-0 price-total"><?= format_price_custom($final_total) ?> บาท</span>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // ==========================================
    // ฟังก์ชันคัดลอกแบบใหม่ (Modern Clipboard API)
    // เงื่อนไข: ต้องรันบน HTTPS หรือ Localhost เท่านั้น
    // ==========================================
    function copyToClipboard(element) {
        // 1. ดึงข้อความจาก Element และลบเครื่องหมายขีด (-) ออก
        var textToCopy = $(element).text().replace(/-/g, '').trim();

        // 2. ตรวจสอบว่า Browser รองรับ API นี้หรือไม่
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(textToCopy)
                .then(() => {
                    // --- กรณีสำเร็จ ---
                    alert_toast("คัดลอกเลขบัญชีแล้ว", 'success');
                })
                .catch(err => {
                    // --- กรณีเกิดข้อผิดพลาด ---
                    console.error('เกิดข้อผิดพลาดในการคัดลอก: ', err);
                    alert_toast("ไม่สามารถคัดลอกได้", 'error');
                });
        } else {
            // กรณี Browser เก่ามากๆ ที่ไม่รู้จัก API นี้
            alert_toast("Browser ของคุณไม่รองรับการคัดลอกอัตโนมัติ", 'error');
        }
    }

    // ==========================================
    // ส่วนจัดการการ Submit Form (เหมือนเดิม)
    // ==========================================
    $('#place-order-form').submit(function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'ยืนยันการชำระเงิน',
            text: "คุณได้ทำการโอนเงินเรียบร้อยแล้วใช่หรือไม่?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f57421',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ใช่, โอนแล้ว',
            cancelButtonText: 'ตรวจสอบก่อน',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                var btn = $('#btn-submit-order');
                var originalText = btn.html();
                btn.attr('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> กำลังบันทึก...');
                start_loader();

                $.ajax({
                    url: _base_url_ + 'classes/Master.php?f=place_order',
                    data: new FormData($('#place-order-form')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    dataType: 'json',
                    error: err => {
                        console.log(err);
                        alert_toast("เกิดข้อผิดพลาดในการเชื่อมต่อ", 'error');
                        btn.attr('disabled', false).html(originalText);
                        end_loader();
                    },
                    success: function(resp) {
                        if (resp.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ดำเนินการสั่งซื้อเรียบร้อย',
                                html: '<small class="text-muted">เพื่อให้เจ้าหน้าที่ตรวจสอบความถูกต้อง และยืนยันคำสั่งซื้อของท่าน<br>ระบบจะนำท่านไปยังหน้าแจ้งยอดชำระเงิน<br>ขอบคุณที่ใช้บริการ</small>',
                                confirmButtonText: 'ตกลง',
                                confirmButtonColor: '#f57421',
                                allowOutsideClick: false
                            }).then((result) => {
                                if (resp.id) {
                                    window.location.href = "./?p=user/slip_payment&order_id=" + resp.id;
                                } else {
                                    window.location.href = "./?p=user/slip_payment";
                                }
                            });
                        } else {
                            alert_toast(resp.msg || "เกิดข้อผิดพลาด", 'error');
                            btn.attr('disabled', false).html(originalText);
                        }
                        end_loader();
                    }
                })
            }
        });
    });
</script>