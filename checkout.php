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
                                <ul class="list-group mb-3">
                                    <?php foreach ($cart_items as $item): ?>
                                        <?php
                                        $is_discounted = $item['final_price'] < $item['price'];
                                        ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <div>
                                                <h6 class="my-0">
                                                    <?= $item['product'] ?>
                                                    <?php if ($is_discounted): ?>
                                                        <span class="badge badge-danger ml-1">ลดราคา</span>
                                                    <?php endif; ?>
                                                </h6>

                                                <small class="text-muted">
                                                    จำนวน: <?= $item['quantity'] ?> ×
                                                    <?php if ($is_discounted): ?>
                                                        <span class="text-danger font-weight-bold"><?= format_num($item['final_price'], 2) ?></span>
                                                        <span class="text-muted"><del><?= format_num($item['price'], 2) ?></del></span>
                                                    <?php else: ?>
                                                        <?= format_num($item['price'], 2) ?>
                                                    <?php endif; ?>
                                                </small>
                                            </div>
                                            <span class="text-muted font-weight-bold"><?= format_num($item['final_price'] * $item['quantity'], 2) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span><strong>รวม</strong></span>
                                        <strong class="text-bold"><?= format_num($cart_total, 2) ?></strong>
                                    </li>
                                </ul>


                                <table class="table table-bordered mb-4">
                                    <tr>
                                        <th>ชื่อ</th>
                                        <td><?= htmlentities($customer['firstname'] . ' ' . $customer['middlename'] . ' ' . $customer['lastname']) ?></td>
                                    </tr>
                                    <tr>
                                        <th>Email</th>
                                        <td><?= htmlentities($customer['email']) ?></td>
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
                                <input type="hidden" name="total_amount" value="<?= $cart_total ?>">
                                <input type="hidden" name="selected_items" value="<?= htmlspecialchars($_POST['selected_items']) ?>">
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
</script>