<?php

require_once 'moneyspace/Api.php';

$api = new Api();

$ms_data = array(
    "firstname" => "example", // ชื่อลูกค้า
    "lastname" => "payment", // สกุลลูกค้า
    "email" => "", // อีเมลล์เพื่อรับ ใบสำคัญรับเงิน (RECEIPT)
    "phone" => "", // เบอร์โทรศัพท์
    "amount" => "1.67", // จำนวนเงิน
    "description" => "iphone", // รายละเอียดสินค้า
    "address" => "111/22", // ที่อยู่ลูกค้า
    "message" => "", // ข้อความถึงร้านค้า
    "order_id" => "EX".date("YmdHis"),  // เลขที่ออเดอร์ ( ตัวอักษรภาษาอังกฤษพิมพ์ใหญ่ หรือตัวเลข สูงสุด 20 ตัว)
    "payment_type" => "qrnone"
);

$response = $api->CreatePayment($ms_data); // Call function
sleep(10); // set delay time to 10 sec after call create payment
$array_response = json_decode($response); // JSON to Array

if ($array_response[0]->status == "success"){

    $image_qrprom = $array_response[0]->image_qrprom; // ลิ้งรูป QR Code Promptpay

    $transaction_ID = $array_response[0]->transaction_ID; // รหัสธุรกรรม

}

?>


<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
</head>
<script type="text/javascript">

    var timeout = 300

    function moneyspace_check_payment(response){
        let res_json = JSON.parse(response);
        if (res_json[0]['Status Payment '] == "Pay Success"){ // ตรวจสอบสถานะ
            $("#scan").hide(); // ซ่อนข้อความ
            $("#qrpayment").hide(); // ซ่อน QR
            $("#successpayment").show(); // โชว์ข้อความ
            $("#createqr").show(); // โชว์ข้อความ
            $("#cancel").hide(); // ซ่อน QR
            $("#cancel").text("...");
        }else if (res_json[0]['Status Payment '] == "Pending"){
            $("#cancel").text("ยกเลิก QR Code (ภายใน " + timeout + " วินาที)");
        }
    }

    function moneyspace_cancel_payment(response){
        let res_json = JSON.parse(response);
        if (res_json[0]['status'] == "success"){
            timeout = 0
            $("#cancel").css("background-color", "#555555");
            $("#cancel").text("QR Code : " + '<?=$transaction_ID?>' + " ถูกยกเลิกแล้ว");
        }
    }

    $(document).ready(function () {
        let interval = setInterval(() => {
            if (timeout === 0) {
                CallMethod('./Cancel_Payment.php', {'transaction_ID': '<?=$transaction_ID?>'}, moneyspace_cancel_payment); // ยกเลิก QR Code
                clearInterval(interval)                
            } else {
                CallMethod("./Check_Transaction.php", {'transaction_ID': '<?=$transaction_ID?>'}, moneyspace_check_payment); // Call ตรวจสอบสถานะ
                timeout--
            }             
        }, 1000)

    });

    function CallMethod(url, parameters, successCallback) {
             $.ajax({
                    type: 'POST',
                    url: url,
                    data: parameters,
                    success: successCallback,
                    error: function(xhr, textStatus, errorThrown) {
                        console.log('error');
                    }
            });
    }

</script>
<body>

<div align="center">
    <h2>QR Code Promptpay</h2>
    <hr>
    <h2 id="scan">กรุณาสแกนชำระเงิน</h2>
    <img id="qrpayment" src="<?=$image_qrprom?>" alt="">
    <h3 id="successpayment" style="color:green" hidden>ชำระเงินเรียบร้อยแล้ว</h3>
    <hr>
    <button id="cancel" type="button" class="button" style="background-color: #f44336;" onclick="CallMethod('./Cancel_Payment.php', {'transaction_ID': '<?=$transaction_ID?>'}, moneyspace_cancel_payment);">ยกเลิก QR Code</button>
    <br>
    <a href="" id="createqr" class="button" style="background-color: #008CBA;" hidden>สร้าง QR Code อีกครั้ง</a>
</div>

<style>
.button {
  position: relative;
  border: none;
  color: #FFFFFF;
  padding: 10px;
  width: 250px;
  text-align: center;
  -webkit-transition-duration: 0.4s; /* Safari */
  transition-duration: 0.4s;
  text-decoration: none;
  overflow: hidden;
  cursor: pointer;
}
</style>

</body>
</html>