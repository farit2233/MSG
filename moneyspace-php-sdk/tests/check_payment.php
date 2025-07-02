<?php
$result = "";
if (!empty($_POST)) {
    $secret_id = $_POST["secret_id"];
    $secret_key = $_POST["secret_key"];
    $transaction_ID = $_POST["transaction_ID"];

    $data = array("secret_id" => $secret_id
        , "secret_key" => $secret_key
        , "transaction_ID" => $transaction_ID
        );
    
    $result = check_payment($data);
}

function check_payment($data) {
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

    return $response;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <title>Check Payment Document</title>
</head>
<body>
    <div class="container">
    <div class="container text-center">
        <h1>Check Payment example</h1>
    </div>
    <a href="/index.php">Go Home</a> | 
    <a href="/samples_code/check_payment.md" target="blank">View the complete code</a>
    <form method="post">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="secret_id">secret_id *</label>
                <input type="text" class="form-control" id="secret_id" name="secret_id" required>
            </div>
            <div class="form-group col-md-6">
                <label for="secret_key">secret_key *</label>
                <input type="text" class="form-control" id="secret_key" name="secret_key" required>
            </div>
        </div>
        <div class="form-group">
            <label for="transaction_ID">transaction_ID *</label>
            <input type="text" class="form-control" id="transaction_ID" name="transaction_ID" required>
        </div>
        <button type="submit" class="btn btn-primary">submit</button>
        
        </form>
        <hr>
        <h2>Result</h2>
        <pre class="alert alert-secondary" style="height: 400px; white-space: pre-wrap; white-space: -moz-pre-wrap; white-space: -pre-wrap; white-space: -o-pre-wrap; word-wrap: break-word;">
        <?php 
            print_r($result);
        ?>
        </pre>
    </div>
</body>
<footer>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</footer>
</html>