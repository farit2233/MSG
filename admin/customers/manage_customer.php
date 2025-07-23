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
	img#cimg {
		height: 10em;
		width: 10em;
		object-fit: cover;
		border-radius: 100% 100%;
	}

	.bg-gradient-dark-FIXX {
		background-color: #202020;
	}

	.bg-btn-update {
		background: none;
		color: #f57421;
		border: 2px solid #f57421;
		padding: 10px 50px;
		margin-top: 1rem;
		margin-bottom: 1rem;
	}

	.bg-btn-update:hover {
		background-color: #f57421;
		color: white;
		display: inline-block;
	}

	.cart-header-bar {
		border-left: 4px solid #ff6600;
		padding: 16px 20px;
		border-radius: 12px;
		margin-bottom: 24px;
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
	}

	input.form-control {
		border-radius: 13px;
		font-size: 16px;
	}

	label {
		font-size: 18px;
	}

	.custom-input {
		border-radius: 13px;
		font-size: 16px;
	}

	.section-title-with-line h3 {
		position: relative;
		border-bottom: 2px solid #f57421 !important;
		padding-bottom: 0.5rem;
		margin-bottom: 1rem;

	}

	.section-title-with-line h3::after {
		content: none !important;
		/* ปิดการสร้างเส้นซ้อนแบบ pseudo element */
	}

	@media (max-width: 767.98px) {
		.bg-btn-update {
			width: 100%;
		}
	}
</style>
<div class="card rounded-1">
	<div class="cart-header-bar">
		<h3 class="mb-0"><i class="fa-solid fa-pen-to-square"></i> แก้ไขข้อมูลส่วนตัว</h3>
	</div>
	<div class="card-body">
		<form id="update-form" action="" method="post">
			<div class="section-title-with-line mb-4">
				<h3>โปรไฟล์</h3>
			</div>
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<div class="form-group d-flex justify-content-center mt-2">
							<img src="<?php echo validate_image(isset($avatar) ? $avatar : '') ?>" alt="" id="cimg"
								class="img-fluid img-thumbnail" style="max-width: 200px;">
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
			<div class="section-title-with-line mb-4">
				<h3>ข้อมูลส่วนตัว</h3>
			</div>
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="firstname" class="control-label">ชื่อ</label>
						<input type="text" class="form-control form-control-sm" required name="firstname" id="firstname" value="<?= isset($firstname) ? $firstname : '' ?>">
					</div>
					<div class="form-group">
						<label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
						<input type="text" class="form-control form-control-sm" name="middlename" id="middlename" value="<?= isset($middlename) ? $middlename : '' ?>">
					</div>
					<div class="form-group">
						<label for="lastname" class="control-label">นามสกุล</label>
						<input type="text" class="form-control form-control-sm" required name="lastname" id="lastname" value="<?= isset($lastname) ? $lastname : '' ?>">
					</div>
					<div class="form-group">
						<label for="gender" class="control-label custom-input">เพศ</label>
						<select class="custom-select custom-select-sm custom-input" reqiured="" name="gender" id="gender">
							<option value="Male" <?= isset($gender) && $gender == 'Male' ? "selected" : '' ?>>ชาย</option>
							<option value="Female" <?= isset($gender) && $gender == 'Female' ? "selected" : '' ?>>หญิง</option>
						</select>
					</div>
				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="email" class="control-label">Email</label>
						<input type="text" class="form-control form-control-sm" required name="email" id="email" value="<?= isset($email) ? $email : '' ?>">
					</div>
					<div class="form-group">
						<label for="contact" class="control-label">เบอร์โทร</label>
						<input type="text" class="form-control form-control-sm" required name="contact" id="contact" value="<?= isset($contact) ? $contact : '' ?>">
					</div>
					<div class="form-group">
						<label for="password" class="control-label">รหัสผ่านใหม่</label>
						<input type="password" class="form-control form-control-sm" name="password" id="password">
					</div>
					<div class="form-group">
						<label for="cpassword" class="control-label">ยืนยัน รหัสผ่านใหม่</label>
						<input type="password" class="form-control form-control-sm" id="cpassword">
					</div>
				</div>
			</div>
			<div class="section-title-with-line mb-4">
				<h3>ที่อยู่จัดส่ง</h3>
			</div>
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="address" class="control-label">บ้านเลขที่ ถนน</label>
						<input type="text" class="form-control form-control-sm" name="address" id="address" value="<?= isset($address) ? $address : '' ?>">
					</div>

					<div class="form-group">
						<label for="city" class="control-label">ตำบล</label>
						<input type="text" class="form-control form-control-sm" name="sub_district" id="sub_district" value="<?= isset($sub_district) ? $sub_district : '' ?>">
					</div>
					<div class="form-group">
						<label for="city" class="control-label">อำเภอ</label>
						<input type="text" class="form-control form-control-sm" name="district" id="district" value="<?= isset($district) ? $district : '' ?>">
					</div>
				</div>

				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="state_province" class="control-label">จังหวัด</label>
						<input type="text" class="form-control form-control-sm" name="province" id="province" value="<?= isset($province) ? $province : '' ?>">
					</div>
					<div class="form-group">
						<label for="postal_code" class="control-label">รหัสไปรษณีย์</label>
						<input type="text" class="form-control form-control-sm" name="postal_code" id="postal_code" value="<?= isset($postal_code) ? $postal_code : '' ?>">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-8 col-12 text-md-left text-center mb-2 mb-md-0">
				</div>
				<div class="col-md-4 col-12 text-md-right text-center">
					<button type="submit" class="btn bg-btn-update btn-block rounded-pill">บันทึกข้อมูล</button>
				</div>
			</div>
		</form>
	</div>
</div>

<script>
	function displayImg(input, _this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		} else {
			// ปรับปรุงให้ใช้ข้อมูล avatar ที่มีอยู่ หากไม่ได้เลือกรูปใหม่
			$('#cimg').attr('src', "<?php echo validate_image(isset($avatar) ? $avatar : '') ?>");
		}
	}
	$(document).ready(function() {
		end_loader();
		$('.pass_view').click(function() {
			var input = $(this).siblings('input')
			var type = input.attr('type')
			if (type == 'password') {
				$(this).html('<i class="fa fa-eye"></i>')
				input.attr('type', 'text').focus()
			} else {
				$(this).html('<i class="fa fa-eye-slash"></i>')
				input.attr('type', 'password').focus()
			}
		})
		$('#update-form').submit(function(e) {
			e.preventDefault();
			var _this = $(this)
			var el = $('<div>')
			el.addClass('alert alert-danger err_msg')
			el.hide()
			$('.err_msg').remove()
			// เพิ่มการตรวจสอบว่ารหัสผ่านตรงกันหรือไม่
			if ($('#password').val() != $('#cpassword').val()) {
				el.text('รหัสผ่านไม่ตรงกัน')
				_this.prepend(el)
				el.show('slow')
				$('html, body').scrollTop(0)
				return false;
			}
			if (_this[0].checkValidity() == false) {
				_this[0].reportValidity();
				return false;
			}
			start_loader()
			$.ajax({
				url: _base_url_ + "classes/Users.php?f=registration", // ใช้ URL เดิม
				method: 'POST',
				type: 'POST',
				data: new FormData($(this)[0]),
				dataType: 'json',
				cache: false,
				processData: false,
				contentType: false,
				error: err => {
					console.log(err)
					alert('An error occurred')
					end_loader()
				},
				success: function(resp) {
					if (resp.status == 'success') {
						location.reload()
					} else if (!!resp.msg) {
						el.html(resp.msg)
						el.show('slow')
						_this.prepend(el)
						$('html, body').scrollTop(0)
					} else {
						alert('An error occurred')
						console.log(resp)
					}
					end_loader()
				}
			})
		})
	})
</script>