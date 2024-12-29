<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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
            "INSERT INTO out_product_details (out_id, product_code, product_name, quantity, unit_price, expiration_date,
            product_model, production_date, shelf_life, sticker_color, reminder_date, received_date, unit, unit_cost,
            sender_code, sender_company, recorder, category, status, position)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        foreach ($products as $product) {
            $status = 'OUT';
            $stmt_details->bind_param(
                "issidsssissssdssssss",
                $out_id,
                $product['product_code'],
                $product['product_name'],
                $product['quantity'],
                $product['unit_price'],
                $product['expiration_date'],
                $product['product_model'],
                $product['production_date'],
                $product['shelf_life'],
                $product['sticker_color'],
                $product['reminder_date'],
                $product['received_date'],
                $product['unit'],
                $product['unit_cost'],
                $product['sender_code'],
                $product['sender_company'],
                $product['recorder'],
                $product['category'],
                $status,
                $product['position']
            );
            if (!$stmt_details->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึก sell_product_details: " . $stmt_details->error);
            }
            $sql_update_product = "UPDATE products SET quantity = quantity - ? WHERE product_code = ?";
            $stmt_update_product = $conn->prepare($sql_update_product);
            $stmt_update_product->bind_param("is", $product['quantity'], $product['product_code']);

            if (!$stmt_update_product->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการอัปเดต quantity ใน products: " . $stmt_update_product->error);
            }

        }

        $conn->commit(); // Commit transaction
        echo "<script>alert('ตัดสต็อกสำเร็จ'); window.location.href = 'waste_stock.php';</script>";
        exit;
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
