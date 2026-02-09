<?php
// ไฟล์: check_low_stock.php

// เรียกใช้ PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../../config.php');

// หากใช้ Composer, ให้ include autoload
require_once(__DIR__ . '../../../vendor/autoload.php'); // แก้ไข path ตามที่อยู่จริงของ vendor/autoload.php

// -----------------------------------------------------------------
// --- CONFIGURATION - กรุณาตั้งค่าทั้งหมดในส่วนนี้ ---
// -----------------------------------------------------------------

// 2. การตั้งค่าสต็อก (Stock)
define('LOW_STOCK_THRESHOLD', 10); // เกณฑ์สต็อกใกล้หมด (ตามตัวอย่างคือ 10)

// -----------------------------------------------------------------
// --- END CONFIGURATION ---
// -----------------------------------------------------------------

// ฟังก์ชันส่งอีเมล (อัปเดตให้รับ HTML และ Plain Text)
function send_email_notification($subject, $html_body, $plain_text_body) // <-- CHANGED
{
    $mail = new PHPMailer(true);
    try {
        //Server settings (ตั้งค่าตามที่คุณให้มา)
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // "ssl"
        $mail->Port       = SMTP_PORT;
        $mail->CharSet    = 'UTF-8';

        //Recipients
        $mail->setFrom(SMTP_USER, FROM_NAME);

        // เพิ่มอีเมล Admin ทั้ง 3 อีเมล
        if (defined('ADMIN_EMAILS')) {
            $admin_list = json_decode(ADMIN_EMAILS, true);
            if (is_array($admin_list)) {
                foreach ($admin_list as $email) {
                    $mail->addAddress($email, 'Admin');
                }
            }
        } else {
            // Fallback: ถ้าหา list ไม่เจอ ให้ส่งเข้าเมลตัวเองทดสอบ
            $mail->addAddress(SMTP_USER, 'Admin');
        }

        // Content
        $mail->isHTML(true); // <-- IMPORTANT
        $mail->Subject = $subject;
        $mail->Body    = $html_body; // <-- CHANGED: ใช้ HTML body
        $mail->AltBody = $plain_text_body; // <-- CHANGED: ใช้ Plain text สำหรับ AltBody

        $mail->send();
        echo "[SUCCESS] Email sent successfully.\n";
    } catch (Exception $e) {
        echo "[ERROR] Email could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
    }
}

// ฟังก์ชันส่ง Telegram (แก้ไขแล้ว)
function send_telegram_notification($message)
{
    $botToken = TELEGRAM_BOT_TOKEN;
    $chatId = TELEGRAM_CHAT_ID;

    // 1. URL ไม่ต้องยัดไส้ parameter ยาวๆ ใส่แค่ endpoint พอ
    $url = "https://api.telegram.org/bot{$botToken}/sendMessage";

    // 2. สร้างตัวแปร $data (ที่ของเดิมหายไป)
    // การส่งแบบ Array ดีกว่า เพราะไม่ต้อง urlencode เอง curl จัดการให้หมด
    $data = [
        'chat_id' => $chatId,
        'text' => $message,
        'parse_mode' => 'HTML' // แนะนำ HTML จัดการง่ายกว่า Markdown
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // เอา $data ยัดใส่ตรงนี้
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // เพิ่ม Timeout กันค้าง (สำคัญมาก)
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);

    // เช็ค Error ของ Curl โดยตรงด้วย
    if (curl_errno($ch)) {
        echo "[ERROR] Curl error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);

    // เช็คผลลัพธ์จาก Telegram
    if ($response) {
        $result = json_decode($response, true);
        if (isset($result['ok']) && $result['ok']) {
            echo "[SUCCESS] Telegram message sent.\n";
        } else {
            echo "[ERROR] Telegram API Error: " . ($result['description'] ?? 'Unknown error') . "\n";
        }
    } else {
        echo "[ERROR] No response from Telegram.\n";
    }
}
// -----------------------------------------------------------------
// --- MAIN SCRIPT ---
// -----------------------------------------------------------------

echo "Connecting to database...\n";

// 1. เชื่อมต่อฐานข้อมูล
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
if ($conn->connect_error) {
    die("[ERROR] Connection failed: " . $conn->connect_error . "\n");
}
$conn->set_charset("utf8");
echo "Database connected.\n";

// 2. ค้นหาสินค้าที่สต็อกใกล้หมด
$low_stock_threshold = LOW_STOCK_THRESHOLD;

$sql = "
    SELECT 
        pl.id,
        pl.name,
        (SELECT sl.sku FROM `stock_list` sl WHERE sl.product_id = pl.id LIMIT 1) as sku,
        (
            COALESCE((SELECT SUM(quantity) FROM `stock_list` WHERE product_id = pl.id), 0)
            -
            COALESCE((SELECT SUM(oi.quantity) FROM `order_items` oi 
                     INNER JOIN `order_list` ol ON oi.order_id = ol.id 
                     WHERE oi.product_id = pl.id AND ol.delivery_status != 10), 0)
        ) as current_stock
    FROM 
        product_list pl
    WHERE 
        pl.delete_flag = 0 AND pl.status = 1
    HAVING 
        current_stock <= {$low_stock_threshold}
    ORDER BY
        current_stock ASC;
";

echo "Running query to find low stock items (Threshold <= {$low_stock_threshold})...\n";

$result = $conn->query($sql);

if (!$result) {
    die("[ERROR] Query failed: " . $conn->error . "\n");
}

if ($result->num_rows > 0) {
    echo "[FOUND] Found {$result->num_rows} low stock items. Preparing notifications...\n";

    $subject = "แจ้งเตือน: สต็อกสินค้าใกล้หมด (" . date("d/m/Y") . ")";

    // --- สร้างข้อความแบบ Plain Text (สำหรับ Telegram และ AltBody) ---
    $plain_text_header = "รายการสินค้าที่สต็อกใกล้หมด (น้อยกว่าหรือเท่ากับ {$low_stock_threshold} ชิ้น)\n\n";
    $plain_text_list = [];

    // --- สร้างข้อความแบบ HTML (สำหรับ Email) ---
    $html_message_body = "
    <html>
    <head>
    <style>
        body { font-family: 'Tahoma', sans-serif; }
        table { 
            border-collapse: collapse; 
            width: 90%; 
            margin: 20px 0;
            font-size: 14px;
        }
        th, td { 
            border: 1px solid #ddd; 
            padding: 10px; 
            text-align: left;
        }
        th { 
            background-color: #f2f2f2; 
            color: #333;
        }
        tr:nth-child(even) { background-color: #f9f9f9; }
        td.stock { 
            text-align: center; 
            font-weight: bold;
            color: #D9534F; /* สีแดงเตือน */
        }
    </style>
    </head>
    <body>
        <h2>แจ้งเตือนสต็อกสินค้าใกล้หมด</h2>
        <p>พบรายการสินค้าที่สต็อกใกล้หมด (น้อยกว่า หรือเท่ากับ {$low_stock_threshold} ชิ้น) ดังนี้</p>
        
        <table>
            <thead>
                <tr>
                    <th>SKU</th>
                    <th>ชื่อสินค้า</th>
                    <th>คงเหลือ (ชิ้น)</th>
                </tr>
            </thead>
            <tbody>
    "; // <-- HTML Body ส่วนหัว

    $html_table_rows = ""; // ตัวแปรเก็บแถว <tr>...</tr>

    while ($row = $result->fetch_assoc()) {
        $stock_level = number_format($row['current_stock'], 0);
        $sku = $row['sku'] ? $row['sku'] : 'N/A';

        // 1. เพิ่มข้อมูลสำหรับ Plain Text
        $plain_text_list[] = "SKU: {$sku} | {$row['name']} | คงเหลือ: {$stock_level} ชิ้น";

        // 2. เพิ่มข้อมูลสำหรับ HTML Table
        $html_table_rows .= "
            <tr>
                <td>{$sku}</td>
                <td>{$row['name']}</td>
                <td class='stock'>{$stock_level}</td>
            </tr>
        ";
    }

    // --- ประกอบร่างข้อความ Plain Text ---
    $plain_text_body = $plain_text_header . implode("\n", $plain_text_list);

    // --- ประกอบร่างข้อความ HTML ---
    $html_message_body .= $html_table_rows; // เพิ่มแถวของสินค้า
    $html_message_body .= "
            </tbody>
        </table>
        <p>กรุณาตรวจสอบ และเติมสต็อกสินค้าโดยด่วน</p>
    </body>
    </html>
    "; // <-- HTML Body ส่วนท้าย

    // 3. ส่งการแจ้งเตือน

    // ส่ง Email (ส่งทั้ง HTML และ Plain Text)
    send_email_notification($subject, $html_message_body, $plain_text_body);

    // ส่ง Telegram (ใช้ Plain Text)
    $telegram_message = "‼️ *แจ้งเตือนสต็อกใกล้หมด* ‼️\n\n" . $plain_text_body;
    send_telegram_notification($telegram_message);
} else {
    echo "[INFO] No low stock items found. All good!\n";
}

// 4. ปิดการเชื่อมต่อ
$conn->close();
echo "Process finished.\n";
