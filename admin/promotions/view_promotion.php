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
                            <dt class="text-muted">ชื่อโปรโมชั่น</dt>
                            <dd><?= isset($name) ? $name : "" ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">รายละเอียดโปรโมชั่น</dt>
                            <dd><?= $description ?? '' ?></dd>
                        </div>
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
                        <div class="col-md-3">&nbsp;</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">มูลค่าส่วนลด</dt>
                            <dd><?= $discount_value ?? '' ?></dd>
                        </div>
                        <div class="col-md-4">
                            <dt class="text-muted">ช่วงเวลา</dt>
                            <?= date("Y-m-d", strtotime($start_date ?? '')) ?> ถึง
                            <?= date("Y-m-d", strtotime($end_date ?? '')) ?>
                        </div>
                    </div>
                </div>

            </dl>
        </div>
        <div class="card card-outline card-dark rounded-0 mb-3">
            <div class="card-header">
                <div class="card-title">สินค้าที่มีโปรโมชั่น</div>
                <div class="card-tools">
                    <?php if (isset($_GET['id']) && $_GET['id'] > 0): ?>
                        <div class="card-tools">
                            <a href="./?page=promotions/promotion_products&id=<?= $_GET['id'] ?>" class="btn btn-flat btn-dark">
                                <i class="fas fa-plus"></i> เพิ่มสินค้า
                            </a>
                        </div>
                    <?php endif; ?>
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
                                $qry = $conn->query("
                                    SELECT 
                                        pp.id as pp_id,
                                        p.id as product_id,
                                        p.name as product_name,
                                        p.brand,
                                        p.price,
                                        p.image_path
                                    FROM promotion_products pp
                                    INNER JOIN product_list p ON pp.product_id = p.id
                                ");
                                while ($row = $qry->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $i++ ?></td>
                                        <td class="text-center">
                                            <?php if (!empty($row['image_path']) && file_exists('uploads/products/' . $row['image_path'])): ?>
                                                <img src="uploads/products/<?= $row['image_path'] ?>" alt="" class="img-thumbnail" style="max-height: 75px;">
                                            <?php else: ?>
                                                <span class="text-muted">ไม่มีรูป</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($row['brand']) ?></td>
                                        <td><?= htmlspecialchars($row['product_name']) ?></td>
                                        <td class="text-right"><?= number_format($row['price'], 2) ?> ฿</td>
                                        <td class="text-center">
                                            <a href="./?page=products/view_product&id=<?= $row['product_id'] ?>" class="btn btn-sm btn-primary">ดู</a>
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
    $(function() {
        $('#delete_data').click(function() {
            _conf("Are you sure to delete this product_type permanently?", "delete_product_type", ["<?= isset($id) ? $id : '' ?>"])
        })
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
                    location.replace("./?page=product_type");
                } else {
                    alert_toast("An error occured.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>