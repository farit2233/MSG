<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<div class="card card-outline rounded-0 card-orange">
    <div class="card-header">
        <h4 class=" text-bold">หน้าช่วยเหลือ</h4>
    </div>
    <div class="card-body">
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <h5 class="text-bold">รายละเอียดหน้าช่วยเหลือ</h5>
            </div>
            <div class="card-body">
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
        <button class="btn btn-success btn-sm btn-flat" form="system-frm"><i class="fa fa-save"></i> บันทึก</button>
        <a class="btn btn-danger btn-sm border btn-flat btn-foot" href="./?page=system_info/help_info"><i class="fa fa-times"></i> ยกเลิก</a>
        <a class="btn btn-light btn-sm border btn-flat btn-foot" href="./?page=home"><i class="fa fa-angle-left"></i> กลับสู่หน้าหลัก</a>
    </div>
</div>

<script>
    $(document).ready(function() {
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

    })
</script>