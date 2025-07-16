<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<style>
    .product-type-logo {
        width: 3em;
        height: 3em;
        object-fit: cover;
        object-position: center center;
    }
</style>
<div class="card card-outline rounded-0 card-dark">
    <div class="card-header">
        <h3 class="card-title text-bold">ประเภทสินค้าทั้งหมด</h3>
        <div class="card-tools">
            <a href="./?page=product_type/manage_product_type" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างประเภทสินค้าใหม่</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped table-bordered" id="list">
                <colgroup>
                    <col width="5%">
                    <col width="20%">
                    <col width="30%">
                    <col width="20%">
                    <col width="15%">
                    <col width="10%">
                </colgroup>
                <thead class="text-center">
                    <tr>
                        <th>ที่</th>
                        <th>ชื่อหมวดหมู่</th>
                        <th>รายละเอียดหมวดหมู่</th>
                        <th>วันที่สร้าง</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT * FROM `product_type` WHERE `delete_flag` = 0 ORDER BY `id` ASC, `name` ASC");
                    while ($row = $qry->fetch_assoc()):
                        $id = $row['id'];
                    ?>
                        <td class="text-center"><?php echo $i++; ?></td>
                        <td><?= $row['name'] ?></td>
                        <td>
                            <p class="mb-0 truncate-1"><?php echo ($row['description']) ?></p>
                        </td>
                        <td><?php echo date("Y-m-d H:i", strtotime($row['date_created'])) ?></td>
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
                                <!--a class="dropdown-item" href="./?page=product_type/view_product_type&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> ดู</a>
                                <div class="dropdown-divider"></div-->
                                <a class="dropdown-item" href="./?page=product_type/manage_product_type&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> แก้ไขหมวดหมู่</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> ลบหมวดหมู่</a>
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
            _conf("Are you sure to delete this ประเภท permanently?", "delete_product_type", [$(this).attr('data-id')])
        })
        $('.table').dataTable({
            columnDefs: [{
                orderable: false,
                targets: [5]
            }],
            order: [0, 'asc']
        });
        $('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
    })

    function delete_product_type($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_product_type",
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