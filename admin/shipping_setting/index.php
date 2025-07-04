<?php

if ($_settings->chk_flashdata('success')): ?>
    <script>
        alert_toast("<?= $_settings->flashdata('success') ?>", 'success');
    </script>
<?php endif; ?>

<style>
    .action-buttons {
        min-width: 100px;
    }
</style>

<div class="card card-outline card-dark rounded-0">
    <div class="card-header">
        <h3 class="card-title">ตั้งค่าขนส่ง</h3>
        <div class="card-tools">
            <?php if ($_settings->userdata('type') == 1): ?>
                <a href="./?page=shipping_setting/manage_shipping" class="btn btn-flat btn-dark"><i class="fas fa-plus"></i> เพิ่มขนส่ง</a>
            <?php else: ?>
                <span class="text-muted"></span>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <table class="table table-hover table-striped table-bordered" id="shipping-list">
                <colgroup>
                    <col width="5%">
                    <col width="15%">
                    <col width="5%">
                    <col width="5%">
                    <col width="5%">
                    <col width="15%">
                    <col width="10%">
                    <col width="10%">
                    <col width="10%">
                </colgroup>
                <thead>
                    <tr class="text-center">
                        <th>ที่</th>
                        <th colspan="4">ชื่อขนส่ง</th>
                        <th>ค่าจัดส่ง</th>
                        <th>เก็บเงินปลายทาง</th>
                        <th>สถานะ</th>
                        <th class="text-center action-buttons">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    $qry = $conn->query("SELECT sm.*, MAX(sp.price) AS max_price 
                         FROM shipping_methods sm
                         LEFT JOIN shipping_prices sp ON sm.id = sp.shipping_method_id
                         GROUP BY sm.id 
                         ORDER BY sm.id ASC");

                    while ($row = $qry->fetch_assoc()):
                    ?>
                        <tr>
                            <td class="text-center"><?= $i++; ?></td>
                            <td colspan="4"><?= $row['name'] ?></td>
                            <td class="text-center">฿ <?= number_format($row['cost'], 2) ?> - ฿ <?= number_format($row['max_price'], 2) ?></td>
                            <td class="text-center"><?= $row['cod_enabled'] ? '<span class="badge badge-success">เปิดใช้งาน</span>' : '<span class="badge badge-secondary">ไม่ได้ใช้งาน</span>' ?></td>
                            <td class="text-center">
                                <?= $row['is_active'] ? '<span class="badge badge-success">เปิดใช้งาน</span>' : '<span class="badge badge-secondary">ไม่ได้ใช้งาน</span>' ?>
                            </td>
                            <td align="center">
                                <?php if ($_settings->userdata('type') == 1): ?>
                                    <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        Action
                                        <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item" href="./?page=shipping_setting/manage_shipping&id=<?= $row['id'] ?>">
                                            <span class="fa fa-edit text-dark"></span> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>">
                                            <span class="fa fa-trash text-danger"></span> Delete
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>

<script>
    $(function() {
        $('#shipping-list').dataTable({
            columnDefs: [{
                orderable: false,
                targets: [5]
            }],
            order: [
                [0, 'asc']
            ]
        });

        $('.delete_data').click(function() {
            const id = $(this).attr('data-id');
            _conf("ยืนยันการลบข้อมูลขนส่งนี้หรือไม่?", "delete_shipping", [id]);
        });
    });

    function delete_shipping(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_shipping",
            method: "POST",
            data: {
                id
            },
            success: function(resp) {
                if (resp == 1) {
                    location.reload();
                } else {
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                }
            },
            error: function(err) {
                console.log(err);
                alert_toast("เกิดข้อผิดพลาด", 'error');
                end_loader();
            }
        });
    }
</script>