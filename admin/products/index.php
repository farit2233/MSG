<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.product-img {
		width: 3em;
		height: 3em;
		object-fit: cover;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<h3 class="card-title text-bold">สินค้าทั้งหมด</h3>
		<div class="card-tools">
			<a href="./?page=products/manage_product" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างสินค้าใหม่</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="10%">
				</colgroup>
				<thead class="text-center">
					<tr>
						<th>ที่</th>
						<th>วันที่สร้าง</th>
						<th>รูปภาพสินค้า</th>
						<th>แบรนด์</th>
						<th>ชื่อสินค้า</th>
						<th>ราคา</th>
						<th>สถานะ</th>
						<th>จัดการ</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$i = 1;
					$qry = $conn->query("SELECT * FROM `product_list` WHERE delete_flag = 0 ORDER BY `brand` ASC, `name` ASC ");
					while ($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
							<td class="text-center">
								<img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail p-0 border product-img">
							</td>
							<td class=""><?= $row['brand'] ?></td>
							<td class="">
								<div style="line-height:1em">
									<div><?= $row['name'] ?></div>
									<div><small class="text-muted"><?= $row['dose'] ?></small></div>
								</div>
							</td>
							<td class="text-right">
								<?php
								$price = (float) $row['price'];
								$discounted_price = $price;

								if (!empty($row['discount_type']) && !empty($row['discount_value'])) {
									if ($row['discount_type'] === 'percent') {
										$discounted_price = $price - ($price * ($row['discount_value'] / 100));
									} elseif ($row['discount_type'] === 'amount') {
										$discounted_price = $price - $row['discount_value'];
									}
									if ($discounted_price < 0) $discounted_price = 0;
								}
								?>

								<?php if ($discounted_price < $price): ?>
									<span class="text-muted" style="text-decoration: line-through;"><?= format_num($price, 2) ?> ฿</span><br>
									<span class="text-danger font-weight-bold"><?= format_num($discounted_price, 2) ?> ฿</span><br>
									<small class="badge badge-success">
										<?= $row['discount_type'] === 'percent'
											? $row['discount_value'] . '% OFF'
											: format_num($row['discount_value'], 2) . '฿ OFF' ?>
									</small>
								<?php else: ?>
									<span class="font-weight-bold"><?= format_num($price, 2) ?> ฿</span>
								<?php endif; ?>
							</td>

							<td class="text-center">
								<?php if ($row['status'] == 1): ?>
									<span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>
								<?php else: ?>
									<span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>
								<?php endif; ?>
							</td>
							<td class="text-center">
								<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									จัดการ
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item" href="./?page=products/view_product&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> ดู</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item" href="./?page=products/manage_product&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> แก้ไขสินค้า</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> ลบสินค้า</a>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.delete_data').click(function() {
			_conf("Are you sure to delete this product permanently?", "delete_product", [$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [2, 6]
			}],
			order: [0, 'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
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
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>