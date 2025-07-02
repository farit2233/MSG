<?php 

require_once 'moneyspace/Api.php';

$api = new Api();

$ms_data = array(
    "transaction_ID" => $_POST["transaction_ID"], // รหัสธุรกรรม
);

$response = $api->Cancel_Payment($ms_data); //  Call function

echo $response;

?>