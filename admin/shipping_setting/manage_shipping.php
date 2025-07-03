<?php
$provider_id = null;
$display_name = '';
$description = '';
$shipping_type = 'fixed';
$cost = 0.00;


$cod_enabled = 0;
$is_active = 1;
$id = '';

if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM shipping_methods WHERE id = '{$_GET['id']}' ");
    if ($qry && $qry->num_rows > 0) {
        $row = $qry->fetch_assoc();
        foreach ($row as $k => $v) {
            $$k = $v;
        }
        $display_name = $name;
    }
}
?>

<!-- Begin: ฟอร์มการตั้งค่าขนส่งทั้งหมดภายใน Card -->
<div class="card card-outline card-primary rounded-0">
    <div class="card-header">
        <h1 class="card-title"><?php echo isset($id) && $id > 0 ? 'แก้ไขข้อมูลการจัดส่ง' : 'เพิ่มข้อมูลการจัดส่ง'; ?></h1>
    </div>
    <form action="" id="shipping-form" method="POST">
        <input type="hidden" name="id" value="<?= $id ?>">
        <div class="card-body">
            <!-- Card 1: ข้อมูลการจัดส่ง -->
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title h3">ข้อมูลการจัดส่ง</h3>
                </div>
                <div class="card-body">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">บริษัทขนส่ง <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select name="provider_id" id="provider_id" class="form-control select2" required>
                                <option value="">-- เลือกบริษัทขนส่ง --</option>
                                <?php
                                $prov_q = $conn->query("SELECT * FROM shipping_providers WHERE status = 1 ORDER BY name ASC");
                                while ($prow = $prov_q->fetch_assoc()): ?>
                                    <option value="<?= $prow['id'] ?>" <?= $provider_id == $prow['id'] ? 'selected' : '' ?>>
                                        <?= $prow['name'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label">ชื่อการจัดส่งสำหรับลูกค้า <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" name="display_name" class="form-control" placeholder="ชื่อแสดงในหน้าสั่งซื้อสินค้าของลูกค้า" value="<?= htmlspecialchars($display_name) ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="รายละเอียดการจัดส่ง เช่น ระยะเวลา รอบส่ง"><?= htmlspecialchars($description) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Card 2: ราคาจัดส่งตามน้ำหนัก -->
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title h3">ราคาจัดส่งตามน้ำหนัก</h3>
                </div>
                <div class="card-body">
                    <div id="shipping-price-fields">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">ราคา (บาท)</label>
                            <div class="col-sm-3">
                                <input type="number" name="price[]" class="form-control" placeholder="ราคา" required>
                            </div>
                            <label class="col-sm-3 col-form-label">น้ำเริ่มต้น (กก.)</label>
                            <div class="col-sm-3">
                                <input type="number" name="weight_from[]" class="form-control" placeholder="น้ำหนักเริ่มต้น" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">น้ำหนักสูงสุด (กก.)</label>
                            <div class="col-sm-3">
                                <input type="number" name="weight_to[]" class="form-control" placeholder="น้ำหนักสูงสุด" required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-success" id="add-price-row">+ เพิ่มแถว</button>
                </div>
            </div>

            <!-- Card 3: เก็บเงินปลายทาง -->
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title">เก็บเงินปลายทาง</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" name="cod_enabled" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="cod_enabled" name="cod_enabled" value="1" <?= $cod_enabled ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="cod_enabled">ตั้งค่าให้การจัดส่งนี้เป็นแบบเก็บเงินปลายทาง</label>
                    </div>
                </div>
            </div>

            <!-- Card 4: สถานะการแสดงผล -->
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <h3 class="card-title">สถานะการแสดงผล</h3>
                </div>
                <div class="card-body">
                    <input type="hidden" name="is_active" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" <?= $is_active ? 'checked' : '' ?>>
                        <label class="custom-control-label" for="is_active">ปิดเพื่อซ่อนการจัดส่งจากหน้าร้าน แต่ร้านค้ายังสามารถจัดการต่อได้</label>
                    </div>
                </div>
            </div>

        </div>
        <div class="card-footer text-center">
            <button type="submit" class="btn btn-primary btn-lg mr-2">บันทึก</button>
            <a href="./?page=shipping_setting" class="btn btn-secondary btn-lg">ยกเลิก</a>
        </div>
    </form>
</div>

<script>
    $(function() {
        $('#provider_id').select2({
            placeholder: "เลือกหรือพิมพ์ชื่อบริษัทขนส่ง",
            width: '100%'
        });

        // การเพิ่มแถวราคา
        $('#add-price-row').click(function() {
            var newRow = '<div class="form-group row">' +
                '<label class="col-sm-3 col-form-label">ราคา (บาท)</label>' +
                '<div class="col-sm-3">' +
                '<input type="number" name="price[]" class="form-control" placeholder="ราคา" required>' +
                '</div>' +
                '<label class="col-sm-3 col-form-label">น้ำเริ่มต้น (กก.)</label>' +
                '<div class="col-sm-3">' +
                '<input type="number" name="weight_from[]" class="form-control" placeholder="น้ำหนักเริ่มต้น" required>' +
                '</div>' +
                '</div>' +
                '<div class="form-group row">' +
                '<label class="col-sm-3 col-form-label">น้ำหนักสูงสุด (กก.)</label>' +
                '<div class="col-sm-3">' +
                '<input type="number" name="weight_to[]" class="form-control" placeholder="น้ำหนักสูงสุด" required>' +
                '</div>' +
                '</div>';
            // เพิ่มแถวใหม่ที่ด้านล่าง
            $('#shipping-price-fields').append(newRow);
        });

        // ส่งข้อมูลจากฟอร์มไปยัง Master.php
        $('#shipping-form').submit(function(e) {
            e.preventDefault();
            var _this = $(this);
            $('.err-msg').remove(); // ลบข้อความ error เก่า
            start_loader(); // แสดง loader ว่ากำลังบันทึกข้อมูล

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_shipping", // URL ที่จะส่งข้อมูล
                data: new FormData(this), // ข้อมูลจากฟอร์ม
                cache: false,
                contentType: false, // ไม่ใช้ contentType แบบ default
                processData: false, // ไม่ต้องการแปลงข้อมูลเป็น query string
                method: 'POST', // ใช้ POST method
                type: 'POST',
                dataType: 'json', // รับข้อมูลในรูปแบบ JSON
                error: function(err) {
                    console.log(err);
                    alert_toast("เกิดข้อผิดพลาด", 'error'); // แสดง toast เมื่อเกิดข้อผิดพลาด
                    end_loader(); // หยุด loader
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        alert_toast("บันทึกข้อมูลเรียบร้อยแล้ว", 'success');
                        setTimeout(function() {
                            location.href = "./?page=shipping_setting"; // รีเฟรชหน้า
                        }, 1000);
                    } else if (resp.status == 'failed' && !!resp.msg) {
                        var el = $('<div class="alert alert-danger err-msg">').text(resp.msg);
                        _this.prepend(el); // แสดงข้อความ error ที่หน้าฟอร์ม
                        $("html, body").scrollTop(0);
                        end_loader();
                    } else {
                        alert_toast("ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>