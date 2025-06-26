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
<div class="card card-outline card-primary rounded-0">
	<div class="card-header">
		<h1 class="card-title"><?php echo isset($id) ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่'; ?></h1>
	</div>
	<form action="" id="product-form" method="POST" enctype="multipart/form-data">
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
						<label>รหัสสินค้า (SKU)</label>
						<input type="text" name="sku" class="form-control" value="<?= isset($sku) ? $sku : '' ?>">
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
						<input type="checkbox" class="custom-control-input" id="discount_toggle">
						<label class="custom-control-label" for="discount_toggle">เปิดใช้งานส่วนลด</label>
					</div>
					<div id="discount_section" class="border p-3 bg-light">
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="discount_type" id="discount_amount" value="amount">
							<label class="form-check-label" for="discount_amount">ลดเป็นจำนวนเงิน (บาท)</label>
						</div>
						<div class="form-check form-check-inline">
							<input class="form-check-input" type="radio" name="discount_type" id="discount_percent" value="percent">
							<label class="form-check-label" for="discount_percent">ลดเป็นเปอร์เซ็นต์ (%)</label>
						</div>
						<div class="form-group mt-2">
							<label>มูลค่าส่วนลด</label>
							<input type="number" name="discount_value" class="form-control" min="0" step="any">
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
								<div class="col"><input type="number" step="any" name="dim_h" class="form-control" placeholder="สูง" value="<?= isset($dim_h) ? $dim_h : '' ?>"></div>
								<div class="col-auto d-flex align-items-center">cm</div>
							</div>
						</div>
					</div>

					<hr>
					<h5>ค่าจัดส่ง (เปิด/ปิดการใช้งานได้)</h5>
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
										<div class="custom-control custom-switch">
											<input type="checkbox" class="custom-control-input shipping-toggle" id="ship<?= $method_id ?>" name="shipping_enabled[<?= $method_id ?>]" checked>
											<label class="custom-control-label" for="ship<?= $method_id ?>"><?= $method_name ?></label>
										</div>
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

		$(function() {
			$('.select2').select2({
				width: '100%'
			});
			$('#discount_section').find('input').prop('disabled', true);

			$('#discount_toggle').change(function() {
				$('#discount_section').find('input').prop('disabled', !this.checked);
			});
		});

		$(document).ready(function() {
			// ซ่อน/แสดง ส่วนลดตาม toggle
			$('#discount_toggle').change(function() {
				$('#discount_section').toggle(this.checked);
			});

			// เริ่มต้น ถ้ามีการแก้ไขราคา, ส่วนลด หรือประเภทส่วนลด
			$('input[name="price"], input[name="discount_value"], input[name="discount_type"]').on('input change', function() {
				calculateFinalPrice();
			});

			function calculateFinalPrice() {
				const price = parseFloat($('input[name="price"]').val()) || 0;
				const discountType = $('input[name="discount_type"]:checked').val();
				const discountValue = parseFloat($('input[name="discount_value"]').val()) || 0;
				let finalPrice = price;

				if (discountType === 'amount') {
					finalPrice = price - discountValue;
				} else if (discountType === 'percent') {
					finalPrice = price - (price * discountValue / 100);
				}

				// ป้องกันราคาติดลบ
				finalPrice = finalPrice < 0 ? 0 : finalPrice;

				$('#final-price').val(finalPrice.toFixed(2));
			}

			// ถ้า checkbox เปิดใช้งานส่วนลดตอนโหลดหน้า ให้แสดง
			if ($('#discount_toggle').is(':checked')) {
				$('#discount_section').show();
			} else {
				$('#discount_section').hide();
			}
		});

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
					alert_toast("เกิดข้อผิดพลาด", 'error');
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
						alert_toast("เกิดข้อผิดพลาด", 'error');
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

	function calculateShippingCosts() {
		const w = parseFloat($('input[name="dim_w"]').val()) || 0;
		const l = parseFloat($('input[name="dim_l"]').val()) || 0;
		const h = parseFloat($('input[name="dim_h"]').val()) || 0;
		const realWeight = parseFloat($('input[name="weight"]').val()) || 0;

		$.each(shippingMethods, function(methodId, data) {
			const divider = data.divider;
			const volumetricWeight = (w * l * h) / divider;
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

			$('#shipping_price_' + methodId).val(`${estimatedPrice} (${parcelSize})`).prop('readonly', true);
			$('#parcel_size_' + methodId).val(`${parcelSize} (${finalWeight.toFixed(2)} kg) = ${estimatedPrice} บาท`);
		});

	}
	$('input[name="dim_w"], input[name="dim_l"], input[name="dim_h"], input[name="weight"]').on('input change', function() {
		calculateShippingCosts();
	});
</script>