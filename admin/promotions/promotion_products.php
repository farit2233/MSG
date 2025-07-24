<?php if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?php echo $_settings->flashdata('success') ?>", 'success')
    </script>
<?php endif; ?>
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    section {
        font-size: 16px;
    }
</style>

<section class="card card-outline card-dark rounded-0">
    <div class="card-header">
        <div class="card-title">โปรโมชั่นทั้งหมด</div>
        <div class="card-tools">
            <a href="./?page=promotions/manage_promotion" class="btn btn-flat btn-dark">
                <i class="fas fa-plus"></i> สร้างโปรโมชั่นใหม่
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div class="card-title" style="font-size: 18px !important;">รายการโปรโมชั่น</div>
                <div class="card-tools">
                    <a href="./?page=promotions/manage_promotion" class="btn btn-flat btn-dark">
                        <i class="fas fa-plus"></i> เพิ่มสินค้าหรือหมวดหมู่
                    </a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="25%">
                        <col width="10%">
                        <col width="10%">
                        <col width="20%">
                        <col width="10%">
                    </colgroup>
                    <thead class="text-center">
                        <tr>
                            <th>ที่</th>
                            <th>ชื่อโปรโมชั่น</th>
                            <th>รายละเอียด</th>
                            <th>ประเภท</th>
                            <th>มูลค่าส่วนลด</th>
                            <th>ช่วงเวลา</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        $qry = $conn->query("SELECT * FROM `promotions` ORDER BY `date_created`  ASC, `name` ASC");
                        while ($row = $qry->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center"><?= $i++ ?></td>
                                <td><?= $row['name'] ?></td>
                                <td><?= $row['description'] ?></td>
                                <td class="text-center">
                                    <?php
                                    switch ($row['type']) {
                                        case 'fixed':
                                            echo 'ลดราคา (บาท)';
                                            break;
                                        case 'percent':
                                            echo 'ลดเปอร์เซ็นต์';
                                            break;
                                        case 'free_shipping':
                                            echo 'ส่งฟรี';
                                            break;
                                        case 'code':
                                            echo 'โค้ดส่วนลด';
                                            break;
                                        default:
                                            echo '-';
                                    }
                                    ?>
                                </td>
                                <td class="text-right"><?= number_format($row['discount_value'], 2) ?></td>
                                <td class="text-center">
                                    <?= date("Y-m-d", strtotime($row['start_date'])) ?> ถึง<br>
                                    <?= date("Y-m-d", strtotime($row['end_date'])) ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($_settings->userdata('type') == 1): ?>
                                        <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            จัดการ
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item" href="./?page=promotions/manage_promotion&id=<?php echo $row['id'] ?>">
                                                <span class="fa fa-edit text-dark"></span> แก้โปรโมชั่น
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>">
                                                <span class="fa fa-trash text-danger"></span> ลบโปรโมชั่น
                                            </a>
                                        </div>
                                    <?php else : ?>
                                        <span class="text-center"> - </span>
                                    <?php endif; ?>
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
        $('#list').dataTable({
            columnDefs: [{
                orderable: false,
                targets: [2, 6]
            }],
            order: [
                [0, 'asc']
            ],
            language: {
                lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                zeroRecords: "ไม่พบข้อมูล",
                info: "หน้า _PAGE_ จาก _PAGES_ หน้า",
                infoEmpty: "ไม่มีข้อมูลที่จะแสดง",
                infoFiltered: "(กรองจาก _MAX_ รายการทั้งหมด)",
                search: "ค้นหา:",
                paginate: {
                    first: "หน้าแรก",
                    last: "หน้าสุดท้าย",
                    next: "ถัดไป",
                    previous: "ก่อนหน้า"
                }
            }
        });

        $('.delete_data').click(function() {
            const id = $(this).data('id');
            _conf("คุณแน่ใจหรือไม่ว่าต้องการลบโปรโมชั่นนี้?", "delete_promotion", [id]);
        });
    });

    function delete_promotion(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_promotion",
            method: "POST",
            data: {
                id: id
            },
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("เกิดข้อผิดพลาด", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("ไม่สามารถลบได้", 'error');
                    end_loader();
                }
            }
        });
    }
</script>