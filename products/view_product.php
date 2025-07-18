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
	} else {
		echo "<script>alert('You don’t have access to this page'); location.replace('./');</script>";
	}
} else {
	echo "<script>alert('You don’t have access to this page'); location.replace('./');</script>";
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

?>
<style>
	.breadcrumb {
		font-size: 0.95rem;
		background: none;
		padding: 0;
		margin-bottom: 1rem;
	}

	.breadcrumb-item+.breadcrumb-item::before {
		content: "/";
	}


	.text-muted-FIXX-FIXX {
		color: #202020;
	}

	.price {
		font-size: 30px;
		font-weight: normal;
	}

	.price-head {
		font-weight: bold;
	}

	.price-n {
		color: #f57421;
		font-size: 40px;
	}

	.bg-price {
		background-color: rgba(245, 116, 33, 0.1);
		/* สีส้มโปร่งใส 30% */
	}

	.stock {
		font-size: 18px;
		font-weight: normal;
	}

	.stock-n {
		font-size: 22px;
	}

	.product-info-sticky {
		position: sticky;
		top: 2rem;
		z-index: 2;
	}

	.addcart-plus {
		background: none;
		color: #f57421;
		border: 2px solid #f57421;
		padding: 5px 15px;
		margin-right: 1rem;
	}

	.addcart {
		background: none;
		color: #f57421;
		border: 2px solid #f57421;
		padding: 10px 50px;
	}

	.group-qty {
		margin: 1rem 1rem;
	}

	.btn-shop {
		min-width: 120px;
		/* ความกว้างขั้นต่ำ */
		text-align: center;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		font-size: 14px;

		background: #f57421;
		color: white;
		border: 2px solid #f57421;
		padding: 10px 20px;
		margin-top: 0.5rem;
		margin-bottom: 0.5rem;
		margin-right: 0.5rem;
		/* <-- เว้นระยะห่างขวา */
		transition: all 0.2s ease-in-out;
	}

	.addcart:hover,
	.addcart-plus:hover {
		background-color: #f57421;
		color: white;
		display: inline-block;
	}

	.btn-shop:hover {
		color: white;
		filter: brightness(90%);
	}

	.plain-link {
		color: inherit;
		text-decoration: none;
		cursor: pointer;
		margin-left: 0.5rem;
	}

	.plain-link,
	.plain-link:visited,
	.plain-link:hover,
	.plain-link:active {
		color: inherit;
		text-decoration: none;
	}

	.sku {
		margin-left: 0.5rem;
		font-weight: normal !important;
	}


	.product-img-holder {
		width: 100%;
		aspect-ratio: 1 / 1;
		/* ทำให้กล่องภาพเป็นจัตุรัส */
		overflow: hidden;
		background: #f5f5f5;
		position: relative;
	}

	.product-img {
		width: 100%;
		height: 100%;
		object-fit: cover;
		object-position: center center;
		transition: all .3s ease-in-out;
	}

	.product-item:hover .product-img {
		transform: scale(1.1)
	}

	.bg-gradient-dark-FIXX {
		background-color: #202020;
	}

	.banner-wrapper {
		width: 100%;
		height: auto;
		object-fit: cover;
		/* ครอบคลุมทั้งจอ */
		display: block;
	}

	.banner-wrapper img {
		width: 100%;
		/* เต็มความกว้าง */
		height: auto;
		/* รักษาสัดส่วนภาพ */
		display: block;
		object-fit: cover;
		/* หรือ contain แล้วแต่ภาพ */
	}

	.card-title {
		display: -webkit-box;
		-webkit-line-clamp: 2;
		/* จำนวนบรรทัด */
		-webkit-box-orient: vertical;
		overflow: hidden;
	}

	.banner-price {
		font-size: 20px;
		color: #f57421;
	}

	.modal-content {
		background-color: #fff;
		/* หรือสีที่ต้องการ เช่น white */
		border: none;
		box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
		/* เพิ่มเงาเบา ๆ ถ้าอยากให้ลอย */
	}

	/* สำหรับป้ายสินค้าหมด */
	.out-of-stock-label {
		position: absolute;
		top: 50%;
		left: 50%;
		transform: translate(-50%, -50%);
		background-color: rgba(255, 0, 0, 0.7);
		/* สีแดงโปร่งแสง */
		color: white;
		padding: 8px 15px;
		border-radius: 5px;
		font-weight: bold;
		z-index: 10;
		/* ให้แสดงทับบนรูปภาพ */
		text-align: center;
		white-space: nowrap;
	}

	/* สำหรับสินค้าที่หมดสต็อก */
	.out-of-stock .product-img {
		filter: grayscale(100%);
		/* ทำให้รูปภาพเป็นขาวดำ */
		opacity: 0.6;
		/* ทำให้รูปภาพจางลง */
	}

	/* อาจจะเพิ่ม Overlay เมื่อสินค้าหมด */
	.out-of-stock .product-img-holder::before {
		content: '';
		position: absolute;
		top: 0;
		left: 0;
		width: 100%;
		height: 100%;
		background-color: rgba(0, 0, 0, 0.3);
		/* Overlay สีดำโปร่งแสง */
		z-index: 5;
		/* อยู่ใต้ label แต่ทับรูปภาพ */
	}

	.product-description-mobile {
		display: none;
	}

	.product-specs {
		font-size: 14px;
		line-height: 1.6;
		color: #000;
	}

	.spec-row {
		display: flex;
		justify-content: space-between;
		padding: 6px 0;
		border-bottom: 1px solid #f1f1f1;
	}

	.spec-label {
		color: #888;
		/* สีเทาอ่อน */
		flex: 0 0 40%;
		word-break: break-word;
	}

	.spec-value {
		flex: 1;
		text-align: right;
		font-weight: 500;
		color: #333;
		word-break: break-word;
	}

	.badge-sm {
		font-size: 12px;
		/* ลดขนาดฟอนต์ */
		padding: 4px 5px;
		/* ปรับ padding */
		background-color: #f79c60;
	}


	.btn-readmore {
		min-width: 120px;
		/* ความกว้างขั้นต่ำ */
		text-align: center;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		font-size: 14px;

		background: #f57421;
		color: white;
		border: 2px solid #f57421;
		padding: 10px 20px;
		margin-top: 0.5rem;
		margin-bottom: 0.5rem;
		margin-right: 0.5rem;
		/* <-- เว้นระยะห่างขวา */
		transition: all 0.2s ease-in-out;
	}

	.btn-readmore:hover {
		color: white;
		filter: brightness(90%);
	}

	#text-pc {
		max-height: 150px;
		/* ย่อตามความเหมาะสม */
		overflow: hidden;
		transition: max-height 0.3s ease;
		position: relative;
	}

	#text-pc.collapsed::after {
		content: "";
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 50px;
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0), white);
	}

	#text-pc.expanded {
		max-height: none;
	}

	/* มือถือ */
	#text-mobile {
		max-height: 150px;
		/* ย่อตามความเหมาะสม */
		overflow: hidden;
		transition: max-height 0.3s ease;
		position: relative;
	}

	#text-mobile.collapsed::after {
		content: "";
		position: absolute;
		bottom: 0;
		left: 0;
		width: 100%;
		height: 50px;
		background: linear-gradient(to bottom, rgba(255, 255, 255, 0), white);
	}

	#text-mobile.expanded {
		max-height: none;
	}

	.more-text {
		line-height: 1.5;
		/* หรือ 1.6, 1.8 ตามความเหมาะสม */
	}


	@media only screen and (max-width: 768px) {
		.product-info-sticky {
			position: static !important;
		}

		.price-n {
			font-size: 28px;
		}

		.stock-n {
			align-items: center;
			font-size: 20px;
		}

		.addcart {
			margin-top: 1rem;
		}

		.btn-shop,
		.addcart {
			width: 100%;
			margin-right: 0 !important;
			padding: 10px 0;
		}

		.modal-dialog.modal-lg {
			max-width: 95% !important;
		}

		.product-description-mobile {
			display: block;
		}

		.product-description-mobile-pc {
			display: none;
		}

		.product-specs {
			font-size: 14px;
			line-height: 1.6;
			color: #000;
		}

		.spec-row {
			display: flex;
			justify-content: space-between;
			padding: 12px 0;
			border-bottom: 1px solid #f1f1f1;
		}

		.spec-label {
			color: #888;
			/* สีเทาอ่อน */
			flex: 0 0 50%;
			word-break: break-word;
		}

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
								<!-- รูปสินค้า (ซ้าย) -->
								<div class="col-md-5 mb-3">
									<a href="#" data-toggle="modal" data-target="#productImageModal">
										<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>"
											alt="<?= isset($name) ? $name : '' ?>"
											class="img-thumbnail p-0 border w-100"
											id="product-img">
									</a>

									<!----------------- Desktop ----------------->
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
									<!-- คำอธิบายสินค้าใต้รูป -->
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
								<div class=""></div>
								<!-- รายละเอียดสินค้า (ขวา) -->
								<div class="col-md-7 product-info-sticky">
									<h2 class="fw-bold mb-3"><?= isset($name) ? $name : "" ?></h2>
									<p class="mb-3 text-muted">แบรนด์: <b><?= isset($brand) ? $brand : "" ?></b></p>
									<?php
									$discount_type_label = null;
									$percent_off = 0;

									if (!empty($discounted_price) && $discounted_price < $price) {
										$percent_off = round((($price - $discounted_price) / $price) * 100);

										if ($percent_off >= 50) {
											$discount_type_label = 'hot';  // 🔥 ลดราคาร้อน
										} else {
											$discount_type_label = 'normal'; // ลดราคาธรรมดา
										}
									}
									?>


									<?php if ($discount_type_label === 'hot'): ?>
										<!-- 🔥 ลดราคาร้อน -->
										<section class="mb-3">
											<div class="border rounded overflow-hidden shadow-sm">
												<div class="bg-danger text-white px-3 py-2">
													<h3 class="price-head m-0">🔥 ลดราคาร้อน</h3>
												</div>
												<div class="bg-price px-3 py-3">
													<div class="d-flex align-items-center mb-2">
														<div class="price-n m-0 mr-2 px-3 py-1 rounded" style="font-weight: bold;">
															<?= format_num($discounted_price, 2) ?> ฿
														</div>
														<span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 8px;">-<?= $percent_off ?>%</span>
													</div>
													<div class="price-old m-0 mr-2 px-3" style="text-decoration: line-through; color: #888;">
														<?= format_num($price, 2) ?> ฿
													</div>
												</div>
											</div>
										</section>

									<?php elseif ($discount_type_label === 'normal'): ?>
										<!-- ✅ ลดราคาธรรมดา -->
										<section class="mb-3">
											<div class="border rounded overflow-hidden shadow-sm">
												<div class="bg-warning text-dark px-3 py-2">
													<h3 class="price-head m-0">📉 ลดราคา</h3>
												</div>
												<div class="bg-price px-3 py-3">
													<div class="d-flex align-items-center mb-2">
														<div class="price-n m-0 mr-2 px-3 py-1 rounded" style="font-weight: bold; ">
															<?= format_num($discounted_price, 2) ?> ฿
														</div>
														<span class="badge badge-success" style="font-size: 0.8rem; padding: 4px 8px;">-<?= $percent_off ?>%</span>
													</div>
													<div class="price-old m-0 mr-2 px-3" style="text-decoration: line-through; color: #888;">
														<?= format_num($price, 2) ?> ฿
													</div>
												</div>
											</div>
										</section>

									<?php else: ?>
										<!-- ไม่มีโปรโมชัน -->
										<dl>
											<dd class="price-n"><?= format_num($price, 2) ?> ฿</dd>
										</dl>
									<?php endif; ?>

									<!-- 🧾 สินค้าในคลัง -->
									<dl>
										<dt class="text-muted stock">สินค้าในคลัง</dt>
										<dd class="pl-4 stock-n">
											<?= isset($available) ? format_num($available, 0) : "" ?>
										</dd>
									</dl>

									<!-- ปุ่ม Add to Cart -->
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

									<!-- ติดต่อสอบถาม -->
									<div class=" mt-4">
										<div class="border rounded p-3 bg-light shadow-sm">
											<h6 class="fw-bold">ติดต่อสอบถาม</h6>
											<p class="mb-1"><i class="fab fa-line text-success"></i><a href="<?php echo $_settings->info('Line') ?>" target="_blank"> Line </a></p>
											<p class="mb-0"><i class="fab fa-facebook text-primary"></i><a href="<?php echo $_settings->info('Facebook') ?>" target="_blank"> Facebook </a></p>
											<p class="mb-0"><i class="fa fa-phone text-primary"></i> โทร: <?php echo $_settings->info('mobile') ?></p>
										</div>
									</div>


									<!----------------- Mobile ----------------->
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
										<!-- คำอธิบายสินค้าใต้รูป -->
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
							<!--div class="card-footer py-1 text-center">
					<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?p=products"><i class="fa fa-angle-left"></i>กลับไปยังสินค้าทั้งหมด</a>
				</div-->
						</div>
					</div>
				</div>

				<!-- Modal รูปสินค้า -->
				<div class="modal fade" id="productImageModal" tabindex="-1" role="dialog" aria-hidden="true">
					<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
						<div class="modal-content position-relative">
							<!-- ปุ่มกากบาท -->
							<button type="button" class="close position-absolute" style="right: 10px; top: 10px; z-index: 10;" data-dismiss="modal" aria-label="Close">
								<i class="fa fa-times"></i>
							</button>

							<div class="modal-body p-0 text-center">
								<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>"
									alt="<?= isset($name) ? $name : '' ?>"
									class="img-fluid rounded">
							</div>
						</div>
					</div>
				</div>


			</div>

			<!--------------------สินค้าที่เกี่ยวข้อง--------------------->
			<?php
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
											<a class="card rounded-0 shadow product-item text-decoration-none text-reset h-100 <?= ($rel['available'] <= 0 ? 'out-of-stock' : '') ?>" href="./?p=products/view_product&id=<?= $rel['id'] ?>">
												<div class="position-relative">
													<div class="img-top position-relative product-img-holder">
														<?php if ($rel['available'] <= 0): ?>
															<div class="out-of-stock-label">สินค้าหมด</div>
														<?php endif; ?>
														<img src="<?= validate_image($rel['image_path']) ?>" alt="<?= $rel['name'] ?>" class="product-img">
													</div>
												</div>
												<div class="card-body">
													<div style="line-height:1.5em">
														<div class="card-title w-100 mb-0"><?= $rel['name'] ?></div>
														<div class="d-flex justify-content-between w-100 mb-3" style="height: 2.5em; overflow: hidden;">
															<div class="w-100">
																<small class="text-muted" style="line-height: 1.25em; display: block;">
																	<?= $rel['brand'] ?>
																</small>
															</div>
														</div>
														<div class="d-flex justify-content-end align-items-center">
															<?php if (!is_null($rel['discounted_price']) && $rel['discounted_price'] < $rel['price']): ?>

																<span class="banner-price fw-bold me-2"><?= format_price_custom($rel['discounted_price']) ?> ฿</span>

																<?php $discount_percentage = round((($rel['price'] - $rel['discounted_price']) / $rel['price']) * 100); ?>
																<span class="badge badge-sm text-white">ลด <?= $discount_percentage ?>%</span>

															<?php else: ?>
																<span class="banner-price"><?= format_price_custom($rel['price']) ?> ฿</span>
															<?php endif; ?>
														</div>

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
</script>