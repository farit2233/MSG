<?php
// --- PHP Block ---
// ตรวจสอบการ Login
if ($_settings->userdata('id') != '') {
    $qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$_settings->userdata('id')}'");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_array() as $k => $v) {
            if (!is_numeric($k)) $$k = $v;
        }
    }
} else {
    echo "<script>alert('กรุณาเข้าสู่ระบบ'); location.replace('./');</script>";
}

// --- ดึงข้อมูลธนาคารจาก bank_system ---
$banks_list = [];
if (isset($conn)) {
    // เลือกเฉพาะที่ Active และ Visible
    $bank_qry = $conn->query("SELECT * FROM bank_system WHERE is_active = 1 AND is_visible = 1 ORDER BY bank_name ASC");
    if ($bank_qry) {
        while ($row = $bank_qry->fetch_assoc()) {
            $banks_list[] = $row;
        }
    }
}

// รับค่า Order Code และ Total Price (ถ้ามี)
$order_code_val = isset($_GET['order_code']) ? $_GET['order_code'] : '';
$total_price_val = isset($_GET['amount']) ? $_GET['amount'] : '';
?>

<style>
    /* จัดแต่ง Input File (Browse ขวา) */
    .custom-file-label::after {
        content: "Browse";
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
        max-height: 200px;
        /* ปรับความสูงไม่ให้ใหญ่เกินไป */
        border-radius: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Style สำหรับส่วนเลือกธนาคารให้สูงเท่า Input ปกติ */
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
        /* ความสูงมาตรฐาน Bootstrap input */
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
    }

    .bank-option-card:hover {
        border-color: #f57421;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }

    .bank-option-card.selected {
        border-color: #f57421;
        background-color: #fff5ee;
        position: relative;
    }

    .bank-option-card.selected::after {
        content: '\f00c';
        font-family: "Font Awesome 5 Free";
        font-weight: 900;
        position: absolute;
        top: 5px;
        right: 8px;
        color: #f57421;
    }

    .bank-option-card img {
        width: 50px;
        height: 50px;
        object-fit: contain;
        margin-bottom: 10px;
    }
</style>

<section class="py-5 profile-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-3">
                <?php include 'user/inc/navigation.php'; ?>
            </div>

            <div class="col-lg-9">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div class="profile-section-title-with-line mb-4">
                            <h4>แจ้งชำระเงิน (Slip Payment)</h4>
                            <p class="text-muted">กรอกข้อมูลให้ครบถ้วนเพื่อยืนยันการโอนเงิน</p>
                        </div>

                        <form id="payment-form" method="post" enctype="multipart/form-data">

                            <div class="row">
                                <div class="col-md-6" style="<?= empty($order_code_val) ? '' : 'display:none;' ?>">
                                    <div class="form-group">
                                        <label for="order_code">รหัสรายการสั่งซื้อ (Order Code) <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="order_code" id="order_code" value="<?= $order_code_val ?>" required placeholder="เช่น ORD-12345">
                                    </div>
                                </div>
                                <div class="col-md-6" style="<?= empty($total_price_val) ? '' : 'display:none;' ?>">
                                    <div class="form-group">
                                        <label for="total_price">ยอดโอน (บาท) <span class="text-danger">*</span></label>
                                        <input type="number" step="0.01" class="form-control" name="total_price" id="total_price" value="<?= $total_price_val ?>" required placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer_name">ชื่อ / สกุล <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="customer_name" id="customer_name"
                                            value="<?= isset($firstname) ? $firstname . ' ' . $lastname : '' ?>" required placeholder="ระบุชื่อ-นามสกุล">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="contact">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="<?= isset($contact) ? $contact : '' ?>" required placeholder="08xxxxxxxx">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">อีเมล <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control" name="email" id="email"
                                            value="<?= isset($email) ? $email : '' ?>" required placeholder="email@example.com">
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
                                    <div id="selected-bank-details" class="d-none mb-3 p-3 bg-light rounded border">
                                        <div class="d-flex align-items-center">
                                            <img id="selected-bank-img" src="" style="width: 50px; height: 50px; object-fit: contain; margin-right: 15px;">
                                            <div style="line-height: 1.3;">
                                                <h6 class="mb-1 font-weight-bold" id="selected-bank-name"></h6>
                                                <div class="font-weight-bold mb-1 text-primary" id="selected-bank-number"></div>
                                                <div class="small text-muted"><i class="fa fa-user mr-1"></i> <span id="selected-bank-company"></span></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_time">วันที่ และเวลาที่โอน <span class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" name="date_time" id="date_time" required>
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
                                    <button type="submit" class="btn btn-primary btn-block rounded-pill btn-lg shadow-sm" style="background-color: #f57421; border-color: #f57421;">
                                        ยืนยันชำระเงิน
                                    </button>
                                </div>
                            </div>

                            <div class="text-center text-muted small mt-2">
                                <i class="fa fa-shield-alt mr-1"></i> ข้อมูลปลอดภัยด้วยการเข้ารหัส SSL
                            </div>

                        </form>
                    </div>
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
                                        <h6 class="font-weight-bold mb-1" style="font-size: 0.9rem;"><?= $bank['bank_name'] ?></h6>
                                        <div class="font-weight-bold" style="font-size: 0.85rem;"><?= $bank['bank_number'] ?></div>
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">ยกเลิก</button>
                    <button type="button" class="btn btn-primary" style="background-color: #f57421; border-color: #f57421;" onclick="confirmBankSelection()">ยืนยันการเลือก</button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    let tempSelectedBank = null;

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

    $(document).ready(function() {
        $('#payment-form').submit(function(e) {
            e.preventDefault();

            if ($('#customer_bank').val() == '') {
                alert("กรุณาเลือกบัญชีธนาคารที่โอนเงินเข้ามา");
                return;
            }

            start_loader();
            var formData = new FormData(this);
            // ไม่ต้องรวม firstname/lastname แล้ว เพราะใช้ input name="customer_name" โดยตรง

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
</script>