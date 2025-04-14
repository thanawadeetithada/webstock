<?php
require 'config.php'; // ไฟล์เชื่อมต่อฐานข้อมูล
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(32)); // สร้าง token
        $expiry = date("Y-m-d H:i:s", strtotime("+1 hour")); // หมดอายุใน 1 ชั่วโมง

        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // ตั้งค่าอีเมล
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'mitinventor015@gmail.com';
            $mail->Password = 'ukebjwmfzmwuipjw'; // ใช้ App Password แทน
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('mitinventor015@gmail.com', 'WEB STOCK');
            $mail->addAddress($email);
            $mail->CharSet = 'UTF-8';

            $resetLink = "https://web-stock.ct.ws/reset_password.php?token=$token";

            $mail->isHTML(true);
            $mail->Subject = 'รีเซ็ตรหัสผ่าน';
            $mail->Body = "<p>คุณสามารถรีเซ็ตรหัสผ่านได้โดยคลิกที่ลิงก์นี้: <a href='$resetLink'>$resetLink</a></p>";

            $mail->send();
            echo "<script>alert('ลิงก์รีเซ็ตรหัสผ่านถูกส่งไปยังอีเมลของคุณแล้ว'); window.location.href = 'login.php';</script>";
        } catch (Exception $e) {
            echo "<script>alert('ไม่สามารถส่งอีเมลได้: {$mail->ErrorInfo}');</script>";
        }
    } else {
        echo "<script>alert('ไม่พบอีเมลนี้ในระบบ');</script>";
    }
}
?>
