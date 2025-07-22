<?php if ($_settings->chk_flashdata('success')): ?>
	<script>
		alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
	</script>
<?php endif; ?>
<style>
	.customer-avatar {
		width: 3rem;
		height: 3rem;
		object-fit: scale-down;
		object-position: center center;
	}
</style>
<div class="card card-outline rounded-0 card-dark">
	<div class="card-header">
		<h3 class="card-title">รายชื่อบัญชีลูกค้า</h3>
		<div class="card-tools">
			<a href="./?page=customers/manage_customer" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างบัญชีใหม่</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid">
			<div class="table-responsive">
				<table class="table table-hover table-striped table-bordered" id="list">
					<colgroup>
						<col width="5%">
						<col width="15%">
						<col width="25%">
						<col width="20%">
						<col width="20%">
						<col width="15%">
					</colgroup>
					<thead>
						<tr>
							<th class="text-center">ที่</th>
							<th class="text-center">รูปภาพ</th>
							<th class="text-center">ชื่อ</th>
							<th class="text-center">Email</th>
							<th class="text-center">วันที่สร้าง</th>
							<th class="text-center">จัดการ</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$i = 1;
						$qry = $conn->query("SELECT *, concat(firstname,' ', coalesce(concat(middlename,' '), '') , lastname) as `name` from `customer_list` order by `name` asc ");
						while ($row = $qry->fetch_assoc()):
						?>
							<tr>
								<td class="text-center"><?php echo $i++; ?></td>

								<td class="text-center">
									<img src="<?= validate_image($row['avatar']) ?>" alt="" class="img-thumbnail rounded-circle customer-avatar">
								</td>
								<td><?php echo $row['name'] ?></td>
								<td><?php echo $row['email'] ?></td>
								<td class="text-center"><?php echo date("Y-m-d H:i", strtotime($row['date_updated'])) ?></td>
								<td class="text-center">
									<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
										จัดการ
										<span class="sr-only">Toggle Dropdown</span>
									</button>
									<div class="dropdown-menu" role="menu">
										<a class="dropdown-item" href="./?page=customers/manage_customer&id=<?= $row['id'] ?>"><span class="fa fa-edit text-dark"></span> แก้ไข</a>
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
</div>
<script>
	$(document).ready(function() {
		$('.delete_data').click(function() {
			_conf("คุณแน่ใจหรือไม่ที่จะลบบัญชีลูกค้า", "delete_customer", [$(this).attr('data-id')])
		})
		$('.table').dataTable({
			columnDefs: [{
				orderable: false,
				targets: [2, 5]
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

	function delete_customer($id) {
		start_loader();
		$.ajax({
			url: _base_url_ + "classes/Users.php?f=delete_customer",
			method: "POST",
			data: {
				id: $id
			},
			dataType: 'json',
			error: err => {
				console.log(err)
				alert_toast("An error occured.", 'error');
				end_loader();
			},
			success: function(resp) {
				if (resp.status == 'success') {
					location.reload();
				} else {
					alert_toast("An error occured.", 'error');
					end_loader();
				}
			}
		})
	}
</script>