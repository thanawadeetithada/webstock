<?php
session_start();
include 'config.php';

$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'check_duplicate' && isset($_GET['product_code'])) {
    header('Content-Type: application/json');
    $product_code = $_GET['product_code'];

    $check_sql = "SELECT product_code FROM products WHERE product_code = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $product_code);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt_check->close();
    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $product_model = $_POST['product_model'];
    $production_date = $_POST['production_date'];
    $shelf_life = $_POST['shelf_life'];
    $expiration_date = $_POST['expiration_date'];
    $sticker_color = $_POST['sticker_color'];
    $reminder_date = $_POST['reminder_date'];
    $received_date = $_POST['received_date'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $unit_cost = $_POST['unit_cost'];
    $sender_code = $_POST['sender_code'];
    $sender_company = $_POST['sender_company'];
    $recorder = $_POST['recorder'];
    $unit_price = $_POST['unit_price'];
    $category = $_POST['category'];

    $check_sql = "SELECT * FROM products WHERE product_code = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $product_code);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $sql = "UPDATE products SET
                    product_name = ?, product_model = ?, production_date = ?, shelf_life = ?,
                    expiration_date = ?, sticker_color = ?, reminder_date = ?, received_date = ?,
                    quantity = ?, unit = ?, unit_cost = ?, sender_code = ?, sender_company = ?,
                    recorder = ?, unit_price = ?, category = ?
                WHERE product_code = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "sssisssisdsssdsss",
            $product_name, $product_model, $production_date, $shelf_life, $expiration_date,
            $sticker_color, $reminder_date, $received_date, $quantity, $unit, $unit_cost,
            $sender_code, $sender_company, $recorder, $unit_price, $category, $product_code
        );

        if ($stmt->execute()) {
            echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {

        $sql = "INSERT INTO products (
    product_code, product_name, product_model, production_date, shelf_life,
    expiration_date, sticker_color, reminder_date, received_date, quantity,
    unit, unit_cost, sender_code, sender_company, recorder, unit_price, category
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssissssisdsssds",
            $product_code, $product_name, $product_model, $production_date, $shelf_life,
            $expiration_date, $sticker_color, $reminder_date, $received_date, $quantity,
            $unit, $unit_cost, $sender_code, $sender_company, $recorder, $unit_price, $category
        );

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ');</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    $stmt_check->close();
    $conn->close();
}
