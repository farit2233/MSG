<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT p.*, c.id as `category_id`, c.name as `category`, 
(COALESCE((SELECT SUM(quantity) FROM stock_list WHERE product_id = p.id), 0)
- COALESCE((SELECT SUM(quantity) FROM order_items WHERE product_id = p.id), 0)
) as `available`
FROM product_list p
INNER JOIN category_list c ON p.category_id = c.id
WHERE p.id = '{$_GET['id']}' AND p.delete_flag = 0");
	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
		// คำนวณจำนวนที่สั่งได้สูงสุด
		if ($available >= 100) {
			$max_order_qty = floor($available / 3);
		} elseif ($available >= 50) {
			$max_order_qty = floor($available / 2);
		} elseif ($available >= 30) {
			$max_order_qty = floor($available / 1.5);
		} else {
			$max_order_qty = max(1, floor($available / 1));
		}
		$product_images = [];
		// 1. เพิ่มรูปภาพหลักเป็นรูปแรกในอาร์เรย์
		if (isset($image_path) && !empty($image_path)) {
			$product_images[] = validate_image($image_path);
		}

		// 2. ดึงรูปภาพเพิ่มเติมจากตาราง product_image_path
		$img_qry = $conn->query("SELECT * FROM `product_image_path` WHERE product_id = '{$id}' ORDER BY `id` ASC");
		while ($row = $img_qry->fetch_assoc()) {
			$product_images[] = validate_image($row['image_path']);
		}
	} else {
		echo "<script>alert('You don't have access to this page'); location.replace('./');</script>";
	}
} else {
	echo "<script>alert('You don't have access to this page'); location.replace('./');</script>";
}

$platform_links = [
	'shopee' => '',
	'lazada' => '',
	'tiktok' => ''
];

$plat_q = $conn->query("SELECT shopee_url, lazada_url, tiktok_url FROM product_links WHERE product_id = '{$_GET['id']}'");
if ($plat_q && $plat_q->num_rows > 0) {
	$row = $plat_q->fetch_assoc();
	$platform_links['shopee'] = $row['shopee_url'] ?? '';
	$platform_links['lazada'] = $row['lazada_url'] ?? '';
	$platform_links['tiktok'] = $row['tiktok_url'] ?? '';
}

// ตรวจสอบและสร้างฟังก์ชันสำหรับจัดรูปแบบราคา (หากยังไม่มี)
if (!function_exists('format_price_custom')) {
	function format_price_custom($price)
	{
		$formatted_price = format_num($price, 2);
		if (substr($formatted_price, -3) == '.00') {
			return format_num($price, 0);
		}
		return $formatted_price;
	}
}
if (!function_exists('format_price_custom')) {
	function format_price_custom($discounted_price)
	{
		$formatted_price = format_num($discounted_price, 2);
		if (substr($formatted_price, -3) == '.00') {
			return format_num($discounted_price, 0);
		}
		return $formatted_price;
	}
}
?>
<style>
	#productImageModal .modal-dialog {
		max-width: 800px !important;
		z-index: 1050 !important;
		margin: auto;
		position: fixed;
		top: 28%;
		left: 50%;
		transform: translate(-50%, -50%);
	}

	.product-gallery {
		display: flex;
		flex-wrap: wrap;
		gap: 10px;
		/* ระยะห่างระหว่างรูป */
	}

	.gallery-thumbnail {
		width: 80px;
		height: 80px;
		object-fit: cover;
		cursor: pointer;
		border: 2px solid #ddd;
		border-radius: 4px;
		transition: border-color 0.3s;
	}

	/* Modal Navigation */
	#productImageModal .modal-prev,
	#productImageModal .modal-next {
		cursor: pointer;
		position: absolute;
		top: 50%;
		width: auto;
		padding: 16px;
		margin-top: -30px;
		color: white;
		font-weight: bold;
		font-size: 24px;
		transition: 0.3s ease;
		border-radius: 0 3px 3px 0;
		user-select: none;
		background-color: rgba(0, 0, 0, 0.5);
	}

	#productImageModal .modal-next {
		right: 0;
		border-radius: 3px 0 0 3px;
	}

	#productImageModal .modal-prev {
		left: 0;
	}

	#productImageModal .modal-prev:hover,
	#productImageModal .modal-next:hover {
		background-color: rgba(0, 0, 0, 0.8);
	}

	.image-modal {
		width: 70%;
		display: block;
		margin-left: auto;
		margin-right: auto;
	}

	.product-gallery-container {
		position: relative;
		overflow-x: hidden;
		/* ไม่ให้ scroll visible */
		padding: 10px 0;
		display: flex;
		align-items: center;
		/* ทำให้ปุ่มอยู่ในแนวเดียวกัน */
	}

	.product-gallery {
		display: flex;
		flex-wrap: nowrap;
		gap: 10px;
		overflow-x: auto;
		scroll-behavior: smooth;
		/* ทำให้การเลื่อนดูนุ่มนวล */
		padding-bottom: 10px;
		/* ปรับระยะห่างด้านล่าง */
	}

	/* สไตล์รูปภาพในแกลเลอรี */
	.product-gallery img {
		width: 100px;
		/* ขนาดรูป */
		height: auto;
		cursor: pointer;
	}

	/* ปุ่มเลื่อน */
	.gallery-prev-btn,
	.gallery-next-btn {
		position: absolute;
		top: 50%;
		transform: translateY(-50%);
		background-color: rgba(0, 0, 0, 0.5);
		color: white;
		border: none;
		padding: 10px 12px;
		font-size: 16px;
		cursor: pointer;
		z-index: 2;
		display: flex;
		align-items: center;
		justify-content: center;
		border-radius: 50%;
	}

	/* ปุ่มเลื่อนซ้าย */
	.gallery-prev-btn {
		left: 10px;
	}

	/* ปุ่มเลื่อนขวา */
	.gallery-next-btn {
		right: 10px;
	}

	/* เพิ่ม effect เมื่อ hover ปุ่ม */
	.gallery-prev-btn:hover,
	.gallery-next-btn:hover {
		background-color: rgba(0, 0, 0, 0.8);
	}
</style>
<section class="py-3">
	<div class="container">
		<div class="content py-5 px-3 text-dark X">
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb bg-transparent px-0">
					<li class="breadcrumb-item"><a href="./" class="plain-link">HOME</a></li>
					<li class="breadcrumb-item"><a href="./?p=products&cid=<?= $category_id ?>" class="plain-link"><?= $category ?? 'ไม่ระบุหมวด' ?></a></li>
					<li class="breadcrumb-item active" aria-current="page"><?= $name ?? '' ?></li>
				</ol>
			</nav>
		</div>
		<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
			<div class="col-lg-12 col-md-10 col-sm-12 col-xs-12">
				<div class="card rounded-0">
					<div class="card-body">
						<div class="container-fluid">
							<div class="row align-items-start">
								<div class="col-md-5 mb-3">
									<a href="#" data-toggle="modal" data-target="#productImageModal">
										<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>"
											alt="<?= isset($name) ? $name : '' ?>"
											class="img-thumbnail p-0 border w-100"
											id="product-img">
									</a>
									<div class="product-gallery-container mt-2">
										<button class="gallery-prev-btn"><i class="fa-solid fa-chevron-left"></i></button>
										<div class="product-gallery">
											<?php foreach ($product_images as $index => $img_src): ?>
												<a href="#" data-toggle="modal" data-target="#productImageModal">
													<img src="<?= $img_src ?>"
														alt="<?= isset($name) ? $name : '' ?> - Thumbnail <?= $index + 1 ?>"
														class="gallery-thumbnail <?= ($index == 0) ? 'active' : '' ?>"
														data-full-src="<?= $img_src ?>"
														data-index="<?= $index ?>">
												</a>
											<?php endforeach; ?>
										</div>
										<button class="gallery-next-btn"><i class="fa-solid fa-chevron-right"></i></button>
									</div>

									<div class="product-description-mobile-pc mt-3">
										<h5><b>ข้อมูลจำเพาะของสินค้า</b></h5>
										<div class="product-specs">
											<div class="spec-row">
												<div class="spec-label">น้ำหนักสินค้า</div>
												<div class="spec-value"><?= $product_weight ?> กรัม.</div>
											</div>
											<?php if (!empty($product_width) && !empty($product_length) && !empty($product_height)): ?>
												<div class="spec-row">
													<div class="spec-label">ขนาดสินค้า (ก x ย x ส)</div>
													<div class="spec-value"><?= $product_width ?> x <?= $product_length ?> x <?= $product_height ?> ซม.</div>
												</div>
											<?php endif; ?>
										</div>
									</div>

									<?php if (!empty($description)): ?>
										<div class="product-description-mobile-pc mt-3">
											<h5><b>รายละเอียด</b></h5>
											<div id="text-pc" class="collapsed">
												<div class="more-text">
													<?php
													$paragraphs = preg_split('/\r\n|\r|\n/', trim($description));
													foreach ($paragraphs as $para) {
														if (trim($para) !== '') {
															echo '<p>' . htmlspecialchars(trim($para)) . '</p>';
														}
													}
													?>
												</div>
											</div>
											<div class="text-center mt-2">
												<button class="btn btn-readmore rounded-pill" id="toggleButton-pc">ดูเพิ่มเติม +</button>
											</div>
										</div>
									<?php endif; ?>
								</div>

								<div class="col-md-7 product-info-sticky">
									<h2 class="fw-bold mb-3"><?= isset($name) ? $name : "" ?></h2>
									<p class="mb-3 text-muted">แบรนด์: <b><?= isset($brand) ? $brand : "" ?></b></p>

									<?php
									$final_price = $vat_price;
									$percent_off = 0;
									$discount_type_label = null;

									// ตรวจสอบว่ามี discounted_price ไหม
									if (!empty($discounted_price) && $discounted_price < $vat_price) {
										$final_price = $discounted_price;
										$percent_off = round((($vat_price - $discounted_price) / $vat_price) * 100);
										$discount_type_label = ($percent_off >= 50) ? 'hot' : 'normal';
									} elseif (!empty($vat_price) && $vat_price > 0) {
										$final_price = $vat_price;
									}
									?>

									<?php if ($discount_type_label === 'hot'): ?>
										<section class="mb-3">
											<div class="border rounded overflow-hidden shadow-sm">
												<div class="bg-danger text-white px-3 py-2">
													<h3 class="price-head m-0">🔥 ลดราคาร้อน</h3>
												</div>
												<div class="bg-price px-3 py-3">
													<div class="d-flex align-items-center mb-2">
														<div class="price-n m-0 mr-2 px-3 py-1 rounded">
															<?= format_price_custom($final_price, 2) ?> ฿
														</div>
														<span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 8px;">-<?= $percent_off ?>%</span>
													</div>
													<div class="price-old m-0 mr-2 px-3" style="text-decoration: line-through; color: #888;">
														<?= format_price_custom($vat_price, 2) ?> ฿
													</div>
												</div>
											</div>
										</section>

									<?php elseif ($discount_type_label === 'normal'): ?>
										<section class="mb-3">
											<div class="border rounded overflow-hidden shadow-sm">
												<div class="bg-warning text-dark px-3 py-2">
													<h3 class="price-head m-0">📉 ลดราคา</h3>
												</div>
												<div class="bg-price px-3 py-3">
													<div class="d-flex align-items-center mb-2">
														<div class="price-n m-0 mr-2 px-3 py-1 rounded">
															<?= format_price_custom($final_price, 2) ?> ฿
														</div>
														<span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 8px;">-<?= $percent_off ?>%</span>
													</div>
													<div class="price-old m-0 mr-2 px-3" style="text-decoration: line-through; color: #888;">
														<?= format_price_custom($vat_price, 2) ?> ฿
													</div>
												</div>
											</div>
										</section>

									<?php else: ?>
										<dl>
											<dd class="price-n"><?= format_price_custom($final_price, 2) ?> ฿</dd>
										</dl>
									<?php endif; ?>


									<dl>
										<dt class="text-muted stock">สินค้าในคลัง</dt>
										<dd class="pl-4 stock-n">
											<?= isset($available) ? format_num($available, 0) : "" ?>
										</dd>
									</dl>

									<div class="mb-3">
										<?php if ($available > 0): ?>
											<div class="d-flex flex-wrap align-items-center group-qty">
												<div class="input-group" style="width: 20rem;">
													<button class="btn addcart-plus" style="margin-right: 5px;" type="button" onclick="decreaseQty()">−</button>
													<input type="number" id="qty" name="qty" class="form-control text-center input-mobile"
														value="1" min="1" max="<?= $max_order_qty ?>" required>
													<button class="btn addcart-plus" style="margin-left: 5px;" type="button" onclick="increaseQty()">+</button>
												</div>
												<?php if ($_settings->userdata('id') != '' && $_settings->userdata('login_type') == 2): ?>
													<button class="btn addcart rounded-pill" type="button" id="add_to_cart">
														<i class="fa-solid fa-basket-shopping"></i> หยิบใส่ตระกร้า
													</button>
												<?php else: ?>
													<button class="btn addcart rounded-pill" type="button" onclick="guest_add_to_cart()">
														<i class="fa-solid fa-basket-shopping"></i> หยิบใส่ตระกร้า
													</button>
												<?php endif; ?>
											</div>
											<div class="mb-3">
												<?php if ($slow_prepare == 1): ?>
													<div class="small-text text-danger">* ขนส่งนานกว่าปกติ</div>
												<?php endif; ?>
											</div>
											<div class="mb-3">
												<div class="mb-3 d-flex flex-wrap gap-2">
													<?php if (!empty($platform_links['shopee'])): ?>
														<a class="btn btn-shop rounded-pill" href="<?= $platform_links['shopee'] ?>" target="_blank">
															<i class="fa-brands fa-shopify"></i> Shopee
														</a>
													<?php endif; ?>
													<?php if (!empty($platform_links['lazada'])): ?>
														<a class="btn btn-shop rounded-pill" href="<?= $platform_links['lazada'] ?>" target="_blank">
															<i class="fa fa-store"></i> Lazada
														</a>
													<?php endif; ?>
													<?php if (!empty($platform_links['tiktok'])): ?>
														<a class="btn btn-shop rounded-pill" href="<?= $platform_links['tiktok'] ?>" target="_blank">
															<i class="fa-brands fa-tiktok"></i> TikTokShop
														</a>
													<?php endif; ?>
												</div>
											</div>
										<?php else: ?>
											<div class="alert alert-danger mt-2">
												<i class="fa fa-exclamation-circle"></i> สินค้าหมดชั่วคราว ขออภัยด้วยค่ะ
											</div>
										<?php endif; ?>
									</div>
									<p class="mb-3">
										หมวดหมู่สินค้า:
										<?php
										// ดึงหมวดหมู่หลัก
										$cat_main = $conn->query("SELECT name FROM category_list WHERE id = {$category_id}")->fetch_assoc();
										$main_name = $cat_main['name'] ?? 'ไม่ระบุ';

										// แสดงเฉพาะหมวดหมู่หลักอย่างเดียว
										echo '<a href="./?p=products&cid=' . $category_id . '" class="plain-link"><b>' . $main_name . '</b></a>';
										?>
										<label class="sku"> | </label> <label class="sku">รหัสสินค้า:</label> <b style="margin-left: 0.5rem;"><?= $sku ?> </b>
									</p>

									<div class=" mt-4">
										<div class="border rounded p-3 bg-light shadow-sm">
											<h6 class="fw-bold">ติดต่อสอบถาม</h6>
											<p class="mb-1"><i class="fab fa-line text-success"></i><a href="<?php echo $_settings->info('Line') ?>" target="_blank"> Line </a></p>
											<p class="mb-0"><i class="fab fa-facebook text-primary"></i><a href="<?php echo $_settings->info('Facebook') ?>" target="_blank"> Facebook </a></p>
											<p class="mb-0"><i class="fa fa-phone text-primary"></i> โทร: <?php echo $_settings->info('mobile') ?></p>
										</div>
									</div>

									<div class="product-description-mobile mt-3">
										<h5><b>ข้อมูลจำเพาะของสินค้า</b></h5>
										<div class="product-specs">
											<div class="spec-row">
												<div class="spec-label">น้ำหนักสินค้า</div>
												<div class="spec-value"><?= $product_weight ?> กรัม.</div>
											</div>
											<?php if (!empty($product_width) && !empty($product_length) && !empty($product_height)): ?>
												<div class="spec-row">
													<div class="spec-label">ขนาดสินค้า (ก x ย x ส)</div>
													<div class="spec-value"><?= $product_width ?> x <?= $product_length ?> x <?= $product_height ?> ซม.</div>
												</div>
											<?php endif; ?>
										</div>
									</div>
									<div class="col-md-5 mb-3">
										<?php if (!empty($description)): ?>
											<div class="product-description-mobile mt-3">
												<h5><b>รายละเอียด</b></h5>
												<div id="text-mobile" class="collapsed">
													<div class="more-text">
														<?php
														$paragraphs = preg_split('/\r\n|\r|\n/', trim($description));
														foreach ($paragraphs as $para) {
															if (trim($para) !== '') {
																echo '<p>' . htmlspecialchars(trim($para)) . '</p>';
															}
														}
														?>
													</div>
												</div>
												<div class="text-center mt-2">
													<button class="btn btn-readmore rounded-pill" id="toggleButton-mobile">ดูเพิ่มเติม +</button>
												</div>
											</div>

										<?php endif; ?>
									</div>

								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal fade" id="productImageModal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-image" role="document">
						<div class="modal-content position-relative">
							<button type="button" class="close position-absolute" style="right: 10px; top: 10px; z-index: 10;" data-dismiss="modal" aria-label="Close">
								<i class="fa fa-times"></i>
							</button>

							<div class="modal-body p-0 text-center">
								<div class="modal-body p-0 text-center image-modal">
									<img id="modal-image" src="<?= validate_image(isset($image_path) ? $image_path : '') ?>"
										alt="<?= isset($name) ? $name : '' ?>" class="img-fluid rounded">
								</div>
								<div class="product-gallery mt-2">
									<?php foreach ($product_images as $index => $img_src): ?>
										<img src="<?= $img_src ?>"
											alt="<?= isset($name) ? $name : '' ?> - Thumbnail <?= $index + 1 ?>"
											class="gallery-thumbnail <?= ($index == 0) ? 'active' : '' ?>"
											data-full-src="<?= $img_src ?>"
											data-index="<?= $index ?>">
									<?php endforeach; ?>
								</div>

								<a class="modal-prev"><i class="fa-solid fa-chevron-left"></i></a>
								<a class="modal-next"><i class="fa-solid fa-chevron-right"></i></a>
							</div>
						</div>
					</div>
				</div>

			</div>


			<?php
			//สินค้าที่เกี่ยวข้อง
			// เพิ่มการคำนวณ 'available' ในส่วนของสินค้าที่เกี่ยวข้อง
			$related = $conn->query("SELECT *, 
			(COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = product_list.id ), 0) 
			- COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = product_list.id), 0)) as `available` 
			FROM `product_list` 
			WHERE category_id = '{$category_id}' AND id != '{$id}' AND delete_flag = 0 
			ORDER BY RAND() LIMIT 4");

			// ============== โค้ดที่แก้ไข เริ่มต้นที่นี่ ==============

			// ตรวจสอบและสร้างฟังก์ชันสำหรับจัดรูปแบบราคา (หากยังไม่มี)
			if (!function_exists('format_price_custom')) {
				function format_price_custom($price)
				{
					$formatted_price = format_num($price, 2);
					if (substr($formatted_price, -3) == '.00') {
						return format_num($price, 0);
					}
					return $formatted_price;
				}
			}

			if ($related->num_rows > 0): ?>
				<div class="container">
					<div class="row mt-n3 justify-content-center">
						<div class="col-lg-10 col-md-11 col-sm-11 col-sm-11">

							<div class="card-body">
								<h1 align="center">สินค้าที่เกี่ยวข้อง</h1>
								<div class="row gy-3 gx-3">
									<?php while ($rel = $related->fetch_assoc()): ?>
										<div class="col-6 col-md-4 col-lg-3 d-flex" style="margin-top: 1rem;">
											<a class="card rounded-0 product-item text-decoration-none text-reset h-100" href="./?p=products/view_product&id=<?= $rel['id'] ?>">
												<div class="position-relative">
													<div class="img-top position-relative product-img-holder">
														<img src="<?= validate_image($rel['image_path']) ?>" alt="" class="product-img">
													</div>
												</div>
												<div class="card-body d-flex flex-column">
													<div>
														<div class="card-title w-100 mb-0"><?= $rel['name'] ?></div>
														<div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
															<div class="w-100">
																<small class="text-muted" style="line-height: 1.25em; display: block;">
																	<?= $rel['brand'] ?>
																</small>
															</div>
														</div>
													</div>

													<div class="d-flex justify-content-end align-items-center mt-auto">
														<?php
														// เริ่มต้นด้วย price เป็น fallback
														$display_price = isset($rel['price']) && $rel['price'] > 0 ? $rel['price'] : 0;

														if (!is_null($rel['discounted_price']) && $rel['discounted_price'] > 0 && $rel['discounted_price'] < $rel['price']) {
															$display_price = $rel['discounted_price'];
															$discount_percentage = round((($rel['price'] - $rel['discounted_price']) / $rel['price']) * 100);
															echo '<span class="banner-price fw-bold me-2">' . format_price_custom($display_price) . ' ฿</span>';
															echo '<span class="badge badge-sm text-white">ลด ' . $discount_percentage . '%</span>';
														} elseif (!is_null($rel['vat_price']) && $rel['vat_price'] > 0) {
															$display_price = $rel['vat_price'];
															echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
														} else {
															// fallback ใช้ price จริง
															echo '<span class="banner-price">' . format_price_custom($display_price) . ' ฿</span>';
														}
														?>
													</div>
												</div>
											</a>
										</div>
									<?php endwhile; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>
<script>
	document.addEventListener("DOMContentLoaded", function() {
		// มือถือ
		const textMobile = document.getElementById("text-mobile");
		const buttonMobile = document.getElementById("toggleButton-mobile");

		// PC
		const textPC = document.getElementById("text-pc");
		const buttonPC = document.getElementById("toggleButton-pc");

		// ฟังก์ชันเดียวกันสำหรับมือถือและ PC
		function toggleText(text, button) {
			if (text && button) {
				button.addEventListener("click", function() {
					const isCollapsed = text.classList.toggle("collapsed") === false;
					text.classList.toggle("expanded", isCollapsed);
					button.textContent = isCollapsed ? "ดูน้อยลง -" : "ดูเพิ่มเติม +";
				});
			}
		}

		// ผูกฟังก์ชันให้ทำงานทั้งบนมือถือและพีซี
		toggleText(textMobile, buttonMobile);
		toggleText(textPC, buttonPC);
	});


	$(function() {
		$('#add_to_cart').click(function() {
			let qty = $('#qty').val(); // ดึงจำนวนจาก input
			add_cart(qty); // เรียกฟังก์ชันโดยตรง ไม่ต้องยืนยัน
		});
	});

	function add_cart(qty) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=add_to_cart",
			method: "POST",
			data: {
				product_id: "<?= isset($id) ? $id : '' ?>",
				qty: qty
			},
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occurred.", 'error');
				end_loader();
			},
			success: function(resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else if (!!resp.msg) {
					alert_toast(resp.msg, 'error');
				} else {
					alert_toast("An error occurred.", 'error');
				}
				end_loader();
			}
		})
	}

	function decreaseQty() {
		const qtyInput = document.getElementById('qty');
		let current = parseInt(qtyInput.value) || 1;
		if (current > parseInt(qtyInput.min)) {
			qtyInput.value = current - 1;
		}
	}

	function increaseQty() {
		const qtyInput = document.getElementById('qty');
		let current = parseInt(qtyInput.value) || 1;
		const max = parseInt(qtyInput.max) || 999;
		if (current < max) {
			qtyInput.value = current + 1;
		}
	}
	document.addEventListener("DOMContentLoaded", function() {
		const qtyInput = document.getElementById("qty");
		const maxQty = parseInt(qtyInput.max);

		qtyInput.addEventListener("input", function() {
			if (parseInt(qtyInput.value) > maxQty) {
				Swal.fire({
					title: 'แจ้งเตือน',
					text: 'คุณสามารถสั่งซื้อได้สูงสุด ' + maxQty + ' ชิ้นเท่านั้น',
					icon: 'warning',
					confirmButtonText: 'ตกลง'
				});
				qtyInput.value = maxQty;
			}
		});
	});

	function update_cart_count() {
		const cart = JSON.parse(localStorage.getItem('guest_cart')) || [];
		const totalQty = cart.reduce((sum, item) => sum + parseInt(item.qty), 0);
		const cartCountEl = document.querySelector('.cart-count');
		if (cartCountEl) {
			cartCountEl.textContent = totalQty;
			cartCountEl.classList.toggle('d-none', totalQty === 0);
		}
	}


	function decreaseQty() {
		const qtyInput = document.getElementById('qty');
		let current = parseInt(qtyInput.value) || 1;
		if (current > parseInt(qtyInput.min)) {
			qtyInput.value = current - 1;
		}
	}

	function increaseQty() {
		const qtyInput = document.getElementById('qty');
		let current = parseInt(qtyInput.value) || 1;
		const max = parseInt(qtyInput.max) || 999;
		if (current < max) {
			qtyInput.value = current + 1;
		} else {
			alert_toast(`จำกัดสูงสุด ${max} ชิ้นต่อคำสั่งซื้อนะครับ 🧾`, 'warning');
		}
	}

	function guest_add_to_cart() {
		const product_id = "<?= $id ?>";
		const name = "<?= $name ?>";
		const price = <?= $price ?>;
		const discounted_price = <?= ($discounted_price && $discounted_price < $price) ? $discounted_price : 'null' ?>;
		const qty = parseInt(document.getElementById('qty').value) || 1;
		const image = "<?= validate_image($image_path) ?>";

		let cart = JSON.parse(localStorage.getItem('guest_cart')) || [];

		const index = cart.findIndex(item => item.id === product_id);
		if (index > -1) {
			cart[index].qty += qty;
		} else {
			cart.push({
				id: product_id,
				name,
				price,
				discounted_price,
				qty,
				image
			});
		}

		localStorage.setItem('guest_cart', JSON.stringify(cart));
		alert_toast("เพิ่มสินค้าในตะกร้าแล้ว", 'success');
		update_cart_count();
	}
	$(document).ready(function() {
		// คำนวณจำนวนภาพทั้งหมด
		const totalImages = <?= count($product_images) ?>;

		let currentIndex = 0; // ใช้ติดตามตำแหน่งปัจจุบันของภาพ

		// เมื่อคลิก thumbnail เพื่อเปลี่ยนภาพ
		$('.gallery-thumbnail').click(function() {
			// เอา active ออกจากภาพเก่า
			$('.gallery-thumbnail').removeClass('active');
			// กำหนดให้ thumbnail ที่คลิกเป็น active
			$(this).addClass('active');

			// อัพเดต src ของภาพใน modal
			$('#modal-image').attr('src', $(this).data('full-src'));

			// อัพเดตตำแหน่งปัจจุบัน
			currentIndex = $(this).data('index');
		});

		// คลิกปุ่มเลื่อนไปข้างหน้า (next)
		$('.modal-next').click(function() {
			currentIndex = (currentIndex + 1) % totalImages; // ทำให้เลื่อนไปเรื่อยๆ (วนลูป)
			changeImage(currentIndex);
		});

		// คลิกปุ่มเลื่อนย้อนกลับ (prev)
		$('.modal-prev').click(function() {
			currentIndex = (currentIndex - 1 + totalImages) % totalImages; // เลื่อนไปย้อนกลับ (วนลูป)
			changeImage(currentIndex);
		});

		$('.gallery-next-btn').click(function() {
			$('.product-gallery').animate({
				scrollLeft: $('.product-gallery').scrollLeft() + 120 // เลื่อน 120px ขวา
			}, 300); // ความเร็วในการเลื่อน
		});

		// เมื่อกดปุ่มเลื่อนซ้าย
		$('.gallery-prev-btn').click(function() {
			$('.product-gallery').animate({
				scrollLeft: $('.product-gallery').scrollLeft() - 120 // เลื่อน 120px ซ้าย
			}, 300); // ความเร็วในการเลื่อน
		});
		// ฟังก์ชันในการเปลี่ยนภาพ
		function changeImage(index) {
			// เลือก thumbnail ที่ตรงกับ index
			const selectedThumbnail = $('.gallery-thumbnail').eq(index);

			// อัพเดต active class
			$('.gallery-thumbnail').removeClass('active');
			selectedThumbnail.addClass('active');

			// อัพเดต src ของภาพใน modal
			$('#modal-image').attr('src', selectedThumbnail.data('full-src'));
		}
	});
</script>