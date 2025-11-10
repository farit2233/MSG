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
	.user-avatar {
		width: 3rem;
		height: 3rem;
		object-fit: scale-down;
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
		<div class="card-title">รายชื่อบัญชีสมาชิก</div>
		<div class="card-tools">
			<a href="./?page=user/manage_user" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างบัญชีสมาชิกใหม่</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered" id="list">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="20%">
						<col width="20%">
						<col width="15%">
						<col width="15%">
						<col width="10%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">ที่</th>
							<th class="text-center">รูปโปรไฟล์</th>
							<th class="text-center">ชื่อ</th>
							<th class="text-center">ชื่อบัญชี</th>
							<th class="text-center">ประเภท</th>
							<th class="text-center">วันที่สร้าง</th>
							<th class="text-center">จัดการ</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT *, concat(firstname,' ', coalesce(concat(middlename,' '), '') , lastname) as `name` from `users` where id != '{$_settings->userdata('id')}' order by concat(firstname,' ', lastname) asc ");
						while ($row = $qry->fetch_assoc()):
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>
								<td class="text-center">
									<img src="<?= validate_image($row['avatar']) ?>" alt="" class="img-thumbnail rounded-circle user-avatar">
								</td>
								<td><?php echo $row['name'] ?></td>
								<td><?php echo $row['username'] ?></td>
								<td class="text-center">
									<?php if ($row['type'] == 1): ?>
										แอดมิน
									<?php elseif ($row['type'] == 2): ?>
										สต๊าฟ
									<?php else: ?>
										N/A
									<?php endif; ?>
								</td>
								<td class="text-center"><?php echo formatDateThai($row['date_updated']); ?></td>
								<td class="text-center">
									<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										จัดการ
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item" href="./?page=user/manage_user&id=<?= $row['id'] ?>"><span class="fa fa-edit text-dark"></span> แก้ไข</a>
										<div class="dropdown-divider"></div>
										<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> ลบ</a>
									</div>
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
		$('.delete_data').click(function() {
			_conf("คุณแน่ใจหรือไม่ที่จะลบบัญชีนี้?", "delete_user", [$(this).attr('data-id')])
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

	function delete_user($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Users.php?f=delete_users",
			method: "POST",
			data: {
				id: $id
			},
			dataType: 'json', // <-- 1. เพิ่มบรรทัดนี้ เพื่อบอกว่าเราคาดหวัง JSON
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function(resp) {
				// 2. แก้ไขเงื่อนไขให้ตรวจสอบ status จาก JSON
				if (typeof resp == 'object' && resp.status == 'success') {
					location.reload(); // โหลดหน้าใหม่เมื่อสำเร็จ
				} else {
					// แสดงข้อความ error จาก PHP (ถ้ามี)
					var msg = (resp && resp.msg) ? resp.msg : "An error occured.";
					alert_toast(msg, 'error');
					end_loader();
				}
			}
		})
	}
</script>