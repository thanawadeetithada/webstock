<?php
session_start();
include('config.php');

if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    header("Content-Type: application/json");

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    $sql = "SELECT product_code, product_name, quantity, unit, unit_cost, received_date, expiration_date, sticker_color, category 
            FROM products 
            WHERE expiration_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "SQL error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ss", $startDate, $endDate);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Execution error: " . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid parameters. Please provide startDate and endDate."]);
    exit();
}
?>
