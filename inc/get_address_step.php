<?php
// ไฟล์: get_address_step.php
require_once('../config.php');

if (isset($_POST['function']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $func = $_POST['function'];

    // --- กรณีที่ 1: ส่ง ID จังหวัดมา -> อยากได้ "อำเภอ" ---
    if ($func == 'provinces') {
        // สังเกต: ใช้ตาราง districts (ซึ่งคืออำเภอในฐานข้อมูลชุดนี้)
        $sql = "SELECT * FROM districts WHERE province_id = '$id' ORDER BY name_th ASC";
        $qry = $conn->query($sql);

        echo '<option value="" selected disabled>กรุณาเลือกอำเภอ / เขต</option>';
        while ($row = $qry->fetch_assoc()) {
            echo '<option value="' . $row['id'] . '">' . $row['name_th'] . '</option>';
        }
        exit();
    }

    // --- กรณีที่ 2: ส่ง ID อำเภอมา -> อยากได้ "ตำบล" ---
    if ($func == 'amphures') {
        // สังเกต: ใช้ตาราง sub_districts (ซึ่งคือตำบล)
        // และเชื่อมด้วย district_id (ซึ่งคือ ID อำเภอ)
        $sql = "SELECT * FROM sub_districts WHERE district_id = '$id' ORDER BY name_th ASC";
        $qry = $conn->query($sql);

        echo '<option value="" selected disabled>กรุณาเลือกตำบล / แขวง</option>';
        while ($row = $qry->fetch_assoc()) {
            // ส่ง zip_code ไปด้วยในตัว
            echo '<option value="' . $row['id'] . '" data-zip="' . $row['zip_code'] . '">' . $row['name_th'] . '</option>';
        }
        exit();
    }
}
