<?php
require_once('config.php');

if (isset($_POST['coupon_code'])) {
    $coupon_code = $_POST['coupon_code'];

    // ตรวจสอบคูปองในฐานข้อมูล
    $qry = $conn->prepare("SELECT * FROM coupon_code_list WHERE coupon_code = ? AND status = 1 AND delete_flag = 0");
    $qry->bind_param("s", $coupon_code);
    $qry->execute();
    $result = $qry->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();

        // ตรวจสอบวันที่เริ่มต้นและหมดอายุ
        $current_date = date('Y-m-d H:i:s');
        if ($coupon['start_date'] <= $current_date && $coupon['end_date'] >= $current_date) {
            $response = [
                'type' => $coupon['type'],
                'discount_value' => $coupon['discount_value'],
                'error' => ''
            ];
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'คูปองหมดอายุ']);
        }
    } else {
        echo json_encode(['error' => 'คูปองไม่ถูกต้อง']);
    }
}
