<?php
// ต้องเรียก config.php เพื่อเชื่อมต่อฐานข้อมูล
require_once('../config.php');

// รับ ID ของลูกค้าจาก parameter
$customer_id = isset($_GET['pid']) ? $_GET['pid'] : '';

if (!empty($customer_id)) {
    // 1. ดึงข้อมูลที่อยู่หลักจาก customer_list
    $main_address_qry = $conn->query("SELECT * FROM `customer_list` WHERE id = '{$customer_id}'");
    if ($main_address_qry->num_rows > 0) {
        $main_address = $main_address_qry->fetch_assoc();
    }

    // 2. ดึงข้อมูลที่อยู่ทั้งหมดจาก customer_addresses
    $addresses_qry = $conn->query("SELECT * FROM `customer_addresses` WHERE customer_id = '{$customer_id}'");
}
?>

<div class="container-fluid">
    <form id="address_selection_form">
        <?php if (isset($main_address)): ?>
            <div class="address-item p-2 border rounded mb-2">
                <input type="radio" name="selected_address" id="address_main" value="main"
                    data-address="<?= htmlspecialchars($main_address['address']) ?>"
                    data-sub_district="<?= htmlspecialchars($main_address['sub_district']) ?>"
                    data-district="<?= htmlspecialchars($main_address['district']) ?>"
                    data-province="<?= htmlspecialchars($main_address['province']) ?>"
                    data-postal_code="<?= htmlspecialchars($main_address['postal_code']) ?>"
                    checked>
                <label for="address_main" class="mb-0 ml-2">
                    <strong>ที่อยู่หลัก:</strong><br>
                    <?= htmlspecialchars($main_address['address']) ?>, <?= htmlspecialchars($main_address['sub_district']) ?>, <?= htmlspecialchars($main_address['district']) ?>, <?= htmlspecialchars($main_address['province']) ?> <?= htmlspecialchars($main_address['postal_code']) ?>
                </label>
            </div>
        <?php endif; ?>

        <?php if (isset($addresses_qry) && $addresses_qry->num_rows > 0): ?>
            <?php while ($row = $addresses_qry->fetch_assoc()): ?>
                <div class="address-item p-2 border rounded mb-2">
                    <input type="radio" name="selected_address" id="address_<?= $row['id'] ?>" value="<?= $row['id'] ?>"
                        data-address="<?= htmlspecialchars($row['address']) ?>"
                        data-sub_district="<?= htmlspecialchars($row['sub_district']) ?>"
                        data-district="<?= htmlspecialchars($row['district']) ?>"
                        data-province="<?= htmlspecialchars($row['province']) ?>"
                        data-postal_code="<?= htmlspecialchars($row['postal_code']) ?>">
                    <label for="address_<?= $row['id'] ?>" class="mb-0 ml-2">
                        <strong>ที่อยู่เพิ่มเติม:</strong><br>
                        <?= htmlspecialchars($row['address']) ?>, <?= htmlspecialchars($row['sub_district']) ?>, <?= htmlspecialchars($row['district']) ?>, <?= htmlspecialchars($row['province']) ?> <?= htmlspecialchars($row['postal_code']) ?>
                    </label>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-muted">ยังไม่มีที่อยู่เพิ่มเติม</p>
        <?php endif; ?>
    </form>
</div>

<style>
    /* Style สำหรับรายการที่อยู่ใน Modal */
    .address-item {
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .address-item:hover {
        background-color: #f5f5f5;
    }
</style>