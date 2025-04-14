<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // ✅ เปิดโหมด Debug (แสดงข้อผิดพลาด)
    $mail->SMTPDebug = 2;  // 0 = ปิด, 2 = แสดงผลเต็มที่
    $mail->Debugoutput = 'html';

    // ✅ ตั้งค่า Gmail SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mitinventor015@gmail.com';  // 🔹 เปลี่ยนเป็น Gmail ของคุณ
    $mail->Password = 'etptordrjdzhhsas';    // 🔹 ใช้ App Password (ห้ามใช้รหัสผ่านปกติ)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // ✅ ตั้งค่าผู้ส่งและผู้รับ
    $mail->setFrom('mitinventor015@gmail.com', 'Test Email');
    $mail->addAddress('mitinventor015@gmail.com'); // 🔹 เปลี่ยนเป็นอีเมลผู้รับที่ต้องการ

    // ✅ ตั้งค่าเนื้อหาอีเมล
    $mail->isHTML(true);
    $mail->Subject = '🔥 ทดสอบส่งอีเมลจาก PHP';
    $mail->Body    = '<h1>✅ อีเมลนี้ถูกส่งจากโค้ด PHP ผ่าน Gmail SMTP สำเร็จแล้ว!</h1>';

    // ✅ ลองส่งอีเมล
    if ($mail->send()) {
        echo '✅ อีเมลถูกส่งสำเร็จ!';
    } else {
        echo '❌ ไม่สามารถส่งอีเมลได้: ' . $mail->ErrorInfo;
    }

} catch (Exception $e) {
    echo "❌ PHPMailer Error: {$mail->ErrorInfo}";
}
?>
