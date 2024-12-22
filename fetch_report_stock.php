<?php
include('config.php');

header('Content-Type: application/json');

// รับค่าจาก JavaScript
$input = json_decode(file_get_contents('php://input'), true);

$startDate = $input['startDate'];
$endDate = $input['endDate'];
$status = $input['status'];

try {
    $query = "";

    if ($status === 'ทั้งหมด' || $status === '') {
        $query = "
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, expiration_date AS stock_date, sticker_color, category, status 
            FROM products 
            WHERE expiration_date BETWEEN '$startDate' AND '$endDate'
            UNION ALL
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, out_date AS stock_date, sticker_color, category, status 
            FROM out_product_details 
            WHERE out_date BETWEEN '$startDate' AND '$endDate'
            UNION ALL
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, sell_date AS stock_date, sticker_color, category, status 
            FROM sell_product_details 
            WHERE sell_date BETWEEN '$startDate' AND '$endDate'
        ";
    } elseif ($status === 'SELL') {
        $query = "
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, sell_date AS stock_date, sticker_color, category, status 
            FROM sell_product_details 
            WHERE sell_date BETWEEN '$startDate' AND '$endDate'
        ";
    } elseif ($status === 'OUT') {
        $query = "
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, out_date AS stock_date, sticker_color, category, status 
            FROM out_product_details 
            WHERE out_date BETWEEN '$startDate' AND '$endDate'
        ";
    } elseif ($status === 'active') {
        $query = "
            SELECT product_code, product_name, quantity, unit, unit_cost, received_date, expiration_date AS stock_date, sticker_color, category, status 
            FROM products 
            WHERE expiration_date < CURDATE() 
            AND expiration_date BETWEEN '$startDate' AND '$endDate'
        ";
    }

    $result = $conn->query($query);

    if ($result) {
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        echo json_encode($products);
    } else {
        echo json_encode(["error" => "SQL error: " . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>
