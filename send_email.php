<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';
require_once('config.php');  // <- อย่าลืมเชื่อม DB

$order_id = 42;

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
<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: auto;'>
  <h2 style='color: #16542b; border-bottom: 2px solid #16542b; padding-bottom: 10px;'>🧾 คำสั่งซื้อของคุณ$code</h2>
  
  <p>เรียนคุณ <strong>$fullname</strong>,</p>
  <p>ขอบคุณที่สั่งซื้อสินค้ากับร้านของเรา 🙏</p>
  
  <table style='width: 100%; border-collapse: collapse; margin-top: 20px;'>
    <thead style='background-color: #16542b; color: white;'>
      <tr>
        <th style='padding: 8px; border: 1px solid #ddd;'>สินค้า</th>
        <th style='padding: 8px; border: 1px solid #ddd;'>จำนวน</th>
        <th style='padding: 8px; border: 1px solid #ddd;'>ราคาต่อชิ้น</th>
        <th style='padding: 8px; border: 1px solid #ddd;'>รวม</th>
      </tr>
    </thead>
    <tbody>";


while ($row = $items->fetch_assoc()) {
    $subtotal = $row['price'] * $row['quantity'];
    $body .= "
      <tr>
        <td style='padding: 8px; border: 1px solid #ddd;'>{$row['name']}</td>
        <td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>{$row['quantity']}</td>
        <td style='padding: 8px; border: 1px solid #ddd; text-align: right;'>" . number_format($row['price'], 2) . "</td>
        <td style='padding: 8px; border: 1px solid #ddd; text-align: right;'>" . number_format($subtotal, 2) . "</td>
      </tr>";
}

$body .= "
    </tbody>
  </table>

  <h3 style='text-align: right; color: #16542b;'>ยอดรวม: " . number_format($order['total_amount'], 2) . " บาท</h3>

  <p style='margin-top: 30px;'>📦 ระบบกำลังดำเนินการจัดส่งไปยัง</p>
  <div style='padding: 10px; background-color: #f9f9f9; border: 1px dashed #ccc;'>
    {$order['delivery_address']}
  </div>

  <p style='margin-top: 30px;'>หากคุณมีคำถาม สามารถติดต่อเราได้ที่ <a href='mailto:support@example.com'>support@example.com</a></p>

  <p style='color: #888;'>ขอขอบคุณอีกครั้งที่ไว้วางใจร้านของเรา 🙏 ขอบคุณครับ/ค่ะ</p>
</div>
";

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
