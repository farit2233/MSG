	<?php
	// คงการดึงข้อมูลจาก id ที่ส่งมาใน URL ของหน้าแรกไว้
	if (isset($_GET['id'])) {
		$user = $conn->query("SELECT * FROM customer_list where id ='{$_GET['id']}' ");
		foreach ($user->fetch_array() as $k => $v) {
			if (!is_numeric($k))
				$$k = $v;
		}
	}
	?>
	<?php if ($_settings->chk_flashdata('success')): ?>
		<script>
			alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
		</script>
	<?php endif; ?>

	<style>
		/* --- Styles from Registration page theme --- */
		.profile-page {
			padding-top: 0px !important;
			background-size: cover;
			background-repeat: no-repeat;
			overflow-x: hidden;
		}

		.profile-page label {
			font-size: 18px;
		}

		.profile-page input,
		.profile-page select {
			border-radius: 13px;
			font-size: 16px;
		}

		.profile-page .custom-file-label {
			border-radius: 13px;
			font-size: 16px !important;
		}

		.profile-section-title-with-line h3 {
			position: relative;
			border-bottom: 2px solid #f57421 !important;
			padding-bottom: 0.5rem;
			margin-bottom: 1rem;
		}

		.profile-section-title-with-line h3::after {
			content: none !important;
		}

		.profile-cart-header-bar {
			border-left: 4px solid #ff6600;
			padding: 16px 20px;
			border-radius: 12px;
			margin-bottom: 24px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
		}

		.profile-card {
			border-radius: 13px;
			max-width: 767.98px;
			margin: auto;
			width: 100%;
		}

		img#cimg {
			height: 10em;
			width: 10em;
			object-fit: cover;
			border-radius: 100%;
			border: 3px solid #f57421;
			padding: 4px;
		}

		.custom-file-input:focus~.custom-file-label {
			border-color: #f57421;
			box-shadow: 0 0 0 0.2rem rgba(245, 116, 33, 0.25);
		}

		.btn-update {
			background: none;
			color: #f57421;
			border: 2px solid #f57421;
			padding: 10px 10px;
			margin-top: 1rem;
			margin-bottom: 1rem;
			width: 100%;
		}

		.btn-update:hover {
			background-color: #f57421;
			color: white;
		}

		#cropModal .modal-dialog-user {
			position: fixed !important;
			/* ทำให้ dialog ล็อกตาม viewport */
			top: 50%;
			/* กึ่งกลางแนวตั้ง */
			left: 50%;
			/* กึ่งกลางแนวนอน */
			transform: translate(-50%, -50%);
			/* ปรับให้ตรงกลางพอดี */
			max-width: 600px;
			width: 100%;
			margin: 0;
			max-height: 100px;
			height: 100%;
			/* ไม่ให้ Bootstrap ใส่ margin */
		}

		#cropModal .modal-content {
			border-radius: 15px;
			overflow: hidden;
		}

		.img-container {
			max-height: 500px;
			overflow: hidden;
			background-color: #2c2f33;
			display: flex;
			justify-content: center;
			align-items: center;
		}

		.img-container img {
			max-width: 100%;
		}

		.cropper-view-box,
		.cropper-face {
			border-radius: 50%;
		}

		.cropper-container .cropper-move {
			cursor: grab !important;
		}

		.cropper-container .cropper-move:active {
			cursor: grabbing !important;
		}

		#crop_button {
			background-color: #f57421;
			border-color: #f57421;
		}

		.zoom-controls {
			display: flex;
			align-items: center;
			justify-content: center;
			gap: 10px;
			margin-top: 15px;
		}

		.zoom-controls i {
			color: #6c757d;
		}

		#zoom_slider {
			width: 70%;
			cursor: pointer;
		}

		input[type=range].form-control-range::-webkit-slider-thumb {
			background: #f57421;
		}

		input[type=range].form-control-range::-moz-range-thumb {
			background: #f57421;
		}

		#password {
			border: none;
			/* ลบกรอบ */
			background: transparent;
			/* กำหนดให้พื้นหลังเป็นโปร่งใส */
			padding: 10px 15px;
			/* เพิ่มระยะห่างข้างใน */
			font-size: 16px;
			/* ขนาดตัวอักษร */

		}

		#password:focus {
			outline: none;
			/* ลบกรอบที่แสดงเวลาโฟกัส */
			text-decoration: underline !important;
			/* เพิ่มเส้นใต้เมื่อมีการ focus */
		}
	</style>
	<section class="py-3 profile-page">
		<div class="container">
			<div class="row mt-n4 justify-content-center align-items-center flex-column">
				<div class="col-lg-10 col-md-11 col-sm-12 col-xs-12">
					<div class="card profile-card shadow" style="margin-top: 3rem;">
						<div class="card-body">
							<div class="container-fluid">
								<div class="profile-cart-header-bar">
									<h3 class="mb-0"><i class="fa-solid fa-pen-to-square"></i><?= isset($id) ? "แก้ไขข้อมูลบัญชี" : "สร้างบัญชีใหม่" ?></h3>
								</div>
								<form id="update-form" action="" method="post">
									<div class="profile-section-title-with-line mb-4">
										<h3>โปรไฟล์</h3>
									</div>
									<div class="row justify-content-center">
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group">
												<div class="form-group d-flex justify-content-center mt-2">
													<img src="<?php echo validate_image(isset($avatar) ? $avatar : '') ?>" alt="Avatar Preview" id="cimg" class="img-fluid">
												</div>
												<div class="custom-file custom-input">
													<input type="file" class="custom-file-input custom-input" id="customFile" name="img"
														onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
													<label class="custom-file-label custom-input" for="customFile">เลือกรูปจากไฟล์ในเครื่อง</label>

												</div>
											</div>
										</div>
									</div>

									<input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
									<input type="hidden" name="cropped_image" id="cropped_image">

									<div class="profile-section-title-with-line mb-4">
										<h3>ข้อมูลส่วนตัว</h3>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="firstname" class="control-label">ชื่อ</label>
												<input type="text" class="form-control " required name="firstname" id="firstname" value="<?= isset($firstname) ? $firstname : '' ?>">
											</div>
											<div class="form-group">
												<label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
												<input type="text" class="form-control " name="middlename" id="middlename" value="<?= isset($middlename) ? $middlename : '' ?>">
											</div>
											<div class="form-group">
												<label for="lastname" class="control-label">นามสกุล</label>
												<input type="text" class="form-control " required name="lastname" id="lastname" value="<?= isset($lastname) ? $lastname : '' ?>">
											</div>
											<div class="form-group">
												<label for="gender" class="control-label custom-input">เพศ</label>
												<select class="custom-select custom-input" reqiured="" name="gender" id="gender">
													<option value="Male" <?= isset($gender) && $gender == 'Male' ? "selected" : '' ?>>ชาย</option>
													<option value="Female" <?= isset($gender) && $gender == 'Female' ? "selected" : '' ?>>หญิง</option>
												</select>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="email" class="control-label">Email</label>
												<input type="text" class="form-control " required name="email" id="email" value="<?= isset($email) ? $email : '' ?>">
											</div>
											<div class="form-group">
												<label for="contact" class="control-label">เบอร์โทร</label>
												<input type="text" class="form-control " required name="contact" id="contact" value="<?= isset($contact) ? $contact : '' ?>">
											</div>
											<!--div class="form-group">
												<label for="password" class="control-label">รหัสผ่านใหม่</label>
												<input type="password" class="form-control " name="password" id="password">
											</div>
											<div class="form-group">
												<label for="cpassword" class="control-label">ยืนยัน รหัสผ่านใหม่</label>
												<input type="password" class="form-control " id="cpassword">
											</div-->
											<div class="form-group">
												<label for="contact" class="control-label">รหัสผ่าน</label>
												<a class="form-control" type="button" id="password" data-id="<?= isset($id) ? $id : '' ?>">เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i></a>
											</div>
										</div>
									</div>
									<div class="profile-section-title-with-line mb-4">
										<h3>ที่อยู่จัดส่ง</h3>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="address" class="control-label">บ้านเลขที่ ถนน</label>
												<input type="text" class="form-control " name="address" id="address" value="<?= isset($address) ? $address : '' ?>">
											</div>

											<div class="form-group">
												<label for="city" class="control-label">ตำบล</label>
												<input type="text" class="form-control " name="sub_district" id="sub_district" value="<?= isset($sub_district) ? $sub_district : '' ?>">
											</div>
											<div class="form-group">
												<label for="city" class="control-label">อำเภอ</label>
												<input type="text" class="form-control " name="district" id="district" value="<?= isset($district) ? $district : '' ?>">
											</div>
										</div>

										<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
											<div class="form-group">
												<label for="state_province" class="control-label">จังหวัด</label>
												<input type="text" class="form-control " name="province" id="province" value="<?= isset($province) ? $province : '' ?>">
											</div>
											<div class="form-group">
												<label for="postal_code" class="control-label">รหัสไปรษณีย์</label>
												<input type="text" class="form-control " name="postal_code" id="postal_code" value="<?= isset($postal_code) ? $postal_code : '' ?>">
											</div>
										</div>
									</div>
									<div class="row justify-content-center">
										<div class="col-md-5 col-12 text-center">
											<button type="submit" class="btn btn-update btn-block rounded-pill">บันทึกการเปลี่ยนแปลง</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<script>
		$(document).ready(function() {
			end_loader();
			$('#password').click(function() {
				var userId = $(this).data('id'); // ดึงค่า id จาก data-id ของปุ่ม
				modal_confirm('เปลี่ยนรหัสผ่าน <i class="fa fa-pencil"></i>', 'customers/password.php?id=' + userId); // ส่งไปที่ modal
			});
			// --- Form Submission Logic ---
			$('#update-form').submit(function(e) {
				e.preventDefault();
				var _this = $(this);
				var el = $('<div>');
				el.addClass('alert alert-danger err_msg');
				el.hide();
				$('.err_msg').remove();

				if ($('#password').val() != '' && $('#password').val() != $('#cpassword').val()) {
					el.text('รหัสผ่านใหม่ไม่ตรงกัน');
					_this.prepend(el);
					el.show('slow');
					$('html, body').scrollTop(0);
					return false;
				}

				if (_this[0].checkValidity() == false) {
					_this[0].reportValidity();
					return false;
				}

				start_loader();
				var formData = new FormData($(this)[0]);

				// เช็คว่ามีการเลือกไฟล์ใหม่ไหม ถ้าไม่มีให้ส่งรูปเก่าที่มี
				if (!$('#customFile').val()) {
					formData.append('cropped_image', $('#cropped_image').val()); // รูปเก่าที่ถูกบันทึกไว้
				}

				$.ajax({
					url: _base_url_ + "classes/Users.php?f=registration", // Make sure this endpoint is correct for updates
					method: 'POST',
					data: formData,
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					error: err => {
						console.log(err);
						alert('An error occurred');
						end_loader();
					},
					success: function(resp) {
						if (resp.status == 'success') {
							location.replace('./?page=customers');
						} else if (!!resp.msg) {
							el.html(resp.msg);
							el.show('slow');
							_this.prepend(el);
							$('html, body').scrollTop(0);
						} else {
							alert('An error occurred');
							console.log(resp);
						}
						end_loader();
					}
				});
			});

			// --- Cropper.js Logic ---
			var $modal = $('#cropModal');
			var image = document.getElementById('image_to_crop');
			var cropper;
			var zoomSlider = document.getElementById('zoom_slider');

			function resizeImage(file, maxWidth, maxHeight, callback) {
				var reader = new FileReader();
				reader.onload = function(event) {
					var img = new Image();
					img.onload = function() {
						var canvas = document.createElement('canvas');
						var ctx = canvas.getContext('2d');
						var width = img.width;
						var height = img.height;

						// คำนวณขนาดใหม่
						if (width > height) {
							if (width > maxWidth) {
								height = Math.round(height * (maxWidth / width));
								width = maxWidth;
							}
						} else {
							if (height > maxHeight) {
								width = Math.round(width * (maxHeight / height));
								height = maxHeight;
							}
						}

						// กำหนดขนาดใหม่ให้กับ canvas
						canvas.width = width;
						canvas.height = height;
						ctx.drawImage(img, 0, 0, width, height);
						callback(canvas.toDataURL('image/jpeg'));
					};
					img.src = event.target.result;
				};
				reader.readAsDataURL(file);
			}

			$('#customFile').on('change', function(e) {
				var files = e.target.files;
				if (files && files.length > 0) {
					var reader = new FileReader();
					reader.onload = function(event) {
						image.src = event.target.result;
						$modal.modal({
							backdrop: 'static',
							keyboard: false,
							show: true
						});
					};
					reader.readAsDataURL(files[0]);
					var fileName = $(this).val().split('\\').pop();
					$(this).next('.custom-file-label').html(fileName);
				}
			});

			$modal.on('shown.bs.modal', function() {
				cropper = new Cropper(image, {
					aspectRatio: 1,
					viewMode: 1,
					dragMode: 'move',
					cropBoxMovable: false,
					cropBoxResizable: false,
					wheelZoomRatio: 0,
					background: false,
					responsive: true,
					autoCropArea: 1,
					ready: function() {
						let canvasData = cropper.getCanvasData();
						let initialZoom = canvasData.width / canvasData.naturalWidth;
						zoomSlider.min = initialZoom;
						zoomSlider.max = initialZoom * 3;
						zoomSlider.value = initialZoom;
					}
				});
			}).on('hidden.bs.modal', function() {
				cropper.destroy();
				cropper = null;
				$('#customFile').val('');
				$('#customFile').next('.custom-file-label').html('เปลี่ยนรูปโปรไฟล์');
			});

			zoomSlider.addEventListener('input', function() {
				if (cropper) {
					cropper.zoomTo(this.value);
				}
			});

			$('#crop_button').on('click', function() {
				var canvas = cropper.getCroppedCanvas({
					width: 400,
					height: 400,
					imageSmoothingQuality: 'high',
				});

				var base64data = canvas.toDataURL('image/jpeg');
				$('#cimg').attr('src', base64data);
				$('#cropped_image').val(base64data);
				$modal.modal('hide');
			});
		});
	</script>