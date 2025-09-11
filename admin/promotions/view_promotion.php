<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions_list WHERE id = '{$_GET['id']}' and delete_flag = 0 ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
function formatDateThai($date)
{
    // แปลงวันที่เป็นตัวแปร timestamp
    $timestamp = strtotime($date);
    $day = date("j", $timestamp); // วัน (1-31)
    $month = date("n", $timestamp); // เดือน (1-12)
    $year = date("Y", $timestamp) + 543; // ปี (พ.ศ.)
    $hour = date("H", $timestamp); // ชั่วโมง (00-23)
    $minute = date("i", $timestamp); // นาที (00-59)

    // สร้างวันที่ในรูปแบบไทย
    return "{$day}/{$month}/{$year} เวลา {$hour}:{$minute}";
}
?>
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

    a:hover {
        color: inherit;
        text-decoration: none;
    }

    .info-box:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.25);
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
</style>
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">ข้อมูลเบื้องต้น</div>
    </div>
    <div class="card-body">
        <div class="col-lg-8 col-md-7 col-sm-12 col-xs-12">
            <dl>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">ชื่อโปรโมชัน</dt>
                            <dd><?= isset($name) ? $name : "" ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">รายละเอียดโปรโมชัน</dt>
                            <dd><?= $description ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">ประเภท</dt>
                            <dd>
                                <?php
                                switch ($type ?? '') {
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
                            </dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">มูลค่าส่วนลด</dt>
                            <dd><?= $discount_value ?? '' ?></dd>
                        </div>
                        <div class="col-md-4">
                            <dt class="text-muted">ช่วงเวลา</dt>
                            <span>เริ่ม: <?= formatDateThai($start_date) ?></span>
                            <span> ถึง </span><br>
                            <span>สิ้นสุด: <?= formatDateThai($end_date) ?></span>
                        </div>
                    </div>
                </div>

            </dl>
        </div>
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <div class="card-title">สินค้าที่มีโปรโมชัน</div>
                <div class="card-tools">
                    <button class="btn btn-flat btn-danger delete_all_data" type="button" data-id="<?= $id ?>">
                        <i class="fas fa-trash"></i> ลบสินค้าทั้งหมด
                    </button>
                    <button class="btn btn-flat btn-dark" type="button" id="promotion_products">
                        <i class="fas fa-plus"></i> เพิ่มสินค้า
                    </button>
                </div>

            </div>
            <div class="card-body">
                <div class="container-fluid">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="list">
                            <colgroup>
                                <col width="5%">
                                <col width="10%">
                                <col width="15%">
                                <col width="25%">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead class="text-center">
                                <tr>
                                    <th>ที่</th>
                                    <th>รูปภาพสินค้า</th>
                                    <th>แบรนด์</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา</th>
                                    <th>จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                // ======================= แก้ไขจุดที่ 1: เพิ่ม WHERE clause เพื่อกรองสินค้า =======================
                                $qry = $conn->query("
                                SELECT 
                                    pp.id as pp_id,
                                    p.id as product_id,
                                    p.name as product_name,
                                    p.brand,
                                    p.price,
                                    p.vat_price,
                                    p.discounted_price,
                                    p.image_path
                                FROM promotion_products pp
                                INNER JOIN product_list p ON pp.product_id = p.id
                                WHERE pp.promotion_id = '{$id}'
                            ");
                                while ($row = $qry->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center">
                                            <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail p-0 border product-img">
                                        </td>
                                        <td><?= htmlspecialchars($row['brand']) ?></td>
                                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td class="text-right">
                                            <?php
                                            $price = (float)$row['price'];
                                            $vat_price = !empty($row['vat_price']) ? (float)$row['vat_price'] : null;
                                            $discounted_price = !empty($row['discounted_price']) ? (float)$row['discounted_price'] : null;

                                            // กำหนดตัวแปรแสดงผลหลัก
                                            $display_price = $discounted_price ?? $vat_price ?? $price;
                                            $original_price = $vat_price ?? $price;

                                            // แสดงผล
                                            if (!is_null($discounted_price) && $discounted_price < $original_price) {
                                                // มีส่วนลด: ขีดฆ่า original และแสดง discounted สีแดง
                                                echo '<span class="text-muted" style="text-decoration: line-through;">' . number_format($original_price, 0, '.', ',') . ' ฿</span><br>';
                                                echo '<span class="text-danger font-weight-bold">' . number_format($discounted_price, 0, '.', ',') . ' ฿</span>';
                                            } else {
                                                // ไม่มีส่วนลด: แสดง display_price ปกติ
                                                echo '<span class="font-weight-bold">' . number_format($display_price, 0, '.', ',') . ' ฿</span>';
                                            }
                                            ?>
                                        </td>

                                        <td class="text-center">
                                            <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                จัดการ
                                                <span class="sr-only">Toggle Dropdown</span>
                                            </button>
                                            <div class="dropdown-menu" role="menu">
                                                <a class="dropdown-item" href="./?page=products/manage_product&id=<?= $row['product_id'] ?>">
                                                    <span class="fa fa-eye text-dark"></span> ดู
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['pp_id'] ?>">
                                                    <span class="fa fa-trash text-danger"></span> ลบรายการ
                                                </a>
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
    </div>
    <div class="card-footer py-1 text-center">
        <a class="btn btn-light btn-sm border btn-flat" href="./?page=promotions"><i class="fa fa-angle-left"></i> กลับ</a>
    </div>
</section>
<script>
    $(document).ready(function() {
        $('#list').dataTable({
            columnDefs: [{
                orderable: false,
                targets: [2, 5]
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
            _conf("คุณแน่ใจหรือไม่ว่าต้องการลบโปรโมชันนี้?", "delete_promotion", [id]);
        });
        $('.delete_all_data').click(function() {
            const promotion_id = $(this).data('id');
            _conf("คุณแน่ใจหรือไม่ที่จะลบสินค้าทั้งหมดออกจากโปรโมชันนี้?", "delete_all_promotion_products", [promotion_id]);
        });
    });
    $(function() {
        $('#delete_data').click(function() {
            _conf("Are you sure to delete this product_type permanently?", "delete_product_type", ["<?= isset($id) ? $id : '' ?>"])
        })
    })

    $(function() {
        $('.delete_data').click(function() {
            _conf("คุณแน่ใจหรือไม่ที่จะลบสินค้านี้ออกจากโปรโมชัน?", "delete_promotion_product", [$(this).attr('data-id')])
        });
        $('#promotion_products').click(function() {
            uni_modal_promotion("เพิ่มสินค้า", "promotions/promotion_products.php?id=<?= isset($id) ? $id : '' ?>")
        })
    })

    // ฟังก์ชันใหม่สำหรับลบสินค้าออกจากโปรโมชัน
    function delete_promotion_product($id) {
        start_loader();
        alert_toast('กำลังลบสินค้าออกจากโปรโมชัน...', 'info');
        $.ajax({
            // ส่งไปที่ฟังก์ชันใหม่ใน Master.php
            url: _base_url_ + "classes/Master.php?f=delete_promotion_product",
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
                    // เมื่อสำเร็จ ให้รีโหลดหน้าปัจจุบัน
                    location.reload();
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        });
    }
    // ฟังก์ชันสำหรับลบสินค้าทั้งหมดออกจากโปรโมชัน
    function delete_all_promotion_products(promotion_id) {
        start_loader();
        alert_toast('กำลังลบสินค้าทั้งหมดออกจากโปรโมชัน...', 'info');
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_all_promotion_products",
            method: "POST",
            data: {
                promotion_id: promotion_id
            },
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("เกิดข้อผิดพลาด", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    alert_toast('เกิดข้อผิดพลาด', 'error');
                    end_loader();
                }
            }
        });
    }
</script>