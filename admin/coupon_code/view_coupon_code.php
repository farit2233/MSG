<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM coupon_code_list WHERE id = '{$_GET['id']}' and delete_flag = 0 ");
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
        <div class="col-lg-12 col-md-7 col-sm-12 col-xs-12">
            <dl>
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">ชื่อคูปอง</dt>
                            <dd><?= isset($name) ? $name : "" ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">รหัสคูปอง</dt>
                            <dd><?= $coupon_code ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">รายละเอียดคูปอง</dt>
                            <dd><?= $description ?? '' ?></dd>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">จำนวนสิทธิการใช้คูปองต่อ 1 ลูกค้า</dt>
                            <dd><?= $limit_coupon ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">จำนวนคูปองที่มี</dt>
                            <dd>
                                <?php if ($unl_coupon == 1): ?>
                                    จำนวนไม่จำกัด
                                <?php else: ?>
                                    <?= $coupon_amount ?? '' ?>
                                <?php endif; ?>

                            </dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">ใช่ร่วมกับโปรโมชัน</dt>
                            <dd>
                                <?php if ($cpromo == 1): ?>
                                    <span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">ใช่ได้กับทุกสินค้า</dt>
                            <dd>
                                <?php if ($all_products_status == 1): ?>
                                    <span class="badge badge-success px-3 rounded-pill">ใช้ได้กับทุกสินค้า</span>
                                <?php else: ?>
                                    <span class="badge badge-danger px-3 rounded-pill">ใช้ไม่ได้กับทุกสินค้า</span>
                                <?php endif; ?>
                            </dd>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">ประเภทส่วนลด</dt>
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
                        <div class="col-md-3">
                            <dt class="text-muted">สั่งซื้อขั้นต่ำ</dt>
                            <dd>
                                <?php if ($minimum_order == 0): ?>
                                    ไม่มีขั้นต่ำ
                                <?php else: ?>
                                    <?= $minimum_order ?? '' ?>
                                <?php endif; ?>
                            </dd>
                        </div>
                        <div class="col-md-3">
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
                    <button class="btn btn-flat btn-dark" type="button" id="coupon_code_products">
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
                                            ccp.id as ccd_id,
                                            p.id as product_id, 
                                            p.name as product_name,
                                            p.brand,
                                            p.price,
                                            p.vat_price,
                                            p.discounted_price,
                                            p.image_path
                                        FROM coupon_code_products ccp
                                        INNER JOIN product_list p ON ccp.product_id = p.id
                                        WHERE ccp.coupon_code_id = '{$id}'
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
                                                <a class="dropdown-item" href="./?page=products/view_product&id=<?= $row['product_id'] ?>">
                                                    <span class="fa fa-eye text-dark"></span> ดู
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['ccd_id'] ?>">
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
        <a class="btn btn-light btn-sm border btn-flat" href="./?page=coupon_code"><i class="fa fa-angle-left"></i> กลับ</a>
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
            _conf("คุณแน่ใจหรือไม่ว่าต้องการลบโปรโมชันนี้?", "delete_coupon_code", [id]);
        });
    });
    $(function() {
        $('#delete_data').click(function() {
            _conf("Are you sure to delete this product_type permanently?", "delete_product_type", ["<?= isset($id) ? $id : '' ?>"])
        })
    })

    $(function() {
        // เมื่อคลิกปุ่ม "เพิ่มสินค้า"
        $('#coupon_code_products').click(function() {
            var all_products_status = <?= $all_products_status ?? 0 ?>; // ค่าของ all_products_status จาก PHP

            // เช็คว่า all_products_status == 1 หรือไม่
            if (all_products_status == 1) {
                // ถ้า all_products_status == 1 แสดง alert แจ้งว่าไม่จำเป็นต้องเพิ่มสินค้า
                Swal.fire({
                    title: 'คูปองนี้สามารถใช้ได้กับทุกสินค้าแล้ว',
                    text: 'คุณไม่จำเป็นต้องเพิ่มสินค้าอีก',
                    icon: 'success',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#3085d6',
                    onClose: () => {
                        // เมื่อคลิก "ตกลง" ปิด alert
                    }
                });
            } else {
                // ถ้า all_products_status == 0 ให้เปิด modal เพิ่มสินค้า
                uni_modal_promotion("เพิ่มสินค้า", "coupon_code/coupon_code_products.php?id=<?= isset($id) ? $id : '' ?>");
            }
        });

        // ฟังก์ชันลบสินค้าออกจากโปรโมชัน
        $('.delete_data').click(function() {
            _conf("คุณแน่ใจหรือไม่ที่จะลบสินค้านี้ออกจากโปรโมชัน?", "delete_coupon_code_products", [$(this).attr('data-id')])
        });
    });

    // ฟังก์ชันใหม่สำหรับลบสินค้าออกจากโปรโมชัน
    function delete_coupon_code_products(id) {
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_coupon_code_products",
            method: "POST",
            data: {
                id: id
            },
            dataType: "json",
            error: function(err) {
                console.log(err);
                alert_toast("เกิดข้อผิดพลาด", 'error');
                end_loader();
            },
            success: function(resp) {
                if (resp.status == 'success') {
                    alert_toast(resp.message, 'success');
                    location.reload();
                } else {
                    alert_toast(resp.error, 'error');
                    end_loader();
                }
            }
        });
    }
</script>