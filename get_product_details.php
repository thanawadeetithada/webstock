<?php
include('config.php');

if (isset($_GET['product_code'])) {
    $productCode = $_GET['product_code'];

    $stmt = $conn->prepare("SELECT expiration_date, position FROM products WHERE product_code = ?");
    $stmt->bind_param("s", $productCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'expiration_date' => $row['expiration_date'],
            'position' => $row['position']
        ]);
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูลสินค้า']);
    }
} else {
    echo json_encode(['error' => 'ไม่มีการส่งค่า product_code']);
}
?>
