<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("
		SELECT 
		p.*, 
		c.name AS `category`, 
		t.name AS `product_type`,
		(
			COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = p.id), 0) - 
			COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = p.id), 0)
		) AS `available`
		FROM `product_list` p
		INNER JOIN `category_list` c ON p.category_id = c.id
		LEFT JOIN `product_type` t ON c.product_type_id = t.id
		WHERE p.id = '{$_GET['id']}' AND p.delete_flag = 0
	");

	if ($qry->num_rows > 0) {
		foreach ($qry->fetch_assoc() as $k => $v) {
			$$k = $v;
		}
	}
}

function formatDateThai($date)
{
	// ถ้าวันที่ว่างหรือไม่ถูกต้อง
	if (empty($date)) {
		return 'ข้อมูลวันที่ไม่ถูกต้อง';
	}

	// แปลงวันที่เป็น timestamp
	$timestamp = strtotime($date);
	if ($timestamp === false) {
		return 'ข้อมูลวันที่ไม่ถูกต้อง';
	}

	// ดึงข้อมูลวัน เดือน ปี (พ.ศ.) และเวลา
	$day = date("j", $timestamp);
	$month = date("n", $timestamp);
	$year = date("Y", $timestamp); // ปี (พ.ศ.)
	$hour = date("H", $timestamp); // ชั่วโมง (00-23)
	$minute = date("i", $timestamp); // นาที (00-59)

	// ส่งคืนวันที่ในรูปแบบไทย
	return "{$day}/{$month}/{$year} เวลา {$hour}:{$minute}";
}
?>
<style>
	#product-img {
		max-width: 100%;
		max-height: 20em;
		object-fit: scale-down;
		object-position: center center;
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
</style>
<section class="card card-outline card-orange rounded-0">
	<div class="card-header">
		<div class="card-title text-bold">รายละเอียดสต๊อกสินค้า</div>
	</div>
	<div class="card-body">
		<div class="flex-column  justify-content-center align-items-center">
			<div class="card card-outline card-dark rounded-0 mb-3">
				<div class="card-header">
					<div class="card-title" style="font-size: 18px !important;">สินค้า</div>
				</div>
				<div class="card-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-lg-4 col-md-5 col-sm-12 col-xs-12">
								<center>
									<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>" alt="<?= isset($name) ? $name : '' ?>" class="img-thumbnail p-0 border" id="product-img">
								</center>
							</div>
							<div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
								<dl>
									<div class="container-fluid">
										<div class="row">
											<div class="col-md-3">
												<dt class="text-muted">ชื่อสินค้า</dt>
												<a href="./?page=products/manage_product&id=<?= isset($id) ? $id : '' ?>" target="_blank"><?= isset($name) ? $name : "" ?></a>
											</div>
											<div class="col-md-3">
												<dt class="text-muted">รหัสสินค้า</dt>
												<dd><?= $sku ?? '' ?></dd>
											</div>
											<div class="col-md-3">
												<dt class="text-muted">แบรนด์</dt>
												<dd><?= $brand ?? '' ?></dd>
											</div>
											<div class="col-md-3">&nbsp;</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<dt class="text-muted">หมวดหมู่</dt>
												<dd><?= $category ?? '' ?></dd>
											</div>
											<div class="col-md-3">
												<dt class="text-muted">ประเภทสินค้า</dt>
												<dd><?= $product_type ?? '' ?></dd>
											</div>
											<div class="col-md-3">&nbsp;</div>
											<div class="col-md-3">&nbsp;</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<dt class="text-muted">ราคา</dt>
												<dd><?= isset($price) ? format_num($price, 2) . ' ฿' : '' ?></dd>
											</div>
											<div class="col-md-3">
												<dt class="text-muted">ราคาปัจจุบัน(VAT)</dt>
												<dd>
													<?php
													$price = (float) ($price ?? 0);
													$vat_price = !empty($vat_price) ? (float)$vat_price : null;
													$discounted_price = !empty($discounted_price) ? (float)$discounted_price : null;

													if (!is_null($discounted_price)) {
														// แสดงราคาส่วนลด ถ้ามี
														echo '<span class="text-danger font-weight-bold">' . number_format($discounted_price, 0, '.', ',') . ' ฿</span>';
													} elseif (!is_null($vat_price)) {
														// แสดง VAT ถ้ามี
														echo number_format($vat_price, 0, '.', ',') . ' ฿';
													} else {
														// แสดงราคาเต็ม
														echo number_format($price, 0, '.', ',') . ' ฿';
													}
													?>
												</dd>
											</div>
											<div class="col-md-3">&nbsp;</div>
											<div class="col-md-3">&nbsp;</div>
										</div>

										<div class="row">
											<div class="col-md-3">
												<dt class="text-muted h6 mb-0">จำนวนสินค้าที่มี</dt>
												<dd class="h5"><?= isset($available) ? format_num($available, 0) : "0" ?></dd>
											</div>
											<div class="col-md-3">&nbsp;</div>
											<div class="col-md-3">&nbsp;</div>
											<div class="col-md-3">&nbsp;</div>
										</div>
									</div>

								</dl>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer py-1 text-center">
					<a class="btn btn-light btn-sm border rounded-0" href="./?page=inventory"><i class="fa fa-angle-left"></i> กลับ</a>
					<button class="btn btn-dark btn-sm rounded-0" type="button" id="new_entry"><i class="fa fa-plus-square"></i> เพิ่มสต๊อกสินค้าใหม่</button>
				</div>
			</div>
		</div>
		<div class="card card-outline card-dark rounded-0 mb-3">
			<div class="card-header">
				<div class="card-title">ประวัติการเพิ่มสต๊อก</div>
			</div>
			<div class="card-body">
				<div class="container-fluid">
					<table class="table table-striped table-bordered" id="stock-history">
						<colgroup>
							<col width="20%">
							<col width="35%">
							<col width="35%">
							<col width="10%">
						</colgroup>
						<thead>
							<tr>
								<th class="p-1 text-center">วันที่</th>
								<th class="p-1 text-center">SKU</th>
								<th class="p-1 text-center">จำนวน</th>
								<th class="p-1 text-center">จัดการ</th>
							</tr>
						</thead>
						<tbody>
							<?php if (isset($id)): ?>
								<?php
								$stocks = $conn->query("SELECT * FROM `stock_list` where product_id = '{$id}' order by abs(unix_timestamp(date_created))");
								while ($row = $stocks->fetch_assoc()):
								?>
									<tr class="<?= !empty($row['expiration']) && (strtotime($row['expiration']) <= strtotime(date("Y-m-d"))) ? "text-danger" : "" ?>">
										<td class="p-1 align-middle"><?= formatDateThai($row['date_created']); ?></td>
										<td class="p-1 align-middle"><?= $row['sku'] ?></td>
										<td class="p-1 text-right align-middle"><?= format_num($row['quantity'], 0) ?><?= !empty($row['expiration']) && (strtotime($row['expiration']) <= strtotime(date("Y-m-d"))) ? " (Expired)" : "" ?></td>
										<td class="p-1 text-center align-middle">
											<div class="btn-group">
												<button class="btn btn-flat btn-xs bg-gradient-dark edit_stock" title="Edit Stock" type="button" data-id='<?= $row['id'] ?>'><i class="fa fa-edit text-sm"></i></button>
												<button class="btn btn-flat btn-xs btn-danger bg-gradient-danger delete_stock" title="Delete Stock" type="button" data-code='<?= $row['sku'] ?>' data-id='<?= $row['id'] ?>'><i class="fa fa-trash text-sm"></i></button>
											</div>
										</td>
									</tr>
								<?php endwhile; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
	$(function() {
		$('#new_entry').click(function() {
			uni_modal('<i class="far fa-plus-square"></i> เพิ่มสต๊อกสินค้าใหม่', 'inventory/manage_stock.php?pid=<?= isset($id) ? $id : '' ?>')
		})
		$('.edit_stock').click(function() {
			uni_modal('<i class="fa fa-edit"></i> แก้ไขสต๊อกสินค้า', 'inventory/manage_stock.php?pid=<?= isset($id) ? $id : '' ?>&id=' + $(this).attr('data-id'))
		})
		$('.delete_stock').click(function() {
			_conf("คุณแน่ใจหรือไม่ที่จะลบสต๊อก <b>[" + $(this).attr('data-code') + "]</b> นี้?", "delete_stock", [$(this).attr('data-id')])
		})
		$('#stock-history').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [4]
			}],
			order: [0, 'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')

	})

	function delete_stock($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_stock",
			method: "POST",
			data: {
				id: $id
			},
			dataType: "json",
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function(resp) {
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>