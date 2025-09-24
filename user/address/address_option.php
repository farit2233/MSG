<?php
// ต้องเรียก config.php เพื่อเชื่อมต่อฐานข้อมูล
require_once('../../config.php');

// รับ ID ของลูกค้าจาก parameter
$customer_id = isset($_GET['pid']) ? $_GET['pid'] : '';

if (!empty($customer_id)) {
    // 1. ดึงข้อมูลที่อยู่หลักจาก customer_addresses (กำหนด is_primary = 1)
    $main_address_qry = $conn->query("SELECT * FROM `customer_addresses` WHERE customer_id = '{$customer_id}' AND is_primary = 1");

    if ($main_address_qry->num_rows > 0) {
        $main_address = $main_address_qry->fetch_assoc();
    }

    // 2. ดึงข้อมูลที่อยู่ทั้งหมดจาก customer_addresses
    $addresses_qry = $conn->query("SELECT * FROM `customer_addresses` WHERE customer_id = '{$customer_id}' AND is_primary = 0");
}
?>

<style>
    /* Style สำหรับรายการที่อยู่ใน Modal */
    .address-item {
        cursor: pointer;
        transition: background-color 0.2s;
        position: relative;
    }

    .address-item:hover {
        background-color: #f5f5f5;
    }

    /* --- เพิ่ม Style สำหรับปุ่มลบ --- */
    .delete-address-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        background-color: white;
        border-radius: 50%;
        color: #dc3545;
        font-size: 22px;
        line-height: 1;
        text-decoration: none;
        opacity: 0;
        transition: opacity 0.2s ease-in-out;
        z-index: 10;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* แสดงปุ่มลบเมื่อ hover ที่ card */
    .address-item:hover .delete-address-btn {
        opacity: 1;
    }

    .delete-address-btn:hover {
        color: #a71d2a;
    }
</style>

<div class="container-fluid">

    <form id="address_selection_form">
        <?php if (isset($main_address)): ?>
            <div class="address-item p-2 border rounded mb-2">
                <input type="radio" name="selected_address" id="address_main" value="main"
                    data-address="<?= htmlspecialchars($main_address['address']) ?>"
                    data-sub_district="<?= htmlspecialchars($main_address['sub_district']) ?>"
                    data-district="<?= htmlspecialchars($main_address['district']) ?>"
                    data-province="<?= htmlspecialchars($main_address['province']) ?>"
                    data-postal_code="<?= htmlspecialchars($main_address['postal_code']) ?>"
                    checked>
                <label for="address_main" class="mb-0 ml-2">
                    <strong>ที่อยู่หลัก:</strong><br>
                    <?= htmlspecialchars($main_address['address']) ?>, <?= htmlspecialchars($main_address['sub_district']) ?>, <?= htmlspecialchars($main_address['district']) ?>, <?= htmlspecialchars($main_address['province']) ?> <?= htmlspecialchars($main_address['postal_code']) ?>
                </label>
            </div>
        <?php endif; ?>

        <?php if (isset($addresses_qry) && $addresses_qry->num_rows > 0): ?>
            <?php while ($row = $addresses_qry->fetch_assoc()): ?>
                <div class="address-item p-2 border rounded mb-2">
                    <a href="#" class="delete-address-btn" data-id="<?= $row['id'] ?>" title="ลบที่อยู่">
                        <i class="fa-solid fa-times-circle"></i>
                    </a>
                    <input type="radio" name="selected_address" id="address_<?= $row['id'] ?>" value="<?= $row['id'] ?>"
                        data-address="<?= htmlspecialchars($row['address']) ?>"
                        data-sub_district="<?= htmlspecialchars($row['sub_district']) ?>"
                        data-district="<?= htmlspecialchars($row['district']) ?>"
                        data-province="<?= htmlspecialchars($row['province']) ?>"
                        data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>">
                    <label for="address_<?= $row['id'] ?>" class="mb-0 ml-2">
                        <strong>ที่อยู่เพิ่มเติม:</strong><br>
                        <?= htmlspecialchars($row['address']) ?>, <?= htmlspecialchars($row['sub_district']) ?>, <?= htmlspecialchars($row['district']) ?>, <?= htmlspecialchars($row['province']) ?> <?= htmlspecialchars($row['postal_code']) ?>
                    </label>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-muted">ยังไม่มีที่อยู่เพิ่มเติม</p>
        <?php endif; ?>
        <p class="text-center text-sm text-muted"><a href="#" id="new_address">เพิ่มที่อยู่ใหม่</a></p>
    </form>

    <form id="new_address_form" style="display: none;">
        <div class="form-group">
            <label for="address" class="control-label">บ้านเลขที่ ถนน <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="address" id="address" required>
        </div>
        <div class="form-group">
            <label for="sub_district" class="control-label">ตำบล <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="sub_district" id="sub_district" required>
        </div>
        <div class="form-group">
            <label for="district" class="control-label">อำเภอ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="district" id="district" required>
        </div>
        <div class="form-group">
            <label for="province" class="control-label">จังหวัด <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="province" id="province" required>
        </div>
        <div class="form-group">
            <label for="postal_code" class="control-label">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="postal_code" id="postal_code" required>
        </div>
        <div class="text-right">
            <small><a href="#" id="back_to_change_book_address">กลับไปหน้าสมุดบัญชี</a></small>
        </div>
    </form>

</div>

<style>
    /* Style สำหรับรายการที่อยู่ใน Modal */
    .address-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .address-item:hover {
        background-color: #f5f5f5;
    }
</style>

<script>
    $(document).ready(function() {
        $('#new_address').click(function(e) {
            e.preventDefault();
            showNewAddressForm();
        });

        // เมื่อคลิกลิงก์ "กลับไปหน้าเปลี่ยนรหัสผ่าน"
        $('#back_to_change_book_address').click(function(e) {
            e.preventDefault();
            showBookAddress();
        });

        function showBookAddress() {
            $('#address_selection_form').show();
            $('#new_address_form').hide();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_confirm_address').show();
            $('.modal-footer #btn_new_address').hide();
            $('.modal-title').html('สมุดที่อยู่ <i class="fa fa-pencil"></i>');
        };

        function showNewAddressForm() {
            $('#address_selection_form').hide();
            $('#new_address_form').show();
            $('#reset_success_message').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_confirm_address').hide();
            $('.modal-footer #btn_new_address').show();
            $('.modal-title').html('เพิ่มที่อยู่ใหม่ <i class="fa fa-pencil"></i>');
        };

        // เมื่อ submit ฟอร์ม "เปลี่ยนรหัสผ่าน"
        $('#address_selection_form').submit(function(e) {
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=registration",
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        // ซ่อนฟอร์มและปุ่มทั้งหมด แล้วแสดงข้อความสำเร็จ
                        $('#address_selection_form').show();
                        $('#new_address_form').hide();
                        $('.modal-footer #btn_change_password').hide();
                        $('.modal-footer #btn_forgot_password').hide();
                    } else {
                        Swal.fire('เกิดข้อผิดพลาด', resp.msg, 'error');
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire('เกิดข้อผิดพลาด', 'ไม่สามารถส่งคำขอรีเซ็ตรหัสผ่านได้', 'error');
                    end_loader();
                }
            });
        });

        // เมื่อ submit ฟอร์ม
        $('#new_address_form').submit(function(e) {
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=new_address",
                method: 'POST',
                data: $(this).serialize() + "&customer_id=<?= $customer_id ?>", // ควรส่ง customer_id ไปด้วย
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        // เปลี่ยนจากแสดง alert เป็น reload หน้าแทน
                        location.reload();
                    } else {
                        // แสดง alert error
                        Swal.fire({
                            title: 'เกิดข้อผิดพลาด',
                            text: resp.msg,
                            icon: 'error',
                            confirmButtonText: 'ตกลง'
                        });
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire({
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถส่งคำขอเพิ่มที่อยู่ได้',
                        icon: 'error',
                        confirmButtonText: 'ตกลง'
                    });
                    end_loader();
                }
            });
        });

        $('.delete-address-btn').click(function(e) {
            e.preventDefault(); // ป้องกันการทำงานปกติของลิงก์
            e.stopPropagation(); // หยุดการแพร่กระจายของอีเวนต์ไปที่ parent (address-item)

            var address_id = $(this).data('id'); // ดึง ID ของที่อยู่จาก attribute data-id
            var address_item = $(this).closest('.address-item'); // หา element .address-item ที่ใกล้ที่สุด

            // ใช้ Swal2 เพื่อยืนยันการลบ
            Swal.fire({
                title: 'คุณแน่ใจหรือไม่?',
                text: "คุณต้องการลบที่อยู่นี้ใช่ไหม",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ใช่, ลบเลย!',
                cancelButtonText: 'ยกเลิก',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    start_loader(); // เริ่มแสดง loader
                    $.ajax({
                        url: _base_url_ + "classes/Users.php?f=delete_address",
                        method: 'POST',
                        data: {
                            id: address_id
                        }, // ข้อมูลที่ส่งไปคือ ID ของที่อยู่
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.status == 'success') {
                                // ลบรายการที่อยู่ออกจากหน้าเว็บโดยไม่ต้องรีโหลด
                                address_item.fadeOut('slow', function() {
                                    $(this).remove();
                                });
                                Swal.fire(
                                    'ลบสำเร็จ!',
                                    'ที่อยู่ของคุณถูกลบแล้ว',
                                    'success'
                                )
                            } else {
                                Swal.fire(
                                    'เกิดข้อผิดพลาด!',
                                    resp.msg || 'ไม่สามารถลบที่อยู่ได้',
                                    'error'
                                );
                            }
                            end_loader(); // สิ้นสุดการแสดง loader
                        },
                        error: function(err) {
                            console.log(err);
                            Swal.fire(
                                'เกิดข้อผิดพลาด!',
                                'เกิดปัญหาในการสื่อสารกับเซิร์ฟเวอร์',
                                'error'
                            );
                            end_loader();
                        }
                    });
                }
            });
        });
    });
</script>