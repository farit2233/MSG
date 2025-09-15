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

                                    // ✨ ตรรกะการคำนวณจำนวนสั่งซื้อสูงสุด (มีอยู่แล้ว)
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

                                    <label class="list-group-item cart-item d-flex w-100 <?= $row['available'] <= 0 ? 'out-of-stock' : '' ?>"
                                        data-id='<?= $row['id'] ?>'
                                        data-max='<?= format_num($row['available'], 0) ?>'
                                        style="cursor: pointer;">
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
                                                <img src="<?= validate_image($row['image_path']) ?>" class="product-logo" alt="">
                                            </div>

                                            <div class="col-auto flex-shrink-1 flex-grow-1">
                                                <h4 class="product-title"><?= $row['product'] ?></h4>
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

                                    </label>
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
    function update_item(cart_id, qty) {
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
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
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
        // ✨ FIX: เพิ่ม :not(.guest) เพื่อไม่ให้กระทบตะกร้าของ Guest
        $('.add-qty:not(.guest)').click(function() {
            var item = $(this).closest('.cart-item');
            var qtyInput = item.find('.qty');
            var qty = parseInt(qtyInput.val()) || 1;
            var max = parseInt(item.attr('data-max'));
            if (qty < max) {
                qty++;
                qtyInput.val(qty);
                update_item(item.attr('data-id'), qty);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'สินค้าในคลังมีไม่พอ',
                    text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
                });
            }
        });

        $('.minus-qty:not(.guest)').click(function() {
            var item = $(this).closest('.cart-item');
            var qtyInput = item.find('.qty');
            var qty = parseInt(qtyInput.val()) || 1;
            if (qty > 1) {
                qty--;
                qtyInput.val(qty);
                update_item(item.attr('data-id'), qty);
            }
        });

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
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_cart(id);
                }
            });
        });
    });
    document.addEventListener('input', function(e) {
        if (!e.target.matches('.qty.guest')) return;

        const itemEl = e.target.closest('.cart-item');
        const index = parseInt(itemEl.dataset.id);
        const max = parseInt(itemEl.dataset.max);
        let newQty = parseInt(e.target.value);

        if (isNaN(newQty) || newQty < 1) {
            newQty = 1;
        }
        if (newQty > max) {
            newQty = max;
            Swal.fire({
                icon: 'warning',
                title: 'สินค้าในคลังมีไม่พอ',
                text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
            });
        }
        e.target.value = newQty; // อัปเดตค่าในช่อง input ทันที
        updateGuestCartQty(index, newQty, true); // true หมายถึงการตั้งค่าใหม่ ไม่ใช่การบวกลบ
    });
    // ===================================
    // ✨ SECTION: For Guest Users (Vanilla JS) - แก้ไขทั้งหมด
    // ===================================
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('guest_cart_container');
        if (!container) return;
        const userLoggedIn = <?= ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
        if (userLoggedIn) return;

        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart.length === 0) {
            container.innerHTML = '<h5 class="text-center text-muted">ตะกร้าว่างเปล่า ช็อปเลย!</h5>';
            container.style.display = 'block';
            return;
        }

        // ✨ FIX: ดึง ID สินค้าทั้งหมดเพื่อส่งไปขอข้อมูลสต็อก
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

                    // ✨ FIX: นำ Logic การหาร 3 มาใช้กับ Guest
                    let max_order_qty = 0;
                    if (available > 0) {
                        if (available >= 100) {
                            max_order_qty = Math.floor(available / 3);
                        } else if (available >= 50) {
                            max_order_qty = Math.floor(available / 2);
                        } else if (available >= 30) {
                            max_order_qty = Math.floor(available / 1.5);
                        } else {
                            max_order_qty = Math.max(1, Math.floor(available / 1));
                        }
                    }

                    const isOutOfStock = available <= 0;
                    const currentQty = Math.min(item.qty, max_order_qty); // ปรับจำนวนในตะกร้าไม่ให้เกินสต็อก
                    if (item.qty > currentQty) { // อัปเดต localStorage หากจำนวนเดิมเกิน
                        cart[index].qty = currentQty;
                        localStorage.setItem('guest_cart', JSON.stringify(cart));
                    }

                    const show_discount = item.discounted_price && item.discounted_price < item.vat_price;
                    const price_to_use = show_discount ? item.discounted_price : item.vat_price;
                    const subtotal = price_to_use * currentQty;

                    html += `
            <label class="list-group-item cart-item d-flex w-100 ${isOutOfStock ? 'out-of-stock' : ''}" data-id="${index}" data-product-id="${item.id}" data-max="${max_order_qty}">
                <div class="col-auto pr-2">
                    <input type="checkbox" class="form-check-input cart-check guest" data-price="${subtotal}" ${isOutOfStock ? 'disabled' : ''} />
                </div>
                <div class="cart-item-content d-flex w-100 align-items-start">
                    <div class="col-3 text-center"><img src="${item.image || 'assets/img/default.png'}" class="product-logo" alt=""></div>
                    <div class="col-auto flex-shrink-1 flex-grow-1">
                        <h4 class="product-title">${item.name}</h4>
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
                        ? `<h5 class="text-muted mb-0"><del>${formatPriceForGuest(item.vat_price * currentQty)} บาท</del></h5><h4><b class="text-danger">ลดเหลือ: ${formatPriceForGuest(subtotal)} บาท</b></h4>`
                        : `<h4><b>ราคา: ${formatPriceForGuest(subtotal)} บาท</b></h4>`
                    }
                </div>
            </label>`;
                });
                container.innerHTML = html;
                container.style.display = 'block';
                calculateSelectedTotal();
            });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.guest')) return;

        const itemEl = e.target.closest('.cart-item');
        if (!itemEl) return;
        const index = parseInt(itemEl.dataset.id);

        if (e.target.matches('.add-qty.guest')) {
            updateGuestCartQty(index, 1);
        }
        if (e.target.matches('.minus-qty.guest')) {
            updateGuestCartQty(index, -1);
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
                cancelButtonText: 'ยกเลิก'
            }).then(result => result.isConfirmed && deleteGuestCartItem(index));
        }
    });

    function updateGuestCartQty(index, value, isDirectSet = false) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (!cart[index]) return;

        const itemData = cart[index];
        let newQty = isDirectSet ? value : (itemData.qty || 1) + value;

        // ✨ ค้นหา Element ของสินค้าชิ้นนั้นๆ
        const itemEl = document.querySelector(`.cart-item[data-id="${index}"]`);
        if (!itemEl) return;

        const max = parseInt(itemEl.dataset.max);

        // ✨ ตรวจสอบจำนวนไม่ให้เกินสต็อกหรือไม่ให้น้อยกว่า 1
        if (newQty > max) {
            newQty = max;
            Swal.fire({
                icon: 'warning',
                title: 'สินค้าในคลังมีไม่พอ',
                text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + max + ' ชิ้น'
            });
        }
        if (newQty < 1) newQty = 1;

        // ✨ อัปเดตเฉพาะเมื่อจำนวนมีการเปลี่ยนแปลงจริงๆ
        if (itemData.qty !== newQty) {
            itemData.qty = newQty;
            localStorage.setItem('guest_cart', JSON.stringify(cart));

            // --- ส่วนของการอัปเดตหน้าเว็บโดยตรง (DOM Manipulation) ---

            // 1. อัปเดตตัวเลขในช่อง input
            itemEl.querySelector('.qty.guest').value = newQty;

            // 2. คำนวณราคาสินค้าใหม่
            const show_discount = itemData.discounted_price && itemData.discounted_price < itemData.vat_price;
            const price_to_use = show_discount ? itemData.discounted_price : itemData.vat_price;
            const newSubtotal = price_to_use * newQty;

            // 3. อัปเดตราคาที่แสดงผล
            const priceContainer = itemEl.querySelector('.col-auto.text-right');
            let priceHTML = '';
            if (show_discount) {
                priceHTML = `<h5 class="text-muted mb-0"><del>${formatPriceForGuest(itemData.vat_price * newQty)} บาท</del></h5><h4><b class="text-danger">ลดเหลือ: ${formatPriceForGuest(newSubtotal)} บาท</b></h4>`;
            } else {
                priceHTML = `<h4><b>ราคา: ${formatPriceForGuest(newSubtotal)} บาท</b></h4>`;
            }
            priceContainer.innerHTML = priceHTML;

            // 4. อัปเดต data-price ของ checkbox เพื่อให้ยอดรวมถูกต้อง
            const checkbox = itemEl.querySelector('.cart-check.guest');
            checkbox.dataset.price = newSubtotal;

            // 5. เรียกใช้ฟังก์ชันคำนวณยอดรวมใหม่
            calculateSelectedTotal();
        }
    }

    function deleteGuestCartItem(index) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];

        // ✨ ค้นหา Element ของสินค้าที่จะลบ
        const itemEl = document.querySelector(`.cart-item[data-id="${index}"]`);

        // ✨ ลบข้อมูลออกจาก Array และอัปเดต localStorage
        cart.splice(index, 1);
        localStorage.setItem('guest_cart', JSON.stringify(cart));
        updateCartCounter(); // ✨ อย่าลืมเรียกใช้ฟังก์ชันอัปเดตตัวเลขบนไอคอนตะกร้า (ถ้ามี)

        // ✨ ลบ Element ของสินค้านั้นออกจากหน้าเว็บ
        if (itemEl) {
            itemEl.remove();
        }

        // ✨ คำนวณยอดรวมใหม่
        calculateSelectedTotal();

        // ✨ ตรวจสอบว่าถ้าตะกร้าว่าง ให้แสดงข้อความ "ตะกร้าว่างเปล่า"
        if (cart.length === 0) {
            document.getElementById('guest_cart_container').innerHTML = '<h5 class="text-center text-muted">ตะกร้าว่างเปล่า ช็อปเลย!</h5>';
            // ซ่อนปุ่มชำระเงิน (ถ้ามี)
            const checkoutForm = document.getElementById('checkout-form');
            if (checkoutForm) {
                checkoutForm.style.display = 'none';
            }
        }
    }

    function formatPriceForGuest(value) {
        return value.toLocaleString('th-TH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
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
                    selected.push(item.data('id')); // สำหรับ guest ใช้ index
                } else {
                    selected.push($(this).val()); // สำหรับ member ใช้ cart_id
                }

                var qty = parseInt(item.find('.qty').val());
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