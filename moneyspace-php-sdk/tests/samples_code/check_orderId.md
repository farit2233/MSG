```php
<?php 
    // put your secret_id, secret_key and order_id before test.
    // order_id: EX20210515173947
    $data = '{
            "secret_id": "",
            "secret_key": "",
            "order_id": ""
        }';

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.moneyspace.net/CheckOrderID',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS =>$data
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    print_r($response);

?>

```