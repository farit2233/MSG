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
        <div class="card-title">ขนส่งทั้งหมด</div>
        <?php if ($_settings->userdata('type') == 1): ?>
            <div class="card-tools">
                <a href="./?page=shipping_setting/manage_shipping" id="create_new" class="btn btn-flat btn-dark"><span class="fas fa-plus"></span> สร้างขนส่งใหม่</a>
            </div>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered" id="list">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="30%">
                        <col width="15%">
                        <col width="10%">
                        <col width="10%">
                        <col width="10%">
                    </colgroup>
                    <thead class="text-center">
                        <tr>
                            <th>ที่</th>
                            <th>ชื่อขนส่ง</th>
                            <th>รายละเอียด</th>
                            <th>ราคาขนส่ง</th>
                            <th>เก็บเงินปลายทาง</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $i = 1;
                        // *** [START] แก้ไข SQL Query ***
                        // Query to fetch shipping methods and price range (min_price, max_price)
                        $qry = $conn->query("
                        SELECT 
                            sm.id AS shipping_methods_id,
                            sm.name AS shipping_method_name,
                            sm.description AS shipping_method_description,
                            sm.cod_enabled AS cod_enabled,
                            sm.status AS status,
                            sm.delete_flag AS delete_flag,
                            MIN(sp.price) AS min_price,
                            MAX(sp.price) AS max_price
                        FROM 
                            shipping_methods sm
                        LEFT JOIN 
                            shipping_prices sp ON sm.id = sp.shipping_methods_id AND sp.status = 1 -- *** แก้ไข: เพิ่ม AND sp.status = 1 ตรงนี้ ***
                        WHERE 
                            sm.delete_flag = 0
                        GROUP BY 
                            sm.id
                        ORDER BY 
                            sm.id
                        ");
                        // *** [END] แก้ไข SQL Query ***

                        while ($row = $qry->fetch_assoc()):
                        ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class=""><?= $row['shipping_method_name'] ?></td>
                                <td class=""><?= $row['shipping_method_description'] ?></td>
                                <td class="text-center">
                                    <?php
                                    // *** [START] แก้ไขการแสดงผล (แนะนำ) ***
                                    // ตรวจสอบว่า min_price ไม่ใช่ NULL (หมายถึงมีราคาที่ status=1)
                                    if ($row['min_price'] !== null):
                                        echo format_num($row['min_price'], 2) . " ฿ - " . format_num($row['max_price'], 2) . " ฿";
                                    else:
                                        // ถ้าเป็น NULL (ไม่มีราคาที่ใช้งาน) ให้แสดง -
                                        echo "-";
                                    endif;
                                    // *** [END] แก้ไขการแสดงผล ***
                                    ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['cod_enabled'] == 1): ?>
                                        <span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>
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
                                    <?php if ($_settings->userdata('type') == 1): ?>
                                        <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                            จัดการ
                                            <span class="sr-only">Toggle Dropdown</span>
                                        </button>

                                        <div class="dropdown-menu" role="menu">
                                            <a class="dropdown-item" href="./?page=shipping_setting/manage_shipping&id=<?php echo $row['shipping_methods_id'] ?>">
                                                <span class="fa fa-edit text-primary"></span> แก้ไขขนส่ง
                                            </a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['shipping_methods_id'] ?>">
                                                <span class="fa fa-trash text-danger"></span> ลบขนส่ง
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
        $('.delete_data').click(function() {
            _conf("คุณแน่ใจหรือไม่ที่จะลบขนส่งนี้?", "delete_shipping", [$(this).attr('data-id')])
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

    function delete_shipping($id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_shipping ",
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