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

// --- [START] ดึงข้อมูลผู้ให้บริการขนส่ง ---
// ดึงข้อมูลจากตาราง shipping_providers ที่มีสถานะ 1 (ใช้งาน)
$providers_qry = $conn->query("SELECT * FROM `shipping_providers` WHERE status = 1 ORDER BY name ASC");
// --- [END] ---

?>
<div class="container-fluid">
    <form action="" id="take-action-form">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">

        <div class="form-group">
            <label for="provider_id">ผู้ให้บริการขนส่ง</label>
            <select name="provider_id" id="provider_id" class="form-control form-control-sm rounded-0" required>
                <option value="" disabled <?= !isset($provider_id) ? 'selected' : '' ?>>-- เลือกผู้ให้บริการ --</option>
                <?php
                // วนลูปสร้าง <option> จากตาราง shipping_providers
                while ($row = $providers_qry->fetch_assoc()) :
                ?>
                    <option value="<?= $row['id'] ?>"
                        <?php
                        // ตรวจสอบว่า ID ของ provider ตรงกับ provider_id ที่บันทึกไว้ในออเดอร์หรือไม่
                        // ถ้าตรง ให้ใส่ 'selected'
                        echo (isset($provider_id) && $provider_id == $row['id']) ? 'selected' : '';
                        ?>>
                        <?= $row['name'] // 
                        ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
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

            // --- [NEW] ตรวจสอบว่าเลือก provider หรือยัง ---
            if ($('#provider_id').val() == '' || $('#provider_id').val() == null) {
                let el = $('<div>').addClass('alert alert-danger err-msg').text('กรุณาเลือกผู้ให้บริการขนส่ง');
                _this.prepend(el);
                el.show('slow');
                $("html, body, .modal").scrollTop(0);
                return false; // หยุดการทำงาน
            }
            // --- [END] ตรวจสอบ ---

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