<?php
session_start();
include 'config.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจากฟอร์ม
    $total_price = floatval(str_replace(',', '', $_POST['total_price'] ?? 0));
    $sell_date = $_POST['sell_date'] ?? null;
    $sell_username = $_SESSION['username']; // ดึงค่าจาก session
    $created_at = date('Y-m-d H:i:s'); // เวลาปัจจุบัน

    // ตรวจสอบว่าค่าไม่ว่าง
    if (empty($total_price) || empty($sell_date) || empty($sell_username)) {
        die("ข้อมูลไม่ครบถ้วน");
    }

    // รับข้อมูลสินค้าเป็น JSON และแปลงเป็น Array
    $products_json = $_POST['products'] ?? null;

    if (!$products_json) {
        die("ไม่พบข้อมูลสินค้า");
    }
    
    $products = json_decode($products_json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("การแปลง JSON ผิดพลาด: " . json_last_error_msg());
    }
    
    if (!$products || !is_array($products)) {
        die("ข้อมูลสินค้าผิดพลาด");
    }    

    // เริ่ม Transaction
    $conn->begin_transaction();

    try {
        // Insert ข้อมูลใน table sell_product
        $sql_sell = "INSERT INTO sell_product (total_price, sell_date, sell_username, created_at)
                     VALUES (?, ?, ?, ?)";
        $stmt_sell = $conn->prepare($sql_sell);
        $stmt_sell->bind_param("dsss", $total_price, $sell_date, $sell_username, $created_at);

        if (!$stmt_sell->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึก sell_product: " . $stmt_sell->error);
        }

        // ดึง sell_id ที่เพิ่งถูกสร้าง
        $sell_id = $stmt_sell->insert_id;

        // Insert ข้อมูลใน table sell_product_details
        $sql_details = "INSERT INTO sell_product_details 
                        (sell_id, product_code, product_name, quantity, unit_price, expiration_date, 
                         product_model, production_date, shelf_life, sticker_color, reminder_date, 
                         received_date, unit, unit_cost, sender_code, sender_company, recorder, 
                         category, status, position) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_details = $conn->prepare($sql_details);

        foreach ($products as $detail) {
            $status = 'SELL';
            $stmt_details->bind_param(
                "issidsssissssdssssss",
                $sell_id,
                $detail['product_code'],
                $detail['product_name'],
                $detail['quantity'],
                $detail['unit_price'],
                $detail['expiration_date'],
                $detail['product_model'],
                $detail['production_date'],
                $detail['shelf_life'],
                $detail['sticker_color'],
                $detail['reminder_date'],
                $detail['received_date'],
                $detail['unit'],
                $detail['unit_cost'],
                $detail['sender_code'],
                $detail['sender_company'],
                $detail['recorder'],
                $detail['category'],
                $status,
                $detail['position']
                
            );

            if (!$stmt_details->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการบันทึก sell_product_details: " . $stmt_details->error);
            }
            $sql_update_product = "UPDATE products SET quantity = quantity - ? WHERE product_code = ?";
            $stmt_update_product = $conn->prepare($sql_update_product);
            $stmt_update_product->bind_param("is", $detail['quantity'], $detail['product_code']);
        
            if (!$stmt_update_product->execute()) {
                throw new Exception("เกิดข้อผิดพลาดในการอัปเดต quantity ใน products: " . $stmt_update_product->error);
            }
        }

        $invoice_number = 'INV-' . time();
        $payment_status = 'PAID';
        $customer_name = $_POST['customer_name'] ?? ''; 
        $customer_contact = $_POST['customer_contact'] ?? ''; 

        $sql_invoice = "INSERT INTO invoice (sell_id, invoice_number, total_price, issue_date, payment_status, customer_name, customer_contact)
                        VALUES (?, ?, ?, NOW(), ?, ?, ?)";
        $stmt_invoice = $conn->prepare($sql_invoice);
        $stmt_invoice->bind_param("isdsss", $sell_id, $invoice_number, $total_price, $payment_status, $customer_name, $customer_contact);

        if (!$stmt_invoice->execute()) {
            throw new Exception("เกิดข้อผิดพลาดในการบันทึก invoice: " . $stmt_invoice->error);
        }

        $invoice_id = $stmt_invoice->insert_id;

        $conn->commit();

        $_SESSION['payment_end_time'] = time() + (15 * 60);

        header("Location: receipt_sell.php?invoice_id=" . $invoice_id);
        exit;

    } catch (Exception $e) {
        $conn->rollback();
        echo "เกิดข้อผิดพลาด: " . $e->getMessage();
    }

    $stmt_sell->close();
    $stmt_details->close();
    $stmt_update_product->close();

    // ปิดการเชื่อมต่อ
    $conn->close();
} else {
    // หากไม่ได้เข้าผ่าน POST
    header("Location: sell_products.php");
    exit;
}
?>
