<?php
session_start();
include 'config.php';

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['products']) && is_array($data['products'])) {
        $products = $data['products'];

        $current_date = date('Y-m-d');

        $query = $conn->prepare("UPDATE products SET status = 'OUT', out_stock_date = ? WHERE product_code = ?");
        
        $success = true;
        foreach ($products as $product_code) {
            $query->bind_param("ss", $current_date, $product_code);
            if (!$query->execute()) {
                $success = false;
                break;
            }
        }

        if ($success) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตสถานะ']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'การร้องขอไม่ถูกต้อง']);
}
?>
