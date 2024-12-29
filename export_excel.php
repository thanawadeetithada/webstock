<?php
require 'vendor/autoload.php'; // ใช้ PhpSpreadsheet

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include 'config.php';

// Query ดึงข้อมูล
$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, received_date, expiration_date AS stock_date, sticker_color, category, status, position
        FROM products
        WHERE expiration_date < CURDATE()
        UNION ALL
        SELECT product_code, product_name, quantity, unit, unit_cost, received_date, out_date AS stock_date, sticker_color, category, status, position
        FROM out_product_details
        UNION ALL
        SELECT product_code, product_name, quantity, unit, unit_cost, received_date, sell_date AS stock_date, sticker_color, category, status, position
        FROM sell_product_details";

$result = $conn->query($sql);

// สร้าง Spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// หัวตาราง
$header = ['รหัสสินค้า', 'ชื่อสินค้า', 'จำนวน', 'หน่วย', 'ราคา', 'วันที่ตัดสต็อก', 'สีสติ๊กเกอร์', 'หมวดหมู่gg', 'หมวดหมู่','สถานะ', 'ตำแหน่ง'];
$sheet->fromArray($header, NULL, 'A1');

// เพิ่มข้อมูล
if ($result && $result->num_rows > 0) {
    $rowIndex = 2;
    while ($row = $result->fetch_assoc()) {
        $sheet->fromArray(array_values($row), NULL, "A{$rowIndex}");
        $rowIndex++;
    }
}

// ตั้งชื่อไฟล์
$fileName = "product_report_" . date('Ymd_His') . ".xlsx";

// กำหนด Header เพื่อดาวน์โหลด
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment; filename={$fileName}");
header('Cache-Control: max-age=0');

// เขียนไฟล์ Excel
$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
