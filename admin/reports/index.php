<?php
$date = isset($_GET['date']) ? $_GET['date'] : date("Y-m-d");

function formatThaiDate($dateStr)
{
    $thaiMonths = [
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    ];

    // แปลง YYYY-MM-DD เป็น timestamp
    $timestamp = strtotime($dateStr);

    // ดึงค่า วัน, เดือน (index), ปี (ค.ศ.)
    $day = date('j', $timestamp);
    $month = $thaiMonths[date('n', $timestamp) - 1]; // -1 เพื่อใช้เป็น index array
    $year = date('Y', $timestamp) + 543; // +543 เพื่อแปลงเป็น พ.ศ.

    // คืนค่าเป็น "4 พฤศจิกายน 2568"
    return "$day $month $year";
}
?>
<style>
    .card-title {
        font-size: 20px !important;
        font-weight: bold;
    }

    .contact-label {
        font-size: 17px !important;
    }

    .contact-input {
        font-size: 16px !important;
    }

    section {
        font-size: 16px;
    }
</style>

<section class="flex-column justify-content-center align-items-center">
    <div class="card card-outline card-dark rounded-0 mb-3">
        <div class="card-header">
            <div class="card-title">วันที่แสดง</div>
        </div>
        <div class="card-body">
            <form action="" id="filter-form">
                <div class="row align-items-end">
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label for="date" class="control-label contact-label">เลือกวันที่</label>

                            <div class="input-group flatpickr-thai-date">

                                <input type="text" class="form-control form-control-sm rounded-0 contact-input"
                                    name="date" id="date" value="<?= $date ?>" required="required"
                                    placeholder="เลือกวันที่..." data-input>

                                <div class="input-group-append">
                                    <a class="input-group-text" title="เปิด/ปิดปฏิทิน" data-toggle>
                                        <i class="fa fa-calendar"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-12 col-xs-12">
                        <div class="form-group">
                            <button class="btn btn-sm btn-flat btn-dark bg-gradient-dark"><i class="fa fa-filter"></i> ค้นหา</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-outline card-dark rounded-0 mb-3">
        <div class="card-header py-1">
            <div class="card-title">รายการที่ขายแล้ว</div>
            <div class="card-tools">
                <button class="btn btn-flat btn-sm btn-light bg-gradient-light border" type="button" id="print"><i class="fa fa-print"></i> Print</button>
            </div>
        </div>
        <div class="card-body">
            <div class="container-fluid" id="printout">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="5%">
                        <col width="20%">
                        <col width="25%">
                        <col width="20%">
                        <col width="10%">
                        <col width="20%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="px-1 py-1 text-center">ที่</th>
                            <th class="px-1 py-1 text-center">รหัสคำสั่งซื้อ</th>
                            <th class="px-1 py-1 text-center">ชื่อสินค้า</th>
                            <th class="px-1 py-1 text-center">ราคาปัจจุบัน</th>
                            <th class="px-1 py-1 text-center">จำนวน</th>
                            <th class="px-1 py-1 text-center">รวม</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $g_total = 0;
                        $i = 1;
                        $requests = $conn->query("SELECT oi.*,o.code, concat(p.brand, ' ', p.name) as product FROM `order_items` oi inner join order_list o on oi.order_id = o.id inner join product_list p on oi.product_id = p.id where date(o.date_created) = '{$date}' order by abs(unix_timestamp(o.date_created)) asc ");
                        while ($row = $requests->fetch_assoc()):
                            $g_total += $row['price'] * $row['quantity'];
                        ?>
                            <tr>
                                <td class="px-1 py-1 align-middle text-center"><?= $i++ ?></td>
                                <td class="px-1 py-1 align-middle"><?= $row['code'] ?></td>
                                <td class="px-1 py-1 align-middle"><?= $row['product'] ?></td>
                                <td class="px-1 py-1 align-middle text-right"><?= format_num($row['price'], 2) ?></td>
                                <td class="px-1 py-1 align-middle text-right"><?= format_num($row['quantity'], 0) ?></td>
                                <td class="px-1 py-1 align-middle text-right"><?= format_num($row['price'] * $row['quantity'], 2) ?></td>
                            </tr>
                        <?php endwhile; ?>
                        <?php if ($requests->num_rows <= 0): ?>
                            <tr>
                                <td class="py-1 text-center" colspan="6">No records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-center px-1 py-1 align-middle">รวมทั้งสิน</th>
                            <th class="text-right px-1 py-1 align-middle"><?= format_num($g_total, 2) ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</section>

<noscript id="print-header">
    <div>
        <style>
            html {
                min-height: unset !important;
            }
        </style>
        <div class="d-flex w-100 align-items-center">
            <div class="col-2 text-center">
                <img src="<?= validate_image($_settings->info('logo')) ?>" alt="" class="rounded-circle border" style="width: 5em;height: 5em;object-fit:cover;object-position:center center">
            </div>
            <div class="col-8">
                <div style="line-height:1em">
                    <div class="text-center font-weight-bold h5 mb-0">
                        <large><?= $_settings->info('name') ?></large>
                    </div>
                    <div class="text-center font-weight-bold h5 mb-0">
                        <large>รายงานการขายประจำวัน</large>
                    </div>
                    <div class="text-center font-weight-bold h5 mb-0">วันที่ <?= formatThaiDate($date) ?></div>
                </div>
            </div>
        </div>
        <hr>
    </div>
</noscript>
<script>
    function print_r() {
        var h = $('head').clone()
        var el = $('#printout').clone()
        var ph = $($('noscript#print-header').html()).clone()
        h.find('title').text("Daily Report - Print View")
        var nw = window.open("", "_blank", "width=" + ($(window).width() * .8) + ",left=" + ($(window).width() * .1) + ",height=" + ($(window).height() * .8) + ",top=" + ($(window).height() * .1))
        nw.document.querySelector('head').innerHTML = h.html()
        nw.document.querySelector('body').innerHTML = ph[0].outerHTML
        nw.document.querySelector('body').innerHTML += el[0].outerHTML
        nw.document.close()
        start_loader()
        setTimeout(() => {
            nw.print()
            setTimeout(() => {
                nw.close()
                end_loader()
            }, 200);
        }, 300);
    }
    $(function() {

        flatpickr(".flatpickr-thai-date", {
            wrap: true, // สำหรับการใช้งานไอคอน (input-group)
            locale: "th", // ใช้งานภาษาไทย

            // --- ส่วนสำคัญ (หน้านี้ไม่ต้องเลือกเวลา) ---
            altInput: true, // สร้าง input อีกอันไว้แสดงผล
            altFormat: "j F พ.ศ. Y", // รูปแบบที่แสดงให้ผู้ใช้เห็น (เช่น 4 พฤศจิกายน พ.ศ. 2568)
            dateFormat: "Y-m-d", // รูปแบบข้อมูลที่จะส่งไปให้ Server (ตรงกับ $date เดิม)
        });

        $('#filter-form').submit(function(e) {
            e.preventDefault()
            location.href = './?page=reports&' + $(this).serialize()
        })
        $('#print').click(function() {
            print_r()
        })

    })
</script>