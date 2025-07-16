<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `product_type` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>

<div class="card card-outline card-primary rounded-0">
    <div class="card-header">
        <h1 class="card-title"><?= isset($id) ? "แก้ไขประเภทสินค้า" : "สร้างประเภทสินค้าใหม่" ?></h1>
    </div>
    <form action="" id="product-type-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title">ข้อมูลประเภทสินค้า</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>ชื่อประเภทสินค้า <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียด</label>
                        <textarea rows="3" name="description" id="description" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title">สถานะการขาย</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">เปิด/ปิดการใช้งานประเภทสินค้า</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card-footer py-1 text-center">
        <button class="btn btn-success btn-sm btn-flat" form="product-type-form"><i class="fa fa-save"></i> บันทึก</button>
        <a class="btn btn-danger btn-sm border btn-flat" href="./?page=product_type"><i class="fa fa-times"></i> ยกเลิก</a>
    </div>
</div>


<script>
    $(document).ready(function() {
        $('#product-type-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_product_type",
                data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                error: err => {
                    console.log(err)
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.replace('./?page=product_type');
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $("html, body").scrollTop(0);
                        end_loader()
                    } else {
                        alert_toast("เกิดข้อผิดพลาด", 'error');
                        end_loader();
                        console.log(resp)
                    }
                }
            })
        })
    })
</script>