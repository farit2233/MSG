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

	.card-title {
		font-size: 20px !important;
		font-weight: bold;
	}

	section {
		font-size: 16px;
	}
</style>
<section class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<div class="card-title">สต๊อกสินค้า</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered" id="list">
					<colgroup>
						<col width="5%">
						<col width="10%">
						<col width="20%">
						<col width="30%">
						<col width="10%">
						<col width="15%">
						<col width="10%">
					</colgroup>
					<thead class="text-center">
						<tr>
							<th>ที่</th>
							<th>รูปภาพสินค้า</th>
							<th>แบรนด์</th>
							<th>ชื่อสินค้า</th>
							<th>มีอยู่ (ชิ้น)</th>
							<th>วันที่สร้าง</th>
							<th>จัดการ</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;

						// กำหนดเกณฑ์ใกล้หมด
						$low_stock_threshold = 10;

						$qry = $conn->query("
							SELECT pl.*, 
								(COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = pl.id), 0) 
								- COALESCE((SELECT SUM(quantity) FROM `order_items` WHERE product_id = pl.id), 0)) AS available
							FROM `product_list` pl
							WHERE pl.delete_flag = 0 AND pl.`status` = 1
							ORDER BY 
								available ASC,       -- สินค้าใกล้หมดขึ้นก่อน
								pl.brand ASC, 
								pl.name ASC
						");

						while ($row = $qry->fetch_assoc()):
							// ตรวจสอบว่าจำนวนสินค้าต่ำกว่าเกณฑ์หรือไม่
							$stock_class = ($row['available'] <= $low_stock_threshold) ? 'text-danger fw-bold' : '';
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
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
								<td class="text-center <?= $stock_class ?>"><?= format_num($row['available'], 0) ?></td>
								<td class="text-center"><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
								<td class="text-center">
									<a class="btn btn-sm btn-flat btn-light bg-gradient-light border" href="./?page=inventory/view_inventory&id=<?php echo $row['id'] ?>">
										<span class="fa fa-edit text-dark"></span> แก้ไขสต๊อก
									</a>
								</td>
							</tr>
						<?php endwhile; ?>

				</table>
			</div>
		</div>
	</div>
</section>
<script>
	$(document).ready(function() {

		$('.table').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [2, 6]
			}],
			order: [0, 'asc'],
			language: {
				lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
				zeroRecords: "ไม่พบข้อมูล",
				info: "แสดงหน้าที่ _PAGE_ จากทั้งหมด _PAGES_ หน้า",
				infoEmpty: "ไม่มีข้อมูลที่จะแสดง",
				infoFiltered: "(กรองจากทั้งหมด _MAX_ รายการ)",
				search: "ค้นหา:",
				paginate: {
					first: "หน้าแรก",
					last: "หน้าสุดท้าย",
					next: "ถัดไป",
					previous: "ก่อนหน้า"
				}
			}
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
</script>