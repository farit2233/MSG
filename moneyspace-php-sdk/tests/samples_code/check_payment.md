```php
<?php 
    // put your secret_id, secret_key and order_id before test.
    // transaction_ID: MSTRF18000000190442
    $data = '{
            "secret_id": "",
            "secret_key": "",
            "transaction_ID": ""
        }';

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.moneyspace.net/CheckPayment',
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