<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<div class="content py-5 px-3 bg-gradient-dark">
    <h2><b>Contact information</b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
    <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12">
        <div class="card rounded-0 shadow">
            <div class="card-body">
                <form action="" id="system-frm">
                    <div class="form-group w-100">
                        <label class="control-label">Help_info</label>
                        <a href="<?php echo base_url ?>./?p=help" class="btn btn-sm btn-secondary ml-2" target="_blank">ดูหน้า Help</a>
                        <textarea name="content[help]" cols="30" rows="6" class="form-control summernote"><?php echo  is_file(base_app . 'help.html') ? file_get_contents(base_app . 'help.html') : "" ?></textarea>
                    </div>
            </div>
            </form>
        </div>
        <div class="card-footer">
            <div class="col-md-12">
                <div class="row">
                    <button class="btn btn-sm btn-dark" form="system-frm">Update Prices</button>
                </div>
            </div>
        </div>

    </div>
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