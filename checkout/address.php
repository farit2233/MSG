<?php
require_once('../config.php');

// ตรวจสอบว่าเรามีการส่ง ID มาไหม (สำหรับฟังก์ชันเปลี่ยนรหัสผ่าน)
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) {
                $$k = $v;
            }
        }
    } else {
        echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
    }
} else {
    echo "<script>alert('You don\\'t have access for this page'); location.replace('./');</script>";
}
?>
<style>
    .text-size-input {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }

    .password input,
    .password select {
        border-radius: 13px;
        font-size: 16px;
    }

    .address-option {
        border: 1px solid #eee;
        padding: 1rem;
        margin-bottom: 10px;
        border-radius: 10px;
        cursor: pointer;
        position: relative;
        transition: all 0.2s ease;
    }

    .address-option:hover {
        background: #f9f9f9;
    }

    .address-option.selected {
        border: 2px solid #f57421;
        background-color: #fff5f0;
    }

    .address-option .checkmark {
        position: absolute;
        right: 15px;
        top: 15px;
        color: #f57421;
        display: none;
    }

    .address-option.selected .checkmark {
        display: inline;
    }
</style>
<div class="container-fluid password">
    <form id="confirm_form_1">
        <?php
        $addresses = $conn->query("SELECT * FROM customer_addresses WHERE customer_id = '{$_settings->userdata('id')}' ORDER BY is_primary DESC, id ASC");
        while ($row = $addresses->fetch_assoc()):
        ?>
            <div class="address-option d-flex justify-content-between align-items-center mb-2" onclick="selectAddress(this)">
                <div class="flex-grow-1 d-flex align-items-center">
                    <input type="radio" name="address_id" value="<?= $row['id'] ?>" class="address-radio me-3" <?= ($row['is_primary'] == 1) ? 'checked' : '' ?>>
                    <div>
                        <h6 class="mb-0">
                            ที่อยู่ <?= ($row['is_primary'] == 1) ? 'หลัก <i class="fa-solid fa-star fa-sm"></i>' : 'เพิ่มเติม' ?>
                        </h6>
                        <p class="mb-0 text-muted small">
                            <?= htmlspecialchars($row['name']) ?><br>
                            <?= htmlspecialchars($row['contact']) ?><br>
                            <?= htmlspecialchars($row['address']) ?>,
                            ต.<?= htmlspecialchars($row['sub_district']) ?>,
                            อ.<?= htmlspecialchars($row['district']) ?>,
                            จ.<?= htmlspecialchars($row['province']) ?>,
                            <?= htmlspecialchars($row['postal_code']) ?>
                        </p>
                    </div>
                </div>
                <div class="ms-3">
                    <a href="javascript:void(0)" class="edit-address"
                        data-id="<?= $row['id'] ?>"
                        data-name="<?= htmlspecialchars($row['name']) ?>"
                        data-contact="<?= htmlspecialchars($row['contact']) ?>"
                        data-address="<?= htmlspecialchars($row['address']) ?>"
                        data-sub_district="<?= htmlspecialchars($row['sub_district']) ?>"
                        data-district="<?= htmlspecialchars($row['district']) ?>"
                        data-province="<?= htmlspecialchars($row['province']) ?>"
                        data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>"
                        style="text-decoration: none;">
                        <i class="fa-solid fa-pencil-alt"></i> แก้ไข
                    </a>
                </div>
            </div>

        <?php endwhile; ?>
    </form>

    <form id="confirm_form_2" style="display: none;">
        <input type="hidden" name="address_id" id="address_id">
        <input type="hidden" name="customer_id" value="<?= $_settings->userdata('id') ?>">
        <div class="form-group mb-3">
            <label for="name" class="control-label">ชื่อ-นามสกุล<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name" required>
        </div>
        <div class="form-group mb-3">
            <label for="contact" class="control-label">เบอร์โทรศัพท์<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="contact" id="contact" required>
        </div>
        <div class="form-group mb-3">
            <label for="address" class="control-label">บ้านเลขที่, ถนน<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="address" id="address" required>
        </div>
        <div class="form-group mb-3">
            <label for="sub_district" class="control-label">ตำบล/แขวง<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="sub_district" id="sub_district" required>
        </div>
        <div class="form-group mb-3">
            <label for="district" class="control-label">อำเภอ/เขต<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="district" id="district" required>
        </div>
        <div class="form-group mb-3">
            <label for="province" class="control-label">จังหวัด<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="province" id="province" required>
        </div>
        <div class="form-group mb-3">
            <label for="postal_code" class="control-label">รหัสไปรษณีย์<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="postal_code" id="postal_code" required>
        </div>
        <div class="text-right">
            <small><a href="javascript:void(0)" id="back_to_address_list">กลับไปหน้ารายการสมุดที่อยู่</a></small>
        </div>
    </form>
</div>

<script>
    function selectAddress(element) {
        // Deselect all address options
        const addressOptions = document.querySelectorAll('.address-option');
        addressOptions.forEach(option => {
            option.classList.remove('selected');
            option.querySelector('.address-radio').checked = false;
        });

        // Select the clicked address option
        element.classList.add('selected');
        element.querySelector('.address-radio').checked = true;
    }


    $(document).ready(function() {
        // --- ฟังก์ชันสำหรับสลับฟอร์ม ---
        function showAddressListForm() {
            $('#confirm_form_1').show();
            $('#confirm_form_2').hide();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_confirm_form_1').show();
            $('.modal-footer #btn_confirm_form_2').hide();
            $('#uni_modal .modal-title').text('เลือกที่อยู่');
        }

        function showEditAddressForm() {
            $('#confirm_form_1').hide();
            $('#confirm_form_2').show();
            // อัพเดทปุ่มใน Modal Footer
            $('.modal-footer #btn_confirm_form_1').hide();
            $('.modal-footer #btn_confirm_form_2').show();
            $('#uni_modal .modal-title').text('แก้ไขที่อยู่');
        }

        // --- Event Handlers ---
        // ใช้ class selector '.edit-address' แทน id selector
        $('.edit-address').click(function(e) {
            e.preventDefault();
            showEditAddressForm();
            var _this = $(this);
            // ดึงข้อมูลจาก data attributes มาใส่ในฟอร์ม
            $('#address_id').val(_this.data('id'));
            $('#name').val(_this.data('name'));
            $('#contact').val(_this.data('contact'));
            $('#address').val(_this.data('address'));
            $('#sub_district').val(_this.data('sub_district'));
            $('#district').val(_this.data('district'));
            $('#province').val(_this.data('province'));
            $('#postal_code').val(_this.data('postal_code'));
        });

        $('#back_to_address_list').click(function(e) {
            e.preventDefault();
            showAddressListForm();
        });

        // --- Form Submission ---

        // จัดการการ submit ของ Form 2 (บันทึกการแก้ไขที่อยู่)
        // ฟังก์ชันสำหรับบันทึกการเลือกที่อยู่หลัก
        $('#confirm_form_1').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            var address_id = $('input[name="address_id"]:checked').val(); // ดึงค่า address_id ที่ถูกเลือก

            // ถ้าเลือกที่อยู่หลัก (is_primary = 1) จะส่งค่าไปที่เซิร์ฟเวอร์
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=save_address",
                method: 'POST',
                data: {
                    address_id: address_id,
                    is_primary: 1 // ตั้งเป็นที่อยู่หลัก
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload(); // รีโหลดหน้าเมื่อสำเร็จ
                    } else {
                        Swal.fire(
                            'เกิดข้อผิดพลาด!',
                            resp.msg || 'เกิดข้อผิดพลาดในการตั้งที่อยู่หลัก.',
                            'error'
                        );
                    }
                },
                error: function(err) {
                    console.log(err);
                    Swal.fire(
                        'เกิดข้อผิดพลาด!',
                        'เกิดข้อผิดพลาดในการติดต่อกับเซิร์ฟเวอร์.',
                        'error'
                    );
                }
            });
        });


        $('#confirm_form_2').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=save_address",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert("An error occurred");
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload();
                    } else {
                        alert(resp.msg || "An error occurred");
                    }
                    end_loader();
                }
            });
        });
    });
</script>