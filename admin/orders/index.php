<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<?php
$payment_status = isset($_GET['payment_status']) ? $_GET['payment_status'] : '';
$delivery_status = isset($_GET['delivery_status']) ? $_GET['delivery_status'] : '';
$stat_arr = ['ยังไม่ชำระเงิน', 'รอตรวจสอบ', 'ชำระเงินแล้ว', 'ชำระล้มเหลว', 'คืนเงินแล้ว']
?>
<style>
	.card-title {
		font-size: 20px !important;
		font-weight: bold;
	}

	section {
		font-size: 16px;
	}
</style>
<div class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<h3 class="card-title">รายการคำสั่งซื้อ <?= isset($stat_arr[$payment_status]) ? $stat_arr[$payment_status] : 'ทั้งหมด' ?></h3>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered" id="list">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="10%">
						<col width="10%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="p-1 text-center">ที่</th>
							<th class="p-1 text-center">รหัสคำสั่งซื้อ</th>
							<th class="p-1 text-center">ชื่อผู้สั่ง</th>
							<th class="p-1 text-center">ราคารวม</th>
							<th class="p-1 text-center">วันที่</th>
							<th class="p-1 text-center">สถานะชำระเงิน</th>
							<th class="p-1 text-center">การจัดส่ง</th>
							<th class="p-1 text-center">จัดการคำสั่งซื้อ</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$where = "";
						switch ($payment_status) {
							case 0:
								$where = " where o.`payment_status` = 0 ";
								break;
							case 1:
								$where = " where o.`payment_status` = 1 ";
								break;
							case 2:
								$where = " where o.`payment_status` = 2 ";
								break;
							case 3:
								$where = " where o.`payment_status` = 3 ";
								break;
							case 4:
								$where = " where o.`payment_status` = 4 ";
								break;
							case 5:
								$where = " where o.`payment_status` = 5 ";
								break;
							case 6:
								$where = " where o.`payment_status` = 6 ";
								break;
						}
						switch ($delivery_status) {
							case 0:
								$where = " where o.`delivery_status` = 0 ";
								break;
							case 1:
								$where = " where o.`delivery_status` = 1 ";
								break;
							case 2:
								$where = " where o.`delivery_status` = 2 ";
								break;
							case 3:
								$where = " where o.`delivery_status` = 3 ";
								break;
							case 4:
								$where = " where o.`delivery_status` = 4 ";
								break;
							case 5:
								$where = " where o.`delivery_status` = 5 ";
								break;
							case 6:
								$where = " where o.`delivery_status` = 6 ";
								break;
							case 7:
								$where = " where o.`delivery_status` = 7 ";
								break;
							case 8:
								$where = " where o.`delivery_status` = 8 ";
								break;
							case 9:
								$where = " where o.`delivery_status` = 9 ";
								break;
							case 10:
								$where = " where o.`delivery_status` = 10 ";
								break;
						}
						$qry = $conn->query("SELECT o.*, 
						CONCAT(c.firstname, ' ', COALESCE(CONCAT(c.middlename, ' '), ''), c.lastname) as customer 
					FROM `order_list` o 
					INNER JOIN customer_list c ON o.customer_id = c.id 
					{$where} 
					ORDER BY abs(unix_timestamp(o.date_created)) DESC");
						while ($row = $qry->fetch_assoc()):
						?>
							<tr>
								<td class="p-1 align-middle text-center"><?= $i++ ?></td>

								<td class="p-1 align-middle text-center"><?= $row['code'] ?></td>
								<td class="p-1 align-middle"><?= $row['customer'] ?></td>
								<td class="p-1 align-middle text-center"><?= format_num($row['total_amount'], 2) ?></td>
								<td class="p-1 align-middle text-center"><?= date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
								<td class="p-1 align-middle text-center">
									<?php
									switch ((int)$row['payment_status']) {
										case 0:
											echo '<span class="badge bg-secondary">ยังไม่ชำระเงิน</span>';
											break;
										case 1:
											echo '<span class="badge bg-warning text-dark">รอตรวจสอบ</span>';
											break;
										case 2:
											echo '<span class="badge bg-success">ชำระเงินแล้ว</span>';
											break;
										case 3:
											echo '<span class="badge bg-danger">ชำระเงินล้มเหลว</span>';
											break;
										case 4:
											echo '<span class="badge bg-secondary">รอการยกเลิกคำสั่งซื้อ</span>';
											break;
										case 5:
											echo '<span class="badge bg-secondary">ขอคืนเงิน</span>';
											break;
										case 6:
											echo '<span class="badge bg-dark">คืนเงินแล้ว</span>';
											break;
										default:
											echo '<span class="badge bg-light">N/A</span>';
											break;
									}
									?>
								</td>
								<td class="p-1 align-middle text-center">
									<?php
									switch ((int)$row['delivery_status']) {
										case 0:
											echo '<span class="badge bg-secondary">ตรวจสอบคำสั่งซื้อ</span>';
											break;
										case 1:
											echo '<span class="badge bg-info">กำลังเตรียมของ</span>';
											break;
										case 2:
											echo '<span class="badge bg-primary">แพ๊กของแล้ว</span>';
											break;
										case 3:
											echo '<span class="badge bg-warning text-dark">กำลังจัดส่ง</span>';
											break;
										case 4:
											echo '<span class="badge bg-success">จัดส่งสำเร็จ</span>';
											break;
										case 5:
											echo '<span class="badge bg-danger">จัดส่งไม่สำเร็จ</span>';
											break;
										case 6:
											echo '<span class="badge bg-secondary">รอการยกเลิกคำสั่งซื้อ</span>';
											break;
										case 7:
											echo '<span class="badge bg-dark">คืนระหว่างทาง</span>';
											break;
										case 8:
											echo '<span class="badge bg-secondary">ขอคืนสินค้า</span>';
											break;
										case 9:
											echo '<span class="badge bg-dark">คืนของสำเร็จ</span>';
											break;
										case 10:
											echo '<span class="badge bg-danger">ยกเลิกแล้ว</span>';
											break;
										default:
											echo '<span class="badge bg-light">N/A</span>';
											break;
									}
									?>
								</td>
								<td class="p-1 align-middle text-center">
									<a class="btn btn-flat btn-sm btn-light border-gradient-light border view-order" href="./?page=orders/view_order&id=<?= $row['id'] ?>"><i class="fa fa-edit text-dark"></i> จัดการ</a>
								</td>
							</tr>
						<?php endwhile; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$('.delete_data').click(function() {
			_conf("คุณแน่ใจหรือไม่ที่จะลบคำสั่งซื้อนี้?", "delete_request", [$(this).attr('data-id')])
		})
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