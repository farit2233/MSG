<?php
require_once('./../config.php');
if (!$conn) die('Database connection error');
$customer_id = $_settings->userdata('id');
$qry = $conn->query("SELECT * FROM order_list WHERE customer_id = '{$customer_id}' ORDER BY date_created DESC");
?>

<h3 class="mt-4">ประวัติการสั่งซื้อของฉัน</h3>
<hr>
<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>รหัสคำสั่งซื้อ</th>
            <th>วันที่</th>
            <th>ยอดรวม</th>
            <th>สถานะ</th>
            <th>ดูรายละเอียด</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $qry->fetch_assoc()): ?>
            <tr>
                <td><?= $row['code'] ?></td>
                <td><?= date("d/m/Y H:i", strtotime($row['date_created'])) ?></td>
                <td><?= format_num($row['total_amount'], 2) ?> บาท</td>
                <td>
                    <?php
                    switch ($row['status']) {
                        case 0:
                            echo '<span class="badge bg-secondary">รอดำเนินการ</span>';
                            break;
                        case 1:
                            echo '<span class="badge bg-primary">แพ๊กของแล้ว</span>';
                            break;
                        case 2:
                            echo '<span class="badge bg-warning">กำลังจัดส่ง</span>';
                            break;
                        case 3:
                            echo '<span class="badge bg-success">ชำระแล้ว</span>';
                            break;
                        default:
                            echo '<span class="badge bg-light text-dark">N/A</span>';
                    }
                    ?>
                </td>
                <td>
                    <a href="?page=view_order&id=<?= $row['id'] ?>" class="btn btn-sm btn-info">ดูรายละเอียด</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>