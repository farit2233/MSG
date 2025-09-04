<?php
require_once('../../config.php');
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `order_list` where id = '{$_GET['id']}' ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<div class="container-fluid">
    <form action="" id="take-action-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">

        <div class="form-group">
            <label for="payment_status" class="control-label">สถานะการชำระเงิน (Payment Status)</label>
            <select class="form-control form-control-sm rounded-0" name="payment_status" id="payment_status" required>
                <option value="0" <?= isset($payment_status) && $payment_status == 0 ? 'selected' : '' ?>>ยังไม่ชำระเงิน</option>
                <option value="1" <?= isset($payment_status) && $payment_status == 1 ? 'selected' : '' ?>>รอตรวจสอบ</option>
                <option value="2" <?= isset($payment_status) && $payment_status == 2 ? 'selected' : '' ?>>ชำระเงินแล้ว</option>
                <option value="3" <?= isset($payment_status) && $payment_status == 3 ? 'selected' : '' ?>>ชำระเงินล้มเหลว</option>
                <option value="4" <?= isset($payment_status) && $payment_status == 4 ? 'selected' : '' ?>>รอการยกเลิกคำสั่งซื้อ</option>
                <option value="5" <?= isset($payment_status) && $payment_status == 5 ? 'selected' : '' ?>>คืนเงินแล้ว</option>
            </select>
        </div>

        <div class="form-group">
            <label for="delivery_status" class="control-label">สถานะการจัดส่ง (Delivery Status)</label>
            <select class="form-control form-control-sm rounded-0" name="delivery_status" id="delivery_status" required>
                <option value="0" <?= isset($delivery_status) && $delivery_status == 0 ? 'selected' : '' ?>>ตรวจสอบคำสั่งซื้อ</option>
                <option value="1" <?= isset($delivery_status) && $delivery_status == 1 ? 'selected' : '' ?>>กำลังเตรียมของ</option>
                <option value="2" <?= isset($delivery_status) && $delivery_status == 2 ? 'selected' : '' ?>>แพ๊กของแล้ว</option>
                <option value="3" <?= isset($delivery_status) && $delivery_status == 3 ? 'selected' : '' ?>>กำลังจัดส่ง</option>
                <option value="4" <?= isset($delivery_status) && $delivery_status == 4 ? 'selected' : '' ?>>จัดส่งสำเร็จ</option>
                <option value="5" <?= isset($delivery_status) && $delivery_status == 5 ? 'selected' : '' ?>>จัดส่งไม่สำเร็จ</option>
                <option value="6" <?= isset($delivery_status) && $delivery_status == 6 ? 'selected' : '' ?>>รอการยกเลิกคำสั่งซื้อ</option>
                <option value="7" <?= isset($delivery_status) && $delivery_status == 7 ? 'selected' : '' ?>>คืนของระหว่างทาง</option>
                <option value="8" <?= isset($delivery_status) && $delivery_status == 8 ? 'selected' : '' ?>>คืนของสำเร็จ</option>
                <option value="9" <?= isset($delivery_status) && $delivery_status == 9 ? 'selected' : '' ?>>ยกเลิกแล้ว</option>
            </select>
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
                url: _base_url_ + "classes/Master.php?f=update_order_status",
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