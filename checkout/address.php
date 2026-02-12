<?php
require_once('../config.php');

// ตรวจสอบว่าเรามีการส่ง ID มาไหม
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

// --- ส่วนที่เพิ่ม: ดึงข้อมูลจังหวัดมาเตรียมไว้ ---
$province_option = "";
if (isset($conn)) {
    $p_qry = $conn->query("SELECT * FROM provinces ORDER BY name_th ASC");
    while ($row = $p_qry->fetch_assoc()) {
        $province_option .= '<option value="' . $row['id'] . '">' . $row['name_th'] . '</option>';
    }
}
?>
<style>
    .text-size-input {
        font-size: 16px;
    }

    section {
        font-size: 16px;
    }

    .address {
        max-height: 60vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 10px;
    }

    .address::-webkit-scrollbar {
        width: 8px;
    }

    .address::-webkit-scrollbar-thumb {
        background-color: #ccc;
        border-radius: 4px;
    }

    .address::-webkit-scrollbar-track {
        background-color: #f5f5f5;
    }

    /* ปรับ CSS ให้รองรับ Select2 */
    .address input,
    .address select,
    .select2-container .select2-selection--single {
        border-radius: 13px !important;
        font-size: 16px;
        height: auto !important;
        padding: 0.3rem 0.5rem;
    }

    .select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
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
        border: 2px solid #ef3624;
        background-color: #fff5f0;
    }

    .address-option .checkmark {
        position: absolute;
        right: 15px;
        top: 15px;
        color: #ef3624;
        display: none;
    }

    .address-option.selected .checkmark {
        display: inline;
    }
</style>

<div class="container-fluid address">
    <form id="confirm_form_1">
        <?php
        $addresses = $conn->query("SELECT * FROM customer_addresses WHERE customer_id = '{$_settings->userdata('id')}' ORDER BY is_primary DESC, id ASC");
        if ($addresses->num_rows > 0) {
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
                                <?php
                                if (!empty($row['province'])) {
                                    echo "<br>จ." . htmlspecialchars($row['province']);
                                }
                                ?>
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
                            style="color: black; text-decoration: none;">
                            <i class="fa-solid fa-pencil-alt"></i> แก้ไข
                        </a>
                    </div>
                </div>

        <?php
            endwhile;
        } else {
            echo '<div class="text-center text-muted">ยังไม่มีที่อยู่ที่บันทึกไว้</div>';
        }
        ?>
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
            <label for="address" class="control-label">บ้านเลขที่, อาคาร, ถนน<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="address" id="address_details" required>
        </div>
        <div class="form-group mb-3">
            <label for="province" class="control-label">จังหวัด<span class="text-danger">*</span></label>
            <select class="form-control select2" name="province_id" id="province" required style="width: 100%;">
                <option value="" selected disabled>กรุณาเลือกจังหวัด</option>
                <?php echo $province_option; ?>
            </select>
            <input type="hidden" name="province" id="province_name">
        </div>

        <div class="form-group mb-3">
            <label for="amphure" class="control-label">อำเภอ/เขต<span class="text-danger">*</span></label>
            <select class="form-control select2" name="district_id" id="amphure" required disabled style="width: 100%;">
                <option value="" selected disabled>กรุณาเลือกอำเภอ / เขต</option>
            </select>
            <input type="hidden" name="district" id="district_name">
        </div>

        <div class="form-group mb-3">
            <label for="district" class="control-label">ตำบล/แขวง<span class="text-danger">*</span></label>
            <select class="form-control select2" name="sub_district_id" id="district" required disabled style="width: 100%;">
                <option value="" selected disabled>กรุณาเลือกตำบล / แขวง</option>
            </select>
            <input type="hidden" name="sub_district" id="sub_district_name">
        </div>

        <div class="form-group mb-3">
            <label for="postal_code" class="control-label">รหัสไปรษณีย์<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="postal_code" id="postal_code" readonly style="background-color: #e9ecef;">
        </div>

        <div class="text-right">
            <small><a href="javascript:void(0)" id="back_to_address_list">กลับไปหน้ารายการสมุดที่อยู่</a></small>
        </div>
    </form>
</div>

<script>
    function selectAddress(element) {
        const addressOptions = document.querySelectorAll('.address-option');
        addressOptions.forEach(option => {
            option.classList.remove('selected');
            option.querySelector('.address-radio').checked = false;
        });
        element.classList.add('selected');
        element.querySelector('.address-radio').checked = true;
    }

    $(document).ready(function() {
        // Init Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%',
            dropdownParent: $('#modal_confirm'), // ปรับการแสดงผลให้อยู่ใน modal
            dropdownAutoWidth: true, // ให้ความกว้างของ dropdown ตามเนื้อหาของมัน
            width: 'auto' // ปรับความกว้างเป็นอัตโนมัติ
        });


        // Helper function to set Select2 by Text
        function setSelect2ByText(selector, text) {
            $(selector + ' option').each(function() {
                // เปรียบเทียบ Text แบบตัดช่องว่าง
                if ($(this).text().trim() == text.trim()) {
                    $(selector).val($(this).val()).trigger('change');
                    return false;
                }
            });
        }

        // --- Step 1: Province Change ---
        $('#province').change(function() {
            var id = $(this).val();
            var name = $(this).find(':selected').text();
            $('#province_name').val(name);

            // Reset children
            $('#amphure').empty().append('<option value="" selected disabled>กำลังโหลด...</option>').prop('disabled', true);
            $('#district').empty().append('<option value="" selected disabled>กรุณาเลือกตำบล / แขวง</option>').prop('disabled', true);
            $('#postal_code').val('');

            if (id) {
                $.ajax({
                    url: _base_url_ + '/inc/get_address_step.php',
                    method: 'POST',
                    data: {
                        id: id,
                        function: 'provinces'
                    },
                    success: function(data) {
                        $('#amphure').html(data).prop('disabled', false);
                    },
                    error: function() {
                        // Fallback path if base url fails
                        $.ajax({
                            url: '/inc/get_address_step.php',
                            method: 'POST',
                            data: {
                                id: id,
                                function: 'provinces'
                            },
                            success: function(data) {
                                $('#amphure').html(data).prop('disabled', false);
                            }
                        });
                    }
                });
            }
        });

        // --- Step 2: Amphure Change ---
        $('#amphure').change(function() {
            var id = $(this).val();
            var name = $(this).find(':selected').text();
            $('#district_name').val(name);

            // Reset children
            $('#district').empty().append('<option value="" selected disabled>กำลังโหลด...</option>').prop('disabled', true);
            $('#postal_code').val('');

            if (id) {
                $.ajax({
                    url: _base_url_ + '/inc/get_address_step.php',
                    method: 'POST',
                    data: {
                        id: id,
                        function: 'amphures'
                    },
                    success: function(data) {
                        $('#district').html(data).prop('disabled', false);
                    },
                    error: function() {
                        $.ajax({
                            url: '/inc/get_address_step.php',
                            method: 'POST',
                            data: {
                                id: id,
                                function: 'amphures'
                            },
                            success: function(data) {
                                $('#district').html(data).prop('disabled', false);
                            }
                        });
                    }
                });
            }
        });

        // --- Step 3: District (Tambon) Change ---
        $('#district').change(function() {
            var name = $(this).find(':selected').text();
            var zip = $(this).find(':selected').data('zip');
            $('#sub_district_name').val(name);
            $('#postal_code').val(zip);
        });

        // UI Toggles
        function showAddressListForm() {
            $('#confirm_form_1').show();
            $('#confirm_form_2').hide();
            $('.modal-footer #btn_confirm_form_1').show();
            $('.modal-footer #btn_confirm_form_2').hide();
            $('#uni_modal .modal-title').text('เลือกที่อยู่');
        }

        function showEditAddressForm() {
            $('#confirm_form_1').hide();
            $('#confirm_form_2').show();
            $('.modal-footer #btn_confirm_form_1').hide();
            $('.modal-footer #btn_confirm_form_2').show();
            $('#uni_modal .modal-title').text('แก้ไขที่อยู่');
        }

        // Edit Click Handler
        $('.edit-address').click(function(e) {
            e.preventDefault();
            showEditAddressForm();
            var _this = $(this);

            // Basic Fields
            $('#address_id').val(_this.data('id'));
            $('#name').val(_this.data('name'));
            $('#contact').val(_this.data('contact'));
            $('#address_details').val(_this.data('address')); // Note: ID changed to address_details

            // Cascading Selects Logic
            var targetProvince = _this.data('province');
            var targetAmphoe = _this.data('district'); // In DB logic: district = Amphoe
            var targetTambon = _this.data('sub_district'); // In DB logic: sub_district = Tambon
            var targetZip = _this.data('postal_code');

            // 1. Set Province
            setSelect2ByText('#province', targetProvince);

            // 2. Wait for Amphures to load, then Set Amphure
            setTimeout(function() {
                setSelect2ByText('#amphure', targetAmphoe);

                // 3. Wait for Tambons to load, then Set Tambon & Zip
                setTimeout(function() {
                    setSelect2ByText('#district', targetTambon);
                    $('#postal_code').val(targetZip);
                }, 500); // Delay for Tambon load
            }, 500); // Delay for Amphure load
        });

        $('#back_to_address_list').click(function(e) {
            e.preventDefault();
            showAddressListForm();
        });

        // Save Address Forms
        $('#confirm_form_1').submit(function(e) {
            e.preventDefault();
            var address_id = $('input[name="address_id"]:checked').val();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=save_address",
                method: 'POST',
                data: {
                    address_id: address_id,
                    is_primary: 1
                },
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') location.reload();
                    else Swal.fire('Error', resp.msg || 'เกิดข้อผิดพลาด', 'error');
                },
                error: function() {
                    Swal.fire('Error', 'Connection Failed', 'error');
                }
            });
        });

        $('#confirm_form_2').submit(function(e) {
            e.preventDefault();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Users.php?f=save_address",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') location.reload();
                    else alert(resp.msg || "An error occurred");
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    alert("An error occurred");
                    end_loader();
                }
            });
        });

        // บังคับให้เบอร์โทรพิมพ์ได้แค่ตัวเลข
        $('#contact').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });
    });
</script>