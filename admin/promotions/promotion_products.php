<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * FROM promotions WHERE id = '{$_GET['id']}' and delete_flag = 0 ");
    if ($qry->num_rows > 0) {
        foreach ($qry->fetch_assoc() as $k => $v) {
            $$k = $v;
        }
    }
}
?>
<style>
    .product-img {
        width: 4em;
        height: 4em;
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
<section class="card card-outline card-orange rounded-0">
    <div class="card-header">
        <div class="card-title">เพิ่มสินค้าในโปรโมชั่นของ <?= isset($name) ? $name : "" ?></div>
    </div>
    <form action="" id="promotion_product" method="POST">
        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
        <div class="card-body">
            <div class="card card-outline card-dark rounded-0 mb-3">
                <div class="card-header">
                    <div class="card-title" style="font-size: 18px !important;">เลือกรายการสินค้า</div>
                </div>
                <div class="container-fluid py-3">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped table-bordered" id="list">
                            <colgroup>
                                <col width="5%">
                                <col width="5%">
                                <col width="10%">
                                <col width="20%">
                                <col width="25%">
                                <col width="15  %">
                                <col width="10%">
                                <col width="10%">
                            </colgroup>
                            <thead class="text-center">
                                <tr>
                                    <th>เลือก</th>
                                    <th>ที่</th>
                                    <th>รูปภาพสินค้า</th>
                                    <th>แบรนด์</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>ราคา</th>
                                    <th>โปรโมชั่น</th>
                                    <th>สถานะ</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $qry = $conn->query("SELECT * FROM `product_list` WHERE delete_flag = 0 ORDER BY `brand` ASC, `name` ASC ");
                                while ($row = $qry->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><input type="checkbox" name="" id=""></td>
                                        <td class="text-center"><?php echo $i++; ?></td>

                                        <td class="text-center">
                                            <img src="<?= validate_image($row['image_path']) ?>" alt="" class="img-thumbnail p-0 border product-img">
                                        </td>
                                        <td class=""><?= $row['brand'] ?></td>
                                        <td class="">
                                            <div style="line-height:1em">
                                                <div><?= $row['name'] ?></div>
                                                <div><small class="text-muted"><?= $row['dose'] ?></small></div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <?php
                                            $price = (float) $row['price'];
                                            $discounted_price = $price;

                                            if (!empty($row['discount_type']) && !empty($row['discount_value'])) {
                                                if ($row['discount_type'] === 'percent') {
                                                    $discounted_price = $price - ($price * ($row['discount_value'] / 100));
                                                } elseif ($row['discount_type'] === 'amount') {
                                                    $discounted_price = $price - $row['discount_value'];
                                                }
                                                if ($discounted_price < 0) $discounted_price = 0;
                                            }
                                            ?>

                                            <?php if ($discounted_price < $price): ?>
                                                <span class="text-muted" style="text-decoration: line-through;"><?= format_num($price, 2) ?> ฿</span><br>
                                                <span class="text-danger font-weight-bold"><?= format_num($discounted_price, 2) ?> ฿</span><br>

                                            <?php else: ?>
                                                <span class="font-weight-bold"><?= format_num($price, 2) ?> ฿</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($_settings->userdata('type') == 1): ?>
                                                <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                                    มีแล้ว
                                                    <span class="sr-only">Toggle Dropdown</span>
                                                </button>

                                                <div class="dropdown-menu" role="menu">
                                                    <a class="dropdown-item" href="./?page=promotions/view_promotion&id=<?php echo $row['id'] ?>">
                                                        <span class="fa fa-eye text-dark"></span> ดูโปรโมชั่น
                                                    </a>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="./?page=promotions/promotion_products&id=<?php echo $row['id'] ?>">
                                                        <span class="fa fa-edit text-dark"></span> แก้โปรโมชั่น
                                                    </a>
                                                </div>
                                            <?php else : ?>
                                                <span class="text-center"> - </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($row['status'] == 1): ?>
                                                <span class="badge badge-success px-3 rounded-pill">กำลังใช้งาน</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger px-3 rounded-pill">ไม่ได้ใช้งาน</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer py-1 text-center">
            <?php if (isset($_GET['id']) && $_GET['id'] > 0): ?>
                <button class="btn btn-success btn-sm btn-flat" form="promotion_product">
                    <i class="fa fa-save"></i> บันทึก
                </button>
                <a class="btn btn-danger btn-sm border btn-flat" href="./?page=promotions/view_promotion&id=<?= $_GET['id'] ?>">
                    <i class="fa fa-times"></i> ยกเลิก
                </a>
                <a class="btn btn-light btn-sm border btn-flat" href="./?page=promotions/view_promotion&id=<?= $_GET['id'] ?>">
                    <i class="fa fa-angle-left"></i> กลับ
                </a>
            <?php endif; ?>
        </div>
    </form>
</section>

<script>
    $(document).ready(function() {
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

        $('#promotion_product').submit(function(e) {
            e.preventDefault();
            var _this = $(this)
            start_loader();
            $.ajax({
                url: _base_url_ + "classes/Master.php?f=save_promotion_product",
                data: new FormData(this),
                method: 'POST',
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                error: err => {
                    console.log(err);
                    alert_toast("เกิดข้อผิดพลาด", 'error');
                    end_loader();
                },
                success: function(resp) {
                    if (resp.status == 'success') {
                        location.replace('./?page=promotions/view_promotion&id=' + resp.id);
                    } else {
                        alert_toast(resp.msg || "ไม่สามารถบันทึกได้", 'error');
                        end_loader();
                    }
                }
            });
        });
    });
</script>