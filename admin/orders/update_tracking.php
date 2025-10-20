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
            <label for="tracking_id">อัปเดตเลขขนส่ง</label>

            <input type="text" class="form-control" id="tracking_id" name="tracking_id" value="<?= isset($tracking_id) ? $tracking_id : '' ?>">
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
                url: _base_url_ + "classes/Master.php?f=update_tracking_id",
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