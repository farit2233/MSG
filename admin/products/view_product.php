<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
	$qry = $conn->query("SELECT p.*, c.name as `category` from `product_list` p inner join `category_list` c on p.category_id = c.id where p.id = '{$_GET['id']}' and p.delete_flag = 0 ");
	if ($qry->num_rows > 0) {
		$row = $qry->fetch_assoc();
		foreach ($row as $k => $v) {
			$$k = $v;
		}
		$discounted_price = $price;
		if (isset($discount_type) && isset($discount_value)) {
			if ($discount_type === 'percent') {
				$discounted_price = $price - ($price * ($discount_value / 100));
			} elseif ($discount_type === 'amount') {
				$discounted_price = $price - $discount_value;
			}
			// ไม่ให้ติดลบ
			if ($discounted_price < 0) $discounted_price = 0;
		}
	}
}
?>
<style>
	#product-img {
		max-width: 100%;
		max-height: 35em;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="content py-5 px-3 bg-gradient-dark">
	<h2><b>รายละเอียดสินค้า</b></h2>
</div>
<div class="row mt-lg-n4 mt-md-n4 justify-content-center">
	<div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
		<div class="card rounded-0">
			<div class="card-body">
				<div class="container-fluid">
					<center>
						<img src="<?= validate_image(isset($image_path) ? $image_path : '') ?>" alt="<?= isset($name) ? $name : '' ?>" class="img-thumbnail p-0 border" id="product-img">
					</center>
					<dl>
						<dt class="text-muted">แบรนด์</dt>
						<dd class="pl-4"><?= isset($brand) ? $brand : "" ?></dd>
						<dt class="text-muted">ชื่อสินค้า</dt>
						<dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
						<dt class="text-muted">หมวดหมู่สินค้า</dt>
						<dd class="pl-4"><?= isset($category) ? $category : "" ?></dd>
						<dt class="text-muted">รายละเอียดสินค้า</dt>
						<dd class="pl-4"><?= isset($description) ? str_replace(["\n\r", "\n", "\r"], "<br>", $description) : '' ?></dd>
						<dt class="text-muted">ราคา</dt>
						<dd class="pl-4">
							<?php if (!empty($discounted_price) && $discounted_price < $price): ?>
								<span class="text-muted" style="text-decoration: line-through;">
									<?= format_num($price, 2) ?> ฿
								</span>
								<span class="text-danger font-weight-bold ml-2">
									<?= format_num($discounted_price, 2) ?> ฿
								</span>
								<?php
								$percent_off = round((($price - $discounted_price) / $price) * 100);
								?>
								<span class="badge badge-success ml-2">
									<?= $percent_off ?>% OFF
								</span>
							<?php else: ?>
								<span class="font-weight-bold"><?= format_num($price, 2) ?> ฿</span>
							<?php endif; ?>

						</dd>
						<dt class="text-muted">สถานะ</dt>
						<dd class="pl-4">
							<?php if ($status == 1): ?>
								<span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>
							<?php else: ?>
								<span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>
							<?php endif; ?>
						</dd>
					</dl>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-danger btn-sm bg-gradient-danger rounded-0" type="button" id="delete_data"><i class="fa fa-trash"></i> ลบสินค้า</button>
				<a class="btn btn-dark btn-sm bg-gradient-dark rounded-0" href="./?page=products/manage_product&id=<?= isset($id) ? $id : '' ?>"><i class="fa fa-edit"></i> แก้ไขสินค้า</a>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=products"><i class="fa fa-angle-left"></i> กลับ</a>
			</div>
		</div>
	</div>
</div>
<script>
	$(function() {
		$('#delete_data').click(function() {
			_conf("Are you sure to delete this product permanently?", "delete_product", ["<?= isset($id) ? $id : '' ?>"])
		})
	})

	function delete_product($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Master.php?f=delete_product",
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
					location.replace("./?page=products");
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>