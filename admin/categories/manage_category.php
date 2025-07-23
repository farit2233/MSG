<?php
$product_types_result = $conn->query("SELECT id, name FROM `product_type` WHERE status = 1 AND delete_flag = 0 ORDER BY name ASC");


if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `category_list` where id = '{$_GET['id']}' ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			// ทำให้ตัวแปร $id, $product_type_id, $name, $description, $status ถูกสร้างขึ้นโดยอัตโนมัติ
			$$k = $v;
		}
	}
}
?>
<style>
	.card-title {
		font-size: 20px !important;
		font-weight: bold;
	}

	section {
		font-size: 16px;
	}
</style>
<section class="card card-outline card-orange rounded-0">
	<div class="card-header">
		<div class="card-title"><?= isset($id) ? "แก้ไขหมวดหมู่" : "สร้างหมวดหมู่ใหม่" ?></div>
	</div>
	<form action="" id="category-form">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
		<div class="card-body">
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title" style="font-size: 18px !important;">ข้อมูลหมวดหมู่</div>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label>ชื่อหมวดหมู่ <span class="text-danger">*</span></label>
						<input type="text" name="name" id="name" class="form-control" value="<?php echo isset($name) ? $name : ''; ?>" required />
					</div>
					<div class="form-group">
						<label for="description">รายละเอียด</label>
						<textarea rows="3" name="description" id="description" class="form-control"><?php echo isset($description) ? $description : ''; ?></textarea>
					</div>
					<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<label for="product_type_id" class="control-label">เลือกประเภทสินค้า</label>
						<?php
						$product_types_result = $conn->query("SELECT id, name FROM product_type WHERE status = 1 ORDER BY id ASC");
						?>

						<select name="product_type_id" id="product_type_id" class="form-control form-control-sm rounded-0" required>
							<option value="" disabled <?= !isset($product_type_id) ? 'selected' : '' ?>>-- กรุณาเลือก --</option>
							<?php
							while ($pt_row = $product_types_result->fetch_assoc()) :
							?>
								<option value="<?= $pt_row['id'] ?>" <?= (isset($product_type_id) && $product_type_id == $pt_row['id']) ? 'selected' : '' ?>>
									<?= $pt_row['name'] ?>
								</option>
							<?php endwhile; ?>
						</select>
					</div>
				</div>
			</div>
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title" style="font-size: 18px !important;">สถานะการขาย</div>
				</div>
				<div class="card-body">
					<input type="hidden" name="status" value="0">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= (isset($status) && $status == 1) ? 'checked' : '' ?>>
						<label class="custom-control-label" for="status">เปิด/ปิดการใช้งานประเภทสินค้า</label>
					</div>
				</div>
			</div>
		</div>
	</form>
	<div class="card-footer py-1 text-center">
		<button class="btn btn-success btn-sm btn-flat" form="category-form"><i class="fa fa-save"></i> บันทึก</button>
		<a class="btn btn-danger btn-sm border btn-flat" href="./?page=categories"><i class="fa fa-times"></i> ยกเลิก</a>
		<a class="btn btn-light btn-sm border btn-flat" href="./?page=categories"><i class="fa fa-angle-left"></i> กลับ</a>
	</div>
</section>

<script>
	$(document).ready(function() {
		$('#category-form').submit(function(e) {
			e.preventDefault();
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_category",
				data: new FormData($(this)[0]),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				type: 'POST',
				dataType: 'json',
				error: err => {
					console.log(err)
					alert_toast("An error occured", 'error');
					end_loader();
				},
				success: function(resp) {
					if (typeof resp == 'object' && resp.status == 'success') {
						location.replace('./?page=categories/manage_category&id=' + resp.cid)
					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-danger err-msg").text(resp.msg)
						_this.prepend(el)
						el.show('slow')
						$("html, body").scrollTop(0);
						end_loader()
					} else {
						alert_toast("An error occured", 'error');
						end_loader();
						console.log(resp)
					}
				}
			})
		})

	})
</script>