<?php

if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
} else {
}
?>

<section class="py-3">
    <div class="container">
        <div class="row mt-n4  justify-content-center align-items-center flex-column">
            <div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
                <div class="card rounded-0 shadow" style="margin-top: 3rem;">
                    <div class="card-body">
                        <div class=" container-fluid">
                            <div class="cart-header-bar">
                                <i class="fa fa-basket-shopping mr-2" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">ตะกร้าของฉัน</h3>
                            </div>

                            <div class="list-group-item d-flex w-100 align-items-center" style="border-bottom: 2px solid #eee; padding-top: 0.75rem; padding-bottom: 0.75rem;">

                                <div class="col-auto flex-grow-1 d-flex align-items-center">

                                    <input type="checkbox" class="form-check-input check-all" id="check-all-box" title="เลือกทั้งหมด">

                                    <label for="check-all-box" class="title-all">เลือกทั้งหมด</label>
                                </div>

                                <div class="col-auto">
                                    <a href="javascript:void(0)" id="deselect-all-link" class="text-danger" style="text-decoration: none; display: none;">ยกเลิกทั้งหมด</a>
                                </div>
                            </div>
                            <div id="item_list" class="list-group">
                                <?php
                                // ... (โค้ด PHP ที่เหลือ)
                                ?>
                                <div id="item_list" class="list-group">
                                    <?php
                                    $gt = 0;
                                    $cart = $conn->query("SELECT 
                                    c.*, 
                                    p.name as product, 
                                    p.brand as brand, 
                                    p.price, 
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

                                    while ($row = $cart->fetch_assoc()):
                                        $available = $row['available'];

                                        if ($available >= 100) {
                                            $max_order_qty = floor($available / 3);
                                        } elseif ($available >= 50) {
                                            $max_order_qty = floor($available / 2);
                                        } elseif ($available >= 30) {
                                            $max_order_qty = floor($available / 1.5);
                                        } else {
                                            $max_order_qty = max(1, floor($available / 1));
                                        }

                                        // ✨ ตรวจสอบและปรับปรุงจำนวนสินค้าในตะกร้า หากเกินจำนวนที่สั่งได้
                                        if ($row['quantity'] > $max_order_qty && $max_order_qty > 0) {
                                            $row['quantity'] = $max_order_qty;
                                            // อัปเดตฐานข้อมูลเบื้องหลังเพื่อความถูกต้อง
                                            $conn->query("UPDATE `cart_list` SET quantity = '{$max_order_qty}' WHERE id = '{$row['id']}'");
                                        }

                                        $show_discount = !empty($row['discounted_price']) && $row['discounted_price'] < $row['price'];
                                        $price_to_use = $show_discount ? $row['discounted_price'] : $row['price'];
                                        $gt += $price_to_use * $row['quantity'];
                                    ?>

                                        <div class="list-group-item cart-list-item d-flex w-100 <?= $row['available'] <= 0 ? 'out-of-stock' : '' ?>"
                                            data-id='<?= $row['id'] ?>'
                                            data-max='<?= $max_order_qty ?>'
                                            data-unit-price='<?= $price_to_use ?>'
                                            data-original-price='<?= $row['price'] ?>'>

                                            <div class="col-auto pr-2">
                                                <input type="checkbox"
                                                    class="form-check-input cart-check"
                                                    name="selected_cart[]"
                                                    value="<?= $row['id'] ?>"
                                                    id="cart_check_<?= $row['id'] ?>"
                                                    data-price="<?= $price_to_use * $row['quantity'] ?>"
                                                    <?= $row['available'] <= 0 ? 'disabled' : '' ?>>
                                            </div>

                                            <div class="cart-list-item-content d-flex w-100 align-items-start">
                                                <div class="col-3 text-center">
                                                    <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>">

                                                        <?php
                                                        // 1. ดึง Path หลัก
                                                        $cart_main_path = $row['image_path'];
                                                        // 2. แปลงเป็น Path ขนาดกลาง (Medium)
                                                        $cart_medium_path = preg_replace('/(\.webp)(\?.*)?$/', '_medium.webp$2', $cart_main_path);
                                                        ?>
                                                        <img src="<?= validate_image($cart_medium_path) ?>" class="cart-product-logo" alt="" style="cursor: pointer;">
                                                    </a>
                                                </div>

                                                <div class="col-auto flex-shrink-1 flex-grow-1">
                                                    <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>" style="text-decoration: none; color: inherit;">
                                                        <h4 class="cart-product-title" style="cursor: pointer;"><?= $row['product'] ?></h4>
                                                    </a>

                                                    <div class="text-muted d-flex w-100">
                                                        <div class="input-group" style="width: 20rem;">
                                                            <button class="btn addcart-plus minus-qty" type="button">-</button>
                                                            <input type="number" class="form-control text-center qty" value="<?= $row['quantity'] ?>" min="1" max="<?= $row['available'] ?>" required>
                                                            <button class="btn addcart-plus add-qty" type="button">+</button>
                                                            <button class="btn btn-danger ms-2 del-item" type="button">
                                                                <i class="fa-solid fa-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-auto text-right">
                                                <?php if ($show_discount): ?>
                                                    <h5 class="text-muted mb-0">
                                                        <del><?= format_num($row['price'] * $row['quantity'], 2) ?> บาท</del>
                                                    </h5>
                                                    <h4><b class="text-danger">ลดเหลือ: <?= format_num($row['discounted_price'] * $row['quantity'], 2) ?> บาท</b></h4>
                                                <?php else: ?>
                                                    <h4><b>ราคา: <?= format_num($row['price'] * $row['quantity'], 2) ?> บาท</b></h4>
                                                <?php endif; ?>
                                            </div>

                                        </div>
                                    <?php endwhile; ?>

                                </div>
                                <div id="guest_cart_container" style="display: none;"></div>
                                <?php if ($cart->num_rows <= 0 && $_settings->userdata('id') != ''): ?>
                                    <h5 class="text-center text-muted">ตะกร้าว่างเปล่า ช็อปเลย!</h5>
                                <?php endif; ?>
                                <div class="d-flex justify-content-end py-3">
                                    <div class="col-auto">
                                        <h3 class="selected-total"><b>รวมรายการที่เลือก: <span id="selected-total">0.00</span></b> บาท</h3>
                                    </div>
                                </div>
                                <?php if ($gt > 0): ?>
                                    <div class="py-1 text-center">
                                        <form id="checkout-form" method="post" action="./?p=checkout">
                                            <input type="hidden" name="selected_items" id="selected_items">
                                            <button type="submit" class="btn addcart rounded-pill">
                                                ชำระรายการที่เลือก
                                            </button>
                                        </form>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</section>
<script>
    // ===================================
    // ✨ SECTION: Utility Functions (Global)
    // ===================================

    /**
     * ฟังก์ชันจัดรูปแบบราคาสำหรับ JS
     */
    function formatPriceForJS(value) {
        return value.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    /**
     * ฟังก์ชันอัปเดตตะกร้า (สำหรับคนล็อกอิน)
     * (คงเดิม) - ยังคงถูกเรียกใช้โดยปุ่ม +/- และ 'change' event
     */
    function update_item(cart_id, qty, itemElement) {
        start_loader()
        $.ajax({
            url: _base_url_ + 'classes/Master.php?f=update_cart',
            method: 'POST',
            data: {
                cart_id: cart_id,
                qty: qty
            },
            dataType: 'json',
            error: err => {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    // อัปเดต DOM (เพื่อให้แน่ใจว่าราคาสุดท้ายถูกต้อง)
                    update_visual_price(itemElement);
                    end_loader();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        });
    }

    /**
     * ✨ ใหม่: ฟังก์ชันอัปเดตราคาที่แสดงผล (Client-side)
     * ไม่มีการเรียก AJAX (ไม่ทำให้หมุน)
     */
    function update_visual_price(itemElement) {
        var qtyInput = itemElement.find('.qty');
        var qtyStr = qtyInput.val();
        var qtyNum = parseInt(qtyStr);
        var max = parseInt(itemElement.attr('data-max'));

        // 1. ตรวจสอบ Max (ขณะพิมพ์)
        if (!isNaN(qtyNum) && qtyNum > max) {
            qtyNum = max;
            qtyInput.val(qtyNum); // แก้ไขค่าในช่อง
            Swal.fire({
                icon: 'warning',
                title: 'สินค้าในคลังมีไม่พอ',
                text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
            });
        }

        // 2. กำหนดจำนวนที่จะใช้คำนวณ (อนุญาตให้ 0 หรือ ว่างเปล่าชั่วคราว)
        var calcQty = qtyNum;
        if (isNaN(calcQty) || calcQty < 1) {
            calcQty = 1; // ใช้ 1 ในการคำนวณ แม้ในช่องจะว่าง
        }

        // 3. อ่านค่าราคา
        var unitPrice = parseFloat(itemElement.attr('data-unit-price'));
        var originalPrice = parseFloat(itemElement.attr('data-original-price'));
        var newSubtotal = unitPrice * calcQty;
        var newOriginalSubtotal = originalPrice * calcQty;
        var showDiscount = (unitPrice < originalPrice);

        // 4. สร้าง HTML ราคาใหม่
        var priceContainer = itemElement.find('.col-auto.text-right');
        var priceHTML = '';
        if (showDiscount) {
            priceHTML = `<h5 class="text-muted mb-0"><del>${formatPriceForJS(newOriginalSubtotal)} บาท</del></h5>
                         <h4><b class="text-danger">ลดเหลือ: ${formatPriceForJS(newSubtotal)} บาท</b></h4>`;
        } else {
            priceHTML = `<h4><b>ราคา: ${formatPriceForJS(newSubtotal)} บาท</b></h4>`;
        }
        priceContainer.html(priceHTML);

        // 5. อัปเดต data-price ของ checkbox
        itemElement.find('.cart-check').data('price', newSubtotal);

        // 6. คำนวณยอดรวมใหม่
        calculateSelectedTotal();
    }


    /**
     * ฟังก์ชันลบสินค้า (สำหรับคนล็อกอิน)
     */
    function delete_cart(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_cart",
            method: "POST",
            data: {
                id: id
            },
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        });
    }

    // ===================================
    // ✨ SECTION: For Logged-in Users (jQuery)
    // ===================================
    $(function() {

        // ✨ (แก้ไข) Event 'input': ทำงานทุกครั้งที่พิมพ์ อัปเดตราคาที่แสดงผล (No AJAX)
        $('.qty:not(.guest)').on('input', function() {
            var item = $(this).closest('.cart-list-item');
            // เรียกฟังก์ชันอัปเดตราคา (Visual only)
            update_visual_price(item);
        });

        // ✨ (ใหม่) Event 'change': ทำงานเมื่อคลิกออก/พิมพ์เสร็จ บันทึกลง DB (AJAX)
        $('.qty:not(.guest)').on('change', function() {
            var item = $(this).closest('.cart-list-item');
            var qtyInput = $(this);
            var qtyNum = parseInt(qtyInput.val());
            var max = parseInt(item.attr('data-max'));
            var id = item.attr('data-id');

            // --- Final Validation (บัคข้อ 2, 3) ---
            if (isNaN(qtyNum) || qtyNum < 1) {
                qtyNum = 1;
            }
            if (qtyNum > max) {
                qtyNum = max;
            }
            qtyInput.val(qtyNum); // ตั้งค่าที่ถูกต้องลงในช่อง
            // --- End Final Validation ---

            // เรียกฟังก์ชัน update_item (ตัวเดิมที่ส่ง AJAX)
            update_item(id, qtyNum, item);
        });

        // (คงเดิม) Event ปุ่มบวก
        $('.add-qty:not(.guest)').click(function() {
            var item = $(this).closest('.cart-list-item');
            var qtyInput = item.find('.qty');
            var qty = parseInt(qtyInput.val()) || 1;
            var max = parseInt(item.attr('data-max'));
            if (qty < max) {
                qty++;
                qtyInput.val(qty);
                update_item(item.attr('data-id'), qty, item);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้าในคลังมีไม่พอ',
                    text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
                });
            }
        });

        // (คงเดิม) Event ปุ่มลบ
        $('.minus-qty:not(.guest)').click(function() {
            var item = $(this).closest('.cart-list-item');
            var qtyInput = item.find('.qty');
            var qty = parseInt(qtyInput.val()) || 1;
            if (qty > 1) {
                qty--;
                qtyInput.val(qty);
                update_item(item.attr('data-id'), qty, item);
            }
        });

        // (คงเดิม) Event ปุ่มลบสินค้า
        $('.del-item:not(.guest)').click(function() {
            var id = $(this).closest('.cart-list-item').attr('data-id');
            Swal.fire({
                icon: 'warning',
                title: 'คุณแน่ใจไหม?',
                text: "ลบสินค้าออกจากตะกร้า?",
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'ใช่ ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_cart(id);
                }
            });
        });
    });


    // ===================================
    // ✨ SECTION: For Guest Users (Vanilla JS)
    // ===================================

    // ✨ (ใหม่) Event 'input' (สำหรับ Guest) - Visual only
    document.addEventListener('input', function(e) {
        if (!e.target.matches('.qty.guest')) return;
        const itemEl = e.target.closest('.cart-list-item');
        const index = parseInt(itemEl.dataset.id);
        const newQty = e.target.value; // ส่งค่า string
        // (index, value, isDirectSet, saveToStorage)
        updateGuestCartQty(index, newQty, true, false); // false = ไม่บันทึก
    });

    // ✨ (ใหม่) Event 'change' (สำหรับ Guest) - Save to localStorage
    document.addEventListener('change', function(e) {
        if (!e.target.matches('.qty.guest')) return;
        const itemEl = e.target.closest('.cart-list-item');
        const index = parseInt(itemEl.dataset.id);
        const newQty = e.target.value; // ส่งค่า string
        // (index, value, isDirectSet, saveToStorage)
        updateGuestCartQty(index, newQty, true, true); // true = บันทึก
    });


    /**
     * (คงเดิม) ฟังก์ชันวาดตะกร้า (สำหรับ Guest)
     */
    function renderGuestCart() {
        const container = document.getElementById('guest_cart_container');
        if (!container) return;

        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart.length === 0) {
            container.innerHTML = '<h5 class="text-center text-muted">ตะกร้าว่างเปล่า ช็อปเลย!</h5>';
            container.style.display = 'block';
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) checkoutForm.style.display = 'none';
            calculateSelectedTotal();
            return;
        } else {
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) checkoutForm.style.display = 'block';
        }

        const productIds = cart.map(item => item.id).join(',');

        fetch(_base_url_ + 'classes/get_product_stock.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'product_ids=' + productIds
            })
            .then(response => response.json())
            .then(stockData => {
                let html = '';
                cart.forEach((item, index) => {
                    const available = stockData[item.id] || 0;
                    let max_order_qty = 0;
                    if (available > 0) {
                        if (available >= 100) max_order_qty = Math.floor(available / 3);
                        else if (available >= 50) max_order_qty = Math.floor(available / 2);
                        else if (available >= 30) max_order_qty = Math.floor(available / 1.5);
                        else max_order_qty = Math.max(1, Math.floor(available / 1));
                    }

                    const isOutOfStock = available <= 0;
                    const currentQty = Math.min(item.qty, max_order_qty);

                    const show_discount = item.discounted_price && item.discounted_price < item.vat_price;
                    const price_to_use = show_discount ? item.discounted_price : item.vat_price;
                    const subtotal = price_to_use * currentQty;

                    let image_path = item.image || 'assets/img/default.png';

                    // ใช้รูปขนาด medium สำหรับทุกรูปภาพ
                    if (image_path && image_path.includes('.webp')) {
                        image_path = image_path.replace(/(\.webp)(\?.*)?$/, '_medium.webp$2');
                    }

                    html += `
            <div class="list-group-item cart-list-item d-flex w-100 ${isOutOfStock ? 'out-of-stock' : ''}" data-id="${index}" data-product-id="${item.id}" data-max="${max_order_qty}">
                <div class="col-auto pr-2">
                    <input type="checkbox" class="form-check-input cart-check guest" data-price="${subtotal}" ${isOutOfStock ? 'disabled' : ''} />
                </div>

                <div class="cart-list-item-content d-flex w-100 align-items-start">
                    <div class="col-3 text-center">
                        <a href="./?p=products/view_product&id=${item.id}">
                            <img src="${image_path}" class="cart-product-logo" alt="" style="cursor: pointer;"> 
                        </a>
                    </div>

                    <div class="col-auto flex-shrink-1 flex-grow-1">
                        <a href="./?p=products/view_product&id=${item.id}" style="text-decoration: none; color: inherit;">
                            <h4 class="cart-product-title" style="cursor: pointer;">${item.name}</h4>
                        </a>
                        
                        <div class="text-muted d-flex w-100">
                            <div class="input-group" style="width: 20rem;">
                                <button class="btn addcart-plus minus-qty guest" type="button" ${isOutOfStock ? 'disabled' : ''}>−</button>
                                <input type="number" class="form-control text-center qty guest" value="${currentQty}" min="1" max="${max_order_qty}" ${isOutOfStock ? 'disabled' : ''}>
                                <button class="btn addcart-plus add-qty guest" type="button" ${isOutOfStock ? 'disabled' : ''}>+</button>
                                <button class="btn btn-danger ms-2 del-item guest" type="button"><i class="fa-solid fa-trash-can"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-auto text-right">
                    ${show_discount
                        ? `<h5 class="text-muted mb-0"><del>${formatPriceForJS(item.vat_price * currentQty)} บาท</del></h5><h4><b class="text-danger">ลดเหลือ: ${formatPriceForJS(subtotal)} บาท</b></h4>`
                        : `<h4><b>ราคา: ${formatPriceForJS(subtotal)} บาท</b></h4>`
                    }
                </div>
            </div>`;
                });

                container.innerHTML = html;
                container.style.display = 'block';
                calculateSelectedTotal();
                syncSelectAll();
            })
            .catch(error => {
                console.error('เกิดข้อผิดพลาดในการดึงข้อมูลสต็อกสินค้า:', error);
            });
    }

    // (คงเดิม) Event เมื่อโหลดหน้า (สำหรับ Guest)
    document.addEventListener('DOMContentLoaded', function() {
        const userLoggedIn = <?= ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
        if (userLoggedIn) return;
        renderGuestCart();
    });

    // ✨ (แก้ไข) Event คลิกปุ่ม (สำหรับ Guest) - เรียก updateGuestCartQty แบบบังคับบันทึก
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.guest')) return;

        const itemEl = e.target.closest('.cart-list-item');
        if (!itemEl) return;
        const index = parseInt(itemEl.dataset.id);

        if (e.target.matches('.add-qty.guest')) {
            // (index, value, isDirectSet, saveToStorage)
            updateGuestCartQty(index, 1, false, true);
        }
        if (e.target.matches('.minus-qty.guest')) {
            // (index, value, isDirectSet, saveToStorage)
            updateGuestCartQty(index, -1, false, true);
        }
        if (e.target.matches('.del-item.guest, .del-item.guest *')) {
            Swal.fire({
                icon: 'warning',
                title: 'คุณแน่ใจไหม?',
                text: "ลบสินค้าออกจากตะกร้า?",
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#95a5a6',
                confirmButtonText: 'ใช่ ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then(result => result.isConfirmed && deleteGuestCartItem(index));
        }
    });

    /**
     * ✨ (แก้ไข) ฟังก์ชันอัปเดตจำนวน (สำหรับ Guest)
     * เพิ่ม parameter: saveToStorage
     */
    function updateGuestCartQty(index, value, isDirectSet = false, saveToStorage = true) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (!cart[index]) return;

        const itemData = cart[index];
        const itemEl = document.querySelector(`.cart-list-item[data-id="${index}"]`);
        if (!itemEl) return;

        const qtyInput = itemEl.querySelector('.qty.guest');
        const max = parseInt(itemEl.dataset.max);
        let newQtyNum;

        if (isDirectSet) {
            // มาจาก 'input' หรือ 'change' (value คือ string)
            newQtyNum = parseInt(value);
        } else {
            // ✨ แก้ไข 1: อ่านค่าปัจจุบันจากช่อง input (qtyInput.value)
            // ไม่ใช่จาก localStorage (itemData.qty)
            let currentQtyInBox = parseInt(qtyInput.value) || 1;
            newQtyNum = currentQtyInBox + value;
        }

        // --- Validation ---
        if (!isNaN(newQtyNum) && newQtyNum > max) {
            newQtyNum = max;
            qtyInput.value = newQtyNum; // อัปเดตช่อง input
            Swal.fire({
                icon: 'warning',
                title: 'สินค้าในคลังมีไม่พอ',
                text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
            });
        }

        // ถ้าบังคับบันทึก (change, +/-,) ให้บังคับ 1
        if (saveToStorage) {
            if (isNaN(newQtyNum) || newQtyNum < 1) {
                newQtyNum = 1;
            }
            // ✨ แก้ไข 2: อัปเดตค่าในช่อง input เสมอ
            // เมื่อเป็น_การกด +/- หรือ change
            qtyInput.value = newQtyNum;
        }
        // ถ้าไม่บันทึก (input) อนุญาตให้ 0 หรือ NaN ชั่วคราว (ไม่แก้ qtyInput.value)

        // --- End Validation ---

        // คำนวณราคาที่จะแสดง
        let calcQty = newQtyNum;
        if (isNaN(calcQty) || calcQty < 1) {
            calcQty = 1; // ใช้ 1 คำนวณ แม้ในช่องจะว่าง
        }

        // --- อัปเดต DOM ---
        const show_discount = itemData.discounted_price && itemData.discounted_price < itemData.vat_price;
        const price_to_use = show_discount ? itemData.discounted_price : itemData.vat_price;
        const newSubtotal = price_to_use * calcQty;

        const priceContainer = itemEl.querySelector('.col-auto.text-right');
        let priceHTML = '';
        if (show_discount) {
            priceHTML = `<h5 class="text-muted mb-0"><del>${formatPriceForJS(itemData.vat_price * calcQty)} บาท</del></h5><h4><b class="text-danger">ลดเหลือ: ${formatPriceForJS(newSubtotal)} บาท</b></h4>`;
        } else {
            priceHTML = `<h4><b>ราคา: ${formatPriceForJS(newSubtotal)} บาท</b></h4>`;
        }
        priceContainer.innerHTML = priceHTML;

        const checkbox = itemEl.querySelector('.cart-check.guest');
        checkbox.dataset.price = newSubtotal;
        calculateSelectedTotal();
        // --- จบส่วนอัปเดต DOM ---

        // (ส่วนบันทึก - ทำเมื่อ saveToStorage = true)
        if (saveToStorage) {
            // ✨ แก้ไข 3: อัปเดต itemData.qty *ก่อน* บันทึก
            // (เพื่อให้แน่ใจว่าค่าใน localStorage ตรงกับที่คำนวณได้)
            if (itemData.qty !== newQtyNum) {
                itemData.qty = newQtyNum;
                localStorage.setItem('guest_cart', JSON.stringify(cart));
            }
        }
    }


    /**
     * (คงเดิม) ฟังก์ชันลบสินค้า (สำหรับ Guest)
     */
    function deleteGuestCartItem(index) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        cart.splice(index, 1);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        if (typeof updateCartCounter === 'function') updateCartCounter();
        renderGuestCart();
    }


    // ===================================
    // SECTION: Common Functions (คำนวณราคารวม, ตรวจสอบก่อนชำระเงิน)
    // ===================================

    function syncSelectAll() {
        var $totalCheckboxes = $('.cart-check:not(:disabled)');
        var $totalChecked = $('.cart-check:not(:disabled):checked');
        var $selectAllBox = $('#check-all-box');
        var $deselectLink = $('#deselect-all-link');

        if ($totalCheckboxes.length === 0) {
            // ไม่มีสินค้าที่เลือกได้
            $selectAllBox.prop('checked', false);
            $selectAllBox.prop('disabled', true);
            $deselectLink.hide();
        } else {
            // มีสินค้า
            $selectAllBox.prop('disabled', false);
            // ตรวจสอบว่า 'เลือกทั้งหมด' ควรถูกติ๊กหรือไม่
            $selectAllBox.prop('checked', $totalCheckboxes.length === $totalChecked.length);

            // แสดง/ซ่อน ลิงก์ 'ยกเลิกทั้งหมด'
            if ($totalChecked.length > 0) {
                $deselectLink.show();
            } else {
                $deselectLink.hide();
            }
        }
    }

    $(function() {

        // ✨ (แก้ไข) Event เมื่อติ๊ก checkbox ของสินค้า
        $(document).on('change', '.cart-check', function() {
            calculateSelectedTotal(); // 1. คำนวณราคารวม (ตัวเดิม)
            syncSelectAll(); // 2. ซิงค์ปุ่ม 'เลือกทั้งหมด'
        });

        // ✨ (ใหม่) Event เมื่อติ๊ก 'เลือกทั้งหมด'
        $('#check-all-box').on('change', function() {
            var isChecked = $(this).is(':checked');
            // เลือก/ไม่เลือก รายการทั้งหมดที่ *ไม่พิการ*
            // และสั่ง .trigger('change') เพื่อให้ listener ด้านบนทำงาน (คำนวณราคา + ซิงค์)
            $('.cart-check:not(:disabled)').prop('checked', isChecked).trigger('change');
        });

        // ✨ (ใหม่) Event เมื่อคลิก 'ยกเลิกทั้งหมด'
        $('#deselect-all-link').on('click', function(e) {
            e.preventDefault();
            // ยกเลิกการเลือก รายการที่ถูกติ๊กไว้
            // และสั่ง .trigger('change') เพื่อให้ listener ด้านบนทำงาน (คำนวณราคา + ซิงค์)
            $('.cart-check:checked').prop('checked', false).trigger('change');
        });


        $('#checkout-form').submit(function(e) {
            var selected = [];
            var valid = true;
            var errorMessage = '';

            $('.cart-check:checked').each(function() {
                var item = $(this).closest('.cart-list-item');
                if ($(this).hasClass('guest')) {
                    selected.push(item.data('id'));
                } else {
                    selected.push($(this).val());
                }

                // (คงเดิม) อ่านค่า qty ล่าสุด
                var qty = parseInt(item.find('.qty').val());
                if (isNaN(qty) || qty < 1) {
                    qty = 1;
                }
                var max = parseInt(item.attr('data-max'));
                if (qty > max) {
                    valid = false;
                    var productName = item.find('h4').text();
                    errorMessage += `• ${productName} มีในสต๊อกแค่ ${max} ชิ้น<br>`;
                }
            });

            if (selected.length === 0) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'ยังไม่ได้เลือกสินค้า',
                    text: 'กรุณาเลือกสินค้าที่ต้องการชำระก่อน'
                });
                return false;
            }
            if (!valid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'สินค้าไม่เพียงพอ',
                    html: errorMessage
                });
                return false;
            }
            $('#selected_items').val(selected.join(','));
        });

        // ✨ (ใหม่) เรียกซิงค์สถานะครั้งแรกเมื่อโหลดหน้า
        syncSelectAll();
        calculateSelectedTotal(); // เรียกคำนวณราคาครั้งแรก
    });


    function calculateSelectedTotal() {
        let total = 0;
        $('.cart-check:checked').each(function() {
            total += parseFloat($(this).data('price'));
        });
        let formattedTotal = total.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        $('#selected-total').text(formattedTotal);
    }
</script>