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
</style>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="py-4">
                    <div class="card-body">
                        <div class="container-fluid">
                            <div id="address-list">
                                <div class="profile-section-title-with-line ">
                                    <h4>ที่อยู่ของฉัน</h4>
                                </div>
                                <div class="d-flex">
                                    <a href="#" class="ml-auto" id="address_option">
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
                                                <div class="card p-3 border rounded <?= ($row['is_primary'] == 1) ? 'border-primary' : '' ?>">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-0">
                                                                <strong>ที่อยู่ <?= ($row['is_primary'] == 1) ? 'หลัก' : 'เพิ่มเติม' ?></strong>
                                                            </h6>
                                                            <p class="mb-0 text-muted">
                                                                <?= htmlspecialchars($row['name']) ?>,
                                                                <?= htmlspecialchars($row['address']) ?>,
                                                                <?= htmlspecialchars($row['sub_district']) ?>,
                                                                <?= htmlspecialchars($row['district']) ?>,
                                                                <?= htmlspecialchars($row['province']) ?>
                                                                <?= htmlspecialchars($row['postal_code']) ?>
                                                            </p>
                                                        </div>
                                                        <div class="ms-3 d-flex flex-column align-items-end">
                                                            <a href="#" class="edit-address-btn mb-1 text-sm"
                                                                data-id="<?= $row['id'] ?>"
                                                                data-name="<?= htmlspecialchars($row['name']) ?>"
                                                                data-address="<?= htmlspecialchars($row['address']) ?>"
                                                                data-sub_district="<?= htmlspecialchars($row['sub_district']) ?>"
                                                                data-district="<?= htmlspecialchars($row['district']) ?>"
                                                                data-province="<?= htmlspecialchars($row['province']) ?>"
                                                                data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>"
                                                                style="text-decoration: none;"> แก้ไข
                                                            </a>
                                                            <a href="#" class="set-primary-btn mb-1 text-sm" data-id="<?= $row['id'] ?>"
                                                                <?= ($row['is_primary'] == 1) ? 'style="pointer-events: none; color: #6c757d;"' : '' ?>
                                                                style="text-decoration: none;">ตั้งเป็นที่อยู่หลัก
                                                            </a>

                                                            <a href="#" class="delete-address-btn mb-1 text-sm" data-id="<?= $row['id'] ?>"
                                                                <?= ($row['is_primary'] == 1) ? 'style="pointer-events: none; color: #6c757d;"' : '' ?>
                                                                style="text-decoration: none;">ลบ
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
                                            <p>ยังไม่มีที่อยู่บันทึกไว้</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <form id="address-form" method="post" style="display: none;">
                                <div class="profile-section-title-with-line ">
                                    <h4 id="form-title">เพิ่มที่อยู่ใหม่</h4>
                                </div>
                                <input type="hidden" name="address_id" id="address_id">
                                <input type="hidden" name="customer_id" value="<?= isset($id) ? $id : '' ?>">
                                <div class="d-flex">
                                    <a href="#" class="ml-auto" id="cancel-edit">
                                        <i class="fa-solid fa-xmark"></i> กลับ
                                    </a>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="name" class="control-label">ชื่อ นามสกุล</label>
                                            <input type="text" class="form-control" name="name" id="name" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="control-label">บ้านเลขที่, ถนน</label>
                                            <input type="text" class="form-control" name="address" id="address" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="sub_district" class="control-label">ตำบล/แขวง</label>
                                            <input type="text" class="form-control" name="sub_district" id="sub_district" required>
                                        </div>

                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="form-group">
                                            <label for="district" class="control-label">อำเภอ/เขต</label>
                                            <input type="text" class="form-control" name="district" id="district" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="province" class="control-label">จังหวัด</label>
                                            <input type="text" class="form-control" name="province" id="province" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="postal_code" class="control-label">รหัสไปรษณีย์</label>
                                            <input type="text" class="form-control" name="postal_code" id="postal_code" required>
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

        function resetAddressForm() {
            $('#address-form')[0].reset();
            $('#address_id').val('');
            $('#form-title').text('เพิ่มที่อยู่ใหม่');
        }

        $('#address_option').click(function(e) {
            e.preventDefault();
            resetAddressForm();
            $('#address-list').hide();
            $('#address-form').show();
        });

        // ================== START: การเปลี่ยนแปลงใน JavaScript ==================
        $('.edit-address-btn').click(function(e) {
            e.preventDefault();
            var _this = $(this); // อ้างอิงถึงปุ่ม 'แก้ไข' ที่ถูกคลิก

            // 1. เปลี่ยนหัวข้อฟอร์ม
            $('#form-title').text('แก้ไขที่อยู่');

            // 2. ดึงข้อมูลจาก data attributes ของปุ่มมาใส่ในฟอร์ม
            $('#address_id').val(_this.data('id'));
            $('#name').val(_this.data('name'));
            $('#address').val(_this.data('address'));
            $('#sub_district').val(_this.data('sub_district'));
            $('#district').val(_this.data('district'));
            $('#province').val(_this.data('province'));
            $('#postal_code').val(_this.data('postal_code'));

            // 3. ซ่อนรายการที่อยู่และแสดงฟอร์ม
            $('#address-list').hide();
            $('#address-form').show();
        });
        // =================== END: การเปลี่ยนแปลงใน JavaScript ===================

        $('#cancel-edit').click(function(e) {
            e.preventDefault();
            $('#address-form').hide();
            $('#address-list').show();
        });

        $('#address-form').submit(function(e) {
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