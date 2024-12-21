<?php
session_start();
include 'config.php';

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['sell_products']) || !is_array($data['sell_products'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid data format']);
            exit;
        }

        $products = $data['sell_products'];
        $seller_username = $_SESSION['username'] ?? 'Unknown';
        $sell_date = date('Y-m-d H:i:s');

        $stmt = $conn->prepare(
            "INSERT INTO sell_product 
            (product_code, product_name, quantity, unit_price, total_price, expiry_date, sell_date, seller_username,
            product_model, production_date, shelf_life, sticker_color, reminder_date, receive_date, unit, unit_cost,
            sender_code, sender_company, recorder, category, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );

        foreach ($products as $product) {
            $product_code = $product['product_code'];
            $product_name = $product['product_name'];
            $quantity = $product['quantity'];
            $unit_price = $product['unit_price'];
            $total_price = $quantity * $unit_price;
            $expiry_date = $product['expiry_date'] ?? null;
        
            // ค่าของคอลัมน์ใหม่ (แก้ไขให้ดึงค่าที่เหมาะสม)
            $product_model = $product['product_model'] ?? null;
            $production_date = $product['production_date'] ?? null;
            $shelf_life = $product['shelf_life'] ?? null;
            $sticker_color = $product['sticker_color'] ?? null;
            $reminder_date = $product['reminder_date'] ?? null;
            $receive_date = $product['receive_date'] ?? null;
            $unit = $product['unit'] ?? null;
            $unit_cost = $product['unit_cost'] ?? null;
            $sender_code = $product['sender_code'] ?? null;
            $sender_company = $product['sender_company'] ?? null;
            $recorder = $product['recorder'] ?? null;
            $category = $product['category'] ?? null;
            $status = $product['status'] ?? null;
        
            $stmt->bind_param(
                "ssiddssssssssssdsssss",
                $product_code,
                $product_name,
                $quantity,
                $unit_price,
                $total_price,
                $expiry_date,
                $sell_date,
                $seller_username,
                $product_model,
                $production_date,
                $shelf_life,
                $sticker_color,
                $reminder_date,
                $receive_date,
                $unit,
                $unit_cost,
                $sender_code,
                $sender_company,
                $recorder,
                $category,
                $status
            );
        
            $stmt->execute();
        }

        $stmt->close();
        $conn->close();

        echo json_encode([
            'success' => true,
            'message' => 'คุณต้องการชำระเงินสินค้าทั้งหมดนี้ใช่หรือไม่?',
            'redirect' => 'payment.php',
            'products' => $products,
            'total_price' => array_sum(array_map(function ($product) {
                return $product['quantity'] * $product['unit_price'];
            }, $products))
        ]);
            } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>