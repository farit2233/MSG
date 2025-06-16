<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>

<style>
	img#cimg {
		height: 15vh;
		width: 15vh;
		object-fit: cover;
		border-radius: 100% 100%;
	}

	img#cimg2 {
		height: 50vh;
		width: 100%;
		object-fit: contain;
		/* border-radius: 100% 100%; */
	}

	.note-editable {
		font-family: inherit !important;
	}

	.note-dropdown-menu .dropdown-fontsize {
		max-height: 200px;
		overflow-y: auto;
	}
</style>
<div class="col-lg-12">
	<div class="card card-outline rounded-0 card-dark">
		<div class="card-header">
			<h5 class="card-title">System Information</h5>
			<!-- <div class="card-tools">
				<a class="btn btn-block btn-sm btn-default btn-flat border-navy new_department" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
			</div> -->
		</div>
		<div class="card-body">
			<form action="classes/SystemSettings.php?f=update_settings_info" id="system-frm" method="POST" enctype="multipart/form-data">
				<div id="msg" class="form-group"></div>
				<div class="form-group">
					<label for="name" class="control-label">System Name</label>
					<input type="text" class="form-control form-control-sm" name="name" id="name" value="<?php echo $_settings->info('name') ?>">
				</div>
				<div class="form-group">
					<label for="short_name" class="control-label">System Short Name</label>
					<input type="text" class="form-control form-control-sm" name="short_name" id="short_name" value="<?php echo  $_settings->info('short_name') ?>">
				</div>
				<div class="form-group w-100">
					<label class="control-label">About Us</label>
					<a href="<?php echo base_url ?>./?p=about" class="btn btn-sm btn-secondary ml-2" target="_blank">ดูหน้า About</a>
					<textarea name="content[about]" cols="30" rows="6" class="form-control summernote"><?php echo  is_file(base_app . 'about.html') ? file_get_contents(base_app . 'about.html') : "" ?></textarea>
				</div>
				<div class="form-group">
					<label for="" class="control-label">System Logo</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input rounded-circle" id="customFile1" name="img" onchange="displayImg(this,$(this))">
						<label class="custom-file-label" for="customFile1">Choose file</label>
					</div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
				</div>
				<div class="form-group">
					<label for="" class="control-label">Website Cover</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input rounded-circle" id="customFile2" name="cover" onchange="displayImg2(this,$(this))">
						<label class="custom-file-label" for="customFile2">Choose file</label>
					</div>
				</div>
				<div class="form-group d-flex justify-content-center">
					<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="" id="cimg2" class="img-fluid img-thumbnail">
				</div>
				<div class="form-group">
					<label for="" class="control-label">Banner Images</label>
					<div class="custom-file">
						<input type="file" class="custom-file-input rounded-circle" id="customFile3" name="banners[]" multiple accept=".png,.jpg,.jpeg" onchange="displayImg3(this,$(this))">
						<label class="custom-file-label" for="customFile3">Choose file</label>
					</div>
					<small><i>Choose to upload new banner immages</i></small>
				</div>
				<?php
				$upload_path = "uploads/banner";
				if (is_dir(base_app . $upload_path)):
					$file = scandir(base_app . $upload_path);
					foreach ($file as $img):
						if (in_array($img, array('.', '..')))
							continue;


				?>
						<div class="d-flex w-100 align-items-center img-item">
							<span><img src="<?php echo base_url . $upload_path . '/' . $img . "?v=" . (time()) ?>" width="150px" height="100px" style="object-fit:cover;" class="img-thumbnail" alt=""></span>
							<span class="ml-4"><button class="btn btn-sm btn-default text-danger rem_img" type="button" data-path="<?php echo base_app . $upload_path . '/' . $img ?>"><i class="fa fa-trash"></i></button></span>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>
			</form>
		</div>
		<div class="card-footer">
			<div class="col-md-12">
				<div class="row">
					<button class="btn btn-sm btn-dark" form="system-frm">Update</button>
				</div>
			</div>
		</div>

	</div>
</div>
<script>
	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
				_this.siblings('.custom-file-label').html(input.files[0].name)
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function displayImg2(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				_this.siblings('.custom-file-label').html(input.files[0].name)
				$('#cimg2').attr('src', e.target.result);
			}

			reader.readAsDataURL(input.files[0]);
		}
	}

	function displayImg3(input, _this) {
		var fnames = [];
		Object.keys(input.files).map(function(k) {
			fnames.push(input.files[k].name)

		})
		_this.siblings('.custom-file-label').html(fnames.join(", "))
	}

	function delete_img($path) {
		start_loader()

		$.ajax({
			url: _base_url_ + 'classes/Master.php?f=delete_img',
			data: {
				path: $path
			},
			method: 'POST',
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured while deleting an Image", "error");
				end_loader()
			},
			success: function(resp) {
				$('.modal').modal('hide')
				if (typeof resp == 'object' && resp.status == 'success') {
					$('[data-path="' + $path + '"]').closest('.img-item').hide('slow', function() {
						$('[data-path="' + $path + '"]').closest('.img-item').remove()
					})
					alert_toast("Image Successfully Deleted", "success");
				} else {
					console.log(resp)
					alert_toast("An error occured while deleting an Image", "error");
				}
				end_loader()
			}
		})
	}
	$(document).ready(function() {
		$('.rem_img').click(function() {
			_conf("Are sure to delete this image permanently?", 'delete_img', ["'" + $(this).attr('data-path') + "'"])
		})
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
	$('#system-frm').submit(function(e) {
		e.preventDefault(); // ป้องกันไม่ให้รีโหลด
		start_loader(); // โหลดตัวหมุน ถ้ามี

		$.ajax({
			url: $(this).attr('action'),
			method: 'POST',
			data: new FormData(this),
			dataType: 'json',
			contentType: false,
			cache: false,
			processData: false,
			error: err => {
				console.error(err);
				alert_toast("เกิดข้อผิดพลาด", "error");
				end_loader();
			},
			success: function(resp) {
				if (resp.status == 'success') {
					alert_toast("บันทึกข้อมูลเรียบร้อยแล้ว", "success");
					setTimeout(() => location.reload(), 1500);
				} else {
					alert_toast(resp.msg || "ไม่สามารถบันทึกได้", "error");
					end_loader();
				}
			}
		});
	});
</script>