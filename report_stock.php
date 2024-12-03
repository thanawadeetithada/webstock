<?php
session_start();
include('include/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// ตัวอย่างข้อมูลสินค้า
$products = [
    ['quantity' => 10, 'name' => 'Product 1', 'unit' => 'ชิ้น', 'price' => 100],
    ['quantity' => 5, 'name' => 'Product 2', 'unit' => 'กล่อง', 'price' => 200],
    ['quantity' => 20, 'name' => 'Product 3', 'unit' => 'ชิ้น', 'price' => 150],
    ['quantity' => 15, 'name' => 'Product 4', 'unit' => 'แพ็ค', 'price' => 250]
];

// กำหนดค่าผู้ทำการขาย (สมมติว่าอยู่ใน session)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';

// ฟังก์ชันสำหรับแยกวันที่และเวลา
$current_date = date('d/m/Y');  // วันที่
$current_time = date('H:i:s');  // เวลา

// คำนวณข้อมูลรวม (จำนวนรายการ, จำนวนชิ้น, ราคา)
$total_items = 0;
$total_quantity = 0;
$total_price = 0;

foreach ($products as $product) {
    $total_items++;
    $total_quantity += $product['quantity'];
    $total_price += $product['quantity'] * $product['price'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานสินค้าที่ตัดออกจากสต็อก</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 80%;
            margin: auto;
            background: #fff;
            padding: 2.5rem;
            border-radius: 8px;
            box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.1);
            margin-top: 6.5rem;
            position: relative;
        }

        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 25px;
            width: 100%;
        }

        .center-section {
            display: flex;
            align-items: center;
            gap: 15px;
            align-items: flex-end;
        }

        .dropdown-container {
            margin: 0;
            display: flex;
            flex-direction: column;
            font-family: Arial, sans-serif;
        }

        .dropdown-container input {
            padding: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
            flex-shrink: 1;
            width: -webkit-fill-available;
        }

        .dropdown-container label {
            font-weight: bold;
            margin-bottom: 5px;
            color: #003d99;
        }

        select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            width: 100%;
            outline: none;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f1f1f1;
        }

        .center-button {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            margin-right: 6.5rem;
        }

        .print-buttons {
            position: absolute;
            top: 20px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .print-buttons button {
            font-size: 14px;
            border-radius: 5px;
            color: black;
            border: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="print-buttons">
            <button><i class="fa-solid fa-print"></i></button>
            <button>พิมพ์รายงาน</button>
        </div>

        <div class="search-container">
            <div class="center-section">
                <div class="dropdown-container">
                    <label for="productCategory">เลือกช่วงวันที่</label>
                    <input type="date">
                </div>
                <label>ถึง</label>
                <div class="dropdown-container">
                    <input type="date">
                </div>

                <div class="dropdown-container">
                    <label for="productCategory">สถานะ</label>
                    <select id="productCategory" name="productCategory">
                        <option value="electronics">กระป๋อง</option>
                        <option value="furniture">กระปุก</option>
                        <option value="clothing">ถุง</option>
                        <option value="books">ลัง</option>
                        <option value="toys">ซอง</option>
                    </select>
                </div>

            </div>
        </div>
        <div class="center-button">
            <button type="button" class="btn btn-primary">ค้นหา</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>หน่วย</th>
                    <th>สีสติ๊กเกอร์</th>
                    <th>ราคา</th>
                    <th>วันหมดอายุ</th>
                    <th>วันตัดสต็อก</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($products as $index => $product) {
                    echo "<tr>";
                    echo "<td>" . $no++ . "</td>";
                    echo "<td>" . $product['quantity'] . "</td>";
                    echo "<td>" . $product['name'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "<td>" . $product['unit'] . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>

    </script>

</body>

</html>
