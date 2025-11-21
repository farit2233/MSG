<?php
// --- PHP Block (No changes needed) ---
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

$province_option = "";
if (isset($conn)) {
    // ดึงข้อมูลจากตาราง provinces (จังหวัด)
    $p_qry = $conn->query("SELECT * FROM provinces ORDER BY name_th ASC");
    while ($row = $p_qry->fetch_assoc()) {
        $province_option .= '<option value="' . $row['id'] . '">' . $row['name_th'] . '</option>';
    }
}
?>

<style>
    /* CSS ไม่มีการเปลี่ยนแปลง */
    #address_option {
        border: none;
        background: transparent;
        padding: 10px 15px;
        font-size: 16px;
    }

    #address_option:focus {
        outline: none;
        text-decoration: underline !important;
    }

    .border-msg {
        border-color: #f57421 !important;
        border-width: 2px;
        border-style: solid;
        box-shadow: none !important;
        border-radius: 13px;
    }

    .card-address a {
        color: black;
        text-decoration: none;
    }

    .card-address a:hover {
        text-decoration: underline;
        line-height: normal;
    }

    .card-address {
        border-radius: 13px;
    }
</style>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="py-4">
                    <div class="card-body card-address">
                        <div class="container-fluid">
                            <div id="address-list">
                                <div class="profile-section-title-with-line ">
                                    <h4>ที่อยู่ของฉัน</h4>
                                    <p>จัดการ และเพิ่มที่อยู่สำหรับจัดส่ง</p>
                                </div>
                                <div class="d-flex">
                                    <a href="#" class="ml-auto clickable-text-btn" id="address_option">
                                        <i class="fa-solid fa-plus"></i> เพิ่มที่อยู่ใหม่
                                    </a>
                                </div>

                                <div class="row mt-3">
                                    <?php
                                    $addresses_qry = $conn->query("SELECT * FROM `customer_addresses` WHERE customer_id = '{$id}' ORDER BY is_primary DESC, id ASC");
                                    if ($addresses_qry->num_rows > 0):
                                        while ($row = $addresses_qry->fetch_assoc()):
                                    ?>
                                            <div class="col-12 mb-3">
                                                <div class="card p-3 card-address <?= ($row['is_primary'] == 1) ? 'border-msg' : '' ?>">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">
                                                                ที่อยู่ <?= ($row['is_primary'] == 1) ? 'หลัก' : 'เพิ่มเติม' ?>
                                                            </h6>
                                                            <p class="mb-0 text-muted">
                                                                <?= !empty($row['name']) ? htmlspecialchars($row['name']) : 'ไม่พบชื่อ' ?><br>
                                                                <?= !empty($row['contact']) ? htmlspecialchars($row['contact']) : 'ไม่พบเบอร์โทรศัพท์' ?><br>
                                                                <?= !empty($row['address']) ? 'ที่อยู่ ' . htmlspecialchars($row['address']) . ',' : ', ไม่พบที่อยู่,' ?><br>
                                                                <?= !empty($row['sub_district']) ? 'ต.' . htmlspecialchars($row['sub_district']) . ',' : '' ?>
                                                                <?= !empty($row['district']) ? 'อ.' . htmlspecialchars($row['district']) . ',' : '' ?>
                                                                <?= !empty($row['province']) ? 'จ.' . htmlspecialchars($row['province']) . ',' : '' ?>
                                                                <?= !empty($row['postal_code']) ? htmlspecialchars($row['postal_code']) : '' ?>
                                                            </p>
                                                        </div>
                                                        <div class="ms-3 d-flex flex-column align-items-end">
                                                            <a href="#" class="edit-address mb-1 text-sm clickable-text-btn"
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

                                                            <a href="#" class="set-primary mb-1 text-sm clickable-text-btn" data-id="<?= $row['id'] ?>"
                                                                <?= ($row['is_primary'] == 1) ? 'style="pointer-events: none; color: #6c757d;"' : '' ?>
                                                                style="text-decoration: none;">
                                                                <i class="<?= ($row['is_primary'] == 1) ? 'fa-solid' : 'fa-regular' ?> fa-star"></i> ที่อยู่หลัก
                                                            </a>

                                                            <a href="#" class="delete-address mb-1 text-sm clickable-text-btn" data-id="<?= $row['id'] ?>"
                                                                <?= ($row['is_primary'] == 1) ? 'style="pointer-events: none; color: #6c757d;"' : '' ?>
                                                                style="text-decoration: none;">
                                                                <i class="fa-solid fa-trash"></i> ลบ
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        <?php
                                        endwhile;
                                    else:
                                        ?>
                                        <div class="col-12 text-center text-muted">
                                            <p>ยังไม่มีที่อยู่ที่บันทึกไว้</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <form id="address-form" method="post" style="display: none;">
                                <div class="profile-section-title-with-line ">
                                    <h4 id="form-title">เพิ่มที่อยู่ใหม่</h4>
                                    <p>เพิ่มที่อยู่สำหรับจัดส่ง</p>
                                </div>
                                <input type="hidden" name="address_id" id="address_id">
                                <input type="hidden" name="customer_id" value="<?= isset($id) ? $id : '' ?>">

                                <div class="d-flex">
                                    <a href="#" class="ml-auto clickable-text-btn" id="cancel-edit">
                                        <i class="fa-solid fa-xmark"></i> กลับ
                                    </a>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">ชื่อ - นามสกุล <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="name" id="name" placeholder="ชื่อ-นามสกุล ผู้รับ" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="contact">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact" id="contact" placeholder="เบอร์โทรศัพท์ที่ติดต่อได้" required>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="address">บ้านเลขที่, อาคาร, ถนน <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="address" id="address" placeholder="บ้านเลขที่, หมู่, ซอย, ถนน" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="province">จังหวัด <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="province_id" id="province" required>
                                                <option value="" selected disabled>กรุณาเลือกจังหวัด</option>
                                                <?php echo $province_option; ?>
                                            </select>
                                            <input type="hidden" name="province" id="province_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="district">อำเภอ / เขต <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="district_id" id="amphure" required disabled>
                                                <option value="" selected disabled>กรุณาเลือกอำเภอ / เขต</option>
                                            </select>
                                            <input type="hidden" name="district" id="district_name">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="sub_district">ตำบล / แขวง <span class="text-danger">*</span></label>
                                            <select class="form-control select2" name="sub_district_id" id="district" required disabled>
                                                <option value="" selected disabled>กรุณาเลือกตำบล / แขวง</option>
                                            </select>
                                            <input type="hidden" name="sub_district" id="sub_district_name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="postal_code">รหัสไปรษณีย์ <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="postal_code" id="postal_code" readonly style="background-color: #e9ecef;">
                                        </div>
                                    </div>
                                </div>
                                <div class="row justify-content-center mt-3">
                                    <div class="col-md-5 col-12 text-center">
                                        <button type="submit" class="btn btn-update btn-block rounded-pill">บันทึกที่อยู่</button>
                                    </div>
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
    $(document).ready(function() {
        // ตั้งค่า Select2
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });

        // ฟังก์ชันช่วยค้นหา ID จากชื่อ (ใช้ตอน Edit)
        function setSelect2ByText(selector, text) {
            $(selector + ' option').each(function() {
                if ($(this).text() == text) {
                    $(selector).val($(this).val()).trigger('change');
                    return false;
                }
            });
        }

        // ============================================
        // 1. เลือกจังหวัด -> โหลด อำเภอ / เขต
        // ============================================
        $('#province').change(function() {
            var id = $(this).val();
            var name = $(this).find(':selected').text();
            $('#province_name').val(name);

            // แก้ไขข้อความ Reset ตรงนี้
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
                        // Fallback กรณีเรียก base_url ไม่เจอ
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

        // ============================================
        // 2. เลือก อำเภอ / เขต -> โหลด ตำบล / แขวง
        // ============================================
        $('#amphure').change(function() {
            var id = $(this).val();
            var name = $(this).find(':selected').text();
            $('#district_name').val(name);

            // แก้ไขข้อความ Reset ตรงนี้
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

        // ============================================
        // 3. เลือก ตำบล / แขวง -> ใส่รหัสไปรษณีย์
        // ============================================
        $('#district').change(function() {
            var name = $(this).find(':selected').text();
            var zip = $(this).find(':selected').data('zip');

            $('#sub_district_name').val(name);
            $('#postal_code').val(zip);
        });

        // ============================================
        // จัดการปุ่มต่างๆ
        // ============================================

        $('#contact').on('input', function() {
            this.value = this.value.replace(/\D/g, '');
        });

        function resetForm() {
            $('#address-form')[0].reset();
            $('#address_id').val('');
            $('#form-title').text('เพิ่มที่อยู่ใหม่');
            $('#province').val('').trigger('change.select2');

            // แก้ไขข้อความ Reset ตรงนี้
            $('#amphure').html('<option value="" selected disabled>กรุณาเลือกอำเภอ / เขต</option>').prop('disabled', true);
            $('#district').html('<option value="" selected disabled>กรุณาเลือกตำบล / แขวง</option>').prop('disabled', true);
        }

        $('#address_option').click(function(e) {
            e.preventDefault();
            resetForm();
            $('#address-list').hide();
            $('#address-form').show();
        });

        $('#cancel-edit').click(function(e) {
            e.preventDefault();
            $('#address-form').hide();
            $('#address-list').show();
        });

        // --- ปุ่มแก้ไข (Edit) ---
        $('.edit-address').click(function(e) {
            e.preventDefault();
            var _this = $(this);

            $('#form-title').text('แก้ไขที่อยู่');
            $('#address-list').hide();
            $('#address-form').show();

            // ใส่ข้อมูลพื้นฐาน
            $('#address_id').val(_this.data('id'));
            $('#name').val(_this.data('name'));
            $('#contact').val(_this.data('contact'));
            $('#address').val(_this.data('address'));

            // เตรียมข้อมูลที่อยู่เดิม
            var targetProvince = _this.data('province');
            var targetAmphoe = _this.data('district');
            var targetTambon = _this.data('sub_district');
            var targetZip = _this.data('postal_code');

            // เลือกค่าตามลำดับ
            setSelect2ByText('#province', targetProvince);

            setTimeout(function() {
                setSelect2ByText('#amphure', targetAmphoe);
                setTimeout(function() {
                    setSelect2ByText('#district', targetTambon);
                    $('#postal_code').val(targetZip);
                }, 500);
            }, 500);
        });

        // Submit Form
        $('#address-form').submit(function(e) {
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
                    if (resp.status == 'success') {
                        location.reload();
                    } else {
                        alert(resp.msg || "เกิดข้อผิดพลาด");
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                    end_loader();
                }
            });
        });

        // ลบที่อยู่
        $('.delete-address').click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'ยืนยันการลบ?',
                text: "คุณต้องการลบที่อยู่นี้หรือไม่?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'ลบเลย',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _base_url_ + "classes/Users.php?f=delete_address",
                        method: 'POST',
                        data: {
                            address_id: id
                        },
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.status == 'success') location.reload();
                            else Swal.fire('Error', resp.msg, 'error');
                        }
                    });
                }
            });
        });

        // ตั้งเป็นที่อยู่หลัก
        $('.set-primary').click(function(e) {
            e.preventDefault();
            var id = $(this).data('id');
            Swal.fire({
                title: 'ตั้งเป็นที่อยู่หลัก?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ตั้งเป็นที่อยู่หลัก',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#f57421',
                cancelButtonColor: '6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: _base_url_ + "classes/Users.php?f=save_address",
                        method: 'POST',
                        data: {
                            address_id: id,
                            is_primary: 1
                        },
                        dataType: 'json',
                        success: function(resp) {
                            if (resp.status == 'success') location.reload();
                            else Swal.fire('Error', resp.msg, 'error');
                        }
                    });
                }
            });
        });
    });
</script>