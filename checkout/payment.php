<?php
// ==========================================
// 1. Logic PHP (เหมือนเดิม)
// ==========================================
if ($_settings->userdata('id') == '') {
    echo "<script>alert('กรุณาเข้าสู่ระบบก่อน'); location.replace('./');</script>";
    exit;
}

$selected_items = $_POST['selected_items'] ?? '';
$delivery_address = $_POST['delivery_address'] ?? '';
$total_weight = $_POST['total_weight'] ?? 0;
$promotion_id = $_POST['promotion_id'] ?? 0;
$coupon_code_id = $_POST['coupon_code_id'] ?? 0;
$bank_id = $_POST['bank_id'] ?? '';
$shipping_cost = $_POST['shipping_cost'] ?? 0;
$final_total = $_POST['final_total'] ?? 0;

if (empty($selected_items) || empty($bank_id)) {
    echo "<script>location.replace('./?p=cart_list');</script>";
    exit;
}

// ดึงข้อมูลสินค้า
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

// คำนวณส่วนลด
$calculated_discount = ($subtotal + $shipping_cost) - $final_total;
if ($calculated_discount < 0) $calculated_discount = 0;

// ดึงข้อมูลธนาคาร
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
    /* Inline CSS เฉพาะการจัด Layout ภายใน (ไม่ไปยุ่งกับ Card หลัก) */
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
                                <img src="<?= validate_image($bank_info['image_path']) ?>" class="bank-logo-compact mr-3 shadow-sm">
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
                                <div class="price-compact"><?= format_price_custom($final_total, 2) ?> บาท</div>
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

                                        <div class="text-right ml-2" style="white-space: nowrap;">
                                            <span class="text-muted">
                                                <?= format_price_custom($item['vat_price'] * $item['quantity'], 2) ?> บาท
                                            </span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="p-3 ">
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">ราคาสินค้า</span>
                                    <span class="text-muted"><?= format_price_custom($subtotal, 2) ?> บาท</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 small">
                                    <span class="text-muted">ค่าจัดส่ง</span>
                                    <span class=" text-muted"><?= format_price_custom($shipping_cost, 2) ?> บาท</span>
                                </div>
                                <?php if ($calculated_discount > 0): ?>
                                    <div class="d-flex justify-content-between mb-2 small text-danger">
                                        <span>ส่วนลด</span>
                                        <span>- <?= format_price_custom($calculated_discount, 2) ?> บาท</span>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex justify-content-between align-items-center mt-2 summary-row total">
                                    <span class="h6 mb-0 price-total">ยอดสุทธิ</span> <!-- เปลี่ยนสีข้อความให้เหมือนกับใน index.php -->
                                    <span class="h5 mb-0 price-total"><?= format_price_custom($final_total, 2) ?> บาท</span> <!-- เปลี่ยนเป็นสีส้ม -->
                                </div>
                                <small class="text-right d-block text-muted mt-1" style="font-size: 11px;">(รวม VAT 7% แล้ว)</small>
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
    function copyToClipboard(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        var textToCopy = $(element).text().replace(/-/g, '').trim();
        $temp.val(textToCopy).select();
        document.execCommand("copy");
        $temp.remove();
        alert_toast("คัดลอกเลขบัญชีแล้ว", 'success');
    }

    // ฟังก์ชัน Submit แบบไม่แนบไฟล์
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
                                html: '<small class="text-muted">เพื่อให้เจ้าหน้าที่ตรวจสอบความถูกต้อง และยืนยันคำสั่งซื้อของท่าน<br>กรุณาแจ้งยอดชำระที่ บัญชีของฉัน > แจ้งยอดชำระเงิน<br>ขอบคุณที่ใช้บริการ</small>',
                                confirmButtonText: 'ตกลง',
                                confirmButtonColor: '#f57421',
                                allowOutsideClick: false
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    location.replace('./');
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