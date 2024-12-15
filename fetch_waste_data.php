<?php
session_start();
include 'config.php';

$year = isset($_GET['year']) ? intval($_GET['year']) : '';
$month = isset($_GET['month']) ? intval($_GET['month']) : '';

$sql_category = "SELECT category,
                        SUM(CASE WHEN status = 'sell' THEN 1 ELSE 0 END) AS sell_count,
                        SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) AS expired_count
                 FROM products
                 WHERE status IN ('sell', 'expired')";

if (!empty($year)) {
    $sql_category .= " AND YEAR(expiry_date) = $year";
}

if (!empty($month)) {
    $sql_category .= " AND MONTH(expiry_date) = $month";
}

$sql_category .= " GROUP BY category";

$result_category = $conn->query($sql_category);

$categories = [];
$sell_counts = [];
$expired_counts = [];

if ($result_category && $result_category->num_rows > 0) {
    while ($row = $result_category->fetch_assoc()) {
        $categories[] = $row['category'];
        $sell_counts[] = $row['sell_count'];
        $expired_counts[] = $row['expired_count'];
    }
}

header('Content-Type: application/json');
echo json_encode([
    'categories' => $categories,
    'sell_counts' => $sell_counts,
    'expired_counts' => $expired_counts,
]);
?>
