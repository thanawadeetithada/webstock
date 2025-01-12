<?php
session_start();
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require_once 'config.php';

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    echo json_encode(['notification_count' => 0]);
    exit;
}

// ดึงจำนวนการแจ้งเตือน (ตัวอย่าง: ดึงจากตาราง notifications)
$sql = "SELECT COUNT(*) AS count FROM notifications WHERE user_id = ? AND status = 'unread'";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $_SESSION['user_id']); // แก้ไขตามโครงสร้างของคุณ
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

echo json_encode(['notification_count' => $data['count']]);
?>
