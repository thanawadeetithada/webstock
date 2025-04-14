<?php
session_start();
include('include/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เมนู</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Arial', sans-serif;
    background-color: white;
    color: #333;
}

.container {
    width: 80%;
    margin: 0 auto;
    text-align: center;
}

main {
    padding: 40px 0;
    display: flex;
    align-items: center;
    height: 92vh;
}

.button-container {
    display: flex;
    flex-direction: column;
    gap: 10px;
    max-width: 800px;
    margin: 0 auto;
}

.button-container .btn {
    padding: 15px 30px;
    font-size: 1.2rem;
    background-color: #33FFFF;
    color: black;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: 1px solid #070707;
}

.button-container .btn:hover {
    transform: scale(1.05);
    box-shadow: none;
}
</style>

<body>

    <main>
        <div class="container">
            <div class="button-container">
                <button class="btn" onclick="window.location.href='sell_products.php';">ขายสินค้า</button>
                <button class="btn" onclick="window.location.href='record_products.php';">บันทึกข้อมูลสินค้า</button>
                <button class="btn" onclick="window.location.href='warehouse.php';">คลังสินค้า</button>
                <button class="btn" onclick="window.location.href='product_expired.php';">ค้นหาสินค้าหมดอายุ</button>
                <button class="btn" onclick="window.location.href='waste_stock.php';">ตัดของเสียจากสต็อก</button>
                <button class="btn" onclick="window.location.href='report_stock.php';">รายงานสินค้าที่ตัดออกจากสต็อก</button>
                <button class="btn" onclick="window.location.href='waste_chart.php';">สถิติของเสีย</button>
                <button class="btn" onclick="window.location.href='generate_QR.php';">สร้างบาร์โค้ด</button>
            </div>
        </div>
    </main>

</body>

</html>