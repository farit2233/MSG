<?php
if ($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2) {
    echo "<script>alert('You dont have access for this page'); location.replace('./');</script>";
}

$selected_items = isset($_POST['selected_items']) ? explode(',', $_POST['selected_items']) : [];

$cart_total = 0;
$cart_items = [];
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
        p.calculated_size
    FROM cart_list c 
    INNER JOIN product_list p ON c.product_id = p.id 
    WHERE c.id IN ($ids) AND customer_id = '{$_settings->userdata('id')}'
");

    while ($row = $cart_qry->fetch_assoc()) {
        // คำนวณราคาหลังลดเพื่อแสดง
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
        $cart_items[] = $row;
    }
}

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
?>
<style>
    .product-logo {
        width: 7em;
        height: 7em;
        object-fit: cover;
        object-position: center center;
    }

    .bg-gradient-dark-FIXX {
        background-color: #202020;
    }

    .cart-header-bar {
        border-left: 4px solid #ff6600;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .addcart {
        background: none;
        color: #f57421;
        border: 2px solid #f57421;
        padding: 10px 50px;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .addcart:hover {
        background-color: #f57421;
        color: white;
        display: inline-block;
    }

    .product-name {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        width: 350px;
    }

    tr.no-border td,
    tr.no-border th {
        border: none !important;
    }

    .cart-items-list table {
        display: table;
    }

    .modal-backdrop-custom {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.4);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 1050;
    }

    .shipping-modal-content {
        width: 90%;
        max-width: 540px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        animation: fadeIn 0.2s ease;
    }

    .shipping-modal-header {
        padding: 1rem 1.5rem;
        font-weight: bold;
        border-bottom: 1px solid #eee;
        font-size: 18px;
    }

    .shipping-modal-body {
        max-height: 60vh;
        overflow-y: auto;
        padding: 1rem 1.5rem;
    }

    .shipping-option {
        border: 1px solid #eee;
        padding: 1rem;
        margin-bottom: 10px;
        border-radius: 10px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .shipping-option:hover {
        background: #f9f9f9;
    }

    .shipping-option.selected {
        border: 2px solid #f57421;
        background-color: #fff5f0;
    }

    .shipping-option .checkmark {
        position: absolute;
        right: 15px;
        top: 15px;
        color: #f57421;
        display: none;
    }

    .shipping-option.selected .checkmark {
        display: inline;
    }

    .shipping-modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        padding: 1rem 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-cancel {
        background: #fff;
        border: 1px solid #ccc;
        padding: 8px 20px;
        border-radius: 6px;
        color: #333;
    }

    .btn-confirm {
        background: #f57421;
        border: none;
        padding: 8px 20px;
        border-radius: 6px;
        color: white;
    }

    @media only screen and (max-width: 768px) {
        .product-name {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100px;
        }

        table.small-table,
        table.small-table th,
        table.small-table td {
            font-size: 14px;
        }
    }
</style>
<section class="py-3">
    <div class="container">
        <div class="row mt-n4  justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow" style="margin-top: 3rem;">
                    <div class="card-body">
                        <div class=" container-fluid">
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
                                <?php
                                // ดึงไซซ์ของสินค้าในตะกร้า
                                $product_sizes = [];
                                $size_qry = $conn->query("SELECT calculated_size FROM product_list WHERE id IN (SELECT product_id FROM cart_list WHERE id IN ($ids))");
                                while ($row = $size_qry->fetch_assoc()) {
                                    if (!empty($row['calculated_size'])) {
                                        $product_sizes[] = strtolower($row['calculated_size']);
                                    }
                                }
                                // ถ้ามีหลายไซซ์ เอาไซซ์ใหญ่สุด
                                $size_priority = ['l' => 3, 'm' => 2, 's' => 1];
                                $cart_size = 's';
                                foreach ($product_sizes as $size) {
                                    if ($size_priority[$size] > $size_priority[$cart_size]) {
                                        $cart_size = $size;
                                    }
                                }
                                $default_shipping = $conn->query("
                                    SELECT *, 
                                    CASE 
                                        WHEN '$cart_size' = 's' THEN weight_cost_s
                                        WHEN '$cart_size' = 'm' THEN weight_cost_m
                                        WHEN '$cart_size' = 'l' THEN weight_cost_l
                                        ELSE weight_cost_s
                                    END as cost 
                                    FROM shipping_methods 
                                    WHERE is_active = 1 
                                    ORDER BY id ASC LIMIT 1
                                ")->fetch_assoc();

                                $default_shipping_id = $default_shipping['id'];
                                $default_shipping_name = $default_shipping['name'];
                                $default_shipping_cost = $default_shipping['cost'];
                                $grand_total = $cart_total + $default_shipping_cost; ?>
                                <div class="table-responsive">
                                    <table class="table table-bordered mb-4 small-table">
                                        <tr>
                                            <th>สั่งซื้อสินค้าแล้ว</th>
                                            <th class="text-muted text-right" colspan="2">ราคาต่อหน่วย</th>
                                            <th class="text-muted text-right">จำนวน</th>
                                            <th class="text-muted text-right">ขนาดไซซ์</th>
                                            <th class="text-muted text-right">รายการย่อย</th>
                                        </tr>

                                        <?php foreach ($cart_items as $item): ?>
                                            <?php
                                            $is_discounted = $item['final_price'] < $item['price'];
                                            ?>
                                            <tr class="no-border">
                                                <td>
                                                    <h6 class="my-0 product-name"><?= $item['product'] ?></h6>
                                                </td>
                                                <td class="text-right" colspan="2">
                                                    <?php if ($is_discounted): ?>
                                                        <span><?= format_num($item['final_price'], 2) ?></span>
                                                        <!-- <span class="text-muted"><del><?= format_num($item['price'], 2) ?></del></span> -->
                                                    <?php else: ?>
                                                        <?= format_num($item['price'], 2) ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= $item['quantity'] ?>
                                                </td>
                                                <td class="text-right">
                                                    <?= $item['calculated_size'] ?>
                                                </td>
                                                <td class="text-right">
                                                    <span><?= format_num($item['final_price'] * $item['quantity'], 2) ?></span>
                                                </td>
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
                                        <!-- บริการขนส่ง -->
                                        <tr class="no-border">
                                            <th>
                                                บริการขนส่ง
                                                <span class="text-danger" style="font-size: 0.8em;">* อิงราคาจากไซซ์ที่ใหญ่ที่สุดในตระกร้า</span>
                                            </th>

                                            <td class="text-right" colspan="2">
                                                <span id="shipping_methods_name" style="margin-left: 10px;"><?= $default_shipping_name ?></span>
                                            </td>
                                            <td></td>
                                            <td class="text-right">
                                                <a href="javascript:void(0);" onclick="openShippingModal()">เปลี่ยน</a>
                                            </td>
                                            <td class="text-right">
                                                <label id="shipping-cost"><?= number_format($default_shipping_cost, 2) ?> บาท</label>
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
                                    </table>

                                    <table class="table table-bordered mb-4 small-table">
                                        <tr>
                                            <th colspan="2">
                                                <h5 class="text-bold">ที่อยู่จัดส่ง</h5>
                                            </th>
                                        </tr>
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
                                    </table>
                                </div>

                            <?php else: ?>
                                <h5 class="text-center text-muted">ไม่มีรายการที่เลือกสำหรับการชำระเงิน</h5>
                            <?php endif; ?>

                        </div>
                        <div class=" container-fluid">
                            <div class="cart-header-bar d-flex align-items-center gap-2">
                                <i class="fa-solid fa-money-bill-wave mr-2 text-success" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">รูปแบบการชำระเงิน</h3>
                            </div>
                            <form action="" id="order-form">
                                <input type="hidden" name="total_amount" id="total_amount" value="<?= $grand_total ?>">
                                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
                                <input type="hidden" name="shipping_cost" id="shipping_cost" value="<?= $default_shipping_cost ?>">
                                <input type="hidden" name="shipping_methods_id" id="shipping_methods_id" value="<?= $default_shipping_id ?>">
                                <input type="hidden" name="delivery_address" value="<?= htmlentities($full_address) ?>">
                                <div class="py-1 text-center">
                                    <button class="btn addcart rounded-pill"
                                        <?= empty($full_address) ? 'disabled' : '' ?>>
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
    <?php

    $shipping_qry = $conn->query("
        SELECT *, 
        CASE 
            WHEN '$cart_size' = 's' THEN weight_cost_s
            WHEN '$cart_size' = 'm' THEN weight_cost_m
            WHEN '$cart_size' = 'l' THEN weight_cost_l
            ELSE weight_cost_s
        END as cost 
        FROM shipping_methods WHERE is_active = 1
    ");
    ?>

    <div id="shippingModal" class="modal-backdrop-custom">
        <div class="shipping-modal-content">
            <div class="shipping-modal-header">เลือก ตัวเลือกการจัดส่ง</div>
            <div class="shipping-modal-body">
                <?php while ($row = $shipping_qry->fetch_assoc()): ?>
                    <div class="shipping-option"
                        onclick="selectShipping('<?= $row['id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', <?= $row['cost'] ?>, this)">
                        <div>
                            <strong><?= $row['name'] ?></strong> - <?= number_format($row['cost'], 2) ?> บาท
                            <span class="checkmark">&#10003;</span>
                        </div>
                        <div class="desc text-muted" style="font-size: 0.9em;"><?= $row['description'] ?></div>
                    </div>
                <?php endwhile; ?>
            </div>
            <div class="shipping-modal-footer">
                <button class="btn-cancel" onclick="closeShippingModal()">ยกเลิก</button>
                <button class="btn-confirm" onclick="confirmShipping()">ยืนยัน</button>
            </div>
        </div>
    </div>



</section>
<script>
    $('#order-form').submit(function(e) {
        e.preventDefault();
        start_loader();

        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=place_order',
            method: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(resp) {
                console.log(resp);
                if (resp.status == 'success') {
                    location.replace('./');
                } else {
                    alert_toast(resp.msg || "เกิดข้อผิดพลาดตอนสั่งซื้อ", 'error');
                }
                end_loader();
            }
        });
    });

    function openShippingModal() {
        document.getElementById('shippingModal').style.display = 'flex';
    }

    function closeShippingModal() {
        document.getElementById('shippingModal').style.display = 'none';
    }

    let selectedShipping = null;

    function selectShipping(id, name, cost, element) {
        // ล้าง class ที่เลือกทั้งหมด
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');
        selectedShipping = {
            id,
            name,
            cost
        };

        // update preview ทันที
        document.getElementById('shipping_methods_id').value = id;
        document.getElementById('shipping_methods_name').innerText = name;
        document.getElementById('shipping_cost').value = cost;
        document.getElementById('shipping-cost').innerText = cost.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) + ' บาท';

        const total = parseFloat(<?= $cart_total ?>) + parseFloat(cost);
        document.getElementById('order-total-text').innerText = total.toLocaleString(undefined, {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        document.getElementById('total_amount').value = total;
    }

    function confirmShipping() {
        if (!selectedShipping) return;
        closeShippingModal();
    }
</script>