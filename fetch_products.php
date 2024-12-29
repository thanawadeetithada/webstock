<?php
include 'config.php'; // ไฟล์สำหรับเชื่อมต่อฐานข้อมูล

if (isset($_GET['query'])) {
    $query = $_GET['query'];
    $sql = "SELECT product_code, product_name, product_model, production_date, shelf_life, expiration_date, sticker_color, reminder_date, received_date, quantity, unit, unit_cost, sender_code, sender_company, recorder, unit_price, status ,category, position FROM products WHERE product_code LIKE ? OR product_name LIKE ?";

    $stmt = $conn->prepare($sql);
    $searchTerm = "%$query%";
    $stmt->bind_param("ss", $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode($products); // ส่งข้อมูลกลับเป็น JSON
    exit;
}
?>