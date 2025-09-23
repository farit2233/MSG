<?php
// เชื่อมต่อฐานข้อมูล
require_once('../config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับค่าจากฟอร์ม
    $address = $_POST['address'];
    $sub_district = $_POST['sub_district'];
    $district = $_POST['district'];
    $province = $_POST['province'];
    $postal_code = $_POST['postal_code'];
    $customer_id = $_POST['customer_id']; // ID ลูกค้า

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare("INSERT INTO customer_addresses (customer_id, address, sub_district, district, province, postal_code) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $customer_id, $address, $sub_district, $district, $province, $postal_code);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'msg' => 'ที่อยู่ถูกเพิ่มเรียบร้อย']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'เกิดข้อผิดพลาด']);
    }

    $stmt->close();
}
