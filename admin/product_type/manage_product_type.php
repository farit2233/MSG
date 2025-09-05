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
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
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
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title"><?= isset($id) ? "แก้ไขประเภทสินค้า" : "สร้างประเภทสินค้าใหม่" ?></div>
    </div>
    <form action="" id="product-type-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="card-body">

            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">ข้อมูลประเภทสินค้า</div>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label>ชื่อประเภทสินค้า <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
                    </div>
                    <div class="form-group">
                        <label for="description">รายละเอียด</label>
                        <textarea rows="3" name="description" id="description" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
                        <input type="checkbox" id="other" name="other" value="1" <?= (isset($other) && $other == 1) ? 'checked' : '' ?>>
                        <label for="other">เปิด / ปิดการใช้สถานะประเภทสินค้าอื่น ๆ <small>*เปิดใช้งานเพื่อให้หมวดหมู่นี้แสดงอยู่ท้ายสุด</small></label>
                    </div>
                </div>
            </div>
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title">สถานะการขาย</div>
                </div>
                <div class="card-body">
                    <input type="hidden" name="status" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="status">เปิด / ปิดการใช้งานประเภทสินค้า</label>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="card-footer py-1 text-center">
        <a class="btn btn-light btn-sm border btn-flat" href="javascript:void(0)" id="backBtn"><i class="fa fa-angle-left"></i> กลับ</a>
        <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
        <button class="btn btn-success btn-sm btn-flat" form="product-type-form"><i class="fa fa-save"></i> บันทึก</button>
    </div>
</section>
<script>
    $(document).ready(function() {
        let formChanged = false;

        $('#product-type-form input, #product-type-form textarea').on('input', function() {
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

        // เมื่อกดปุ่ม "กลับ"
        $('#backBtn').click(function() {
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
                        // กลับไปหน้าหมวดหมู่โปรโมชั่น
                        window.location.href = './?page=product_type';
                    }
                });
            } else {
                // ถ้าไม่มีการเปลี่ยนแปลงก็กลับไปหน้าหมวดหมู่โปรโมชั่น
                window.location.href = './?page=product_type';
            }
        });

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