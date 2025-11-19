<?php
if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
} else {
}
?>

<section class="py-5">
    <div class="container">

        <div class="cart-header-bar">
            <i class="fa fa-basket-shopping mr-2 cart-icon"></i>
            <h3 class="d-inline mb-0 font-weight-bold">ตะกร้าของฉัน</h3>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">

                <div class="cart-card mb-3">
                    <div class="cart-card-body p-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="cart-checkbox-area" style="padding-right: 10px;">
                                <input type="checkbox" class="check-all" id="check-all-box">
                            </div>
                            <label for="check-all-box" class="mb-0 cursor-pointer font-weight-bold">เลือกทั้งหมด</label>
                        </div>
                        <a href="javascript:void(0)" id="deselect-all-link" class="text-muted small text-decoration-none cancel-all" style="display:none;">ล้างการเลือก</a>
                    </div>
                </div>

                <div class="cart-card">
                    <div class="cart-card-body" id="item_list">
                        <?php
                        $gt = 0;
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
                            $cart_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_medium.webp$2', $cart_main_path);
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
                                            <div class="cart-price-text discount"><?= fmt_smart($total_now) ?> ฿</div>
                                        <?php else: ?>
                                            <div class="cart-price-text"><?= fmt_smart($total_now) ?> ฿</div>
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
                                <i class="fa fa-shopping-cart text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i>
                                <h5 class="text-muted">ตะกร้าว่างเปล่า</h5>
                                <a href="./" class="btn btn-outline-primary rounded-pill mt-3 px-4">ไปช็อปเลย</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block">
                <div class="cart-summary-box">
                    <h5 class="mb-3 font-weight-bold">สรุปคำสั่งซื้อ</h5>
                    <div class="summary-row">
                        <span>ยอดรวมสินค้า</span>
                        <span id="summary-subtotal">0.00 ฿</span>
                    </div>
                    <div class="summary-row total">
                        <span>ยอดสุทธิ</span>
                        <span><span id="selected-total-desktop">0.00</span> บาท</span>
                    </div>
                    <form id="checkout-form-desktop" method="post" action="./?p=checkout">
                        <input type="hidden" name="selected_items" class="selected_items_input">
                        <button type="submit" class="btn-checkout mt-3">ดำเนินการชำระเงิน</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="mobile-checkout-bar d-lg-none">
        <div class="d-flex flex-column">
            <span class="text-muted small">ยอดรวมทั้งหมด:</span>
            <span class="text-primary font-weight-bold" style="font-size: 1.2rem;">
                <span id="selected-total-mobile">0.00</span> ฿
            </span>
        </div>
        <form id="checkout-form-mobile" method="post" action="./?p=checkout" class="m-0">
            <input type="hidden" name="selected_items" class="selected_items_input">
            <button type="submit" class="btn btn-primary rounded px-4 font-weight-bold" style="background-color: var(--primary-color); border:none;">
                ชำระเงิน
            </button>
        </form>
    </div>

</section>

<script>
    // ==========================================
    // Helper Functions
    // ==========================================
    function formatPriceForJS(price) {
        let val = parseFloat(price);

        // เช็คว่าเป็นจำนวนเต็มหรือไม่ (ไม่มีเศษ)
        if (val % 1 === 0) {
            // ถ้าเป็นจำนวนเต็ม ให้แสดง 0 ตำแหน่ง (ตัด .00 ทิ้ง)
            return val.toLocaleString('th-TH', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            });
        } else {
            // ถ้ามีเศษ ให้แสดง 2 ตำแหน่งตามปกติ
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
        if ('<?= $_settings->userdata('id') ?>' == '') {
            renderGuestCart();
        } else {
            calculateSelectedTotal();
            syncSelectAllState();
        }

        // ---------------------------------------------------------
        // 1. Event: พิมพ์ตัวเลขเอง (Change) ** สำคัญ **
        // ---------------------------------------------------------
        $(document).on('change', '.qty-input', function() {
            var input = $(this);
            var row = input.closest('.cart-item-row');
            var id = row.attr('data-id');
            var max = parseInt(row.attr('data-max'));
            var val = parseInt(input.val()); // อ่านค่าที่พิมพ์
            var isGuest = input.hasClass('guest');

            // ตรวจสอบค่าที่พิมพ์ (Validation)
            if (isNaN(val) || val < 1) {
                val = 1; // ถ้าลบหมดหรือพิมพ์ 0 ให้เด้งกลับเป็น 1
            }
            if (val > max) {
                val = max; // ถ้าพิมพ์เกิน Stock ให้เด้งกลับเป็น Max
                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้ามีจำกัด',
                    text: 'มีสินค้าสูงสุด ' + max + ' ชิ้น'
                });
            }

            input.val(val); // อัปเดตค่าในกล่องให้ถูกต้อง

            // บันทึกข้อมูล
            if (isGuest) {
                updateGuestCart(id, val);
                update_visual_price(row);
            } else {
                update_item(id, val, row);
            }
        });

        // ---------------------------------------------------------
        // 2. Event: กดปุ่ม +/- (Click)
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
            if (newVal > max) {
                newVal = max;
                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้ามีจำกัด',
                    text: 'สูงสุด ' + max + ' ชิ้น'
                });
            }

            input.val(newVal);

            if (isGuest) {
                updateGuestCart(id, newVal);
                update_visual_price(row);
            } else {
                update_item(id, newVal, row);
            }
        });

        // ---------------------------------------------------------
        // 3. Event อื่นๆ (Delete, Checkbox)
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
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    if (isGuest) deleteGuestCartItem(id);
                    else delete_cart(id);
                }
            })
        });

        $(document).on('change', '.cart-check', function() {
            calculateSelectedTotal();
            syncSelectAllState();
        });

        $('.check-all').change(function() {
            var isChecked = $(this).is(':checked');
            $('.cart-check:not(:disabled)').prop('checked', isChecked);
            calculateSelectedTotal();
            if (isChecked) $('#deselect-all-link').show();
            else $('#deselect-all-link').hide();
        });

        $('#deselect-all-link').click(function() {
            $('.check-all').prop('checked', false);
            $('.cart-check').prop('checked', false);
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
    // Functions
    // ==========================================
    function update_item(cart_id, qty, itemElement) {
        // start_loader(); // Optional
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=update_cart',
            method: 'POST',
            data: {
                cart_id: cart_id,
                qty: qty
            },
            dataType: 'json',
            success: function(resp) {
                if (resp.status == 'success') {
                    update_visual_price(itemElement);
                } else {
                    console.log(resp);
                }
            },
            error: function(err) {
                console.log(err);
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
                    alert_toast("Error deleting item", 'error');
                    end_loader();
                }
            }
        });
    }

    function update_visual_price(itemElement) {
        var qtyInput = itemElement.find('.qty-input');
        var qty = parseInt(qtyInput.val()) || 1;
        var unitPrice = parseFloat(itemElement.attr('data-unit-price'));
        var origPrice = parseFloat(itemElement.attr('data-original-price'));

        var subtotal = unitPrice * qty;
        var origSubtotal = origPrice * qty;
        var showDiscount = unitPrice < origPrice;

        itemElement.find('.cart-check').attr('data-price', subtotal);

        var priceHtml = '';
        if (showDiscount) {
            priceHtml = `<div class="cart-price-del">${formatPriceForJS(origSubtotal)}</div>
                         <div class="cart-price-text discount">${formatPriceForJS(subtotal)} ฿</div>`;
        } else {
            priceHtml = `<div class="cart-price-text">${formatPriceForJS(subtotal)} ฿</div>`;
        }
        itemElement.find('.product-price').html(priceHtml);

        calculateSelectedTotal();
    }

    function calculateSelectedTotal() {
        var total = 0;
        $('.cart-check:checked').each(function() {
            total += parseFloat($(this).attr('data-price'));
        });

        var totalText = formatPriceForJS(total);
        $('#selected-total-desktop').text(totalText);
        $('#selected-total-mobile').text(totalText);
        $('#summary-subtotal').text(totalText + ' ฿');

        var hasChecked = $('.cart-check:checked').length > 0;
        $('.btn-checkout, #checkout-form-mobile button').prop('disabled', !hasChecked);
    }

    function syncSelectAllState() {
        var all = $('.cart-check:not(:disabled)').length;
        var checked = $('.cart-check:checked').length;

        if (all > 0 && all === checked) {
            $('.check-all').prop('checked', true);
            $('#deselect-all-link').show();
        } else {
            $('.check-all').prop('checked', false);
            if (checked > 0) $('#deselect-all-link').show();
            else $('#deselect-all-link').hide();
        }
    }

    // ---------------------------------------------------------
    // Guest Cart Logic (Render ไม่มี readonly)
    // ---------------------------------------------------------
    function renderGuestCart() {
        var container = $('#guest_cart_container');
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];

        if (cart.length == 0) {
            container.html(`
                <div class="text-center py-5">
                    <i class="fa fa-shopping-cart text-muted mb-3" style="font-size: 4rem; opacity: 0.2;"></i>
                    <h5 class="text-muted">ตะกร้าว่างเปล่า</h5>
                    <a href="./" class="btn btn-outline-primary rounded-pill mt-3 px-4">ไปช็อปเลย</a>
                </div>`);
            container.show();
            return;
        }

        var productIds = cart.map(i => i.id).join(',');
        $.ajax({
            url: _base_url_ + 'classes/get_product_stock.php',
            method: 'POST',
            data: {
                product_ids: productIds
            },
            dataType: 'json',
            success: function(stockData) {
                var html = '';
                cart.forEach((item, index) => {
                    var available = (stockData && stockData[item.id]) ? parseInt(stockData[item.id]) : 0;
                    var max_qty = available;

                    // Logic Stock display
                    if (available >= 100) max_qty = Math.floor(available / 3);
                    else if (available >= 50) max_qty = Math.floor(available / 2);
                    else if (available >= 30) max_qty = Math.floor(available / 1.5);
                    else max_qty = Math.max(1, Math.floor(available / 1));

                    var isOutOfStock = available <= 0;
                    var qty = Math.min(item.qty, max_qty > 0 ? max_qty : 1);

                    var showDiscount = item.discounted_price && item.discounted_price < item.vat_price;
                    var price = showDiscount ? item.discounted_price : item.vat_price;
                    var subtotal = price * qty;
                    var origSubtotal = item.vat_price * qty;

                    var img = item.image || 'assets/img/default.png';
                    if (img.includes('.webp')) img = img.replace(/(\.webp)(\?.*)?$/, '_medium.webp$2');

                    // ⚠️ ตรงนี้สำคัญ: เอา readonly ออกจาก input
                    html += `
                    <div class="cart-item-row ${isOutOfStock ? 'out-of-stock' : ''}" 
                         data-id="${index}" data-product-id="${item.id}" 
                         data-max="${max_qty}" data-unit-price="${price}" data-original-price="${item.vat_price}">
                        
                        <div class="cart-checkbox-area">
                            <input type="checkbox" class="cart-check guest" value="${index}" data-price="${subtotal}" ${isOutOfStock ? 'disabled' : ''}>
                        </div>
                        <div class="cart-item-image">
                            <a href="./?p=products/view_product&id=${item.id}"><img src="${img}" alt="${item.name}"></a>
                        </div>
                        <div class="cart-item-details">
                            <a href="./?p=products/view_product&id=${item.id}" class="cart-item-title">${item.name}</a>
                            ${isOutOfStock ? '<div class="text-danger small mt-1">สินค้าหมด</div>' : ''}
                        </div>
                        <div class="cart-item-actions">
                            <div class="product-price text-right mb-2">
                                ${showDiscount 
                                    ? `<div class="cart-price-del">${formatPriceForJS(origSubtotal)}</div><div class="cart-price-text discount">${formatPriceForJS(subtotal)} ฿</div>` 
                                    : `<div class="cart-price-text">${formatPriceForJS(subtotal)} ฿</div>`
                                }
                            </div>
                            <div class="qty-group">
                                <button class="qty-btn minus-qty guest" type="button" ${isOutOfStock?'disabled':''}>-</button>
                                <input type="number" class="qty-input qty guest" value="${qty}" min="1" max="${max_qty}" ${isOutOfStock?'disabled':''}>
                                <button class="qty-btn add-qty guest" type="button" ${isOutOfStock?'disabled':''}>+</button>
                            </div>
                            <button class="btn-delete del-item guest" type="button"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    </div>`;
                });
                container.html(html);
                container.show();
                calculateSelectedTotal();
                syncSelectAllState();
            }
        });
    }

    function updateGuestCart(index, qty) {
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart[index]) {
            cart[index].qty = qty;
            localStorage.setItem('guest_cart', JSON.stringify(cart));
        }
    }

    function deleteGuestCartItem(index) {
        var cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        if (typeof updateCartCounter === 'function') updateCartCounter();
        renderGuestCart();
        setTimeout(calculateSelectedTotal, 100);
    }
</script>