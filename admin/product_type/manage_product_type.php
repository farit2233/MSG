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
<div class="content py-5 px-3 bg-gradient-dark">
    <h2><b><?= isset($id) ? "แก้ไขประเภทสินค้า" : "สร้างประเภทสินค้าใหม่" ?></b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
    <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
        <div class="card rounded-0">
            <div class="card-body">

                <div class="container-fluid">
                    <form action="" id="product-type-form">
                        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
                        <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label for="name" class="control-label">ชื่อประเภทสินค้า</label>
                            <input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
                        </div>
                        <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <label for="description" class="control-label">รายละเอียดประเภทสินค้า</label>
                            <textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
                            <label for="status" class="control-label">สถานะ</label>
                            <select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
                                <option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>กำลังใช้งาน</option>
                                <option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>ไม่ได้ใช้งาน</option>
                            </select>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card-footer py-1 text-center">
                <button class="btn btn-sm btn-success btn-flat" form="product-type-form"><i class="fa fa-save"></i> บันทึก</button>
                <a class="btn btn-danger btn-sm border btn-flat" href="./?page=product_type"><i class="fa fa-times"></i> ยกเลิก</a>
            </div>
        </div>
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
                    alert_toast("An error occured", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (typeof resp == 'object' && resp.status == 'success') {
                        location.replace('./?page=product_type/manage_product_type&id=' + resp.cid)
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div>')
                        el.addClass("alert alert-danger err-msg").text(resp.msg)
                        _this.prepend(el)
                        el.show('slow')
                        $("html, body").scrollTop(0);
                        end_loader()
                    } else {
                        alert_toast("An error occured", 'error');
                        end_loader();
                        console.log(resp)
                    }
                }
            })
        })

    })
</script>