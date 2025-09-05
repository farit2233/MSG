<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    .contact-label {
        font-size: 17px !important;
    }

    .contact-input {
        font-size: 16px !important;
    }

    section {
        font-size: 16px;
    }

    .swal2-confirm {
        background-color: #28a745 !important;
        /* สีเขียว */
        border-color: #28a745 !important;
        /* สีเขียว */
        color: white !important;
        /* สีตัวอักษรเป็นขาว */
    }

    .swal2-confirm:hover {
        background-color: #218838 !important;
        /* สีเขียวเข้ม */
        border-color: #1e7e34 !important;
        /* สีเขียวเข้ม */
    }
</style>
<section class="card card-outline rounded-0 card-dark">
    <div class="card-header">
        <div class="card-title">หน้าติดต่อ</div>
    </div>
    <div class="card-body">
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <div class="card-title" style="font-size: 18px !important;">รายละเอียดหน้าติดต่อ</div>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form action="" id="system-frm">
                        <div id="msg" class="form-group"></div>
                        <div class="form-group">
                            <label for="address" class="control-label contact-label">คําโปรย</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="Synopsis" id="Synopsis"><?php echo $_settings->info('Synopsis') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="control-label contact-label">โทรศัพท์</label>
                            <input type="text" class="form-control form-control-sm rounded-0 contact-input" name="phone" id="phone" value="<?php echo $_settings->info('phone') ?>">
                        </div>
                        <div class="form-group">
                            <label for="mobile" class="control-label contact-label">เบอร์โทร</label>
                            <input type="text" class="form-control form-control-sm rounded-0 contact-input" name="mobile" id="mobile" value="<?php echo $_settings->info('mobile') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label contact-label">Email</label>
                            <input type="email" class="form-control form-control-sm rounded-0 contact-input" name="email" id="email" value="<?php echo $_settings->info('email') ?>">
                        </div>
                        <div class="form-group">
                            <label for="address" class="control-label contact-label">ที่อยู่</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="address" id="address"><?php echo $_settings->info('address') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="office_hours" class="control-label contact-label">วันเวลาเปิดทำการ</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="office_hours" id="office_hours"><?php echo $_settings->info('office_hours') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="Social Media" class="control-label contact-label contact-input">Social Media</label>
                            <br>
                            <label for="address" class="control-label contact-label">Line</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="Line" id="Line"><?php echo $_settings->info('Line') ?></textarea>
                            <label for="address" class="control-label contact-label">Facebook</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="Facebook" id="Facebook"><?php echo $_settings->info('Facebook') ?></textarea>
                            <label for="address" class="control-label contact-label">TikTok</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 contact-input" name="TikTok" id="TikTok"><?php echo $_settings->info('TikTok') ?></textarea>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
        <button class="btn btn-success btn-sm btn-flat" form="system-frm"><i class="fa fa-save"></i> บันทึก</button>
    </div>
</section>
<script>
    $(document).ready(function() {
        let formChanged = false;

        // ตรวจสอบการเปลี่ยนแปลงของฟอร์ม
        $('#system-frm input, #system-frm textarea').on('input', function() {
            formChanged = true;
        });

        // เมื่อกดปุ่ม "ยกเลิก"
        $('#cancelBtn').click(function() {
            if (formChanged) {
                // ถ้ามีการเปลี่ยนแปลงข้อมูล
                Swal.fire({
                    title: 'คุณแน่ใจหรือไม่?',
                    text: "การเปลี่ยนแปลงจะหายไปทั้งหมด และหน้าเพจจะรีเฟรช",
                    icon: 'warning',
                    showCancelButton: true,
                    cancelButtonText: '<i class="fa fa-times"></i> ยกเลิก',
                    confirmButtonText: 'ยืนยัน <i class="fa fa-check"></i>',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // รีเฟรชหน้า
                        location.reload();
                    }
                });
            } else {
                // ถ้าไม่มีการเปลี่ยนแปลงก็รีเฟรชหน้า
                location.reload();
            }
        });
    });
</script>