<?php
// --- ย้ายมาไว้ตรงนี้ ---
$cart = $conn->query("SELECT 
    c.*, 
    p.name as product, 
    p.brand as brand, 
    p.vat_price, 
    p.discounted_price, 
    p.discount_type,
    cc.name as category, 
    p.image_path,
    (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id ), 0) 
        - COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = p.id), 0)) as `available` 
    FROM `cart_list` c 
    INNER JOIN product_list p ON c.product_id = p.id 
    INNER JOIN category_list cc ON p.category_id = cc.id 
    WHERE customer_id = '{$_settings->userdata('id')}'");
// ----------------------

if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
} else {
}
?>

<section class="py-5 cart-body">
    <div class="container">

        <div class="cart-header-bar">
            <i class="fa fa-basket-shopping mr-2 cart-icon"></i>
            <h3 class="d-inline mb-0">ตะกร้าของฉัน</h3>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">

                <div class="cart-card mb-3">
                    <div class="cart-card-body p-3 d-flex align-items-center position-relative">

                        <div class="d-flex align-items-center">
                            <div class="cart-checkbox-area" style="padding-right: 10px;">
                                <input type="checkbox" class="check-all" id="check-all-box"
                                    <?= (isset($cart) && $cart->num_rows <= 0 && $_settings->userdata('id') != '') ? 'disabled' : '' ?>>
                            </div>
                            <label for="check-all-box" class="mb-0 cursor-pointer font-weight-bold">เลือกทั้งหมด</label>
                        </div>

                        <a href="javascript:void(0)"
                            id="deselect-all-link"
                            class="text-muted small text-decoration-none cancel-all"
                            style="display:none; position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);">
                            ล้างการเลือก
                        </a>

                    </div>
                </div>

                <div class="cart-card">
                    <div class="cart-card-body" id="item_list">
                        <?php
                        $gt = 0;
                        // ✅ แก้ไข: ประกาศฟังก์ชันตรงนี้ (นอกลูป)
                        if (!function_exists('fmt_smart')) {
                            function fmt_smart($v)
                            {
                                return (floor($v) == $v) ? number_format($v, 0) : number_format($v, 2);
                            }
                        }

                        // เริ่มวนลูปแสดงสินค้า
                        while ($row = $cart->fetch_assoc()):
                            $available = $row['available'];

                            // Logic Stock
                            if ($available >= 100) {
                                $max_order_qty = floor($available / 3);
                            } elseif ($available >= 50) {
                                $max_order_qty = floor($available / 2);
                            } elseif ($available >= 30) {
                                $max_order_qty = floor($available / 1.5);
                            } else {
                                $max_order_qty = max(1, floor($available / 1));
                            }

                            if ($row['quantity'] > $max_order_qty && $max_order_qty > 0) {
                                $row['quantity'] = $max_order_qty;
                                $conn->query("UPDATE `cart_list` SET quantity = '{$max_order_qty}' WHERE id = '{$row['id']}'");
                            }

                            $show_discount = !empty($row['discounted_price']) && $row['discounted_price'] < $row['vat_price'];
                            $price_to_use = $show_discount ? $row['discounted_price'] : $row['vat_price'];
                            $gt += $price_to_use * $row['quantity'];

                            $cart_main_path = $row['image_path'];
                            $cart_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_thumb.webp$2', $cart_main_path);
                        ?>

                            <div class="cart-item-row <?= $row['available'] <= 0 ? 'out-of-stock' : '' ?>"
                                data-id='<?= $row['id'] ?>'
                                data-max='<?= $max_order_qty ?>'
                                data-unit-price='<?= $price_to_use ?>'
                                data-original-price='<?= $row['vat_price'] ?>'>

                                <div class="cart-checkbox-area">
                                    <input type="checkbox"
                                        class="cart-check"
                                        name="selected_cart[]"
                                        value="<?= $row['id'] ?>"
                                        id="cart_check_<?= $row['id'] ?>"
                                        data-price="<?= $price_to_use * $row['quantity'] ?>"
                                        <?= $row['available'] <= 0 ? 'disabled' : '' ?>>
                                </div>

                                <div class="cart-item-image">
                                    <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>">
                                        <img src="<?= validate_image($cart_medium_path) ?>" alt="<?= $row['product'] ?>">
                                    </a>
                                </div>

                                <div class="cart-item-details">
                                    <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>" class="cart-item-title">
                                        <?= $row['product'] ?>
                                    </a>
                                    <?php if ($row['available'] <= 0): ?>
                                        <div class="text-danger small font-weight-bold mt-1">สินค้าหมดชั่วคราว</div>
                                    <?php endif; ?>
                                </div>

                                <div class="cart-item-actions">
                                    <div class="product-price">
                                        <?php
                                        $total_orig = $row['vat_price'] * $row['quantity'];
                                        $total_now = $price_to_use * $row['quantity'];

                                        // เรียกใช้ฟังก์ชัน fmt_smart ได้ปกติ
                                        if ($show_discount): ?>
                                            <div class="cart-price-del"><?= fmt_smart($total_orig) ?></div>
                                            <div class="cart-price-text discount"><?= fmt_smart($total_now) ?> บาท</div>
                                        <?php else: ?>
                                            <div class="cart-price-text"><?= fmt_smart($total_now) ?> บาท</div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="qty-group">
                                        <button class="qty-btn minus-qty" type="button">-</button>
                                        <input type="number" class="qty-input qty" value="<?= $row['quantity'] ?>" min="1" max="<?= $row['available'] ?>">
                                        <button class="qty-btn add-qty" type="button">+</button>
                                    </div>

                                    <button class="btn-delete del-item" type="button" title="ลบสินค้า">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endwhile; ?>

                        <div id="guest_cart_container" style="display: none;"></div>

                        <?php if ($cart->num_rows <= 0 && $_settings->userdata('id') != ''): ?>
                            <div class="text-center py-5">
                                <i class="fa fa-basket-shopping text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i>
                                <h5 class="text-muted">ตะกร้าว่างเปล่า</h5>
                                <a href="/?p=products" class="btn btn-cart-shop rounded-pill mt-3 px-4">ช็อปเลย!</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block">
                <div class="cart-summary-box">
                    <h5 class="mb-3 font-weight-bold">สรุปคำสั่งซื้อ</h5>

                    <div class="summary-row ...">
                        <span>รวมสินค้าทั้งหมด (<span id="total-items-count">0</span>)</span>
                        <span id="summary-original-total">0.00 บาท</span>
                    </div>

                    <div class="summary-row ... text-danger" id="summary-discount-row" style="display: none;">
                        <span>ส่วนลดสินค้า</span>
                        <span>-<span id="summary-discount-total">0.00</span> บาท</span>
                    </div>
                    <div class="summary-row total d-flex justify-content-between font-weight-bold" style="font-size: 1.2rem;">
                        <span>ยอดรวม</span>
                        <span><span id="selected-total-desktop">0.00</span> บาท</span>
                    </div>

                    <form id="checkout-form-desktop" method="post" action="./?p=checkout">
                        <input type="hidden" name="selected_items" class="selected_items_input">
                        <button type="submit" class="btn-cart-checkout mt-3 btn btn-block">ดำเนินการชำระเงิน</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-checkout-bar d-lg-none">
        <div class="d-flex flex-column">
            <span class="text-muted small">ยอดรวมทั้งหมด:</span>
            <span class="font-weight-bold" style="font-size: 1.2rem;">
                <span id="selected-total-mobile">0.00</span> บาท
            </span>
        </div>
        <form id="checkout-form-mobile" method="post" action="./?p=checkout" class="m-0">
            <input type="hidden" name="selected_items" class="selected_items_input">
            <button type="submit" class="btn btn-cart-checkout-m rounded px-4 font-weight-bold">
                ชำระเงิน
            </button>
        </form>
    </div>

</section>

<script>
    const isLoggedIn = <?= ($_settings->userdata('id') != '') ? 'true' : 'false' ?>;

    // ==========================================
    // Helper Functions
    // ==========================================
    function formatPriceForJS(price) {
        let val = parseFloat(price);
        if (val % 1 === 0) {
            return val.toLocaleString('th-TH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        } else {
            return val.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    // ==========================================
    // Main Logic
    // ==========================================
    $(document).ready(function() {

        // Load Logic
        if (!isLoggedIn) {
            renderGuestCart();
            updateGuestNavbarCount();
        } else {
            calculateSelectedTotal();
            syncSelectAllState();
        }

        // ---------------------------------------------------------
        // 1. Event: พิมพ์ตัวเลขเอง (Change)
        // ---------------------------------------------------------
        $(document).on('change', '.qty-input', function() {
            var input = $(this);
            var row = input.closest('.cart-item-row');
            var id = row.attr('data-id');
            var max = parseInt(row.attr('data-max'));
            var val = parseInt(input.val());
            var isGuest = input.hasClass('guest');

            // Validation
            if (isNaN(val) || val < 1) val = 1;

            // กรณีเกิน Max (จุดที่แก้ไข)
            if (val > max) {
                val = max;
                input.val(val); // ปรับตัวเลขในกล่องให้ถูกก่อน

                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้ามีจำกัด',
                    text: 'มีสินค้าสูงสุด ' + max + ' ชิ้น',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    // เมื่อกดตกลง ค่อยทำงานต่อ
                    if (result.isConfirmed || result.isDismissed) {
                        if (isGuest) {
                            updateGuestCart(id, val);
                            update_visual_price(row);
                        } else {
                            update_item(id, val); // สมาชิก: รีโหลดหน้าตรงนี้
                        }
                    }
                });

                return; // *** สำคัญ: หยุดการทำงานทันที รอให้กดปุ่มก่อน ***
            }

            // กรณีปกติ (ไม่เกิน Max)
            input.val(val);

            if (isGuest) {
                updateGuestCart(id, val);
                update_visual_price(row);
            } else {
                update_item(id, val);
            }
        });

        // ---------------------------------------------------------
        // 2. Event: ปุ่ม +/- (Click)
        // ---------------------------------------------------------
        $(document).on('click', '.add-qty, .minus-qty', function() {
            var btn = $(this);
            var row = btn.closest('.cart-item-row');
            var input = row.find('.qty-input');
            var id = row.attr('data-id');
            var max = parseInt(row.attr('data-max'));
            var current = parseInt(input.val()) || 0;
            var isGuest = input.hasClass('guest');

            var step = btn.hasClass('add-qty') ? 1 : -1;
            var newVal = current + step;

            if (newVal < 1) newVal = 1;

            // กรณีเกิน Max (จุดที่แก้ไข)
            if (newVal > max) {
                newVal = max;
                input.val(newVal); // ปรับตัวเลขในกล่องให้ถูกก่อน

                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้ามีจำกัด',
                    text: 'สูงสุด ' + max + ' ชิ้น',
                    confirmButtonText: 'ตกลง'
                }).then((result) => {
                    // เมื่อกดตกลง ค่อยทำงานต่อ
                    if (result.isConfirmed || result.isDismissed) {
                        if (isGuest) {
                            updateGuestCart(id, newVal);
                            update_visual_price(row);
                        } else {
                            update_item(id, newVal); // สมาชิก: รีโหลดหน้าตรงนี้
                        }
                    }
                });

                return; // *** สำคัญ: หยุดการทำงานทันที รอให้กดปุ่มก่อน ***
            }

            // กรณีปกติ (ไม่เกิน Max)
            input.val(newVal);

            if (isGuest) {
                updateGuestCart(id, newVal);
                update_visual_price(row);
            } else {
                update_item(id, newVal);
            }
        });

        // ---------------------------------------------------------
        // 3. Event: ปุ่มลบ
        // ---------------------------------------------------------
        $(document).on('click', '.del-item', function() {
            var row = $(this).closest('.cart-item-row');
            var id = row.attr('data-id');
            var isGuest = $(this).hasClass('guest');

            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "ต้องการลบสินค้านี้หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#ccc',
                confirmButtonText: 'ใช่ ลบเลย',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isGuest) deleteGuestCartItem(id);
                    else delete_cart(id);
                }
            })
        });

        // Checkbox & Checkout (ส่วนนี้เหมือนเดิม)
        $(document).on('change', '.cart-check', function() {
            calculateSelectedTotal();
            syncSelectAllState();
        });
        $('.check-all').change(function() {
            $('.cart-check:not(:disabled)').prop('checked', $(this).is(':checked'));
            calculateSelectedTotal();
            $('#deselect-all-link').toggle($(this).is(':checked'));
        });
        $('#deselect-all-link').click(function() {
            $('.check-all, .cart-check').prop('checked', false);
            $(this).hide();
            calculateSelectedTotal();
        });
        $('#checkout-form-desktop, #checkout-form-mobile').submit(function(e) {
            var selected = [];
            var valid = true;
            var errorMsg = '';
            $('.cart-check:checked').each(function() {
                var row = $(this).closest('.cart-item-row');
                var id = $(this).hasClass('guest') ? row.attr('data-id') : $(this).val();
                var qty = parseInt(row.find('.qty-input').val());
                var max = parseInt(row.attr('data-max'));
                var name = row.find('.cart-item-title').text().trim();
                if (qty > max) {
                    valid = false;
                    errorMsg += `• <b>${name}</b> เหลือเพียง ${max} ชิ้น<br>`;
                }
                selected.push(id);
            });
            if (selected.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'เตือน',
                    text: 'กรุณาเลือกสินค้าก่อนชำระเงิน'
                });
                return false;
            }
            if (!isLoggedIn) {
                e.preventDefault();
                Swal.fire({
                    icon: 'info',
                    title: 'กรุณาเข้าสู่ระบบ',
                    showCancelButton: true,
                    confirmButtonText: 'ไปหน้าเข้าสู่ระบบ',
                    cancelButtonText: 'ไว้ทีหลัง'
                }).then((result) => {
                    if (result.isConfirmed) location.href = './login.php';
                });
                return false;
            }
            if (!valid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'สินค้าไม่พอ',
                    html: errorMsg
                });
                return false;
            }
            $('.selected_items_input').val(selected.join(','));
        });
    });

    // ==========================================
    // Functions (Member)
    // ==========================================
    function update_item(cart_id, qty) {
        start_loader();
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=update_cart_qty',
            method: 'POST',
            data: {
                id: cart_id,
                qty: qty
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                }
            },
            error: function(err) {
                console.log(err);
                end_loader();
            }
        });
    }

    function delete_cart(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_cart",
            method: "POST",
            data: {
                id: id
            },
            dataType: "json",
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("ลบไม่สำเร็จ", 'error');
                    end_loader();
                }
            },
            error: function(err) {
                console.log(err);
                end_loader();
            }
        });
    }

    // ==========================================
    // Functions (Guest)
    // ==========================================
    function updateGuestNavbarCount() {
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        var totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty), 0);
        var selectors = '#guest_cart_count_mobile, #guest_cart_count, .cart-count, .badge-cart';
        $(selectors).text(totalQty);
        if (totalQty > 0) $(selectors).removeClass('d-none');
        else $(selectors).addClass('d-none');
    }

    function renderGuestCart() {
        var container = $('#guest_cart_container');
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart.length == 0) {
            $('.check-all').prop('disabled', true).prop('checked', false);
            $('#deselect-all-link').hide();
            container.html(`<div class="text-center py-5"><i class="fa fa-basket-shopping text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i><h5 class="text-muted">ตะกร้าว่างเปล่า</h5><a href="/?p=products" class="btn btn-cart-shop rounded-pill mt-3 px-4">ไปช็อปเลย</a></div>`);
            container.show();
            updateGuestNavbarCount();
            return;
        } else {
            $('.check-all').prop('disabled', false);
        }
        var productIds = cart.map(i => i.id);
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=get_guest_stock',
            method: 'POST',
            data: {
                ids: productIds
            },
            dataType: 'json',
            success: function(stockData) {
                var html = '';
                cart.forEach((item, index) => {
                    var available = (stockData && stockData[item.id] !== undefined) ? parseInt(stockData[item.id]) : 999;
                    var max_qty = available;
                    if (available >= 100) max_qty = Math.floor(available / 3);
                    else if (available >= 50) max_qty = Math.floor(available / 2);
                    else if (available >= 30) max_qty = Math.floor(available / 1.5);
                    else max_qty = Math.max(1, available);
                    var isOutOfStock = available <= 0;
                    if (item.qty > max_qty) {
                        item.qty = max_qty;
                        updateGuestCart(index, max_qty, false);
                    }

                    var showDiscount = item.discounted_price && item.discounted_price < item.vat_price;
                    var price = showDiscount ? item.discounted_price : item.vat_price;
                    var subtotal = price * item.qty;
                    var origSubtotal = item.vat_price * item.qty;
                    var img = item.image || 'assets/img/default.png';
                    if (img.includes('.webp')) img = img.replace(/(\.webp)(\?.*)?$/, '_thumb.webp$2');

                    html += `<div class="cart-item-row ${isOutOfStock ? 'out-of-stock' : ''}" data-id="${index}" data-max="${max_qty}" data-unit-price="${price}" data-original-price="${item.vat_price}">
                        <div class="cart-checkbox-area"><input type="checkbox" class="cart-check guest" value="${index}" data-price="${subtotal}" ${isOutOfStock ? 'disabled' : ''}></div>
                        <div class="cart-item-image"><a href="./?p=products/view_product&id=${item.id}"><img src="${img}" alt="${item.name}"></a></div>
                        <div class="cart-item-details"><a href="./?p=products/view_product&id=${item.id}" class="cart-item-title">${item.name}</a>${isOutOfStock ? '<div class="text-danger small mt-1">สินค้าหมด</div>' : ''}</div>
                        <div class="cart-item-actions">
                            <div class="product-price text-right mb-2">${showDiscount ? `<div class="cart-price-del">${formatPriceForJS(origSubtotal)}</div><div class="cart-price-text discount">${formatPriceForJS(subtotal)} บาท</div>` : `<div class="cart-price-text">${formatPriceForJS(subtotal)} บาท</div>`}</div>
                            <div class="qty-group"><button class="qty-btn minus-qty guest" ${isOutOfStock?'disabled':''}>-</button><input type="number" class="qty-input qty guest" value="${item.qty}" min="1" max="${max_qty}" ${isOutOfStock?'disabled':''}><button class="qty-btn add-qty guest" ${isOutOfStock?'disabled':''}>+</button></div>
                            <button class="btn-delete del-item guest"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>`;
                });
                container.html(html).show();
                calculateSelectedTotal();
                syncSelectAllState();
                updateGuestNavbarCount();
            }
        });
    }

    function updateGuestCart(index, qty, rerender = true) {
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart[index]) {
            cart[index].qty = qty;
            localStorage.setItem('guest_cart', JSON.stringify(cart));
            updateGuestNavbarCount();
            if (rerender) renderGuestCart();
        }
    }

    function deleteGuestCartItem(index) {
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        renderGuestCart();
        updateGuestNavbarCount();
        setTimeout(calculateSelectedTotal, 100);
    }

    // ==========================================
    // Common UI Functions
    // ==========================================
    function update_visual_price(itemElement) {
        var qty = parseInt(itemElement.find('.qty-input').val()) || 1;
        var price = parseFloat(itemElement.attr('data-unit-price'));
        var subtotal = price * qty;
        itemElement.find('.cart-check').attr('data-price', subtotal);
        itemElement.find('.product-price .cart-price-text:last').text(formatPriceForJS(subtotal) + ' บาท');
        calculateSelectedTotal();
    }

    function calculateSelectedTotal() {
        // 1. รีเซ็ตค่าเริ่มต้นเป็น 0 ทุกครั้งที่เรียกฟังก์ชัน
        var totalNet = 0; // ราคาสุทธิ (ที่จะจ่าย)
        var totalOriginal = 0; // ราคาเต็ม (ก่อนลด)
        var itemCount = 0; // จำนวนชิ้น

        // 2. วนลูปเฉพาะรายการที่ "ติ๊กถูก" เท่านั้น (:checked)
        $('.cart-check:checked').each(function() {
            var row = $(this).closest('.cart-item-row');

            // ดึงค่าจาก data-attribute ที่เราแปะไว้ที่ row
            var unitPrice = parseFloat(row.attr('data-unit-price')) || 0; // ราคาขายจริง
            var originalPrice = parseFloat(row.attr('data-original-price')) || 0; // ราคาเต็ม
            var qty = parseInt(row.find('.qty-input').val()) || 1;

            // คำนวณผลรวม
            totalNet += unitPrice * qty;
            totalOriginal += originalPrice * qty;
            itemCount += qty;
        });

        // 3. คำนวณส่วนลดรวม
        var totalDiscount = totalOriginal - totalNet;

        // ป้องกันเศษทศนิยมเพี้ยน (เช่น 0.00000001) ให้ถือว่าเป็น 0
        if (totalDiscount < 0.01) {
            totalDiscount = 0;
        }

        // 4. แปลงตัวเลขเป็นรูปแบบเงิน
        var txtNet = formatPriceForJS(totalNet);
        var txtOriginal = formatPriceForJS(totalOriginal);
        var txtDiscount = formatPriceForJS(totalDiscount);

        // 5. อัปเดต UI
        $('#total-items-count').text(itemCount);
        $('#summary-original-total').text(txtOriginal + ' บาท'); // ราคารวม (ตัวบน)
        $('#selected-total-desktop, #selected-total-mobile').text(txtNet); // ราคาสุทธิ (ตัวล่าง)

        // 6. Logic การแสดง/ซ่อน บรรทัดส่วนลด
        if (totalDiscount > 0) {
            // ถ้ามีส่วนลด ให้แสดง และใส่ค่า
            $('#summary-discount-total').text(txtDiscount);
            $('#summary-discount-row').show();
        } else {
            // ถ้าไม่มีส่วนลด (หรือไม่ได้ติ๊กอะไรเลย) ให้ซ่อนทันที
            $('#summary-discount-row').hide();
        }

        // จัดการปุ่ม Checkout
        var hasChecked = $('.cart-check:checked').length > 0;
        $('.btn-checkout, #checkout-form-mobile button').prop('disabled', !hasChecked);
    }

    function syncSelectAllState() {
        var all = $('.cart-check:not(:disabled)').length;
        var checked = $('.cart-check:checked').length;
        $('.check-all').prop('checked', (all > 0 && all === checked));
        $('#deselect-all-link').toggle(checked > 0);
    }
</script>