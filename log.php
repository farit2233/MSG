<?php extract($_POST);  // รับค่าจากฟอร์ม

// เช็กค่าที่จำเป็น
if (!isset($selected_items) || empty($selected_items)) {
    return json_encode(['status' => 'failed', 'msg' => 'ไม่มีสินค้าในคำสั่งซื้อ']);
}

if (!isset($shipping_cost) || empty($shipping_cost)) {
    return json_encode(['status' => 'failed', 'msg' => 'ค่าขนส่งไม่ถูกต้อง']);
}

if (!isset($shipping_methods_id) || empty($shipping_methods_id)) {
    return json_encode(['status' => 'failed', 'msg' => 'วิธีการขนส่งไม่ถูกต้อง']);
}

if (!isset($delivery_address) || empty($delivery_address)) {
    return json_encode(['status' => 'failed', 'msg' => 'กรุณากรอกที่อยู่จัดส่ง']);
}

// ข้อมูลทั้งหมดที่ได้รับถูกต้อง
// ต่อไปนี้ค่อยทำการประมวลผลคำสั่งซื้อ
$customer_id = $this->settings->userdata('id');
$total_amount = $_POST['total_amount'];
$selected_items = $_POST['selected_items'];
$shipping_cost = $_POST['shipping_cost'];
$shipping_methods_id = $_POST['shipping_methods_id'];
$delivery_address = $_POST['delivery_address'];
