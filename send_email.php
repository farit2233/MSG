<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once('config.php');  // <- อย่าลืมเชื่อม DB

$order_id = 33;

// 1. ดึงข้อมูลคำสั่งซื้อ
$order = $conn->query("SELECT o.*, c.firstname, c.lastname, c.email
    FROM order_list o
    INNER JOIN customer_list c ON o.customer_id = c.id
    WHERE o.id = $order_id")->fetch_assoc();

$fullname = $order['firstname'] . ' ' . $order['lastname'];
$email = $order['email'];
$total = number_format($order['total_amount'], 2);
$code = $order['code'];

// 2. ดึงรายการสินค้า
$items = $conn->query("SELECT oi.*, p.name
    FROM order_items oi
    INNER JOIN product_list p ON oi.product_id = p.id
    WHERE oi.order_id = $order_id");

// 3. สร้าง HTML สำหรับอีเมล
$body = "
<h3>ยืนยันคำสั่งซื้อ #{$code}</h3>
<p>เรียนคุณ {$fullname},</p>
<p>คำสั่งซื้อของคุณได้รับการชำระเรียบร้อยแล้ว มีรายละเอียดมีดังนี้</p>
<table border='1' cellspacing='0' cellpadding='8' style='border-collapse: collapse; width:100%'>
    <thead>
        <tr>
            <th>สินค้า</th>
            <th>จำนวน</th>
            <th>ราคาต่อหน่วย</th>
            <th>รวม</th>
        </tr>
    </thead>
    <tbody>
";

while ($row = $items->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $body .= "
        <tr>
            <td>{$row['name']}</td>
            <td>{$row['quantity']}</td>
            <td>" . number_format($row['price'], 2) . "</td>
            <td>" . number_format($subtotal, 2) . "</td>
        </tr>
    ";
}

$body .= "</tbody></table>";
$body .= "<p><strong>ยอดรวมทั้งหมด: {$total} บาท</strong></p>";
$body .= "<p>เราจะดำเนินการจัดส่งโดยเร็วที่สุด 🙏 ขอบคุณครับ/ค่ะ</p>";

// 4. ส่งอีเมลด้วย PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'localhost';
    $mail->Port = 1025;
    $mail->SMTPAuth = false;
    $mail->CharSet = 'UTF-8';

    $mail->setFrom('shop@example.com', 'ร้านเฟรดเทพ');
    $mail->addAddress($email, $fullname);

    $mail->isHTML(true);
    $mail->Subject = "📦 ยืนยันคำสั่งซื้อ #$code";
    $mail->Body = $body;

    $mail->send();
    echo '✅ ส่งเมลยืนยันคำสั่งซื้อสำเร็จ';
} catch (Exception $e) {
    echo "❌ ส่งเมลไม่สำเร็จ: {$mail->ErrorInfo}";
}
