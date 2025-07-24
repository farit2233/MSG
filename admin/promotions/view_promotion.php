<?php
if (isset($_GET['id']) && $_GET['id'] > 0) {
    $qry = $conn->query("SELECT * from `promotions` where id = '{$_GET['id']}' and delete_flag = 0 ");
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
                            <dd class="pl-4"><?= isset($name) ? $name : "" ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">รายละเอียดโปรโมชั่น</dt>
                            <dd><?= $description ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">ประเภท</dt>
                            <dd>
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
                                ?></dd>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">หมวดหมู่</dt>
                            <dd><?= $category ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">ประเภทสินค้า</dt>
                            <dd><?= $product_type ?? '' ?></dd>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-3">&nbsp;</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted">ราคา</dt>
                            <dd><?= isset($price) ? format_num($price, 2) . ' ฿' : '' ?></dd>
                        </div>
                        <div class="col-md-3">
                            <dt class="text-muted">ราคาปัจจุบัน</dt>
                            <dd>
                                <?php
                                $final_price = $price ?? 0;
                                if (!empty($discount_type) && !empty($discount_value)) {
                                    if ($discount_type === 'percent') {
                                        $final_price = $price - ($price * ($discount_value / 100));
                                    } elseif ($discount_type === 'amount') {
                                        $final_price = $price - $discount_value;
                                    }
                                    if ($final_price < 0) $final_price = 0;
                                }

                                if (isset($price) && $final_price < $price) {
                                    echo '<span class="text-danger font-weight-bold">' . format_num($final_price, 2) . ' ฿</span>';
                                } else {
                                    echo format_num($price, 2) . ' ฿';
                                }
                                ?>
                            </dd>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-3">&nbsp;</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <dt class="text-muted h6 mb-0">จำนวนสินค้าที่มี</dt>
                            <dd class="h5"><?= isset($available) ? format_num($available, 0) : "0" ?></dd>
                        </div>
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-3">&nbsp;</div>
                        <div class="col-md-3">&nbsp;</div>
                    </div>
                </div>

            </dl>
        </div>
        <div class="card card-outline card-dark rounded-0 mb-3">
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
        </div>
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