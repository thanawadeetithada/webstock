<?php
// ตรวจสอบว่า session ยังไม่ได้เริ่มต้นให้เริ่มต้น session
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // เริ่ม sessio
}

// กำหนดค่าการเชื่อมต่อฐานข้อมูล
$servername = "localhost"; // หรือ IP ของเซิร์ฟเวอร์ฐานข้อมูล
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "webstock"; // ชื่อฐานข้อมูลของคุณ
// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error); // ถ้าเชื่อมต่อไม่ได้ให้แสดงข้อผิดพลาด
}

// กำหนด character set เป็น utf8 เพื่อรองรับภาษาไทยและตัวอักษรพิเศษ
$conn->set_charset("utf8");

// เปิดการแสดงข้อผิดพลาดสำหรับการดีบัก (development purposes only)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
