<?php
$promotion_category_result = $conn->query("SELECT id, name FROM promotion_category WHERE status = 1 ORDER BY id ASC");

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions_list WHERE id = '{$_GET['id']}'");
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
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลโปรโมชั่น</div>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>ชื่อโปรโมชั่น <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
                        </div>
                        <div class="col-md-6">
                            <label for="promotion_category_id" class="control-label">เลือกประเภทสินค้า</label>
                            <select name="promotion_category_id" id="promotion_category_id" class="form-control rounded-0 select2" required>
                                <option value="" disabled <?= !isset($promotion_category_id) ? 'selected' : '' ?>>-- เลือกหมวดหมู่โปรโมชั่น --</option>
                                <?php
                                while ($pc_row = $promotion_category_result->fetch_assoc()) :
                                ?>
                                    <option value="<?= $pc_row['id'] ?>" <?= (isset($promotion_category_id) && $promotion_category_id == $pc_row['id']) ? 'selected' : '' ?>>
                                        <?= $pc_row['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">รายละเอียด</label>
                        <textarea rows="3" name="description" id="description" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
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

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>มูลค่าส่วนลด</label>
                            <input type="number" name="discount_value" step="0.01" min="0" class="form-control" value="<?= isset($discount_value) ? $discount_value : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label>ยอดสั่งซื้อขั้นต่ำ</label>
                            <input type="number" name="minimum_order" step="0.01" min="0" class="form-control" value="<?= isset($minimum_order) ? $minimum_order : '' ?>">
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-md-6">
                            <label>เริ่มวันที่</label>
                            <input type="datetime-local" name="start_date" class="form-control" required value="<?= isset($start_date) ? date('Y-m-d\TH:i', strtotime($start_date)) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label>สิ้นสุดวันที่</label>
                            <input type="datetime-local" name="end_date" class="form-control" required value="<?= isset($end_date) ? date('Y-m-d\TH:i', strtotime($end_date)) : '' ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">สถานะโปรโมชั่น</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">เปิด / ปิดการใช้งานโปรโมชั่น</label>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer py-1 text-center">
            <button class="btn btn-success btn-sm btn-flat" form="promotion-form"><i class="fa fa-save"></i> บันทึก</button>
            <a class="btn btn-danger btn-sm border btn-flat" href="./?page=promotions"><i class="fa fa-times"></i> ยกเลิก</a>
            <a class="btn btn-light btn-sm border btn-flat" href="./?page=promotions"><i class="fa fa-angle-left"></i> กลับ</a>
        </div>
    </form>

</section>

<script>
    $(document).ready(function() {
        // ใช้ select2 สำหรับหมวดหมู่
        $('.select2').select2({
            width: '100%'
        });

        $(document).ready(function() {
            $('#promotion-form').submit(function(e) {
                e.preventDefault();
                var _this = $(this)
                $('.err-msg').remove();
                start_loader();
                $.ajax({
                    url: _base_url_ + "classes/Master.php?f=save_promotions",
                    data: new FormData($(this)[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    dataType: 'json',
                    error: err => {
                        console.log(err)
                        alert_toast("An error occured", 'error');
                        end_loader();
                    },
                    success: function(resp) {
                        if (typeof resp == 'object' && resp.status == 'success') {
                            location.replace('./?page=promotions/manage_promotion&id=' + resp.cid)
                        } else if (resp.status == 'failed' && !!resp.msg) {
                            var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body").scrollTop(0);
                            end_loader()
                        } else {
                            alert_toast("An error occuredไม่สามารถ", 'error');
                            end_loader();
                            console.log(resp)
                        }
                    }
                })
            })
        })
    });
</script>