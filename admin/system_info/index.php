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

	.card-title {
		font-size: 20px !important;
		font-weight: bold;
	}

	.head-label {
		font-size: 17px;
	}

	.text-size-input {
		font-size: 16px;
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

	/* --- CSS สำหรับปรับปรุง Grid แสดงภาพสไลด์ --- */
	.banner-grid-container {
		display: flex;
		flex-wrap: wrap;
		/* ทำให้รูปภาพขึ้นบรรทัดใหม่เมื่อไม่พอ */
		gap: 15px;
		/* ระยะห่างระหว่างรูป */
		border: 1px solid #ddd;
		border-radius: 5px;
		padding: 15px;
		background-color: #f9f9f9;
		min-height: 120px;
		/* ความสูงขั้นต่ำเผื่อไว้ตอนไม่มีรูป */
		align-content: flex-start;
	}

	.banner-thumb {
		position: relative;
		/* สำหรับจัดตำแหน่งปุ่มลบ */
		width: 150px;
		/* ขนาดความกว้างของกรอบรูป */
		height: 100px;
		/* ขนาดความสูงของกรอบรูป */
		border: 1px solid #ccc;
		border-radius: 4px;
		overflow: hidden;
		/* ซ่อนส่วนเกินของรูป */
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		display: block;
		/* แก้ไขการแสดงผลจาก d-flex เดิม */
	}

	.banner-thumb img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		/* ทำให้รูปภาพเต็มกรอบโดยไม่เสียสัดส่วน */
	}

	.banner-thumb .rem_img {
		position: absolute;
		/* จัดตำแหน่งปุ่มลบให้อยู่มุมบนขวา */
		top: 5px;
		right: 5px;
		background-color: rgba(220, 53, 69, 0.9);
		/* สีแดงโปร่งแสง */
		border: none;
		color: white;
		border-radius: 50%;
		/* ทำให้ปุ่มเป็นวงกลม */
		width: 30px;
		height: 30px;
		line-height: 1;
		padding: 0;
		opacity: 1;
		/* ซ่อนปุ่มไว้ก่อน */
		transition: opacity 0.3s ease;
		/* เพิ่มอนิเมชั่นตอนแสดง/ซ่อน */
		font-size: 14px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	/* ----------------------------------------- */
</style>
<section class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<div class="card-title">ตั้งค่าหน้าเว็บ</div>
	</div>
	<div class="card-body">
		<div class="card card-outline rounded-0 card-dark">
			<div class="card-header">
				<div class="card-title" style="font-size: 18px !important;">รายละเอียดเว็บ</div>
				<!-- <div class=" card-tools">
					<a class="btn btn-block btn-sm btn-default btn-flat border-navy new_department" href="javascript:void(0)"><i class="fa fa-plus"></i> Add New</a>
				</div> -->
			</div>
			<div class="card-body">
				<form action="<?php echo base_url ?>classes/SystemSettings.php?f=update_settings_info" id="system-frm" method="POST" enctype="multipart/form-data">
					<div id="msg" class="form-group"></div>
					<div class="form-group">
						<label for="name" class="control-label head-label">System Name</label>
						<input type="text" class="form-control form-control-sm text-size-input" name="name" id="name" value="<?php echo $_settings->info('name') ?>">
					</div>
					<div class="form-group">
						<label for="short_name" class="control-label head-label">System Short Name</label>
						<input type="text" class="form-control form-control-sm text-size-input" name="short_name" id="short_name" value="<?php echo  $_settings->info('short_name') ?>">
					</div>
					<div class="form-group w-100">
						<a href="<?php echo base_url ?>./?p=about" class="text-dark ml-2" target="_blank"><i class="fa-solid fa-eye"></i> ดูหน้าเกี่ยวกับเรา</a>
						<textarea name="content[about]" cols="30" rows="6" class="form-control summernote"><?php echo  is_file(base_app . 'about.html') ? file_get_contents(base_app . 'about.html') : "" ?></textarea>
					</div>
					<div class="form-group">
						<label for="" class="control-label head-label">โลโก้เว็บ <small>200x200px</small></label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile1" name="img" onchange="displayImg(this,$(this))">
							<label class="custom-file-label text-size-input" for="customFile1">เลือกไฟล์ </label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img src="<?php echo validate_image($_settings->info('logo')) ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
					</div>
					<div class="form-group">
						<label for="" class="control-labe head-label">ปกเว็บไซต์</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile2" name="cover" onchange="displayImg2(this,$(this))">
							<label class="custom-file-label text-size-input" for="customFile2">เลือกไฟล์ </label>
						</div>
					</div>
					<div class="form-group d-flex justify-content-center">
						<img src="<?php echo validate_image($_settings->info('cover')) ?>" alt="" id="cimg2" class="img-fluid img-thumbnail">
					</div>
					<div class="form-group">
						<label for="" class="control-label head-label">ภาพสไลด์หน้าเว็บ <small>1920x600px</small></label>
						<div class="custom-file">
							<input type="file" class="custom-file-input rounded-circle" id="customFile3" name="banners[]" multiple accept=".png,.jpg,.jpeg" onchange="displayImg3(this,$(this))">
							<label class="custom-file-label text-size-input" for="customFile3">เลือกไฟล์ </label>
						</div>
						<small><i>เลือกไฟล์รูปเพื่ออัปโหลดไปยังภาพสไลด์หน้าเว็บ</i></small>
					</div>

					<div class="form-group">
						<label class="control-label head-label">ภาพสไลด์ที่มีอยู่</label>
						<div class="banner-grid-container">
							<?php
							$upload_path = "uploads/banner";
							if (is_dir(base_app . $upload_path)):
								$file = scandir(base_app . $upload_path);
								$has_images = false;
								foreach ($file as $img):
									if (in_array($img, array('.', '..')))
										continue;
									$has_images = true;
							?>
									<div class="img-item banner-thumb">
										<img src="<?php echo base_url . $upload_path . '/' . $img . "?v=" . (time()) ?>" class="img-thumbnail" alt="Banner Image">
										<button class="btn btn-sm btn-danger rem_img" type="button" data-path="<?php echo base_app . $upload_path . '/' . $img ?>" title="ลบรูปภาพนี้">
											<i class="fa fa-trash"></i>
										</button>
									</div>
								<?php endforeach; ?>

								<?php if (!$has_images): ?>
									<p class="text-muted w-100 text-center" style="margin-top: 10px;">-- ยังไม่มีภาพสไลด์ --</p>
								<?php endif; ?>
							<?php else: ?>
								<p class="text-muted w-100 text-center" style="margin-top: 10px;">-- ไม่พบโฟลเดอร์ <?php echo $upload_path ?> --</p>
							<?php endif; ?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="card-footer py-1 text-center">
		<a class="btn btn-secondary btn-sm border btn-flat" href="javascript:void(0)" id="cancelBtn"><i class="fa fa-times"></i> ยกเลิก</a>
		<button type="button" id="save-btn" class="btn btn-success btn-sm btn-flat"><i class="fa fa-save"></i> บันทึก</button>
	</div>
</section>
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
	// ดักฟังการคลิกที่ปุ่มใหม่ของเรา
	$('#save-btn').click(function() {
		// สั่งให้ฟอร์มทำงาน (แต่ไม่ submit แบบดั้งเดิม)
		var form = $('#system-frm')[0];
		var formData = new FormData(form);

		start_loader(); // โหลดตัวหมุน ถ้ามี

		// ปิดปุ่มบันทึกเพื่อป้องกันการกดซ้ำ
		$(this).prop('disabled', true);

		$.ajax({
			url: $('#system-frm').attr('action'), // ดึง url จากฟอร์ม
			method: 'POST',
			data: formData, // ใช้ formData ที่เราสร้าง
			dataType: 'json',
			contentType: false,
			cache: false,
			processData: false,
			error: err => {
				console.error(err);
				alert_toast("เกิดข้อผิดพลาด", "error");
				$('#save-btn').prop('disabled', false); // เปิดปุ่มคืนถ้า error
				end_loader();
			},
			success: function(resp) {
				if (resp.status == 'success') {
					alert_toast("บันทึกข้อมูลเรียบร้อยแล้ว", "success");
					setTimeout(() => location.reload(), 1500);
				} else {
					alert_toast(resp.msg || "ไม่สามารถบันทึกได้", "error");
					$('#save-btn').prop('disabled', false); // เปิดปุ่มคืนถ้าไม่สำเร็จ
					end_loader();
				}
			}
		});
	});
</script>