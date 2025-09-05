<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
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
<section class="card card-outline rounded-0 card-dark">
    <div class="card-header">
        <div class="card-title">หน้าช่วยเหลือ</div>
    </div>
    <div class="card-body">
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <div class="card-title" style="font-size: 18px !important;">รายละเอียดหน้าช่วยเหลือ</div>
            </div>
            <div class=" card-body">
                <div class="container-fluid">
                    <form action="" id="system-frm">
                        <div class="form-group w-100">
                            <a href="<?php echo base_url ?>./?p=help" class="text-dark ml-2" target="_blank"><i class="fa-solid fa-eye"></i> ดูหน้าช่วยเหลือ</a>
                            <textarea name="content[help]" cols="30" rows="6" class="form-control summernote"><?php echo  is_file(base_app . 'help.html') ? file_get_contents(base_app . 'help.html') : "" ?></textarea>
                        </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <div class="card-footer py-1 text-center">
        <a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
        <button class="btn btn-success btn-sm btn-flat" form="system-frm"><i class="fa fa-save"></i> บันทึก</button>
    </div>
</section>

<script>
    $(document).ready(function() {
        let formChanged = false;
        let initialContent = $('.summernote').val(); // เก็บค่าเริ่มต้นของ textarea

        // ตรวจสอบการเปลี่ยนแปลงของฟอร์ม
        $('#system-frm input, #system-frm textarea').on('input', function() {
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

        // เพิ่มเติมสำหรับ summernote ให้เก็บข้อมูลก่อนการรีเฟรช
        $('.summernote').summernote({
            height: 400,
            fontSizes: [
                '8', '10', '12', '14', '16', '18',
                '20', '24', '28', '32',
                '36', '48', '60', '72', '96'
            ],
            toolbar: [
                ['font', ['fontsize', 'bold', 'italic', 'underline']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture']],
                ['view', ['fullscreen', 'codeview']]
            ],
            callbacks: {
                onChange: function(contents, $editable) {
                    // ตรวจจับการเปลี่ยนแปลงเนื้อหาใน summernote
                    formChanged = true;
                },
                onInit: function() {
                    const editor = $(this);
                    editor.on('mouseup keyup', function() {
                        const selection = window.getSelection();
                        if (!selection.rangeCount) return;

                        const range = selection.getRangeAt(0);
                        const parent = $(range.commonAncestorContainer).closest('.note-editable');

                        if (parent.length > 0) {
                            parent.find('span[style*="font-size"]').each(function() {
                                const html = $(this).html();
                                $(this).replaceWith(html); // ล้าง span เดิม เพื่อไม่ให้ซ้อน style
                            });
                        }
                    });
                }
            }
        });
    });
</script>