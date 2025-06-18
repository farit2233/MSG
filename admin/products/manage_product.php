<?php
$main_category_id = null; // ป้องกัน warning
$selected_extra_categories = [];

if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT * from `product_list` where id = '{$_GET['id']}' ");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}

		// ตั้งค่าหมวดหมู่หลักแยกชัดเจน
		$main_category_id = $category_id;

		// ดึงหมวดหมู่เพิ่มเติม (product_categories)
		$res = $conn->query("SELECT category_id FROM product_categories WHERE product_id = '{$_GET['id']}'");
		while ($row = $res->fetch_assoc()) {
			$selected_extra_categories[] = $row['category_id'];
		}
	}
}
function get_platform_link($conn, $product_id, $platform)
{
	$col = "{$platform}_url"; // เช่น shopee_url
	$q = $conn->query("SELECT `{$col}` FROM product_links WHERE product_id = {$product_id}");
	if ($q && $q->num_rows > 0) {
		return $q->fetch_assoc()[$col];
	}
	return '';
}

?>
<style>
	#cimg {
		max-width: 100%;
		max-height: 25em;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b><?= isset($id) ? "แก้ไขสินค้า" : "สร้างสินค้าใหม่" ?></b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-body">

				<div class="container-fluid">
					<form action="" id="product-form" method="POST" enctype="multipart/form-data">
						<input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="brand" class="control-label">แบรนด์</label>
							<input type="text" name="brand" id="brand" class="form-control form-control-sm rounded-0" value="<?php echo isset($brand) ? $brand : ''; ?>" required />
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="name" class="control-label">ชื่อสินค้า</label>
							<input type="text" name="name" id="name" class="form-control form-control-sm rounded-0" value="<?php echo isset($name) ? $name : ''; ?>" required />
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="category_id" class="control-label">หมวดหมู่หลัก</label>
							<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" required>
								<option value="" disabled <?= !isset($main_category_id) ? 'selected' : '' ?>></option>
								<?php
								$cat_q = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
								while ($cat = $cat_q->fetch_assoc()):
								?>
									<option value="<?= $cat['id'] ?>" <?= (isset($main_category_id) && $cat['id'] == $main_category_id) ? 'selected' : '' ?>>
										<?= $cat['name'] ?>
									</option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="form-group col-lg-12">
							<label class="control-label">หมวดหมู่เพิ่มเติม (เฉพาะตามอายุ)</label>
							<div class="row">
								<?php
								$extra_q = $conn->query("SELECT * FROM category_list 
								WHERE delete_flag = 0 
								AND status = 1 
								AND name LIKE 'ของเล่นหมวดหมู่%' 
								ORDER BY name ASC");
								while ($row = $extra_q->fetch_assoc()):
								?>
									<div class="col-md-4">
										<div class="form-check">
											<input type="checkbox" class="form-check-input" name="extra_categories[]" value="<?= $row['id'] ?>"
												<?= in_array($row['id'], $selected_extra_categories) ? 'checked' : '' ?>>
											<label class="form-check-label"><?= $row['name'] ?></label>
										</div>
									</div>
								<?php endwhile; ?>
							</div>
						</div>
						<div class="form-group col-lg-12">
							<label class="control-label">ช่องทางจำหน่าย</label>
							<div class="row">
								<div class="col-md-4">
									<label>Shopee</label>
									<input type="url" name="shopee" class="form-control form-control-sm" value="<?= isset($id) ? get_platform_link($conn, $id, 'shopee') : '' ?>">
								</div>
								<div class="col-md-4">
									<label>Lazada</label>
									<input type="url" name="lazada" class="form-control form-control-sm" value="<?= isset($id) ? get_platform_link($conn, $id, 'lazada') : '' ?>">
								</div>
								<div class="col-md-4">
									<label>TikTokShop</label>
									<input type="url" name="tiktok" class="form-control form-control-sm" value="<?= isset($id) ? get_platform_link($conn, $id, 'tiktok') : '' ?>">
								</div>
							</div>
						</div>


						<div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<label for="description" class="control-label">รายละเอียดสินค้า</label>
							<textarea rows="3" name="description" id="description" class="form-control form-control-sm rounded-0" required><?php echo isset($description) ? $description : ''; ?></textarea>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="price" class="control-label">ราคา</label>
							<input type="number" step="any" name="price" id="price" class="form-control form-control-sm rounded-0 text-right" value="<?php echo isset($price) ? $price : ''; ?>" required />
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="discount_type" class="control-label">ประเภทส่วนลด</label>
							<select name="discount_type" id="discount_type" class="form-control form-control-sm rounded-0">
								<option value="" <?= !isset($discount_type) ? 'selected' : '' ?>>ไม่มีส่วนลด</option>
								<option value="amount" <?= isset($discount_type) && $discount_type == 'amount' ? 'selected' : '' ?>>ลดเป็นจำนวนเงิน (บาท)</option>
								<option value="percent" <?= isset($discount_type) && $discount_type == 'percent' ? 'selected' : '' ?>>ลดเป็นเปอร์เซ็นต์ (%)</option>
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="discount_value" class="control-label">มูลค่าส่วนลด</label>
							<input type="number" step="any" min="0" name="discount_value" id="discount_value"
								class="form-control form-control-sm rounded-0 text-right"
								value="<?= isset($discount_value) ? $discount_value : '' ?>" />
							<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<label class="control-label">ราคาหลังหักส่วนลด</label>
								<p class="form-control-plaintext font-weight-bold" id="final-price-display">-</p>
							</div>

						</div>

						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="status" class="control-label">สถานะ</label>
							<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
								<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>กำลังใช้งาน</option>
								<option value="0" <?= isset($status) && $status == 0 ? 'selected' : '' ?>>ไม่ได้ใช้งาน</option>
							</select>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<label for="status" class="control-label">รูปภาพสินค้า</label>
							<div class="custom-file custom-file-sm">
								<input type="file" class="custom-file-input rounded-0" id="customFile1" name="img" onchange="displayImg(this)">
								<label class="custom-file-label" for="customFile1">เลือกไฟล์จากในเครื่อง</label>
							</div>
						</div>
						<div class="form-group col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<img src="<?php echo validate_image(isset($image_path) ? $image_path : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</form>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-success btn-sm btn-flat" form="product-form"><i class="fa fa-save"></i> บันทึก</button>
				<a class="btn btn-danger btn-sm border btn-flat" href="./?page=products"><i class="fa fa-times"></i> ยกเลิก</a>
			</div>
		</div>
	</div>
</div>
<script>
	function displayImg(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
				$(input).siblings('.custom-file-label').html(input.files[0].name)
			}

			reader.readAsDataURL(input.files[0]);
		} else {
			$('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : '') ?>");
			$(input).siblings('.custom-file-label').html('Choose file')
		}
	}
	$(document).ready(function() {
		$('#category_id').select2({
			placeholder: "Please Select Category Here",
			width: '100%',
			containerCssClass: 'form-control form-control-sm rounded-0'
		})
		$('#product-form').submit(function(e) {
			e.preventDefault();
			var _this = $(this)
			$('.err-msg').remove();
			start_loader();
			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_product",
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
						location.replace('./?page=products/view_product&id=' + resp.pid)
					} else if (resp.status == 'failed' && !!resp.msg) {
						var el = $('<div>')
						el.addClass("alert alert-dark err-msg").text(resp.msg)
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

	function updateFinalPrice() {
		let price = parseFloat($('#price').val()) || 0;
		let discountType = $('#discount_type').val();
		let discountValue = parseFloat($('#discount_value').val()) || 0;
		let finalPrice = price;

		if (discountType === 'amount') {
			finalPrice = price - discountValue;
		} else if (discountType === 'percent') {
			finalPrice = price - (price * discountValue / 100);
		}

		// ป้องกันค่าติดลบ
		finalPrice = finalPrice < 0 ? 0 : finalPrice;

		// แสดงผล
		$('#final-price-display').text(finalPrice.toFixed(2) + ' บาท');
	}

	$('#price, #discount_type, #discount_value').on('input change', updateFinalPrice);

	// เรียกตอนโหลดด้วย ถ้ามีค่า
	updateFinalPrice();
</script>