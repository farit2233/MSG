<?php
// --- PHP Block ---
// ตรวจสอบการ Login
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    // Fetch logic...
} else {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); location.replace('./');</script>";
}

// --- 1. ดึงข้อมูลธนาคารร้านค้า ---
$banks_list = [];
if (isset($conn)) {
    $bank_qry = $conn->query("SELECT * FROM bank_system WHERE is_active = 1 AND is_visible = 1 ORDER BY bank_name ASC");
    if ($bank_qry) {
        while ($row = $bank_qry->fetch_assoc()) {
            $banks_list[] = $row;
        }
    }
}

// --- 2. ดึงรายการสั่งซื้อที่ "ยังไม่จ่าย" ของลูกค้าคนนี้ ---
$orders_list = [];
if (isset($conn)) {
    $customer_id = $_settings->userdata('id');
    $order_qry = $conn->query("SELECT * FROM `order_list` WHERE customer_id = '{$customer_id}' AND payment_status = 0 ORDER BY date_created DESC");
    if ($order_qry) {
        while ($row = $order_qry->fetch_assoc()) {
            $orders_list[] = $row;
        }
    }
}

// รับค่า Order Code
$order_code_val = isset($_GET['order_code']) ? $_GET['order_code'] : '';
?>

<style>
    /* --- CSS สำหรับ Select2 (เพิ่มใหม่) --- */
    /* --- CSS สำหรับ Select2 (ปรับแก้ให้กึ่งกลาง) --- */
    .select2-container .select2-selection--single {
        height: calc(1.5em + .75rem + 2px) !important;
        /* ความสูงมาตรฐาน Bootstrap */
        padding: 0.375rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;

        /* เพิ่ม Flexbox เพื่อจัดกึ่งกลางแนวตั้ง */
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: normal !important;
        /* ปล่อยให้ Flex จัดการความสูง */
        padding-left: 0;
        color: #495057;
        width: 100%;
        /* ให้ข้อความยาวเต็มพื้นที่ */
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        /* จัดลูกศรให้กลางด้วย */
        height: 100% !important;
        top: 0 !important;
        right: 5px;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }

    /* ปรับสีตอน Focus */
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #f57421;
    }

    /* จัดแต่ง Input File */
    .custom-file-label::after {
        content: "Browse";
        text-transform: none !important;
        font-weight: normal;
    }

    .custom-file-label {
        font-weight: normal !important;
    }

    /* กล่อง Preview รูปสลิป */
    #slip-preview-container {
        display: none;
        margin-top: 15px;
        text-align: center;
        border: 2px dashed #ddd;
        padding: 10px;
        border-radius: 5px;
        background-color: #f9f9f9;
    }

    #slip-preview {
        max-width: 100%;
        max-height: 550px;
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Style สำหรับส่วนเลือกธนาคาร */
    .custom-select-trigger {
        border: 1px solid #ced4da;
        padding: .375rem .75rem;
        border-radius: .25rem;
        background-color: #fff;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: calc(1.5em + .75rem + 2px);
    }

    .btn-bank-slip {
        background-color: #f57421;
        color: white;
        transition: all 0.2s ease-in-out;
    }

    .btn-bank-slip:hover {
        color: white;
        filter: brightness(90%);
    }

    /* Card ธนาคารใน Modal */
    .bank-option-card {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        height: 100%;
        background: #fff;
        position: relative;
    }

    .bank-option-card:hover {
        border-color: #f57421;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .bank-option-card.selected {
        border-color: #f57421;
        background-color: #fff5ee;
    }


    .input-group-text {
        background-color: #fff;
        border-left: 0;
        cursor: pointer;
    }

    .flatpickr-thai input {
        border-right: 0;
    }
</style>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="py-4">
                    <div class="card-body">
                        <div class="profile-section-title-with-line mb-4">
                            <h4>แจ้งยอดชำระเงิน</h4>
                            <p class="text-muted">กรอกข้อมูลให้ครบถ้วนเพื่อยืนยันการโอนเงิน</p>
                        </div>

                        <form id="payment-form" method="post" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="order_code">รหัสรายการสั่งซื้อ (Order Code) <span class="text-danger">*</span></label>

                                        <select class="form-control select2" name="order_code" id="order_code" required style="width: 100%;">
                                            <option value="" disabled selected>-- ค้นหา หรือ เลือกรายการที่ชำระ --</option>
                                            <?php if (!empty($orders_list)): ?>
                                                <?php foreach ($orders_list as $order): ?>
                                                    <?php
                                                    // --- แก้ไขเงื่อนไขตรงนี้ ---
                                                    $selected = '';
                                                    // เทียบ ID (ถ้ามีส่งมา) หรือเทียบ Code (ถ้ามีส่งมา)
                                                    if (($order_id_val != '' && $order_id_val == $order['id']) ||
                                                        ($order_code_val != '' && $order_code_val == $order['code'])
                                                    ) {
                                                        $selected = 'selected';
                                                    }
                                                    // ------------------------

                                                    $display_text = $order['code'] . ' (ยอด ' . number_format($order['grand_total'], 2) . ' บาท)';
                                                    ?>
                                                    <option value="<?= $order['code'] ?>"
                                                        data-order-id="<?= $order['id'] ?>"
                                                        data-amount="<?= $order['grand_total'] ?>"
                                                        data-name="<?= htmlspecialchars($order['name']) ?>"
                                                        data-contact="<?= htmlspecialchars($order['contact']) ?>"
                                                        <?= $selected ?>>
                                                        <?= $display_text ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="" disabled>ไม่พบรายการค้างชำระ</option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="total_price">ยอดโอน (บาท) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="total_price" id="total_price" value="" required placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">ชื่อ - สกุล <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="customer_name" id="customer_name" value="" required placeholder="ระบุชื่อ-นามสกุล">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contact" id="contact" value="" required placeholder="08xxxxxxxx">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1" for="email">อีเมล <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email" value="" required placeholder="email@example.com">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="mb-1">บัญชีธนาคารที่โอนเข้ามา <span class="text-danger">*</span></label>
                                        <div class="custom-select-trigger" id="bank-selector-btn">
                                            <span id="selected-bank-text" class="text-muted">กรุณาเลือกบัญชี...</span>
                                            <i class="fa fa-chevron-down text-muted"></i>
                                        </div>
                                        <input type="hidden" name="customer_bank" id="customer_bank" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <div id="selected-bank-details" class="d-none mb-3 p-3 rounded border">
                                        <div class="d-flex align-items-center">
                                            <img id="selected-bank-img" src="" style="width: 50px; height: 50px; object-fit: contain; margin-right: 15px;">
                                            <div style="line-height: 1.3;">
                                                <h6 class="mb-1 font-weight-bold" id="selected-bank-name"></h6>
                                                <div class="font-weight-bold mb-1 " id="selected-bank-number"></div>
                                                <div class="small text-muted"></i> <span id="selected-bank-company"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_time">วันที่ และเวลาที่โอน <span class="text-danger">*</span></label>
                                        <div class="input-group flatpickr-thai">
                                            <input type="text" name="date_time" class="form-control" placeholder="เลือกวัน-เวลาที่โอน..." required data-input>
                                            <div class="input-group-append">
                                                <a class="input-group-text" title="เลือกวันที่" data-toggle>
                                                    <i class="fa fa-calendar"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customFile">หลักฐานการโอน (Slip) <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" id="customFile" name="img" accept="image/*" required onchange="previewSlip(this)">
                                            <label class="custom-file-label" for="customFile">เลือกไฟล์...</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="slip-preview-container">
                                <p class="small text-muted mb-2">ตัวอย่างสลิป:</p>
                                <img id="slip-preview" src="#" alt="Slip Preview">
                            </div>

                            <div class="row justify-content-center mt-4">
                                <div class="col-md-6 col-12 text-center">
                                    <button type="submit" class="btn btn-bank-slip btn-block rounded-pill btn-lg shadow-sm">
                                        ยืนยันชำระเงิน
                                    </button>
                                </div>
                            </div>

                            <div class="text-center text-muted small mt-2">
                                <i class="fa fa-shield-alt mr-1"></i> ข้อมูลปลอดภัยด้วยการเข้ารหัส SSL
                            </div>
                            <input type="hidden" name="order_id" id="order_id">

                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="bankSelectionModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fa fa-building mr-2"></i>เลือกบัญชีธนาคาร</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="row">
                            <?php if (!empty($banks_list)): ?>
                                <?php foreach ($banks_list as $bank): ?>
                                    <div class="col-6 col-md-4 mb-3">
                                        <div class="bank-option-card"
                                            data-id="<?= $bank['id'] ?>"
                                            data-name="<?= htmlspecialchars($bank['bank_name']) ?>"
                                            data-number="<?= htmlspecialchars($bank['bank_number']) ?>"
                                            data-company="<?= htmlspecialchars($bank['bank_company']) ?>"
                                            data-img="<?= validate_image($bank['image_path']) ?>"
                                            onclick="selectBankItem(this)">
                                            <img src="<?= validate_image($bank['image_path']) ?>" alt="<?= $bank['bank_name'] ?>">
                                            <h6 class="font-weight-bold mb-1"><?= $bank['bank_name'] ?></h6>
                                            <div class="font-weight-bold"><?= $bank['bank_number'] ?></div>
                                            <div class="small text-secondary mt-1 text-truncate px-2" title="<?= $bank['bank_company'] ?>"><?= $bank['bank_company'] ?></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center text-muted py-3">ไม่พบข้อมูลบัญชีธนาคารในระบบ</div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-bank-cancel" data-dismiss="modal">ยกเลิก</button>
                        <button type="button" class="btn btn-bank-slip" onclick="confirmBankSelection()">ยืนยันการเลือก</button>
                    </div>
                </div>
            </div>
        </div>
</section>

<script>
    let tempSelectedBank = null;

    $(document).ready(function() {
        // รอ 0.5 วินาที ให้หน้าเว็บโหลดส่วนอื่นเสร็จก่อน
        setTimeout(function() {
            // 1. ดึงค่า order_id จาก URL
            const urlParams = new URLSearchParams(window.location.search);
            const orderId = urlParams.get('order_id');

            if (orderId) {
                console.log("Auto-selecting Order ID:", orderId);

                // 2. ค้นหาตัวเลือกใน Dropdown ที่มี data-order-id ตรงกัน
                // หมายเหตุ: ต้องแน่ใจว่าใน <option> คุณใส่ data-order-id="<?= $order['id'] ?>" ไว้แล้ว
                let targetOption = $('#order_code').find('option[data-order-id="' + orderId + '"]');

                // ถ้าหาไม่เจอ ลองหาจาก value (เผื่อกรณี value เป็น id)
                if (targetOption.length === 0) {
                    targetOption = $('#order_code').find('option[value="' + orderId + '"]');
                }

                if (targetOption.length > 0) {
                    // 3. สั่งให้เลือกค่า และกระตุ้นเหตุการณ์ change
                    let valToSelect = targetOption.val();
                    $('#order_code').val(valToSelect).trigger('change');
                }
            }
        }, 500); // หน่วงเวลา 500ms
        // 1. เริ่มต้น Select2
        $('.select2').select2({
            width: '100%',
            placeholder: "-- ค้นหา หรือ เลือกรายการที่ชำระ --",
            allowClear: true
        });

        // 2. เริ่มต้น Flatpickr (ปฏิทินไทย)
        flatpickr(".flatpickr-thai", {
            wrap: true,
            enableTime: true,
            time_24hr: true,
            locale: "th",
            altInput: true,
            altFormat: "j F พ.ศ. Y (H:i น.)",
            dateFormat: "Y-m-d H:i",
        });

        // 3. Auto Fill ข้อมูลเมื่อเลือก Order (ทำงานร่วมกับ Select2 ได้อัตโนมัติ)
        $('#order_code').change(function() {
            var selectedOption = $('option:selected', this);

            var amount = selectedOption.attr('data-amount');
            var name = selectedOption.attr('data-name');
            var contact = selectedOption.attr('data-contact');
            var order_id = selectedOption.attr('data-order-id'); // รับค่า order_id

            if (amount) $('#total_price').val(parseFloat(amount).toFixed(2));
            if (name) $('#customer_name').val(name);
            if (contact) $('#contact').val(contact);
            if (order_id) $('#order_id').val(order_id); // ส่งค่า order_id ในฟอร์ม
        });


        // Trigger change กรณีมีค่า Default
        if ($('#order_code').val() != "") {
            $('#order_code').trigger('change');
        }

        // 4. Submit Form
        $('#payment-form').submit(function(e) {
            e.preventDefault();

            if ($('#customer_bank').val() == '') {
                alert("กรุณาเลือกบัญชีธนาคารที่โอนเงินเข้ามา");
                $('#bankSelectionModal').modal('show');
                return;
            }

            if ($('input[name="date_time"]').val() == '') {
                alert("กรุณาระบุวัน-เวลาที่โอนเงิน");
                // เลื่อนหน้าจอไปหาช่องวันที่ และ Focus
                $('input[name="date_time"]').focus();
                return; // หยุดการทำงาน ไม่ให้ส่งข้อมูลไป Server
            }

            start_loader();
            var formData = new FormData(this);

            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_payment_slip",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                dataType: 'json',
                success: function(resp) {
                    if (resp.status == 'success') {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: 'แจ้งชำระเงินสำเร็จ',
                                text: 'เจ้าหน้าที่จะดำเนินการตรวจสอบข้อมูลของท่านโดยเร็ว',
                                confirmButtonColor: '#f57421'
                            }).then(() => {
                                location.replace('./');
                            });
                        } else {
                            alert("แจ้งชำระเงินเรียบร้อยแล้ว");
                            location.replace('./');
                        }
                    } else {
                        alert(resp.msg || "เกิดข้อผิดพลาดในการบันทึกข้อมูล");
                    }
                    end_loader();
                },
                error: function(err) {
                    console.log(err);
                    alert("เกิดข้อผิดพลาดในการเชื่อมต่อ");
                    end_loader();
                }
            });
        });
    });

    $('#bank-selector-btn').click(function() {
        $('#bankSelectionModal').modal('show');
    });

    function selectBankItem(element) {
        $('.bank-option-card').removeClass('selected');
        $(element).addClass('selected');
        tempSelectedBank = {
            name: $(element).data('name'),
            number: $(element).data('number'),
            company: $(element).data('company'),
            img: $(element).data('img')
        };
    }

    function confirmBankSelection() {
        if (tempSelectedBank) {
            $('#selected-bank-text').html(tempSelectedBank.name);
            $('#selected-bank-text').addClass('text-dark').removeClass('text-muted');
            $('#customer_bank').val(tempSelectedBank.name);

            $('#selected-bank-details').removeClass('d-none').addClass('d-block fade show');
            $('#selected-bank-name').text(tempSelectedBank.name);
            $('#selected-bank-number').text(tempSelectedBank.number);
            $('#selected-bank-company').text(tempSelectedBank.company);
            $('#selected-bank-img').attr('src', tempSelectedBank.img);

            $('#bankSelectionModal').modal('hide');
        } else {
            alert("กรุณาเลือกบัญชีธนาคาร");
        }
    }

    function previewSlip(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            var fileName = input.files[0].name;
            $(input).next('.custom-file-label').html(fileName);
            reader.onload = function(e) {
                $('#slip-preview').attr('src', e.target.result);
                $('#slip-preview-container').show();
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>