<?php
if (isset($_GET['id'])) {
	$user = $conn->query("SELECT * FROM users where id ='{$_GET['id']}' ");
	foreach ($user->fetch_array() as $k => $v) {
		$meta[$k] = $v;
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
		<form id="manage-user" action="" method="post">
			<input type="hidden" name="id" value="<?= isset($meta['id']) ? $meta['id'] : '' ?>">
			<div class="section-title-with-line mb-4">
				<h3>โปรไฟล์</h3>
			</div>
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<div class="form-group d-flex justify-content-center mt-2">
							<img src="<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] : '') ?>" alt="" id="cimg"
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
			<div class="section-title-with-line mb-4">
				<h3>ข้อมูลส่วนตัว</h3>
			</div>
			<div class="row">
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="firstname" class="control-label">ชื่อ</label>
						<input type="text" class="form-control form-control-sm" required name="firstname" id="firstname" value="<?php echo isset($meta['firstname']) ? $meta['firstname'] : '' ?>">
					</div>
					<div class="form-group">
						<label for="middlename" class="control-label">ชื่อกลาง (ถ้ามี)</label>
						<input type="text" class="form-control form-control-sm" name="middlename" id="middlename" value="<?php echo isset($meta['middlename']) ? $meta['middlename'] : '' ?>">
					</div>
					<div class="form-group">
						<label for="lastname" class="control-label">นามสกุล</label>
						<input type="text" class="form-control form-control-sm" required name="lastname" id="lastname" value="<?php echo isset($meta['lastname']) ? $meta['lastname'] : '' ?>">
					</div>

				</div>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="form-group">
						<label for="type" class="control-label">Type</label>
						<select name="type" id="type" class="form-control form-control-sm rounded-0" required>
							<option value="1" <?php echo isset($meta['type']) && $meta['type'] == 1 ? 'selected' : '' ?>>Administrator</option>
							<option value="2" <?php echo isset($meta['type']) && $meta['type'] == 2 ? 'selected' : '' ?>>Staff</option>
						</select>
					</div>
					<div class="form-group">
						<label for="username">Username</label>
						<input type="text" name="username" id="username" class="form-control" value="<?php echo isset($meta['username']) ? $meta['username'] : '' ?>" required autocomplete="off">
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
			$('#cimg').attr('src', "<?php echo validate_image(isset($meta['avatar']) ? $meta['avatar'] : '') ?>");
		}
	}
	$('#manage-user').submit(function(e) {
		e.preventDefault();
		start_loader()
		$.ajax({
			url: _base_url_ + 'classes/Users.php?f=save',
			data: new FormData($(this)[0]),
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			type: 'POST',
			success: function(resp) {
				if (resp == 1) {
					location.href = './?page=user/list'
				} else {
					$('#msg').html('<div class="alert alert-danger">Username already exist</div>')
					end_loader()
				}
			}
		})
	})
</script>