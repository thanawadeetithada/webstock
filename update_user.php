<?php
require 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    echo json_encode(["success" => false, "message" => "ไม่ได้เข้าสู่ระบบ"]);
    exit();
}

$username = $_SESSION['username']; // ผู้ใช้ที่ล็อกอินอยู่
$new_username = trim($_POST['username']);
$new_email = trim($_POST['email']);
$new_password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
$new_telegram = trim($_POST['telegram_chat_id']);

// ตรวจสอบว่าชื่อผู้ใช้หรืออีเมลซ้ำหรือไม่
$check_query = "SELECT username, email FROM users WHERE (username = ? OR email = ?) AND username != ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("sss", $new_username, $new_email, $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "ชื่อผู้ใช้หรืออีเมลซ้ำ"]);
    exit();
}

// อัปเดตข้อมูลในฐานข้อมูล
$update_query = "UPDATE users SET username = ?, email = ?, telegram_chat_id = ?" . ($new_password ? ", password = ?" : "") . " WHERE username = ?";
$stmt = $conn->prepare($update_query);

if ($new_password) {
    $stmt->bind_param("sssss", $new_username, $new_email, $new_telegram, $new_password, $username);
} else {
    $stmt->bind_param("ssss", $new_username, $new_email, $new_telegram, $username);
}

if ($stmt->execute()) {
    $_SESSION['username'] = $new_username; // อัปเดต session
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "เกิดข้อผิดพลาด"]);
}

$stmt->close();
$conn->close();
?>
