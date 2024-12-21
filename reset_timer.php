<?php
session_start();

// รีเซ็ตเวลาใหม่ (15 นาทีจากเวลาปัจจุบัน)
$_SESSION['payment_end_time'] = time() + (15 * 60);

// ส่ง JSON กลับไปยืนยันว่ารีเซ็ตเสร็จสมบูรณ์
echo json_encode(['success' => true]);
exit;
?>
