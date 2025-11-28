<?php
// ดึงข้อมูลรายชื่อธนาคารจาก bank_providers เพื่อมาทำ Dropdown
$bank_providers = $conn->query("SELECT * FROM `bank_providers` WHERE is_active = 1 ORDER BY name_th ASC");

// ตรวจสอบว่ามี ID ส่งมาหรือไม่ (กรณีแก้ไข)
if (isset($_GET['id']) && $_GET['id'] > 0) {
    // ดึงข้อมูลจาก bank_system
    $qry = $conn->query("SELECT * from `bank_system` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    section {
        font-size: 16px;
    }

    /* ปรับแต่งสี Select2 ให้เข้าธีม */
    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
        border-radius: 0;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
</style>

<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขบัญชีธนาคาร" : "เพิ่มบัญชีธนาคารใหม่" ?></div>
    </div>
    <form action="" id="bank-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลบัญชี</div>
                </div>
                <div class="card-body">

                    <div class="form-group">
                        <label for="bank_provider_id" class="control-label">เลือกธนาคาร <span class="text-danger">*</span></label>
                        <select name="bank_provider_id" id="bank_provider_id" class="form-control rounded-0 select2" required>
                            <option value="" disabled <?= !isset($bank_name) ? 'selected' : '' ?>>-- กรุณาเลือกธนาคาร --</option>
                            <?php
                            if ($bank_providers->num_rows > 0):
                                while ($row = $bank_providers->fetch_assoc()) :
                                    // กำหนดค่าที่จะใช้แสดงและบันทึกเป็น "ภาษาไทย" เท่านั้น
                                    // ถ้าไม่มีชื่อไทย ให้ใช้ชื่ออังกฤษแทน (กันเหนียว)
                                    $thai_name = !empty($row['name_th']) ? $row['name_th'] : $row['name'];

                                    // ตรวจสอบค่า Selected (เทียบกับชื่อไทยที่บันทึกไว้)
                                    $selected = (isset($bank_name) && $bank_name == $thai_name) ? 'selected' : '';
                            ?>
                                    <option value="<?= $row['id'] ?>"
                                        data-name="<?= $thai_name ?>"
                                        data-img="<?= $row['image_path'] ?>"
                                        <?= $selected ?>>
                                        <?= $thai_name ?>
                                    </option>
                            <?php
                                endwhile;
                            endif;
                            ?>
                        </select>

                        <input type="hidden" name="bank_name" id="bank_name" value="<?php echo isset($bank_name) ? $bank_name : ''; ?>">
                        <input type="hidden" name="image_path" id="image_path" value="<?php echo isset($image_path) ? $image_path : ''; ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label>เลขที่บัญชี <span class="text-danger">*</span></label>
                            <input type="text" name="bank_number" id="bank_number" class="form-control rounded-0" value="<?php echo isset($bank_number) ? $bank_number : ''; ?>" placeholder="xxx-x-xxxxx-x" required />
                        </div>

                        <div class="col-md-6 form-group">
                            <label>ชื่อบัญชี (ชื่อบริษัท / ชื่อบุคคล) <span class="text-danger">*</span></label>
                            <input type="text" name="bank_company" id="bank_company" class="form-control rounded-0" value="<?php echo isset($bank_company) ? $bank_company : ''; ?>" required />
                        </div>
                    </div>

                </div>
            </div>

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">การแสดงผลและสถานะ</div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="control-label">แสดงให้ลูกค้าเห็น (Visible)</label>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="is_visible" value="0"> <input type="checkbox" class="custom-control-input" id="is_visible" name="is_visible" value="1" <?= (isset($is_visible) && $is_visible == 1) || !isset($id) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="is_visible">เปิด / ปิด การแสดงผลบนหน้าเว็บ</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="" class="control-label">สถานะการใช้งาน (Active)</label>
                                <div class="custom-control custom-switch">
                                    <input type="hidden" name="is_active" value="0"> <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= (isset($is_active) && $is_active == 1) || !isset($id) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="is_active">เปิด / ปิด การใช้งานระบบ</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="card-footer py-1 text-center">
        <a class="btn btn-light btn-sm border btn-flat" href="javascript:void(0)" id="backBtn"><i class="fa fa-angle-left"></i> กลับ</a>
        <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
        <button class="btn btn-success btn-sm btn-flat" form="bank-form"><i class="fa fa-save"></i> บันทึก</button>
    </div>
</section>

<script>
    $(document).ready(function() {
        // ฟังก์ชันจัดรูปแบบการแสดงผลใน Dropdown
        function formatState(opt) {
            if (!opt.id) {
                return opt.text;
            }

            var optimage = $(opt.element).attr('data-img');
            if (!optimage) {
                return opt.text;
            } else {
                // สร้าง HTML แสดงรูปคู่กับชื่อ
                var $opt = $(
                    '<span><img src="' + optimage + '" class="img-fluid" style="width: 25px; height: 25px; margin-right:10px; border-radius:50%; border:1px solid #ddd;" /> ' + opt.text + '</span>'
                );
                return $opt;
            }
        };

        // Init Select2 พร้อมเรียกใช้ฟังก์ชัน formatState
        $('.select2').select2({
            width: '100%',
            placeholder: "ค้นหา หรือ เลือกธนาคาร",
            templateResult: formatState, // ใช้ตอนกด Dropdown ลงมา
            templateSelection: formatState // ใช้ตอนเลือกแล้วโชว์ในช่อง
        });

        // เมื่อเลือกธนาคาร อัปเดต Hidden Fields
        $('#bank_provider_id').change(function() {
            var option = $('option:selected', this);
            var name = option.attr('data-name'); // จะได้ค่า "ชื่อไทย (ชื่ออังกฤษ)"
            var img = option.attr('data-img');

            $('#bank_name').val(name);
            $('#image_path').val(img);
        });

        // เมื่อมีการเลือกธนาคาร ให้ดึงชื่อและรูปภาพมาใส่ใน Hidden Input อัตโนมัติ
        $('#bank_provider_id').change(function() {
            var option = $('option:selected', this);
            var name = option.attr('data-name');
            var img = option.attr('data-img');

            $('#bank_name').val(name);
            $('#image_path').val(img);
        });

        // ตรวจสอบการเปลี่ยนแปลงข้อมูล
        let formChanged = false;
        $('#bank-form input, #bank-form select').on('change input', function() {
            formChanged = true;
        });

        // ปุ่มยกเลิก
        $('#cancelBtn').click(function() {
            if (formChanged) {
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'รีเฟรชหน้า',
                    cancelButtonText: 'ยกเลิก'
                }).then((result) => {
                    if (result.isConfirmed) location.reload();
                });
            } else {
                location.reload();
            }
        });

        // ปุ่มกลับ
        $('#backBtn').click(function() {
            // เปลี่ยน Link ตรงนี้ให้ตรงกับหน้ารายการ Bank ของคุณ
            window.location.href = './?page=bank_setting';
        });

        // Submit Form
        $('#bank-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();

            $.ajax({
                // แก้ไข URL ให้ตรงกับไฟล์ Class PHP ของคุณ (เช่น save_bank)
                url: _base_url_ + "classes/Master.php?f=save_bank",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        // บันทึกสำเร็จ ให้กลับไปหน้ารายการ
                        location.replace('./?page=bank_setting');
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg);
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body").scrollTop(0);
                    } else {
                        alert_toast("An error occurred", 'error');
                        console.log(resp);
                    }
                    end_loader();
                },
                error: err => {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            })
        })
    });
</script>