<?php
session_start();
require_once('config.php'); // ต้องมั่นใจว่าไฟล์นี้มีการเชื่อมต่อ DB ($conn) และมีข้อมูลตะกร้า ($cart_items)

// --- จำลองข้อมูลตะกร้าและยอดรวม (ในระบบจริงต้องดึงมาจาก Session หรือฐานข้อมูล) ---
// คุณต้องแน่ใจว่าได้ดึงข้อมูล $cart_items และ $cart_total มาเรียบร้อยแล้วก่อนส่วนนี้
// ตัวอย่าง: $cart_items = $_SESSION['cart'];
// $cart_total = 0;
// foreach($cart_items as $item) {
//     $cart_total += $item['price'] * $item['quantity'];
// }
// --- จบส่วนจำลอง ---


header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

if (!isset($_POST['coupon_code'])) {
    echo json_encode(['success' => false, 'error' => 'ไม่พบรหัสคูปอง']);
    exit;
}

$coupon_code = trim($_POST['coupon_code']);

// 1. ค้นหาคูปองในฐานข้อมูล
$qry = $conn->prepare("SELECT * FROM coupon_code_list WHERE coupon_code = ? AND status = 1 AND delete_flag = 0");
$qry->bind_param("s", $coupon_code);
$qry->execute();
$result = $qry->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'error' => 'รหัสคูปองไม่ถูกต้อง']);
    exit;
}

$coupon = $result->fetch_assoc();

// 2. ตรวจสอบวันหมดอายุ
$current_date = date('Y-m-d H:i:s');
if ($coupon['start_date'] > $current_date || $coupon['end_date'] < $current_date) {
    echo json_encode(['success' => false, 'error' => 'คูปองนี้ยังไม่เริ่มใช้งานหรือหมดอายุแล้ว']);
    exit;
}

// 3. ตรวจสอบยอดสั่งซื้อขั้นต่ำ
if ($cart_total < $coupon['minimum_order']) {
    $needed_amount = $coupon['minimum_order'] - $cart_total;
    echo json_encode(['success' => false, 'error' => 'ยอดสั่งซื้อขั้นต่ำไม่ถึง, ต้องการอีก ' . number_format($needed_amount, 2) . ' บาท']);
    exit;
}

// 4. ตรวจสอบว่าคูปองใช้กับสินค้าในตะกร้าได้หรือไม่
if ($coupon['all_products_status'] == 0) { // คูปองที่จำกัดเฉพาะสินค้าบางชิ้น
    $product_ids_in_cart = array_column($cart_items, 'product_id');

    // สร้าง placeholder สำหรับ prepared statement
    $placeholders = implode(',', array_fill(0, count($product_ids_in_cart), '?'));
    $types = str_repeat('i', count($product_ids_in_cart)); // 'i' for integer

    // ค้นหาสินค้าที่ร่วมรายการคูปองนี้
    $promo_prod_qry = $conn->prepare("SELECT product_id FROM coupon_code_products WHERE coupon_code_id = ? AND product_id IN ($placeholders)");
    $promo_prod_qry->bind_param("i" . $types, $coupon['id'], ...$product_ids_in_cart);
    $promo_prod_qry->execute();
    $promo_prod_result = $promo_prod_qry->get_result();

    if ($promo_prod_result->num_rows === 0) {
        echo json_encode(['success' => false, 'error' => 'คูปองนี้ไม่สามารถใช้กับสินค้าในตะกร้าของคุณได้']);
        exit;
    }
}

// ถ้าผ่านทุกเงื่อนไข: บันทึกคูปองใน Session และส่ง response กลับ
$_SESSION['applied_coupon_code'] = $coupon_code;
echo json_encode(['success' => true]);
