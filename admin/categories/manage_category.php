<?php
// --- ดึงข้อมูลประเภทสินค้ามาเตรียมไว้ ---
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
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b><?= isset($id) ? "แก้ไขหมวดหมู่" : "สร้างหมวดหมู่ใหม่" ?></b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-body">

				<div class="container-fluid">
					<form action="" id="category-form">
						<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">

						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="product_type_id" class="control-label">ประเภทสินค้า</label>
							<select name="product_type_id" id="product_type_id" class="form-control form-control-sm rounded-0" required>
								<option value="" disabled <?= !isset($product_type_id) ? 'selected' : '' ?>>-- กรุณาเลือก --</option>
								<?php
								// วนลูปสร้าง <option> จากข้อมูลที่ดึงมา
								while ($pt_row = $product_types_result->fetch_assoc()) :
								?>
									<option value="<?= $pt_row['id'] ?>" <?= (isset($product_type_id) && $product_type_id == $pt_row['id']) ? 'selected' : '' ?>>
										<?= $pt_row['name'] ?>
									</option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="name" class="control-label">ชื่อหมวดหมู่</label>
							<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
						</div>
						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<label for="description" class="control-label">รายละเอียดหมวดหมู่</label>
							<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="status" class="control-label">สถานะ</label>
							<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
								<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>กำลังใช้งาน</option>
								<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>ไม่ได้ใช้งาน</option>
							</select>
						</div>
					</form>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-sm btn-success btn-flat" form="category-form"><i class="fa fa-save"></i> บันทึก</button>
				<a class="btn btn-danger btn-sm border btn-flat" href="./?page=categories"><i class="fa fa-times"></i> ยกเลิก</a>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		// ไม่ต้องแก้ไขส่วนของ script นี้เลย
		// FormData จะรวบรวมข้อมูลจากฟอร์มทั้งหมด (รวมถึง product_type_id) ไปให้เอง
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
						location.replace('./?page=categories/view_category&id=' + resp.cid)
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