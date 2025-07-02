<?php

require_once 'moneyspace/Api.php';

$api = new Api();

$ms_data = array(
    "firstname" => "example", // ชื่อลูกค้า
    "lastname" => "payment", // สกุลลูกค้า
    'email' => 'example@payment.com', // อีเมลล์เพื่อรับ ใบสำคัญรับเงิน (RECEIPT) (บังคับ)
    'phone' => '0888888888', // เบอร์โทร (บังคับ)
    "amount" => "5.10", // จำนวนเงิน 
    "description" => "iphone", // รายละเอียดสินค้า
    "address" => "111/22", // ที่อยู่ลูกค้า
    "feeType" => "include", // ผู้รับผิดชอบค่าธรรมเนียม ( include : ร้านค้ารับผิดชอบค่าธรรมเนียมบัตรเครดิต/เดบิต , exclude : ผู้ซื้อรับผิดชอบค่าธรรมเนียมบัตรเครดิต/เดบิต ไม่สามารถใช้กับประเภทการชำระเงินแบบ qr ได้)
    "message" => "", // ข้อความถึงร้านค้า
    "order_id" => "EX".date("YmdHis"),  // เลขที่ออเดอร์ ( ตัวอักษรภาษาอังกฤษพิมพ์ใหญ่ หรือตัวเลข สูงสุด 20 ตัว)
    "payment_type" => "card", // ประเภทการชำระเงิน ( card : บัตรเครดิต , qrnone : คิวอาร์โค๊ดพร้อมเพย์แบบรูป )
    "success_Url" => "https://www.yourwebsite.com/success", // เมื่อชำระเงินสำเร็จจะ redirect มายัง url
    "fail_Url" => "https://www.yourwebsite.com/fail", // เมื่อชำระเงินไม่สำเร็จจะ redirect มายัง url
    "cancel_Url" => "https://www.yourwebsite.com/cancel", // เมื่อชำระเงินไม่สำเร็จจะ redirect มายัง url
    "agreement" => 1
);



$response = $api->CreatePayment($ms_data); // Call function

$array_response = json_decode($response);


if ($array_response[0]->status == "success"){

    $link_payment = $array_response[0]->link_payment; // ลิ้งชำระเงิน

    $transaction_ID = $array_response[0]->transaction_ID; // รหัสธุรกรรม

    header( "location: ".$link_payment); // เปิดลิ้งชำระเงิน

}elseif ($array_response[0]->status == "error create"){
    

    echo "ข้อมูลไม่ถูกต้อง"; // กรุณาตรวจสอบ secret_id, secret_key, amount, feeType, order_id, payment_type 


}




?>