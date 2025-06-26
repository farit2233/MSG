<?php
$main_category_id = null; // ป้องกัน warning
$selected_extra_categories = [];
$has_discount = (!empty($discount_type) && $discount_value > 0);
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
		display: block;
		/* ทำให้เป็นบล็อกเพื่อใช้ margin auto */
		max-width: 300px;
		/* กำหนดความกว้างสูงสุดตามต้องการ */
		width: 100%;
		/* ให้ขยายเต็มที่ในกรอบไม่เกิน max-width */
		height: auto;
		/* รักษาสัดส่วน */
		margin: 0 auto;
		/* จัดกึ่งกลางแนวนอน */
	}
</style>
<div class="card card-outline card-primary rounded-0">
	<div class="card-header">
		<h1 class="card-title"><?php echo isset($id) ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่'; ?></h1>
	</div>
	<form action="" id="product-form" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="calculated_size" id="calculated_size">
		<input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
		<div class="card-body">

			<!-- Card: รูปภาพสินค้า -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title">รูปภาพสินค้า</h3>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="img">อัปโหลดรูปภาพสินค้า</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" name="img" id="img" onchange="displayImg(this)">
							<label class="custom-file-label" for="img">เลือกไฟล์</label>
						</div>
						<div class="mt-3">
							<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>" id="cimg" class="img-fluid img-thumbnail">
						</div>
					</div>
				</div>
			</div>
			<!-- end Card: รูปภาพสินค้า -->
			<!-- Card 1: ข้อมูลสินค้า -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title h3">ข้อมูลสินค้า</h3>
				</div>
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>ชื่อสินค้า <span class="text-danger">*</span></label>
							<input type="text" name="name" class="form-control" required value="<?= isset($name) ? $name : '' ?>">
						</div>
						<div class="form-group col-md-3">
							<label>หมวดหมู่สินค้า <span class="text-danger">*</span></label>
							<select name="category_id" class="form-control select2" required>
								<option value="">-- เลือกหมวดหมู่ --</option>
								<?php $cat_q = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 AND status = 1 ORDER BY name ASC");
								while ($cat = $cat_q->fetch_assoc()): ?>
									<option value="<?= $cat['id'] ?>" <?= ($main_category_id == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
								<?php endwhile; ?>
							</select>
						</div>
						<div class="form-group col-md-3">
							<label>แบรนด์ / ยี่ห้อ</label>
							<input type="text" name="brand" class="form-control" value="<?= isset($brand) ? $brand : '' ?>">
						</div>
					</div>
					<!-- หมวดหมู่เพิ่มเติม (ถ้ามี) -->
					<div class="form-group">
						<label>หมวดหมู่เพิ่มเติม (เฉพาะตามอายุ)</label>
						<div class="row">
							<?php $extra_q = $conn->query("SELECT * FROM category_list WHERE delete_flag = 0 AND status = 1 AND name LIKE 'ของเล่นหมวดหมู่%' ORDER BY name ASC");
							while ($row = $extra_q->fetch_assoc()): ?>
								<div class="col-md-4">
									<div class="form-check">
										<input type="checkbox" class="form-check-input" name="extra_categories[]" value="<?= $row['id'] ?>" <?= in_array($row['id'], $selected_extra_categories) ? 'checked' : '' ?>>
										<label class="form-check-label"><?= $row['name'] ?></label>
									</div>
								</div>
							<?php endwhile; ?>
						</div>
					</div>
					<div class="form-group">
						<label>รายละเอียดสินค้า</label>
						<textarea name="description" rows="3" class="form-control"><?= isset($description) ? $description : '' ?></textarea>
					</div>
					<div class="form-group">
						<label>รหัสสินค้า (SKU) <span class="text-danger">*</span></label>
						<input type="text" name="sku" class="form-control" value="<?= isset($sku) ? $sku : '' ?>" required>
					</div>
					<div class="form-group">
						<label>ราคาสินค้า (บาท)</label>
						<input type="number" name="price" class="form-control" step="any" min="0" value="<?= isset($price) ? $price : '' ?>" required>
					</div>
				</div>
			</div>
			<!-- end Card 1: ข้อมูลสินค้า -->

			<!-- Card 2: ช่องทางจำหน่าย -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title">ช่องทางจำหน่าย</h3>
				</div>
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-4">
							<label>Shopee</label>
							<input type="url" name="shopee" class="form-control" value="<?= isset($id) ? get_platform_link($conn, $id, 'shopee') : '' ?>">
						</div>
						<div class="form-group col-md-4">
							<label>Lazada</label>
							<input type="url" name="lazada" class="form-control" value="<?= isset($id) ? get_platform_link($conn, $id, 'lazada') : '' ?>">
						</div>
						<div class="form-group col-md-4">
							<label>TikTok</label>
							<input type="url" name="tiktok" class="form-control" value="<?= isset($id) ? get_platform_link($conn, $id, 'tiktok') : '' ?>">
						</div>
					</div>
				</div>
			</div>
			<!-- end Card 2: ช่องทางจำหน่าย -->

			<!-- Card 3: ส่วนลด -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title">ส่วนลด</h3>
				</div>
				<div class="card-body">
					<div class="custom-control custom-switch mb-3">
						<input type="checkbox" class="custom-control-input" id="discount_toggle"
							<?= (isset($discount_value) && $discount_value != 0) ? 'checked' : '' ?>>

						<label class="custom-control-label" for="discount_toggle">เปิดใช้งานส่วนลด</label>
					</div>
					<div id="discount_section" class="border p-3 bg-light">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="discount_type" id="discount_amount" value="amount"
								<?= $discount_type == 'amount' ? 'checked' : '' ?>>
							<label class="form-check-label" for="discount_amount">ลดเป็นจำนวนเงิน (บาท)</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="discount_type" id="discount_percent" value="percent"
								<?= $discount_type == 'percent' ? 'checked' : '' ?>>
							<label class="form-check-label" for="discount_percent">ลดเป็นเปอร์เซ็นต์ (%)</label>
						</div>
						<div class="form-group mt-2">
							<label>มูลค่าส่วนลด</label>
							<input type="number" name="discount_value" class="form-control" min="0" step="any" value="<?= $discount_value ?>">
						</div>
						<div class="form-group">
							<label>ราคาหลังหักส่วนลด</label>
							<input type="text" readonly class="form-control" id="final-price">
						</div>
					</div>
				</div>
			</div>
			<!-- end Card 3: ส่วนลด -->

			<!-- Card: การจัดส่ง -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title">การจัดส่ง</h3>
				</div>
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>น้ำหนัก <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="number" step="any" min="0" name="weight" class="form-control" value="<?= isset($weight) ? $weight : '' ?>" required>
								<div class="input-group-append">
									<span class="input-group-text">kg</span>
								</div>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label>ขนาดพัสดุ (กว้าง x ยาว x สูง)</label>
							<div class="form-row">
								<div class="col"><input type="number" step="any" name="dim_w" class="form-control" placeholder="กว้าง" value="<?= isset($dim_w) ? $dim_w : '' ?>"></div>
								<div class="col"><input type="number" step="any" name="dim_l" class="form-control" placeholder="ยาว" value="<?= isset($dim_l) ? $dim_l : '' ?>"></div>
								<div class="input-group col">
									<input type="number" step="any" name="dim_h" class="form-control" placeholder="สูง" value="<?= isset($dim_h) ? $dim_h : '' ?>">
									<div class="input-group-append">
										<span class="input-group-text">cm</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr>
					<h5>ราคาขนส่ง</h5>
					<table class="table table-bordered table-responsive-lg">
						<thead class="thead-light">
							<tr>
								<th>ชื่อขนส่ง</th>
								<th>ราคาขนส่งคงที่</th>
								<th>ราคาขนส่งตามขนาด</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$shipping_js_data = [];
							$shippings = $conn->query("SELECT * FROM shipping_methods WHERE delete_flag = 0");
							while ($row = $shippings->fetch_assoc()):
								$method_id = $row['id'];
								$method_name = $row['name'];
								$divider = $row['volumetric_divider'];

								$shipping_js_data[$method_id] = [
									'divider' => $divider,
									'cost' => $row['cost'],
									's' => $row['weight_cost_s'],
									'm' => $row['weight_cost_m'],
									'l' => $row['weight_cost_l']
								];
							?>
								<tr>
									<td>
										<h6><?= $method_name ?></h6>
									</td>
									<td>
										<input type="text" name="shipping_price[<?= $method_id ?>]" step="any" min="0" class="form-control shipping-price" id="shipping_price_<?= $method_id ?>" placeholder="ค่าคงที่ (บาท)" readonly>
									</td>
									<td>
										<input type="text" class="form-control parcel-size-display" id="parcel_size_<?= $method_id ?>" placeholder="ขนาด (S/M/L)" readonly>
									</td>
								</tr>

							<?php endwhile; ?>
						</tbody>
					</table>

					<script>
						const shippingMethods = <?= json_encode($shipping_js_data) ?>;
					</script>

					<hr>
					<div class="form-check">
						<input type="checkbox" name="slow_prepare" id="slow_prepare" class="form-check-input" <?= isset($slow_prepare) && $slow_prepare ? 'checked' : '' ?>>
						<label class="form-check-label" for="slow_prepare">เตรียมส่งนานกว่าปกติ</label>
					</div>
				</div>
			</div>
			<!-- end Card: การจัดส่ง -->

			<!-- Card 4: สถานะการขาย -->
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<h3 class="card-title">สถานะการขาย</h3>
				</div>
				<div class="card-body">
					<input type="hidden" name="status" value="0">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= isset($status) && $status == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="status">เปิด/ปิดการขายของสินค้าบนหน้าร้าน</label>
					</div>
				</div>
			</div>
			<!-- end Card 4: สถานะการขาย -->

	</form>

</div>
<div class="card-footer py-1 text-center">
	<button class="btn btn-success btn-sm btn-flat" form="product-form"><i class="fa fa-save"></i> บันทึก</button>
	<a class="btn btn-danger btn-sm border btn-flat" href="./?page=products"><i class="fa fa-times"></i> ยกเลิก</a>
</div>
<script>
	function displayImg(input) {
		if (input.files && input.files[0]) {
			const reader = new FileReader();
			reader.onload = function(e) {
				$('#cimg').attr('src', e.target.result);
				$(input).siblings('.custom-file-label').html(input.files[0].name);
			};
			reader.readAsDataURL(input.files[0]);
		} else {
			$('#cimg').attr('src', "<?= validate_image(isset($image_path) ? $image_path : '') ?>");
			$(input).siblings('.custom-file-label').html('Choose file');
		}
	}

	function calculateFinalPrice() {
		const price = parseFloat($('[name="price"]').val()) || 0;
		const discountType = $('[name="discount_type"]:checked').val();
		const discountValue = parseFloat($('[name="discount_value"]').val()) || 0;
		let finalPrice = price;

		if (discountType === 'amount') {
			finalPrice -= discountValue;
		} else if (discountType === 'percent') {
			finalPrice -= (price * discountValue / 100);
		}

		finalPrice = Math.max(0, finalPrice);
		$('#final-price').val(finalPrice.toFixed(2));
		$('#final-price-display').text(finalPrice.toFixed(2) + ' บาท');
	}

	function calculateShippingCosts() {
		const w = parseFloat($('[name="dim_w"]').val()) || 0;
		const l = parseFloat($('[name="dim_l"]').val()) || 0;
		const h = parseFloat($('[name="dim_h"]').val()) || 0;
		const realWeight = parseFloat($('[name="weight"]').val()) || 0;

		$.each(shippingMethods, (methodId, data) => {
			const volumetricWeight = (w * l * h) / data.divider;
			const finalWeight = Math.max(realWeight, volumetricWeight);

			let parcelSize = 'S';
			let estimatedPrice = data.s;
			if (finalWeight > 2 && finalWeight <= 5) {
				parcelSize = 'M';
				estimatedPrice = data.m;
			} else if (finalWeight > 5) {
				parcelSize = 'L';
				estimatedPrice = data.l;
			}

			$('#calculated_size').val(parcelSize);
			$(`#shipping_price_${methodId}`).val(`${estimatedPrice} (${parcelSize})`).prop('readonly', true);
			$(`#parcel_size_${methodId}`).val(`${finalWeight.toFixed(2)} kg = ${estimatedPrice} บาท (${parcelSize})`);

		});
	}

	$(document).ready(function() {
		// Select2
		$('.select2').select2({
			width: '100%'
		});

		// Toggle ส่วนลด
		function toggleDiscountSection(enabled) {
			$('#discount_section').toggle(enabled).find('input').prop('disabled', !enabled);
		}

		// เรียกเมื่อโหลดหน้า
		const hasDiscount = $('#discount_toggle').is(':checked');
		toggleDiscountSection(hasDiscount);
		$('#discount_toggle').prop('checked', hasDiscount);

		// เมื่อเปลี่ยนสวิตช์ส่วนลด
		$('#discount_toggle').on('change', function() {
			toggleDiscountSection(this.checked);
			calculateFinalPrice();
		});

		// คำนวณราคาทุกครั้งที่มีการแก้ไข
		$('[name="price"], [name="discount_value"], [name="discount_type"]').on('input change', calculateFinalPrice);

		// คำนวณค่าขนส่ง
		$('[name="dim_w"], [name="dim_l"], [name="dim_h"], [name="weight"]').on('input change', calculateShippingCosts);

		// คำนวณครั้งแรกตอนโหลด
		calculateFinalPrice();
		calculateShippingCosts();

		// Form submit
		$('#product-form').submit(function(e) {
			e.preventDefault();
			const form = $(this);
			$('.err-msg').remove();
			start_loader();

			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_product",
				data: new FormData(this),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				dataType: 'json',
				error: err => {
					console.error(err);
					alert_toast("เกิดข้อผิดพลาด", 'error');
					end_loader();
				},
				success: function(resp) {
					if (resp?.status === 'success') {
						location.replace(`./?page=products/view_product&id=${resp.pid}`);
					} else if (resp.status === 'failed' && resp.msg) {
						const el = $('<div>').addClass("alert alert-dark err-msg").text(resp.msg);
						form.prepend(el);
						el.show('slow');
						$("html, body").scrollTop(0);
					} else {
						alert_toast("เกิดข้อผิดพลาด", 'error');
						console.log(resp);
					}
					end_loader();
				}
			});
		});
	});
</script>