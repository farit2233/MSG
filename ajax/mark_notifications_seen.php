<?php
require_once '../config.php';
if ($_settings->userdata('id') && $_settings->userdata('login_type') == 2) {
    $customer_id = $_settings->userdata('id');
    $conn->query("UPDATE order_list SET is_seen = 1 WHERE customer_id = '{$customer_id}'");
    echo json_encode(['status' => 'success']);
}
