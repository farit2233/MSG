<?php
require_once('../../config.php');

// 1. ดึงข้อมูล Order List
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `order_list` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}

// 2. ดึงข้อมูล Slip Payment โดยใช้ order_code ($code) ที่ได้มาจากข้างบน
$slip_path = ""; // กำหนดค่าเริ่มต้น
$is_approve = 0; // กำหนดค่าเริ่มต้น
if (isset($code)) {
    $slip_qry = $conn->query("SELECT slip_path, is_approve FROM `slip_payment` WHERE order_code = '{$code}' LIMIT 1");
    if ($slip_qry->num_rows > 0) {
        $slip_row = $slip_qry->fetch_assoc();
        $slip_path = $slip_row['slip_path'];
        $is_approve = $slip_row['is_approve']; // ดึงค่า is_approve จาก slip_payment
    }
}
?>

<div class="container-fluid">
    <form action="" id="take-action-form">
        <input type="hidden" name="order_id" value="<?= isset($id) ? $id : '' ?>">
        <div class="form-group mb-3">

            <div class="form-group">
                <label class="control-label">สถานะสลิปชำระเงิน</label>
                <select class="form-control form-control-sm rounded-0" name="is_approve" id="is_approve" required>
                    <option value="0" <?= $is_approve == 0 ? 'selected' : '' ?>>รอการยืนยัน</option>
                    <option value="1" <?= $is_approve == 1 ? 'selected' : '' ?>>ยืนยันสลิปชำระเงินแล้ว</option>
                    <option value="2" <?= $is_approve == 2 ? 'selected' : '' ?>>ปฏิเสธสลิปชำระเงิน</option>
                </select>
            </div>

        </div>

        <hr>

        <div class="form-group">
            <label for="slip_img">หลักฐานการโอนเงิน (Slip):</label>
            <div class="d-flex justify-content-center mt-2">
                <?php if (!empty($slip_path)): ?>
                    <img src="<?= base_url . $slip_path ?>" alt="Payment Slip" id="slip_img" class="img-fluid border bg-dark" style="max-height: 450px; object-fit: contain;">

                    <div class="mt-2 text-center w-100" style="position:absolute; bottom:10px;">
                        <a href="<?= base_url . $slip_path ?>" target="_blank" class="btn btn-sm btn-light"><i class="fa fa-search-plus"></i> ดูรูปเต็ม</a>
                    </div>
                <?php else: ?>
                    <div class="alert alert-warning w-100 text-center">
                        <i class="fa fa-exclamation-triangle"></i> ไม่พบหลักฐานการโอนเงิน
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </form>
</div>

<script>
    $(function() {
        // เมื่อกดปุ่ม Save ที่ footer modal
        $('#uni_modal').on('click', '.modal-footer .btn-save', function() {
            $('#take-action-form').submit();
        });

        $('#take-action-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove();
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=update_slip_payment",
                data: new FormData(this),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.reload();
                    } else {
                        let el = $('<div>').addClass('alert alert-danger err-msg').text(resp.msg || 'เกิดข้อผิดพลาด');
                        _this.prepend(el);
                        el.show('slow');
                        $("html, body, .modal").scrollTop(0);
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    alert_toast("An error occurred", 'error');
                    end_loader();
                }
            });
        });
    });
</script>