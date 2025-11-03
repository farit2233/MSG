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
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    $hour = date("H", $timestamp); // ชั่วโมง (00-23)
    $minute = date("i", $timestamp); // นาที (00-59)

    // ส่งคืนวันที่ในรูปแบบไทย
    return "{$day}/{$month}/{$year} เวลา {$hour}:{$minute}";
}

?>
<style>
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
<section class="card card-outline rounded-0 card-dark">
    <div class="card-header">
        <div class="card-title text-bold">ประเภทสินค้าทั้งหมด</div>
        <div class="card-tools">
            <a href="./?page=product_type/manage_product_type" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างประเภทสินค้าใหม่</a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="table-responsive">
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
                        $qry = $conn->query("SELECT * FROM `product_type` WHERE `delete_flag` = 0 ORDER BY `date_created`  ASC, `name` ASC");
                        while ($row = $qry->fetch_assoc()):
                            $id = $row['id'];
                        ?>
                            <tr>

                                <td class="text-center"><?php echo $i++; ?></td>
                                <td><?= $row['name'] ?></td>
                                <td>
                                    <p class="mb-0 truncate-1"><?php echo ($row['description']) ?></p>
                                </td>
                                <td class="text-center"><?php echo formatDateThai($row['date_created']); ?></td>
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
                                        <a class="dropdown-item" href="./?page=product_type/manage_product_type&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-dark"></span> แก้ประเภทสินค้า</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> ลบประเภทสินค้า</a>
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
            _conf("คุณแน่ใจหรือไม่ที่จะลบประเภทสินค้านี้?", "delete_product_type", [$(this).attr('data-id')])
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