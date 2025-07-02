<?php 

require_once 'moneyspace/Api.php';

$api = new Api();

$ms_data = array(
    "order_id" => "", // เลขที่ออเดอร์
);

$response = $api->Check_OrderID($ms_data); //  Call function

echo $response;

?>