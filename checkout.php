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

$grand_total = $cart_total + $default_shipping_cost;
?>

<!-- ============================
HTML: แสดงรายการสินค้าในตะกร้า และที่อยู่จัดส่ง
============================= -->
<section class="py-3">
    <div class="container">
        <div class="row mt-n4 justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow" style="margin-top: 3rem;">
                    <div class="card-body">
                        <div class="container-fluid">
                            <!-- Header -->
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

                                <!-- ตารางรายการสินค้า -->
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

                                            <!-- ช่องว่างสำหรับเว้นบรรทัด -->
                                            <tr class="no-border">
                                                <th></th>
                                            </tr>
                                            <tr class="no-border">
                                                <th></th>
                                            </tr>
                                            <tr class="no-border">
                                                <th></th>
                                            </tr>

                                            <!-- ส่วนขนส่ง -->
                                            <tr class="no-border">
                                                <th>
                                                    บริการขนส่ง
                                                    <span class="text-danger" style="font-size: 0.8em;">* อิงราคาจากน้ำหนักที่ใหญ่ที่สุดในตระกร้า</span>
                                                </th>
                                                <td class="text-right" colspan="2">
                                                    <span id="shipping_methods_name" style="margin-left: 10px;"><?= $default_shipping_name ?></span>
                                                </td>
                                                <td class="text-right">
                                                    <a href="javascript:void(0);" onclick="openShippingModal()">เปลี่ยน</a>
                                                </td>
                                                <td class="text-right">
                                                    <label id="shipping-cost"><?= number_format($default_shipping_cost, 2) ?> บาท</label>
                                                </td>
                                            </tr>

                                            <!-- รวมทั้งหมด -->
                                            <tr>
                                                <th><strong>รวม</strong></th>
                                                <td colspan="5">
                                                    <h5 class="text-bold text-right">
                                                        <span id="order-total-text"><?= format_num($grand_total, 2) ?></span> บาท
                                                    </h5>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <!-- ที่อยู่จัดส่ง -->
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

                        <!-- ฟอร์มชำระเงิน -->
                        <div class="container-fluid">
                            <div class="cart-header-bar d-flex align-items-center gap-2">
                                <i class="fa-solid fa-money-bill-wave mr-2 text-success" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">รูปแบบการชำระเงิน</h3>
                            </div>
                            <form action="" id="order-form">
                                <input type="hidden" name="total_amount" id="total_amount" value="<?= $grand_total ?>">
                                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
                                <input type="hidden" name="shipping_cost" id="shipping_cost" value="<?= $default_shipping_cost ?>">
                                <input type="hidden" name="shipping_methods_id" id="shipping_methods_id" value="<?= $default_shipping_id ?>">
                                <input type="hidden" name="shipping_methods_name" id="shipping_methods_name" value="<?= $default_shipping_name ?>">
                                <input type="hidden" name="delivery_address" value="<?= htmlentities($full_address) ?>">
                                <input type="hidden" id="total_weight" value="<?= $total_weight ?>">
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

    <!-- ============================
    HTML: Modal เลือกขนส่ง
    ============================ -->
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
    // ============================
    // JS: จัดการฟอร์มสั่งซื้อ และ modal ขนส่ง
    // ============================
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

    // เปิด/ปิด modal
    function openShippingModal() {
        document.getElementById('shippingModal').style.display = 'flex';
    }

    function closeShippingModal() {
        document.getElementById('shippingModal').style.display = 'none';
    }

    // เก็บข้อมูลขนส่งที่เลือก
    let selectedShipping = null;

    // เลือกขนส่ง
    function selectShipping(id, name, element) {
        if (!element) return;

        // ล้าง selection เดิม
        document.querySelectorAll('.shipping-option').forEach(el => el.classList.remove('selected'));
        element.classList.add('selected');

        // ดึงน้ำหนักรวมจาก hidden input
        const totalWeight = parseInt(document.getElementById('total_weight').value) || 0;

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
                    const cost = parseFloat(resp.price) || 0;

                    document.getElementById('shipping_methods_id').value = id;
                    document.getElementById('shipping_methods_name').innerText = name;
                    document.getElementById('shipping_cost').value = cost;
                    document.getElementById('shipping-cost').innerText = cost.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' บาท';

                    const cartTotal = parseFloat(<?= json_encode($cart_total) ?>) || 0;
                    const grandTotal = cartTotal + cost;

                    document.getElementById('order-total-text').innerText = grandTotal.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                    document.getElementById('total_amount').value = grandTotal;

                    selectedShipping = {
                        id,
                        name,
                        cost
                    };
                } else {
                    alert('ไม่สามารถคำนวณค่าขนส่งได้');
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดขณะคำนวณค่าขนส่ง');
            }
        });
    }


    // กดยืนยันใน modal
    function confirmShipping() {
        if (!selectedShipping) return;
        closeShippingModal();
    }

    $(document).ready(function() {
        // คำนวณค่าขนส่งใหม่เมื่อรีเฟรชหน้า
        const totalWeight = parseInt(document.getElementById('total_weight').value) || 0;

        if (totalWeight > 0) {
            // เรียกใช้งาน API เพื่อคำนวณค่าขนส่งใหม่
            $.ajax({
                url: _base_url_ + 'classes/Master.php?f=get_shipping_cost',
                method: 'POST',
                data: {
                    shipping_methods_id: $('#shipping_methods_id').val(), // ใช้ shipping method id ที่เลือก
                    total_weight: totalWeight
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status === 'success') {
                        const cost = parseFloat(resp.price) || 0;

                        // อัปเดตค่าใหม่
                        document.getElementById('shipping_cost').value = cost;
                        document.getElementById('shipping-cost').innerText = cost.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        }) + ' บาท';

                        const cartTotal = parseFloat(<?= json_encode($cart_total) ?>) || 0;
                        const grandTotal = cartTotal + cost;

                        document.getElementById('order-total-text').innerText = grandTotal.toLocaleString(undefined, {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        });
                        document.getElementById('total_amount').value = grandTotal;
                    } else {
                        alert('ไม่สามารถคำนวณค่าขนส่งได้');
                    }
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดขณะคำนวณค่าขนส่ง');
                }
            });
        }
    });
</script>