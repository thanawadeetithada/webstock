<?php
// process_out_stock.php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['user_id']) || !isset($_POST['products']) || !isset($_POST['total_price'])) {
        header("Location: waste_stock.php");
        exit;
    }

    $out_username = $_SESSION['username'];
    $total_price = floatval($_POST['total_price']);
    $out_date = date('Y-m-d H:i:s');

    // Decode JSON string into an array
    $products_json = $_POST['products'];
    $products = json_decode($products_json, true); // true to decode as associative array

    // Check if decoding was successful and $products is an array
    if (!is_array($products)) {
        echo "<script>alert('รูปแบบข้อมูลสินค้าที่ส่งมาไม่ถูกต้อง'); window.history.back();</script>";
        exit;
    }

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Insert into out_stock_product
        $stmt = $conn->prepare("INSERT INTO out_stock_product (total_price, out_date, out_username) VALUES (?, ?, ?)");
        $stmt->bind_param("dss", $total_price, $out_date, $out_username);
        $stmt->execute();
        $out_id = $stmt->insert_id; // Get the generated out_id
        // Insert each product into out_product_details
        $stmt_details = $conn->prepare(
            "INSERT INTO out_product_details (out_id, product_code, product_name, quantity, unit_price, expiry_date, 
            product_model, production_date, shelf_life, sticker_color, reminder_date, receive_date, unit, unit_cost, 
            sender_code, sender_company, recorder, category, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($products as $product) {
            $status = 'OUT';
            $stmt_details->bind_param(
                "issidssissdssssssss",
                $out_id,
                $product['product_code'],
                $product['product_name'],
                $product['quantity'],
                $product['unit_price'],
                $product['expiry_date'],
                $product['product_model'],
                $product['production_date'],
                $product['shelf_life'],
                $product['sticker_color'],
                $product['reminder_date'],
                $product['receive_date'],
                $product['unit'],
                $product['unit_cost'],
                $product['sender_code'],
                $product['sender_company'],
                $product['recorder'],
                $product['category'],
                $status
            );
            $stmt_details->execute();
        }

        $conn->commit(); // Commit transaction
        echo "<script>alert('ตัดสต็อกสำเร็จ'); window.location.href = 'waste_stock.php';</script>";
    } catch (Exception $e) {
        $conn->rollback(); // Rollback transaction on error
        echo "<script>alert('เกิดข้อผิดพลาด: " . $e->getMessage() . "'); window.history.back();</script>";
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: waste_stock.php");
    exit;
}
