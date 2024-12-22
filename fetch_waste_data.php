<?php
include 'config.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : null;
$month = isset($_GET['month']) ? intval($_GET['month']) : null;

// เงื่อนไขการกรองตาม year และ month
$whereClauses = [];
if ($year) {
    $whereClauses[] = "YEAR(expiration_date) = $year";
}

if ($month) {
    $whereClauses[] = "MONTH(expiration_date) = $month";
}

$whereSQL = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';

// Query สำหรับดึงข้อมูลใหม่
$expiration_data_sql = "
    SELECT category, COUNT(*) AS total
    FROM products
    $whereSQL
    GROUP BY category
";

$result = $conn->query($expiration_data_sql);

$expiration_data = [];
while ($row = $result->fetch_assoc()) {
    $expiration_data[$row['category']] = $row['total'];
}

// ส่งข้อมูล JSON กลับไปยัง JavaScript
echo json_encode([
    'categories' => array_keys($expiration_data),
    'sell_totals' => array_values($expiration_data), // ตัวอย่างสำหรับกราฟ
    'expiration_totals' => array_values($expiration_data),
]);
