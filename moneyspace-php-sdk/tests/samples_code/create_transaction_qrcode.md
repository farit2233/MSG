```php
<?php
    // put your secret_id, secret_key and order_id before test.
    // order_id: EX20210515173947
    $data = '{
            "secret_id": "",
            "secret_key": "",
            "order_id": "",
            "firstname": "John",
            "lastname": "Exmark",
            "email": "test@gmail.com",
            "phone": "0923456789",
            "amount": 3001,
            "description": "Belt 3001u0e3f ( 1 qty )",
            "address": "LH 164/55 House No. 39/112 Village No., Phetchaburi Rd., Makkasan, Ratchathewi, Bangkok, 10400",
            "message": "",
            "feeType": "include",
            "payment_type": "qrnone",
            "success_Url": "https://www.yourwebsite.com/success",
            "fail_Url": "https://www.yourwebsite.com/fail",
            "cancel_Url": "https://www.yourwebsite.com/cancel",
            "agreement": "4",
        }';

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://a.moneyspace.net/payment/CreateTransaction',
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