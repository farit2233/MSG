<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif;

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
                                WHERE pl.delete_flag = 0
                                ORDER BY 
                                    available ASC,      -- สินค้าใกล้หมดขึ้นก่อน
                                    pl.brand ASC, 
                                    pl.name ASC
                            ");

						while ($row = $qry->fetch_assoc()):
							// ตรวจสอบว่าจำนวนสินค้าต่ำกว่าเกณฑ์หรือไม่
							$stock_class = ($row['available'] <= $low_stock_threshold) ? 'text-danger fw-bold' : '';
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>

								<?php
								// --- [แก้ไข] START: สร้าง Path สำหรับรูป Thumb ---
								// 1. ดึง path รูปหลัก (เช่น .../img.webp?v=123)
								$image_path_with_query = $row['image_path'];

								// 2. แยก path ออกจาก query string (เช่น ?v=123)
								$path_parts = explode('?', $image_path_with_query);
								$clean_path = $path_parts[0]; // (เช่น .../img.webp)
								$query_string = isset($path_parts[1]) ? '?' . $path_parts[1] : '';

								// 3. สร้าง path ของ thumb โดยแทนที่ .webp ด้วย _thumb.webp
								$thumb_path = str_replace('.webp', '_thumb.webp', $clean_path);

								// 4. ประกอบ path กลับพร้อม query string (เช่น .../img_thumb.webp?v=123)
								$final_thumb_path = $thumb_path . $query_string;
								// --- [แก้ไข] END ---
								?>

								<td class="text-center">
									<img src="<?= validate_image($final_thumb_path) ?>" alt="" class="img-thumbnail p-0 border product-img">
								</td>
								<td class=""><?= $row['brand'] ?></td>
								<td class="">
									<div style="line-height:1em">
										<div><?= $row['name'] ?></div>
									</div>
								</td>
								<td class="text-center <?= $stock_class ?>"><?= format_num($row['available'], 0) ?></td>
								<td class="text-center"><?php echo formatDateThai($row['date_created']); ?></td>
								<td class="text-center">
									<a class="btn btn-sm btn-flat btn-light bg-gradient-light border" href="./?page=inventory/view_inventory&id=<?php echo $row['id'] ?>">
										<span class="fa fa-edit text-dark"></span> แก้ไขสต๊อก
									</a>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
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