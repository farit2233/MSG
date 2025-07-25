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

    a {
        color: inherit;
        text-decoration: none;
    }


    .info-box {
        transition: transform 0.5s ease, box-shadow 0.5s ease;
        display: inline-flex !important;
        width: 100%;
        max-width: 240px;
        padding: 0.4rem 0.6rem;
        min-height: auto;
        border-radius: 0.35rem;
        margin: 8px;
    }

    .info-box-content {
        padding-left: 0.5rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .info-box-text {
        font-size: 16px;
        color: #444;
        margin-bottom: 0.2rem;
    }

    .info-box-number {
        font-size: 16px;
        font-weight: bold;
        color: #000;
    }

    .no-link {
        cursor: default;
        text-decoration: none;
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
    <div class="d-flex flex-wrap justify-content-center mt-3">
        <?php
        $boxes = [
            ['label' => 'ยอดขาย', 'bg' => 'bg-white', 'icon' => 'fas fa-layer-group', 'query' => "SELECT * FROM product_type where delete_flag = 0"],
            ['label' => 'จำนวนคำสั่งซื้อ', 'bg' => 'bg-white', 'icon' => 'fas fa-th-list', 'query' => "SELECT * FROM category_list where delete_flag = 0"],
            ['label' => 'จำนวนสินค้า', 'bg' => 'bg-white', 'icon' => 'fas fa-boxes', 'query' => "SELECT id FROM product_list where `status` = 1"],
        ];

        foreach ($boxes as $box):
            $bg = $box['bg'] ?? 'bg-light';
        ?>
            <div class="info-box <?= $bg ?> text-white" style="cursor: default;">
                <span class="info-box-icon"><i class="<?= $box['icon'] ?>"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text text-bold"><?= $box['label'] ?></span>
                    <?php if (isset($box['query'])): ?>
                        <?php $result = $conn->query($box['query']); ?>
                        <span class="info-box-number text-right h5"><?= format_num($result->num_rows ?? 0) ?></span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="card-body">
        <div class="container-fluid">
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
                                            <a class="dropdown-item" href="./?page=promotions/view_promotion&id=<?php echo $row['id'] ?>">
                                                <span class="fa fa-eye text-dark"></span> ดู
                                            </a>
                                            <div class="dropdown-divider"></div>
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