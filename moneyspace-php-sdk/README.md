
  

# Requirements

- PHP 5.6+

- PHP knowledge

# Configuration

- เปิด moneyspace/config/config.php

- กรอก secret id , secret key

# Demo

[![Test on PHPSandbox](https://phpsandbox.io/img/brand/badge.png)](https://phpsandbox.io/n/4qy1l)

# Example

### ตัวอย่าง : การสร้าง Transaction ID และ เปิดเว็บชำระเงิน ( บัตรเครดิต )

- สถานะหลังชำระเงินที่ได้รับจาก Webhook มีดังนี้ : paysuccess , fail

- Transaction ID มีอายุ 24 ชั่วโมงหลังจากหมดอายุสถานะจะเปลี่ยนเป็น Expired

#### เงื่อนไขยอมรับการขอคืนเงิน หรือการยกเลิกรายการการจ่ายเงิน ( **agreement** )

1. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงิน หรือยกเลิกรายการได้
2. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 7 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
3. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 14 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
4. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 30 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
5. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 60 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า

```php

<?php

require_once 'moneyspace/Api.php';

$api = new Api();



$ms_data = array(
    "firstname" => "example", // ชื่อลูกค้า
    "lastname" => "payment", // สกุลลูกค้า
    "email" => "", // อีเมลล์เพื่อรับ ใบสำคัญรับเงิน (RECEIPT)
    "phone" => "", // เบอร์โทรศัพท์
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
    "agreement" => 4
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

```

*****

### ตัวอย่าง : การสร้าง Transaction ID และ เปิดเว็บชำระเงิน ( คิวอาร์โค้ด พร้อมเพย์ )

- สถานะหลังชำระเงินที่ได้รับจาก Webhook มีดังนี้ : paysuccess , fail

- คิวอาร์โค้ดต้องชำระเงินด้วยการสแกนภายใน 8 ชั่วโมง

```php

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
            $("#cancel").hide();
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
  -webkit-transition-duration: 0.4s;
  transition-duration: 0.4s;
  text-decoration: none;
  overflow: hidden;
  cursor: pointer;
}
</style>

</body>
</html>
```

****

### ตัวอย่าง : การสร้าง Transaction ID และ เปิดเว็บชำระเงิน ( ผ่อนชำระรายเดือน 0%  )
- สถานะหลังชำระเงินที่ได้รับจาก Webhook มีดังนี้ : paysuccess , fail

- Transaction ID มีอายุ 24 ชั่วโมงหลังจากหมดอายุสถานะจะเปลี่ยนเป็น Expired

#### ช่วงเดือนในการผ่อนชำระเงิน ( **startTerm , endTerm** )

**ประเภทบัตร (bankType)** | **ร้านค้ารับผิดชอบดอกเบี้ยรายเดือน (feeType : include)** | **ผู้ถือบัตรรับผิดชอบดอกเบี้ยรายเดือน ดอกเบี้ย 0.8% , 1% (feeType : exclude)** |
--- |--- | --- |
KTC | `3, 4, 5, 6, 7, 8, 9, 10` | `3, 4, 5, 6, 7, 8, 9, 10` ( 0.8% )
BAY | `3, 4, 6, 9, 10` | `3, 4, 6, 9, 10`  ( 0.74% )
FCY | `3, 4, 6, 9, 10` | `3, 4, 6, 9, 10, 12, 18, 24, 36`  ( 1% )

KTC : บัตรเคทีซี

BAY : บัตรกรุงศรี วีซ่า , บัตรเซ็นทรัล , บัตรเทสโก้โลตัส

FCY : บัตรกรุงศรีเฟิร์สช้อยส์ , บัตรโฮมโปร , บัตรเมกาโฮม


#### เงื่อนไขยอมรับการขอคืนเงิน หรือการยกเลิกรายการการจ่ายเงิน ( **agreement** )

1. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงิน หรือยกเลิกรายการได้
2. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 7 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
3. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 14 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
4. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 30 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า
5. ข้าพเจ้ายอมรับว่าไม่สามารถขอคืนเงินและเมื่อหากสินค้า / บริการมีปัญหาจะรีบติดต่อกลับ ภายใน 60 วัน หรือ ปฏิบัติตามนโยบายการคืนเงินของร้านค้า




```php
<?php 

require_once 'moneyspace/Api.php';

$api = new Api();


$ms_data = array(
    "firstname" => "example", // ชื่อลูกค้า
    "lastname" => "payment", // สกุลลูกค้า
    "email" => "", // อีเมลล์เพื่อรับ ใบสำคัญรับเงิน (RECEIPT)
    "phone" => "", // เบอร์โทรศัพท์
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


if ($array_response[0]->status == "success"){

    $link_payment = $array_response[0]->link_payment; // ลิ้งชำระเงิน

    $transaction_ID = $array_response[0]->transaction_ID; // รหัสธุรกรรม

    header( "location: ".$link_payment); // เปิดลิ้งชำระเงิน

}else{

    echo "ข้อมูลไม่ถูกต้อง"; // กรุณาตรวจสอบ secret_id, secret_key, amount, feeType, order_id, payment_type

}



?>



```

>  ***Note* : การตรวจสอบสถานะการขำระเงินต้องตรวจสอบด้วย order_id**

****





### ตัวอย่าง : การเช็คสถานะ Transaction ID

 - การเช็คสถานะ Transaction ID เมื่อข้อมูลถูกต้องจะ Response ค่า JSON กลับมาได้แก่  Amount ,  Transaction ID , Description , Status Payment

- สถานะมีดังนี้ : pending , Pay Success , Fail , Cancel , Expired 

- Webhook ไม่มีการส่งค่า
```
| Status Payment | Description |
| ----------- | ----------- |
| pending | รอชำระเงิน |
| Pay Success | ชำระเงินสำเร็จ |
| Fail | ชำระเงินไม่สำเร็จ |
| Cancel | ยกเลิก Transaction ID |
| Expired | หมดอายุ |

```

  

```php

  

<?php

require_once 'moneyspace/Api.php';

$api = new Api();
  


$ms_data = array(

	"transaction_ID" => "MSTRF18000000195523" // รหัสธุรกรรม

);

  

$response = $api->Check_Transaction($ms_data); // Call function

  

echo  $response;
  

?>

  

```

  

*****




### ตัวอย่าง : การเช็คสถานะ Order ID

 - การเช็คสถานะ Order ID เมื่อข้อมูลถูกต้องจะ Response ค่า JSON กลับมาได้แก่ Amount ,  Order ID , Description , Status Payment

- สถานะมีดังนี้ : pending , Pay Success , Fail , Cancel , Expired
 
- Webhook ไม่มีการส่งค่า
```
| Status Payment | Description |
| ----------- | ----------- |
| pending | รอชำระเงิน |
| Pay Success | ชำระเงินสำเร็จ |
| Fail | ชำระเงินไม่สำเร็จ |
| Cancel | ยกเลิก Transaction ID |
| Expired | หมดอายุ |

```

  

  

```php

  

<?php

  
require_once 'moneyspace/Api.php';

$api = new Api();
  

$ms_data = array(

	"order_id" => "", // เลขที่ออเดอร์

);

  

$response = $api->Check_OrderID($ms_data); // Call function

  

echo  $response;
  

?>

  

```

  

*****





### ตัวอย่าง : การรับและตรวจสอบค่าจาก Webhook

- สถานะหลังชำระเงินที่ได้รับจาก Webhook มีดังนี้ : paysuccess , fail

```php

<?php
 


require_once 'moneyspace/Api.php';

$api = new Api();


$GetWebhook = $api->GetWebhook(); 


if($GetWebhook["status_verify"] == "pass"){ // ตรวจข้อมูล Webhook ว่าถูกต้องหรือไม่

    // ตัวอย่างการรับค่าจาก webhook แล้วเขียนลงไฟล์ txt

    $status = $GetWebhook["data"]['status']; 
    $transactionID = $GetWebhook["data"]['transactionID']; 
    $amount = $GetWebhook["data"]['amount']; 
    $orderid = $GetWebhook["data"]['orderid']; 

    $txt = "status : ".$status." , transactionID : ".$transactionID. " , amount : ".$amount. " , orderid : ".$orderid;

    $file = 'webhook_data_'.date('dmY_Hsi').'.txt'; 
    $current = file_get_contents($file);
    file_put_contents($file, $txt);

}





?>


```

****

# Example API 
### ในโฟลเดอร์ tests จะมีตัวอย่างของ API
```
tests
|___creditcard
|   |   create_transaction.php
|___installment
|   |   create_transaction.php
|___qrcode
|   |   create_transaction.php
|___samples_code
|   |   cancel_payment.md
|   |   check_orderId.md
|   |   check_payment.md
|   |   create_transaction_creditcard.md
|   |   create_transaction_installment.md
|   |   create_transaction_qrcode.md
│   cancel_payment.php
│   check_orderId.php
│   check_payment.php 
│   index.php
```

ตัวอย่างของ API และ samples code สำหรับนำไปทดสอบ

# Changelog
-  ##### 2023-05-22 : Update Demo (PHPSandbox) and add require param
-  ##### 2022-05-29 : Add secret_key
-  ##### 2022-05-21 : Remove secret_key and add demo via phpsandbox
-  ##### 2021-07-09 : Add cancel payment file and update sample code
-  ##### 2021-05-16 : Add sample code for checking status and description into the readme
-  ##### 2021-05-16 : Add Sample API for testing
-  ##### 2020-10-16 : Added