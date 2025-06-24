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
        p.discounted_price
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
                                <?php $default_shipping = $conn->query("SELECT * FROM shipping_methods WHERE is_active = 1 ORDER BY id ASC LIMIT 1")->fetch_assoc();
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
                                                    <span><?= format_num($item['final_price'] * $item['quantity'], 2) ?></span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>

                                        <!-- บริการขนส่ง -->
                                        <tr class="no-border">
                                            <th>บริการขนส่ง</th>
                                            <td class="text-right" colspan="2">
                                                <span id="shipping_method_name" style="margin-left: 10px;"><?= $default_shipping_name ?></span>
                                            </td>
                                            <td class="text-right">
                                                <a href="javascript:void(0);" onclick="openShippingModal()">เปลี่ยน</a>
                                            </td>
                                            <td class="text-right">
                                                <label id="shipping-cost"><?= number_format($default_shipping_cost, 2) ?> บาท</label>
                                            </td>
                                        </tr>


                                        <tr>
                                            <th><strong>รวม</strong></th>
                                            <td colspan="4">
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
                                <input type="hidden" name="shipping_method_id" id="shipping_method_id" value="<?= $default_shipping_id ?>">
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
    <div class="modal" id="shippingModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">เลือกบริการขนส่ง</h5>
                    <button type="button" class="close" onclick="closeShippingModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <?php
                    $shipping_qry = $conn->query("SELECT * FROM shipping_methods WHERE is_active = 1");
                    while ($row = $shipping_qry->fetch_assoc()):
                    ?>
                        <div style="margin-bottom: 10px;">
                            <button type="button"
                                class="btn btn-outline-primary btn-block"
                                onclick="selectShipping('<?= $row['id'] ?>', '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', <?= $row['cost'] ?>)">
                                <?= $row['name'] ?> - <?= number_format($row['cost'], 2) ?> บาท
                            </button>
                        </div>
                    <?php endwhile; ?>
                </div>
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
        document.getElementById('shippingModal').style.display = 'block';
    }

    function closeShippingModal() {
        document.getElementById('shippingModal').style.display = 'none';
    }

    function selectShipping(id, name, cost) {
        document.getElementById('shipping_method_id').value = id;
        document.getElementById('shipping_method_name').innerText = name;
        document.getElementById('shipping_cost').value = cost;

        // แสดงค่าส่ง
        document.getElementById('shipping-cost').innerText = cost.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        }) + ' บาท';

        // คำนวณยอดรวมรวมค่าส่ง
        let cartTotal = parseFloat(<?= $cart_total ?>);
        let grandTotal = cartTotal + cost;

        // อัปเดตค่าแสดงผล
        document.getElementById('order-total-text').innerText = grandTotal.toLocaleString('th-TH', {
            minimumFractionDigits: 2
        });

        // ✅ อัปเดตฟอร์มให้ส่งยอดรวมจริงไปหลังบ้าน
        document.querySelector('input[name="total_amount"]').value = grandTotal;

        closeShippingModal();
    }
</script>