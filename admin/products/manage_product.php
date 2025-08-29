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
$gallery_images = [];
if (isset($id)) {
	$img_qry = $conn->query("SELECT * FROM `product_image_path` WHERE product_id = '{$id}' ORDER BY `id` ASC");
	while ($row = $img_qry->fetch_assoc()) {
		$gallery_images[] = $row;
	}
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

	.card-title {
		font-size: 20px !important;
		font-weight: bold;
	}

	.head-detail {
		font-size: 16px;
	}

	section {
		font-size: 16px;
	}

	.product-gallery {
		display: flex;
		flex-wrap: wrap;
		gap: 15px;
		/* ระยะห่างระหว่างรูป */
		background-color: #f8f9fa;
		padding: 15px;
		border: 1px solid #dee2e6;
		border-radius: 5px;
	}

	.gallery-item {
		position: relative;
		width: 120px;
		height: 120px;
	}

	.gallery-item img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		border-radius: 4px;
		border: 1px solid #ddd;
	}

	.gallery-item .btn-delete-img {
		position: absolute;
		top: -10px;
		right: -10px;
		width: 28px;
		height: 28px;
		border-radius: 50%;
		background-color: #dc3545;
		color: white;
		border: none;
		font-size: 14px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
	}

	/* กำหนดสไตล์สำหรับจอมือถือ (ความกว้างน้อยกว่า 768px) */
	@media screen and (max-width: 768px) {

		/* ซ่อนหัวตารางแบบปกติ */
		.table thead {
			display: none;
		}

		/* ทำให้แถว (tr) แสดงผลเป็นบล็อกเหมือนการ์ด */
		.table tr {
			display: block;
			margin-bottom: 1rem;
			border: 1px solid #dee2e6;
			/* เพิ่มเส้นขอบให้แต่ละการ์ด */
			border-radius: .25rem;
		}

		/* ทำให้เซลล์ (td) แสดงผลเป็นบล็อกและจัดเรียงเนื้อหาใหม่ */
		.table td {
			display: flex;
			/* ใช้ Flexbox เพื่อจัดวาง label กับ content */
			justify-content: space-between;
			/* ทำให้ label และ content อยู่คนละฝั่ง */
			align-items: center;
			text-align: right;
			/* จัดข้อความของ content ชิดขวา */
			border: none;
			border-bottom: 1px solid #eee;
			/* เพิ่มเส้นคั่นระหว่างข้อมูล */
			padding: .75rem;
		}

		/* จัดการกับ input ให้อยู่ในกรอบสวยงาม */
		.table td input {
			width: 60%;
			/* กำหนดความกว้างของ input */
			text-align: right;
		}

		.table td h6 {
			text-align: right;
			margin-bottom: 0;
		}

		/* สร้าง Label จาก data-label ที่เราเพิ่มเข้าไปใน HTML */
		.table td::before {
			content: attr(data-label);
			/* ดึงข้อความจาก data-label มาแสดง */
			font-weight: bold;
			text-align: left;
			padding-right: 1rem;
		}

		/* จัดการแถวสุดท้ายไม่ให้มีเส้นขอบล่าง */
		.table td:last-child {
			border-bottom: 0;
		}
	}
</style>
<div class="card card-outline card-orange rounded-0">
	<div class="card-header">
		<div class="card-title"><?= isset($id) ? 'แก้ไขสินค้า' : 'เพิ่มสินค้าใหม่'; ?></div>
	</div>
	<form action="" id="product-form" method="POST" enctype="multipart/form-data">
		<input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
		<div class="card-body">

			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title" style="font-size: 18px !important;">รูปภาพสินค้า</div>
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
				<div class="card-body">
					<div class="form-group">
						<label for="gallery-imgs">อัปโหลดรูปภาพแกลเลอรี (เลือกได้หลายภาพ)</label>
						<div class="custom-file">
							<input type="file" class="custom-file-input" name="gallery_imgs[]" id="gallery-imgs" onchange="previewGallery(this)" multiple>
							<label class="custom-file-label" for="gallery-imgs">เลือกไฟล์</label>
						</div>

						<div class="product-gallery mt-3">
							<?php foreach ($gallery_images as $img): ?>
								<div class="gallery-item" id="gallery-item-<?= $img['id'] ?>">
									<img src="<?= validate_image($img['image_path']) ?>" alt="Gallery Image">
									<button type="button" class="btn-delete-img" data-id="<?= $img['id'] ?>" title="ลบรูปภาพนี้">
										<i class="fa fa-times"></i>
									</button>
								</div>
							<?php endforeach; ?>

							<div id="gallery-preview-container" style="display: contents;"></div>
						</div>
					</div>

				</div>
			</div>
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title div">ข้อมูลสินค้า</div>
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

					<div class="form-group">
						<label>รายละเอียดสินค้า</label>
						<textarea name="description" rows="3" class="form-control"><?= isset($description) ? $description : '' ?></textarea>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label>รหัสสินค้า (SKU) <span class="text-danger">*</span></label>
								<input type="text" name="sku" class="form-control" value="<?= isset($sku) ? $sku : '' ?>" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label>ราคา <span class="text-danger">*</span></label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">฿</span>
									</div>
									<input type="number" step="0.01" name="price" class="form-control" value="<?= isset($price) ? $price : '' ?>" required>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title">ช่องทางจำหน่าย</div>
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
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title">ส่วนลด</div>
				</div>
				<div class="card-body">
					<div class="custom-control custom-switch mb-3">
						<input type="checkbox" class="custom-control-input" id="discount_toggle"
							<?= (isset($discount_value) && $discount_value != 0) ? 'checked' : '' ?>>

						<label class="custom-control-label" for="discount_toggle">เปิดใช้งานส่วนลด</label>
					</div>

					<div id="discount_section" class="border p-3 bg-light">
						<?php
						$discount_type = $discount_type ?? ''; // กำหนดค่าเริ่มต้น
						$discount_value = $discount_value ?? ''; // กำหนดค่าเริ่มต้น
						?>

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
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title">การจัดส่ง</div>
				</div>
				<div class="card-body">
					<div class="form-row">
						<div class="form-group col-md-6">
							<label>น้ำหนัก (กรัม) <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="number" step="any" min="0" name="product_weight" class="form-control" value="<?= isset($product_weight) ? $product_weight : '' ?>" required>
								<div class="input-group-append">
									<span class="input-group-text">g</span>
								</div>
							</div>
						</div>
						<div class="form-group col-md-6">
							<label>ขนาดพัสดุ (กว้าง x ยาว x สูง)</label>
							<div class="form-row">
								<div class="col"><input type="number" step="any" name="product_width" class="form-control" placeholder="กว้าง" value="<?= isset($product_width) ? $product_width : '' ?>"></div>
								<div class="col"><input type="number" step="any" name="product_length" class="form-control" placeholder="ยาว" value="<?= isset($product_length) ? $product_length : '' ?>"></div>
								<div class="input-group col">
									<input type="number" step="any" name="product_height" class="form-control" placeholder="สูง" value="<?= isset($product_height) ? $product_height : '' ?>">
									<div class="input-group-append">
										<span class="input-group-text">cm</span>
									</div>
								</div>
							</div>
						</div>
					</div>

					<hr>
					<h5>ราคาขนส่ง</h5>
					<div class="table-responsive">
						<table class="table table-bordered">
							<thead class="thead-light">
								<tr>
									<th>ชื่อขนส่ง</th>
									<th>ราคาขนส่งคงที่</th>
									<th>ราคาขนส่งตามขนาด</th>
								</tr>
							</thead>
							<tbody>
								<?php
								// 1) เตรียมน้ำหนักจริง
								$product_weight = isset($product_weight) ? (float)$product_weight : 0;

								// 2) วนขนส่งทั้งหมด
								$shippings = $conn->query("SELECT `id`, `name`,`cost` FROM `shipping_methods` WHERE delete_flag = 0 AND status = 1");

								$matched_shipping_price_id = null; // จะเก็บ id ช่วงราคาที่ match จริง

								while ($row = $shippings->fetch_assoc()):
									$method_id = $row['id'];

									// หา rate ตามช่วงน้ำหนัก
									$qry = $conn->query("SELECT * FROM shipping_prices 
								WHERE shipping_methods_id = {$method_id} 
								AND min_weight <= {$product_weight} 
								AND max_weight >= {$product_weight}
								ORDER BY min_weight ASC LIMIT 1");

									$matched_row = $qry && $qry->num_rows ? $qry->fetch_assoc() : null;

									// ถ้าเจอช่วงแรก เอา id เก็บไว้
									if ($matched_row && !$matched_shipping_price_id) {
										$matched_shipping_price_id = $matched_row['id'];
									}

								?>
									<tr data-method-id="<?= $row['id'] ?>">

										<td data-label="ชื่อขนส่ง">
											<h6><?= $row['name'] ?></h6>
										</td>

										<td data-label="ราคาขนส่งคงที่">
											<input type="text" class="form-control" value="<?= number_format($row['cost'], 2) ?> บาท" readonly>
										</td>

										<td data-label="ราคาขนส่งตามขนาด">
											<input type="text" class="form-control dynamic-shipping"
												value="<?= $matched_row ? "ช่วง {$matched_row['min_weight']}-{$matched_row['max_weight']} g | " . number_format($matched_row['price'], 2) . " บาท" : "น้ำหนักสินค้าสูงเกินขีดจำกัด" ?>"
												readonly>
											<div class="weight-error text-danger" style="display: none;"></div>
										</td>

									</tr>
								<?php endwhile; ?>
							</tbody>

						</table>
					</div>
					<div class="form-check">
						<input type="checkbox" name="slow_prepare" id="slow_prepare" class="form-check-input" <?= isset($slow_prepare) && $slow_prepare ? 'checked' : '' ?>>
						<label class="form-check-label" for="slow_prepare">เตรียมส่งนานกว่าปกติ</label>
					</div>
				</div>
			</div>
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title">สถานะการขาย</div>
				</div>
				<div class="card-body">
					<input type="hidden" name="status" value="0">
					<div class="custom-control custom-switch">
						<input type="checkbox" class="custom-control-input" id="status" name="status" value="1" <?= isset($status) && $status == 1 ? 'checked' : '' ?>>
						<label class="custom-control-label" for="status">เปิด/ปิดการขายของสินค้าบนหน้าร้าน</label>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer py-1 text-center">
			<button class="btn btn-success btn-sm btn-flat" form="product-form"><i class="fa fa-save"></i> บันทึก</button>
			<a class="btn btn-danger btn-sm border btn-flat" href="./?page=products"><i class="fa fa-times"></i> ยกเลิก</a>
			<a class="btn btn-light btn-sm border btn-flat" href="./?page=products"><i class="fa fa-angle-left"></i> กลับ</a>
		</div>
	</form>
</div>


<script>
	function previewGallery(input) {
		const previewContainer = document.getElementById("gallery-preview-container");

		// ตรวจสอบว่ามีไฟล์ที่เลือกหรือไม่
		if (input.files && input.files.length > 0) {
			// วนลูปเพื่อแสดงตัวอย่างรูปภาพที่เลือก
			for (let i = 0; i < input.files.length; i++) {
				const file = input.files[i];
				const reader = new FileReader();

				reader.onload = function(e) {
					// สร้างการแสดงตัวอย่างรูปภาพใหม่
					const imgContainer = document.createElement('div');
					imgContainer.classList.add('gallery-item');
					imgContainer.innerHTML = `
                    <img src="${e.target.result}" alt="Gallery Image">
                    <button type="button" class="btn-delete-img" onclick="removeImage(this)" title="ลบรูปภาพนี้">
                        <i class="fa fa-times"></i>
                    </button>
                `;
					previewContainer.appendChild(imgContainer);
				};

				reader.readAsDataURL(file); // อ่านไฟล์เป็น Data URL (แสดงผลเป็นรูปภาพ)
			}
		}
	}

	// ฟังก์ชันสำหรับลบรูปที่เลือกใน preview
	function removeImage(button) {
		const item = button.closest('.gallery-item');
		item.remove();
	}

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

	function updateShippingPrices(weight) {
		const tbody = $('table tbody');
		let isWeightValid = true; // ตัวแปรเช็คว่าถูกต้องไหม

		tbody.find('tr').each(function() {
			const row = $(this);
			const methodId = row.data('method-id');
			let found = false;

			// เช็คว่า weight ตรงกับช่วงขนส่งไหน
			if (SHIPPING_PRICES[methodId]) {
				for (const sp of SHIPPING_PRICES[methodId]) {
					if (weight >= parseFloat(sp.min_weight) && weight <= parseFloat(sp.max_weight)) {
						// แสดงราคาตามช่วง
						row.find('.dynamic-shipping').val(`ช่วง ${sp.min_weight}-${sp.max_weight} g | ${parseFloat(sp.price).toFixed(2)} บาท`);

						// ตั้งค่า shipping_price_id ที่ต้องการ
						$('input[name="shipping_price_id"]').val(sp.id);
						found = true;
						break;
					}
				}
			}

			if (!found) {
				row.find('.dynamic-shipping').val('น้ำหนักสินค้าสูงเกินขีดจำกัด');
				isWeightValid = false; // ถ้าไม่เจอช่วงที่ match ก็ไม่ valid
			}

			// เพิ่มข้อความเตือนในทุกๆ ขนส่ง
			if (weight > 25000) {
				row.find('.weight-error').text('น้ำหนักสินค้าสูงเกินขีดจำกัด (25,000 กรัม)').show(); // ข้อความเตือน
			} else {
				row.find('.weight-error').hide(); // ซ่อนข้อความเตือนเมื่อถูกต้อง
			}
		});

		// แสดงหรือปิดปุ่มบันทึก
		if (weight > 25000) {
			$('#save-btn').prop('disabled', true); // ปิดปุ่มเซฟ
		} else {
			$('#save-btn').prop('disabled', false); // เปิดปุ่มเซฟ
		}
	}



	$(document).ready(function() {
		$('.select2').select2({
			width: '100%'
		});

		function toggleDiscountSection(enabled) {
			$('#discount_section').toggle(enabled).find('input').prop('disabled', !enabled);
		}

		const hasDiscount = $('#discount_toggle').is(':checked');
		toggleDiscountSection(hasDiscount);

		$('#discount_toggle').on('change', function() {
			toggleDiscountSection(this.checked);
			calculateFinalPrice();
		});

		// 🔑 เพิ่ม Event listener คำนวณทันทีเมื่อกรอก
		$('[name="price"], [name="discount_type"], [name="discount_value"]').on('input change', calculateFinalPrice);
		calculateFinalPrice(); // เรียกครั้งแรกเลย

		$('[name="product_weight"]').on('input', function() {
			const weight = parseFloat($(this).val()) || 0;
			updateShippingPrices(weight);
		});
		$('#product-form').submit(function(e) {
			e.preventDefault(); // ป้องกันการ submit ปกติ

			// เช็คว่ามีน้ำหนักเกินขีดจำกัดหรือไม่
			const weight = parseFloat($('[name="product_weight"]').val()) || 0;
			if (weight > 25000) {
				$('#weight-error').text('น้ำหนักสินค้าสูงเกินขีดจำกัด (25,000 กรัม)').show(); // แสดงข้อความเตือน
				Swal.fire({
					icon: 'error',
					title: 'น้ำหนักสินค้าสูงเกินขีดจำกัด',
					text: 'น้ำหนักไม่สามารถเกิน 25,000 กรัมได้',
				});
				return; // ไม่ส่งฟอร์ม
			}


			// หากผ่านเงื่อนไข
			$('.err-msg').remove(); // ลบ error ที่เก่าก่อนหน้า
			start_loader(); // เริ่มโหลดหน้า

			$.ajax({
				url: _base_url_ + "classes/Master.php?f=save_product", // ส่งฟอร์ม
				data: new FormData(this),
				cache: false,
				contentType: false,
				processData: false,
				method: 'POST',
				dataType: 'json',
				error: function(err) {
					console.error(err);
					alert_toast("เกิดข้อผิดพลาด", 'error');
					end_loader();
				},
				success: function(resp) {
					if (resp.status === 'success') {
						location.replace(`./?page=products`);
					} else {
						const el = $('<div>').addClass("alert alert-dark err-msg").text(resp.msg);
						$('#product-form').prepend(el);
						el.show('slow');
						$("html, body").scrollTop(0);
					}
					end_loader();
				}
			});
		});
		$('.btn-delete-img').on('click', function() {
			const imageId = $(this).data('id');
			const galleryItem = $(`#gallery-item-${imageId}`);

			Swal.fire({
				title: 'คุณแน่ใจหรือไม่?',
				text: "รูปภาพนี้จะถูกลบอย่างถาวร!",
				icon: 'warning',
				showCancelButton: true,
				confirmButtonColor: '#d33',
				cancelButtonColor: '#3085d6',
				confirmButtonText: 'ใช่, ลบเลย!',
				cancelButtonText: 'ยกเลิก'
			}).then((result) => {
				if (result.isConfirmed) {
					start_loader();
					$.ajax({
						url: _base_url_ + 'classes/Master.php?f=delete_gallery_image',
						method: 'POST',
						data: {
							id: imageId
						},
						dataType: 'json',
						success: function(resp) {
							if (resp.status === 'success') {
								galleryItem.fadeOut(300, function() {
									$(this).remove();
								});
								alert_toast('ลบรูปภาพสำเร็จ', 'success');
							} else {
								alert_toast('เกิดข้อผิดพลาด: ' + resp.msg, 'error');
							}
							end_loader();
						},
						error: function(err) {
							console.error(err);
							alert_toast('เกิดข้อผิดพลาดในการเชื่อมต่อ', 'error');
							end_loader();
						}
					});
				}
			});
		});

	});
</script>

<?php
// จัดรูปแบบ shipping_prices เป็น array แบบ group ตาม shipping_methods_id
$shipping_prices_data = [];
$shipping_q = $conn->query("SELECT * FROM shipping_prices");
while ($row = $shipping_q->fetch_assoc()) {
	$shipping_prices_data[$row['shipping_methods_id']][] = $row;
}
?>
<script>
	const SHIPPING_PRICES = <?= json_encode($shipping_prices_data) ?>;
</script>