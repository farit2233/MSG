<?php

if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
    // โหลดจาก database (สำหรับคน login)
} else {
    // ใช้ JavaScript อ่านจาก localStorage แล้วแสดงรายการตะกร้า
}


/*if ($_settings->userdata('id') == '' || $_settings->userdata('login_type') != 2) {
 echo "<script>
    Swal.fire({
      icon: 'warning',
    title: 'คุณยังไม่ได้เข้าสู่ระบบ',
  text: 'โปรดเข้าสู่ระบบก่อนใช้งาน',
confirmButtonText: 'เข้าสู่ระบบ',
allowOutsideClick: false,
allowEscapeKey: false
}).then((result) => {
   if (result.isConfirmed) {
     window.location.replace('./login.php');
}
 });

</script>";
exit;
}*/
?>
<style>
    /* ปรับให้ทันสมัย */
    .product-logo {
        width: 7em;
        height: 7em;
        object-fit: cover;
        object-position: center center;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Header bar */
    .cart-header-bar {
        border-left: 4px solid #ff6600;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* ข้อความสินค้า */
    .product-title {
        font-size: 18px;
        font-weight: 600;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cart-item .text-muted {
        font-size: 14px;
    }

    /* Grand total */
    .cart-total {
        font-size: 22px;
        font-weight: bold;
        color: #333;
        text-align: center !important;
        margin-top: 1.5rem;
    }

    /* ปรับขนาดและสี checkbox ให้เห็นชัด */
    .cart-item input[type="checkbox"] {
        width: 20px;
        height: 20px;
        margin-top: 6px;
    }

    .addcart-plus {
        background: none;
        color: #f57421;
        border: 2px solid #f57421;
        padding: 5px 15px;
        margin-right: 1rem;
    }

    .addcart {
        background: none;
        color: #f57421;
        border: 2px solid #f57421;
        padding: 10px 50px;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .addcart:hover,
    .addcart-plus:hover {
        background-color: #f57421;
        color: white;
        display: inline-block;
    }


    .cart-item.out-of-stock {
        background-color: #f8d7da;
        /* แดงอ่อน */
        border: 1px solid #f5c6cb;
        position: relative;
        opacity: 0.8;
    }

    .cart-item.out-of-stock::before {
        content: "สินค้าหมด";
        position: absolute;
        top: 10px;
        right: 10px;
        background: #dc3545;
        color: white;
        padding: 3px 8px;
        font-size: 0.9rem;
        border-radius: 4px;
        font-weight: bold;
        z-index: 2;
    }

    @media (max-width: 431px) {

        .cart-item h4,
        .cart-item h5,
        .cart-item .text-muted {
            font-size: 14px !important;
            line-height: 1.2;
            white-space: normal;
            word-wrap: break-word;
        }
    }

    @media (max-width: 932px) {

        .cart-item {
            flex-direction: column !important;
            align-items: flex-start;
            padding: 10px;
        }

        .cart-item .d-flex.w-100.align-items-center {
            flex-direction: row;
            flex-wrap: nowrap;
            width: 100%;
        }

        .cart-item .col-2.text-center {
            flex: 0 0 30%;
            max-width: 30%;
            padding-right: 10px;
        }

        .cart-item .product-logo {
            width: 100%;
            height: auto;
        }

        .cart-item .col-auto.flex-shrink-1.flex-grow-1 {
            flex: 1 1 70%;
            max-width: 70%;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .cart-item .col-auto.text-right {
            width: 100%;
            text-align: right;
            margin-top: 10px;
        }

        .cart-item .input-group {
            flex-wrap: nowrap;
            width: 100%;
        }

        .cart-item h4,
        .cart-item h5,
        .cart-item .text-muted {
            font-size: 22px;
            line-height: 1.2;
            white-space: normal;
            word-wrap: break-word;
        }

        .input-group input.qty {
            width: 3.5rem;
            min-width: 3.5rem;
        }

        .cart-item .addcart-plus,
        .cart-item .addcart-minus,
        .cart-item .qty,
        .cart-item .del-item {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.8rem !important;
            min-width: 35px;
            height: 35px;
        }

        .cart-item .qty {
            width: 40px;
            text-align: center;
        }

        .cart-item .del-item i {
            font-size: 0.9rem !important;
        }

        .cart-item .text-muted.brand,
        .cart-item .text-muted.category {
            font-size: 0.75rem !important;
            /* หรือ 12px */
            line-height: 1.2;
        }

        .selected-total {
            font-size: 20px;
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
                            <div class="cart-header-bar">
                                <i class="fa fa-basket-shopping mr-2" style="font-size: 30px;"></i>
                                <h3 class="d-inline mb-0">ตะกร้าของฉัน</h3>
                            </div>
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

                                    <div class="list-group-item cart-item d-flex w-100 <?= $row['available'] <= 0 ? 'out-of-stock' : '' ?>"
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

                                        <div class="cart-item-content d-flex w-100 align-items-start">
                                            <div class="col-3 text-center">
                                                <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>">
                                                    <img src="<?= validate_image($row['image_path']) ?>" class="product-logo" alt="" style="cursor: pointer;">
                                                </a>
                                            </div>

                                            <div class="col-auto flex-shrink-1 flex-grow-1">
                                                <a href="./?p=products/view_product&id=<?= $row['product_id'] ?>" style="text-decoration: none; color: inherit;">
                                                    <h4 class="product-title" style="cursor: pointer;"><?= $row['product'] ?></h4>
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
            var item = $(this).closest('.cart-item');
            // เรียกฟังก์ชันอัปเดตราคา (Visual only)
            update_visual_price(item);
        });

        // ✨ (ใหม่) Event 'change': ทำงานเมื่อคลิกออก/พิมพ์เสร็จ บันทึกลง DB (AJAX)
        $('.qty:not(.guest)').on('change', function() {
            var item = $(this).closest('.cart-item');
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
            var item = $(this).closest('.cart-item');
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
            var item = $(this).closest('.cart-item');
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
            var id = $(this).closest('.cart-item').attr('data-id');
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
        const itemEl = e.target.closest('.cart-item');
        const index = parseInt(itemEl.dataset.id);
        const newQty = e.target.value; // ส่งค่า string
        // (index, value, isDirectSet, saveToStorage)
        updateGuestCartQty(index, newQty, true, false); // false = ไม่บันทึก
    });

    // ✨ (ใหม่) Event 'change' (สำหรับ Guest) - Save to localStorage
    document.addEventListener('change', function(e) {
        if (!e.target.matches('.qty.guest')) return;
        const itemEl = e.target.closest('.cart-item');
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

                    // ✨ (แก้ไข) ถ้าจำนวนในตะกร้าเกินสต็อก ให้ปรับ แต่ "ไม่ต้องบันทึก" ตอนวาด
                    // การบันทึกจะเกิดเมื่อ user แก้ไขเอง
                    const currentQty = Math.min(item.qty, max_order_qty);
                    // if (item.qty > currentQty) {
                    //     cart[index].qty = currentQty;
                    //     localStorage.setItem('guest_cart', JSON.stringify(cart));
                    // }

                    const show_discount = item.discounted_price && item.discounted_price < item.vat_price;
                    const price_to_use = show_discount ? item.discounted_price : item.vat_price;
                    const subtotal = price_to_use * currentQty;

                    html += `
                <div class="list-group-item cart-item d-flex w-100 ${isOutOfStock ? 'out-of-stock' : ''}" data-id="${index}" data-product-id="${item.id}" data-max="${max_order_qty}">
                    
                    <div class="col-auto pr-2">
                        <input type="checkbox" class="form-check-input cart-check guest" data-price="${subtotal}" ${isOutOfStock ? 'disabled' : ''} />
                    </div>

                    <div class="cart-item-content d-flex w-100 align-items-start">
                        <div class="col-3 text-center">
                            <a href="./?p=products/view_product&id=${item.id}">
                                <img src="${item.image || 'assets/img/default.png'}" class="product-logo" alt="" style="cursor: pointer;">
                            </a>
                        </div>
                        
                        <div class="col-auto flex-shrink-1 flex-grow-1">
                            <a href="./?p=products/view_product&id=${item.id}" style="text-decoration: none; color: inherit;">
                                <h4 class="product-title" style="cursor: pointer;">${item.name}</h4>
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

        const itemEl = e.target.closest('.cart-item');
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
        const itemEl = document.querySelector(`.cart-item[data-id="${index}"]`);
        if (!itemEl) return;

        const qtyInput = itemEl.querySelector('.qty.guest');
        const max = parseInt(itemEl.dataset.max);
        let newQtyNum;

        if (isDirectSet) {
            // มาจาก 'input' หรือ 'change' (value คือ string)
            newQtyNum = parseInt(value);
        } else {
            // มาจาก '+' หรือ '-' (value คือ 1 หรือ -1)
            newQtyNum = (itemData.qty || 1) + value;
        }

        // --- Validation ---
        if (!isNaN(newQtyNum) && newQtyNum > max) {
            newQtyNum = max;
            qtyInput.value = newQtyNum;
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
                qtyInput.value = newQtyNum; // อัปเดตช่อง input
            }
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
    $(function() {
        calculateSelectedTotal();
        $(document).on('change', '.cart-check', calculateSelectedTotal);

        $('#checkout-form').submit(function(e) {
            var selected = [];
            var valid = true;
            var errorMessage = '';

            $('.cart-check:checked').each(function() {
                var item = $(this).closest('.cart-item');
                if ($(this).hasClass('guest')) {
                    selected.push(item.data('id'));
                } else {
                    selected.push($(this).val());
                }

                // ✨ (สำคัญ) อ่านค่า qty ล่าสุดจากช่อง input โดยตรง
                var qty = parseInt(item.find('.qty').val());

                // ✨ (สำคัญ) ตรวจสอบค่าว่าง/0 ก่อนส่ง
                if (isNaN(qty) || qty < 1) {
                    qty = 1; // (ถึงแม้ว่า change event จะดักไว้แล้ว แต่กันเหนียว)
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
    });

    /**
     * (คงเดิม) ฟังก์ชันคำนวณยอดรวมที่เลือก
     */
    function calculateSelectedTotal() {
        let total = 0;
        $('.cart-check:checked').each(function() {
            total += parseFloat($(this).data('price'));
        });
        let formattedTotal = total.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
        if (formattedTotal.endsWith('.00')) {
            formattedTotal = total.toLocaleString('th-TH', {
                maximumFractionDigits: 0
            });
        }
        $('#selected-total').text(formattedTotal);
    }
</script>