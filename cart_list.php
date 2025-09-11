<?php

if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) {
    // โหลดจากฐานข้อมูลสำหรับผู้ที่ล็อกอิน
    $cart = $conn->query("SELECT 
        c.*, 
        p.name as product, 
        p.brand as brand, 
        p.price, 
        p.discounted_price, 
        p.vat_price,
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
    // Your code for displaying items goes here...
    endwhile;
} else {
    // ใช้ข้อมูลจาก localStorage เมื่อไม่ได้ล็อกอิน
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

if (!function_exists('format_price_custom')) {
    function format_price_custom($price)
    {
        $formatted_price = format_num($price, 2);
        if (substr($formatted_price, -3) == '.00') {
            return format_num($price, 0);
        }
        return $formatted_price;
    }
}
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
                                    p.vat_price,
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
                                    // เงื่อนไขเลือกใช้ราคา
                                    if (!empty($row['discounted_price']) && $row['discounted_price'] < $row['vat_price']) {
                                        $price_to_use = $row['discounted_price'];
                                        $show_discount = true;
                                    } elseif (!empty($row['vat_price']) && $row['vat_price'] > 0) {
                                        $price_to_use = $row['vat_price'];
                                        $show_discount = false;
                                    } else {
                                        $price_to_use = $row['vat_price'];
                                        $show_discount = false;
                                    }

                                    $subtotal = $price_to_use * $row['quantity'];
                                    $gt += $subtotal;
                                ?>

                                    <label class="list-group-item cart-item d-flex w-100 <?= $row['available'] <= 0 ? 'out-of-stock' : '' ?>"
                                        data-id='<?= $row['id'] ?>'
                                        data-max='<?= floor($row['available'] / 3) ?>'
                                        style="cursor: pointer;">

                                        <div class="col-auto pr-2">
                                            <input type="checkbox"
                                                class="form-check-input cart-check"
                                                name="selected_cart[]"
                                                value="<?= $row['id'] ?>"
                                                id="cart_check_<?= $row['id'] ?>"
                                                data-price="<?= $subtotal ?>"
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
                                                    <del><?= format_price_custom($row['vat_price'] * $row['quantity']) ?> บาท</del>
                                                </h5>
                                                <h4><b class="text-danger">ลดเหลือ: <?= format_price_custom($row['discounted_price'] * $row['quantity']) ?> บาท</b></h4>
                                            <?php else: ?>
                                                <h4><b>ราคา: <?= format_price_custom($subtotal) ?> บาท</b></h4>
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
                                    <h3 class="selected-total"><b>รวมรายการที่เลือก: <span id="selected-total">0</span></b> บาท</h3>
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
    function update_item(cart_id = '', qty = 0) {
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
                console.log(err)
                alert_toast("An error occurred.", 'error')
                end_loader()
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload()
                } else {
                    alert_toast("An error occurred.", 'error')
                }
                end_loader()
            }
        })
    }
    $(function() {
        $('.add-qty').click(function() {
            var item = $(this).closest('.cart-item')
            var qty = parseFloat(item.find('.qty').val())
            var id = item.attr('data-id')
            var max = item.attr('data-max')
            if (qty == max)
                qty = max;
            else
                qty += 1;
            item.find('.qty').val(qty)
            update_item(id, qty)
        })
        $('.minus-qty').click(function() {
            var item = $(this).closest('.cart-item')
            var qty = parseFloat(item.find('.qty').val())
            var id = item.attr('data-id')
            if (qty == 1)
                qty = 1;
            else
                qty -= 1;
            item.find('.qty').val(qty)
            update_item(id, qty)
        })
        $('.del-item').click(function() {
            var item = $(this).closest('.cart-item')
            var id = item.attr('data-id')
            Swal.fire({
                icon: 'warning',
                title: 'คุณแน่ใจไหม?',
                text: "ลบสินค้าออกจากตะกร้า?",
                showCancelButton: true,
                confirmButtonColor: '#e74c3c', // สีแดงปุ่มยืนยัน
                cancelButtonColor: '#95a5a6', // สีเทาปุ่มยกเลิก
                confirmButtonText: 'ใช่ ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_cart(id); // ฟังก์ชันลบจริง
                }
            })
        })
    })

    function delete_cart($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_cart",
            method: "POST",
            data: {
                id: $id
            },
            dataType: "json",
            error: err => {
                console.log(err)
                alert_toast("An error occured.", 'error');
                end_loader();
            },
            success: function(resp) {
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }
    $('#checkout-form').submit(function(e) {
        var selected = [];
        var valid = true;
        var errorMessage = '';

        $('.cart-check:checked').each(function() {
            var item = $(this).closest('.cart-item');
            var qty = parseInt(item.find('.qty').val());
            var max = parseInt(item.attr('data-max'));
            var productName = item.find('h4').text();

            if (qty > max) {
                valid = false;
                errorMessage += '• ' + productName + ' มีในสต๊อกแค่ ' + max + ' ชิ้น\n';
            }

            selected.push($(this).val());
        });

        $('#selected_items').val(selected.join(','));

        if (selected.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'ยังไม่ได้เลือกสินค้า',
                text: 'กรุณาเลือกสินค้าที่ต้องการชำระก่อน',
                confirmButtonColor: '#95a5a6',
                confirmButtonText: 'ปิด'
            });
            return false;
        }

        if (!valid) {
            Swal.fire({
                icon: 'error',
                title: 'ไม่สามารถชำระได้',
                html: 'เนื่องจาก:<br>' + errorMessage
            });
            return false;
        }

        return true; // ให้ submit ได้ถ้าทุกอย่างถูกต้อง
    });

    function calculateSelectedTotal() {
        let total = 0;
        $('.cart-check:checked').each(function() {
            total += parseFloat($(this).data('price'));
        });

        let formattedTotal;

        // ถ้าทศนิยมเป็น .00 ให้ลบออก
        if (total % 1 === 0) {
            formattedTotal = total.toLocaleString('th-TH', {
                maximumFractionDigits: 0
            });
        } else {
            formattedTotal = total.toLocaleString('th-TH', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        $('#selected-total').text(formattedTotal);
    }

    // ตัวอย่าง: เรียกฟังก์ชันตอนโหลดหน้าและทุกครั้งที่เช็ค/ยกเลิก
    $(document).ready(function() {
        calculateSelectedTotal();
        $('.cart-check').on('change', calculateSelectedTotal);
    });

    $(function() {
        // เรียกเมื่อมีการเปลี่ยนสถานะ checkbox
        $('.cart-check').on('change', function() {
            calculateSelectedTotal();
        });

        // เรียกตอนโหลดหน้า (กรณีมีติ๊กไว้แล้ว)
        calculateSelectedTotal();
    });
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('guest_cart_container');
        if (!container) return;

        const userLoggedIn = <?= ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2) ? 'true' : 'false' ?>;
        if (userLoggedIn) return;

        const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart.length === 0) {
            container.innerHTML = '<h5 class="text-center text-muted">ตะกร้าว่างเปล่า ช็อปเลย!</h5>';
            container.style.display = 'block';
            return;
        }

        let html = '';
        let grandTotal = 0;

        cart.forEach((item, index) => {
            const show_discount = item.discounted_price && item.discounted_price < item.price;
            const price_to_use = show_discount ? item.discounted_price : item.price;
            const subtotal = price_to_use * item.qty;
            grandTotal += subtotal;

            html += `
<label class="list-group-item cart-item d-flex w-100 ${item.available <= 0 ? 'out-of-stock' : ''}"
    data-id='${item.id}'
    data-max='${item.available}'
    style="cursor: pointer;">
    <div class="col-auto pr-2">
        <input type="checkbox" class="form-check-input cart-check" data-price="${subtotal}" />
    </div>

    <div class="cart-item-content d-flex w-100 align-items-start">
        <div class="col-3 text-center">
            <img src="${item.image || 'assets/img/default.png'}" class="product-logo" alt="">
        </div>

        <div class="col-auto flex-shrink-1 flex-grow-1">
            <h4 class="product-title">${item.name}</h4>
            <div class="text-muted d-flex w-100">
                <div class="input-group" style="width: 20rem;">
                    <button class="btn addcart-plus minus-qty guest" type="button">−</button>
                    <input type="number" class="form-control text-center qty" value="${item.qty}" min="1" max="${item.available}" required>
                    <button class="btn addcart-plus add-qty guest" type="button">+</button>
                    <button class="btn btn-danger ms-2 del-item guest" type="button">
                        <i class="fa-solid fa-trash-can"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="col-auto text-right">
        ${show_discount
            ? `<h5 class="text-muted mb-0"><del>${(item.price * item.qty).toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</del></h5>
               <h4><b class="text-danger">ลดเหลือ: ${subtotal.toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</b></h4>`
            : `<h4><b>ราคา: ${subtotal.toLocaleString('th-TH', {minimumFractionDigits: 2})} บาท</b></h4>`
        }
    </div>
</label>`;
        });

        container.innerHTML = html;
        container.style.display = 'block';
        calculateSelectedTotal(); // เรียกทันทีตอนโหลด
    });

    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-qty') && e.target.classList.contains('guest')) {
            const itemEl = e.target.closest('.cart-item');
            const index = parseInt(itemEl.dataset.id);
            let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
            if (cart[index]) {
                const currentQty = cart[index].qty || 1;
                updateGuestCartQty(index, currentQty + 1);
            }
        }


        if (e.target.classList.contains('minus-qty') && e.target.classList.contains('guest')) {
            const itemEl = e.target.closest('.cart-item');
            const index = parseInt(itemEl.dataset.id);
            const qtyInput = itemEl.querySelector('.qty');
            let qty = parseInt(qtyInput.value) || 1;
            if (qty > 1) updateGuestCartQty(index, qty - 1);
        }

        if (e.target.classList.contains('del-item') && e.target.classList.contains('guest')) {
            const itemEl = e.target.closest('.cart-item');
            const index = parseInt(itemEl.dataset.id);
            Swal.fire({
                icon: 'warning',
                title: 'คุณแน่ใจไหม?',
                text: "ลบสินค้าออกจากตะกร้า?",
                showCancelButton: true,
                confirmButtonColor: '#e74c3c', // สีแดงปุ่มยืนยัน
                cancelButtonColor: '#95a5a6', // สีเทาปุ่มยกเลิก
                confirmButtonText: 'ใช่ ลบเลย!',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteGuestCartItem(index);
                }
            });
        }
    });

    function updateGuestCartQty(index, qty) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart[index]) {
            cart[index].qty = qty;
            localStorage.setItem('guest_cart', JSON.stringify(cart));
            location.reload(); // ถ้าอยาก smooth กว่านี้ ค่อยทำ render ซ้ำโดยไม่ reload
        }
    }

    function deleteGuestCartItem(index) {
        let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
        if (cart[index]) {
            cart.splice(index, 1);
            localStorage.setItem('guest_cart', JSON.stringify(cart));
            location.reload();
        }
    }
</script>