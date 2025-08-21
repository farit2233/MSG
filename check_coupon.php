<?php
require_once('config.php');

header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

// รับข้อมูลจาก AJAX
$coupon_code = isset($_POST['coupon_code']) ? $_POST['coupon_code'] : '';
$cart_items = isset($_POST['cart_items']) ? $_POST['cart_items'] : [];
$cart_total = isset($_POST['cart_total']) ? floatval($_POST['cart_total']) : 0; // ยอดรวมเฉพาะสินค้า

// ตรวจสอบข้อมูลเบื้องต้น
if (empty($coupon_code) || empty($cart_items) || $cart_total <= 0) {
    echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

// 1. ค้นหาและตรวจสอบคูปอง
$qry = $conn->prepare("SELECT * FROM coupon_code_list WHERE coupon_code = ? AND status = 1 AND delete_flag = 0");
$qry->bind_param("s", $coupon_code);
$qry->execute();
$result = $qry->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'รหัสคูปองไม่ถูกต้อง']);
    exit;
}

$coupon = $result->fetch_assoc();

// 2. ตรวจสอบเงื่อนไขของคูปอง (วันหมดอายุ, ยอดขั้นต่ำ)
$current_date = date('Y-m-d H:i:s');
if ($coupon['start_date'] > $current_date || $coupon['end_date'] < $current_date) {
    echo json_encode(['success' => false, 'error' => 'คูปองหมดอายุแล้ว']);
    exit;
}

if ($cart_total < $coupon['minimum_order']) {
    $needed = number_format($coupon['minimum_order'] - $cart_total, 2);
    echo json_encode(['success' => false, 'error' => "ยอดสั่งซื้อขั้นต่ำ {$coupon['minimum_order']} บาท (ขาดอีก {$needed} บาท)"]);
    exit;
}

// 3. คำนวณส่วนลดตามเงื่อนไข all_products_status
$discount_amount = 0;
$base_total_for_discount = 0; // ยอดรวมที่จะใช้เป็นฐานในการคำนวณส่วนลด

if ($coupon['all_products_status'] == 1) {
    // ---- กรณี 1: ใช้ได้กับสินค้าทุกชิ้น ----
    $base_total_for_discount = $cart_total;
} else {
    // ---- กรณี 0: ใช้ได้กับสินค้าที่กำหนด ----
    // ดึง ID สินค้าที่ร่วมรายการของคูปองนี้
    $product_qry = $conn->prepare("SELECT product_id FROM coupon_code_products WHERE coupon_code_id = ?");
    $product_qry->bind_param("i", $coupon['id']);
    $product_qry->execute();
    $product_result = $product_qry->get_result();

    $eligible_product_ids = [];
    while ($row = $product_result->fetch_assoc()) {
        $eligible_product_ids[] = $row['product_id'];
    }

    if (empty($eligible_product_ids)) {
        echo json_encode(['success' => false, 'error' => 'คูปองนี้ไม่มีสินค้าที่ร่วมรายการ']);
        exit;
    }

    // วนลูปสินค้าในตะกร้า เพื่อหายอดรวมของสินค้าที่ร่วมรายการเท่านั้น
    foreach ($cart_items as $item) {
        if (in_array($item['product_id'], $eligible_product_ids)) {
            // ราคาสินค้าต่อชิ้น * จำนวน
            $base_total_for_discount += (floatval($item['price']) * intval($item['quantity']));
        }
    }

    if ($base_total_for_discount <= 0) {
        echo json_encode(['success' => false, 'error' => 'ไม่มีสินค้าที่ร่วมรายการในตะกร้าของคุณ']);
        exit;
    }
}


// 4. คำนวณยอดส่วนลดจากประเภทของคูปอง
$message = "";
switch ($coupon['type']) {
    case 'fixed':
        $discount_amount = floatval($coupon['discount_value']);
        $message = "ส่วนลด " . number_format($discount_amount, 2) . " บาท";
        break;
    case 'percent':
        $discount_value = floatval($coupon['discount_value']);
        $discount_amount = $base_total_for_discount * ($discount_value / 100);
        $message = "ส่วนลด {$discount_value}%";
        break;
    case 'free_shipping':
        // ในเคสนี้เราอาจจะคืนค่าส่งเป็นส่วนลด หรือจะให้ frontend จัดการเองก็ได้
        // สมมติว่าคืนเป็นส่วนลดเท่าค่าส่ง
        // $discount_amount = $shipping_cost_from_ajax; // ต้องส่งค่าส่งมาด้วย
        $message = "คูปองส่งฟรี";
        // สำหรับตัวอย่างนี้ จะไม่คำนวณส่วนลดเป็นตัวเงิน แต่จะส่ง type กลับไป
        break;
}

// 5. ส่งผลลัพธ์กลับไปเป็น JSON
$response = [
    'success' => true,
    'type' => $coupon['type'],
    'discount_amount' => round($discount_amount, 2),
    'message' => $message . $quantity_warning_message

];

echo json_encode($response);
