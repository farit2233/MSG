<?php 

require_once 'moneyspace/Api.php';

$api = new Api();

$ms_data = array(
    "firstname" => "example", // ชื่อลูกค้า
    "lastname" => "payment", // สกุลลูกค้า
    'email' => 'example@payment.com', // อีเมลล์เพื่อรับ ใบสำคัญรับเงิน (RECEIPT) (บังคับ)
    'phone' => '0888888888', // เบอร์โทร (บังคับ)
    "amount" => "4200.00", // จำนวนเงิน ( ขั้นต่ำ 3000.01 บาท )
    "description" => "iphone", // รายละเอียดสินค้า
    "address" => "111/22", // ที่อยู่ลูกค้า
    "feeType" => "include", // ผู้รับผิดชอบค่าธรรมเนียม ( include : ร้านค้ารับผิดชอบดอกเบี้ยรายเดือน , exclude : ผู้ถือบัตรรับผิดชอบดอกเบี้ยรายเดือน ดอกเบี้ย 0.8% , 1% )
    "message" => "", // ข้อความถึงร้านค้า
    "order_id" => "EX".date("YmdHis"),  // เลขที่ออเดอร์ ( ตัวอักษรภาษาอังกฤษพิมพ์ใหญ่ หรือตัวเลข สูงสุด 20 ตัว)
    "success_Url" => "https://www.yourwebsite.com/success", // เมื่อชำระเงินสำเร็จจะ redirect มายัง url
    "fail_Url" => "https://www.yourwebsite.com/fail", // เมื่อชำระเงินไม่สำเร็จจะ redirect มายัง url
    "cancel_Url" => "https://www.yourwebsite.com/cancel", // เมื่อชำระเงินไม่สำเร็จจะ redirect มายัง url
    "agreement" => "1",  // เงื่อนไขยอมรับการขอคืนเงิน หรือการยกเลิกรายการการจ่ายเงิน
    "bankType" => "BAY", // 3 ประเภทบัตร ได้แก่ KTC, BAY, FCY 
    "startTerm" => "3",  // ช่วงเดือนเริ่มต้นในการผ่อนชำระเงิน เช่น 3, 4, 5, 6, 7, 8, 9, 10 
    "endTerm" => "10",  // ช่วงเดือนสิ้นสุดในการผ่อนชำระเงิน เช่น 3, 4, 6, 9, 10, 12, 18, 24, 36 ( fee include สูงสุุด 10 เดือน )
);



$response = $api->Createinstallment($ms_data); // Call function

$array_response = json_decode($response); // JSON to Array


?>


<form id="payform" method="post" action="https://stage.moneyspace.net/baycredit/pay">
<input type="hidden" id="term" name="term" value="3">
<input type="hidden" id="transactionID" name="transactionID" value="<?=$array_response[0]->transaction_ID?>">
<input type="hidden" id="pay_type" name="pay_type" value="">
<input type="hidden" id="locale" name="locale" value="">
<input type="hidden" id="paymonth" name="paymonth" value="1400.00">
<input type="hidden" id="bankType" name="bankType" value="BAY">
<input type="hidden" id="interest" name="interest" value="0.0">
<button class="button" type="submit">ชำระเงิน</button>
</form>