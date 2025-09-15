<?php
require_once('../config.php');

class GetProductStock extends DBConnection
{
    public function __construct()
    {
        parent::__construct();
    }

    public function __destruct()
    {
        parent::__destruct();
    }

    public function get_stock()
    {
        header('Content-Type: application/json');

        if (empty($_POST['product_ids'])) {
            echo json_encode([]);
            exit;
        }

        $product_ids = array_map('intval', explode(',', $_POST['product_ids']));
        $ids_placeholder = implode(',', array_fill(0, count($product_ids), '?'));

        $sql = "SELECT 
                    p.id,
                    (COALESCE((SELECT SUM(quantity) FROM `stock_list` where product_id = p.id), 0) - 
                     COALESCE((SELECT SUM(quantity) FROM `order_items` where product_id = p.id), 0)) as `available`
                FROM `product_list` p
                WHERE p.id IN ({$ids_placeholder})";

        $stmt = $this->conn->prepare($sql);

        // Bind parameters dynamically
        $types = str_repeat('i', count($product_ids));
        $stmt->bind_param($types, ...$product_ids);

        $stmt->execute();
        $result = $stmt->get_result();

        $stock_data = [];
        while ($row = $result->fetch_assoc()) {
            $stock_data[$row['id']] = $row['available'];
        }

        echo json_encode($stock_data);
    }
}

// Instantiate and run
$action = new GetProductStock();
$action->get_stock();
