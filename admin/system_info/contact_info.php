<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<style>
    .head-label {
        font-size: 17px;
    }

    .text-size-input {
        font-size: 16px;
    }
</style>
<div class="card card-outline rounded-0 card-orange">
    <div class="card-header">
        <h4 class=" text-bold">หน้าติดต่อ</h4>
    </div>
    <div class="card-body">
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <h5 class="text-bold">รายละเอียดหน้าติดต่อ</h5>
            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <form action="" id="system-frm">
                        <div id="msg" class="form-group"></div>
                        <div class="form-group">
                            <label for="address" class="control-label head-label">คําโปรย</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="Synopsis" id="Synopsis"><?php echo $_settings->info('Synopsis') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="control-label head-label">โทรศัพท์</label>
                            <input type="text" class="form-control form-control-sm rounded-0 text-size-input" name="phone" id="phone" value="<?php echo $_settings->info('phone') ?>">
                        </div>
                        <div class="form-group">
                            <label for="mobile" class="control-label head-label">เบอร์โทร</label>
                            <input type="text" class="form-control form-control-sm rounded-0 text-size-input" name="mobile" id="mobile" value="<?php echo $_settings->info('mobile') ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="control-label head-label">Email</label>
                            <input type="email" class="form-control form-control-sm rounded-0 text-size-input" name="email" id="email" value="<?php echo $_settings->info('email') ?>">
                        </div>
                        <div class="form-group">
                            <label for="address" class="control-label head-label">ที่อยู่</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="address" id="address"><?php echo $_settings->info('address') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="office_hours" class="control-label head-label">วันเวลาเปิดทำการ</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="office_hours" id="office_hours"><?php echo $_settings->info('office_hours') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="Social Media" class="control-label head-label text-size-input">Social Media</label>
                            <br>
                            <label for="address" class="control-label head-label">Line</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="Line" id="Line"><?php echo $_settings->info('Line') ?></textarea>
                            <label for="address" class="control-label head-label">Facebook</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="Facebook" id="Facebook"><?php echo $_settings->info('Facebook') ?></textarea>
                            <label for="address" class="control-label head-label">TikTok</label>
                            <textarea row="3" class="form-control form-control-sm rounded-0 text-size-input" name="TikTok" id="TikTok"><?php echo $_settings->info('TikTok') ?></textarea>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-success btn-sm btn-flat" form="system-frm"><i class="fa fa-save"></i> บันทึก</button>
        <a class="btn btn-danger btn-sm border btn-flat btn-foot" href="./?page=system_info/contact_info"><i class="fa fa-times"></i> ยกเลิก</a>
        <a class="btn btn-light btn-sm border btn-flat btn-foot" href="./?page=home"><i class="fa fa-angle-left"></i> กลับสู่หน้าหลัก</a>
    </div>
</div>
<script>
</script>