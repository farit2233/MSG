<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขโปรโมชั่น" : "สร้างโปรโมชั่นใหม่" ?></div>
    </div>
    <form action="" id="promotion-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="form-group">
                <label>ชื่อโปรโมชั่น <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" required value="<?= isset($name) ? $name : '' ?>">
            </div>
            <div class="form-group">
                <label>รายละเอียด</label>
                <textarea name="description" class="form-control" rows="3"><?= isset($description) ? $description : '' ?></textarea>
            </div>
            <div class="form-group">
                <label>ประเภทโปรโมชั่น</label>
                <select name="type" class="form-control" required>
                    <option value="fixed" <?= (isset($type) && $type == 'fixed') ? 'selected' : '' ?>>ลดราคาคงที่ (บาท)</option>
                    <option value="percent" <?= (isset($type) && $type == 'percent') ? 'selected' : '' ?>>ลดเป็นเปอร์เซ็นต์ (%)</option>
                    <option value="free_shipping" <?= (isset($type) && $type == 'free_shipping') ? 'selected' : '' ?>>ส่งฟรี</option>
                    <option value="code" <?= (isset($type) && $type == 'code') ? 'selected' : '' ?>>ส่วนลดผ่านโค้ด</option>
                </select>
            </div>
            <div class="form-group">
                <label>มูลค่าส่วนลด</label>
                <input type="number" name="discount_value" step="0.01" min="0" class="form-control" value="<?= isset($discount_value) ? $discount_value : '' ?>">
            </div>
            <div class="form-group">
                <label>ยอดสั่งซื้อขั้นต่ำ</label>
                <input type="number" name="minimum_order" step="0.01" min="0" class="form-control" value="<?= isset($minimum_order) ? $minimum_order : '' ?>">
            </div>
            <div class="form-group">
                <label>โค้ดโปรโมชั่น (ถ้ามี)</label>
                <input type="text" name="promo_code" class="form-control" value="<?= isset($promo_code) ? $promo_code : '' ?>">
            </div>
            <div class="form-group">
                <label>เริ่มวันที่</label>
                <input type="datetime-local" name="start_date" class="form-control" required value="<?= isset($start_date) ? date('Y-m-d\TH:i', strtotime($start_date)) : '' ?>">
            </div>
            <div class="form-group">
                <label>สิ้นสุดวันที่</label>
                <input type="datetime-local" name="end_date" class="form-control" required value="<?= isset($end_date) ? date('Y-m-d\TH:i', strtotime($end_date)) : '' ?>">
            </div>
            <div class="form-group">
                <label>สถานะ</label><br>
                <input type="hidden" name="status" value="0">
                <input type="checkbox" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>> ใช้งานโปรโมชั่น
            </div>
        </div>
    </form>
    <div class="card-footer text-center">
        <button class="btn btn-success btn-sm btn-flat" form="promotion-form"><i class="fa fa-save"></i> บันทึก</button>
        <a class="btn btn-danger btn-sm btn-flat" href="./?page=promotions"><i class="fa fa-times"></i> ยกเลิก</a>
    </div>
</section>

<script>
    $(document).ready(function() {
        $('#promotion-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion",
                data: new FormData(this),
                method: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.replace('./?page=promotions/manage_promotion&id=' + resp.id);
                    } else {
                        alert_toast(resp.msg || "ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>