<?php
include 'config.php';

// อ่านข้อมูลจากคำขอ
$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['product_code'])) {
    $product_code = $conn->real_escape_string($data['product_code']);

    // ลบข้อมูล
    $query = "DELETE FROM products WHERE product_code = '$product_code'";
    if ($conn->query($query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete product']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
