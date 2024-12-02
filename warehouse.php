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
    <title>คลังสินค้า</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
    }

    .search-container {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 30px;
        width: 100%;
    }

    .search-container input {
        padding: 8px 30px 8px 10px;
        width: 35%;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
    }

    .search-container i {
        position: absolute;
        left: 37%;
        transform: translateY(-160%);
        font-size: 18px;
        color: #aaa;
    }

    .right-section {
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

    .right-section button {
        padding-top: 8px;
        padding-bottom: 7px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <input type="text" placeholder="ค้นหาชื่อสินค้า/รหัสสินค้า">
            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <div class="right-section">
                <div class="dropdown-container">
                    <label for="productCategory">หมวดหมู่สินค้า</label>
                    <select id="productCategory" name="productCategory">
                        <option value="electronics">อิเล็กทรอนิกส์</option>
                        <option value="furniture">เฟอร์นิเจอร์</option>
                        <option value="clothing">เสื้อผ้า</option>
                        <option value="books">หนังสือ</option>
                        <option value="toys">ของเล่น</option>
                    </select>
                </div>

                <div class="dropdown-container">
                    <label for="productCategory">หน่วย</label>
                    <select id="productCategory" name="productCategory">
                        <option value="electronics">กระป๋อง</option>
                        <option value="furniture">กระปุก</option>
                        <option value="clothing">ถุง</option>
                        <option value="books">ลัง</option>
                        <option value="toys">ซอง</option>
                    </select>
                </div>

                <button type="button" class="btn btn-outline-danger">All ดูสินค้าทั้งหมดในสต็อกทิ้งหมด</button>
            </div>
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
                    <th>วันหมดอายุ</th>
                    <th>หมวดหมู่สินค้า</th>
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
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>

    <script>
    // เลือกทั้งหมด
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.select-item');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // ลบรายการที่เลือก
    document.getElementById('delete-selected').addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.select-item:checked');
        const rowsToDelete = [];

        selectedCheckboxes.forEach(checkbox => {
            const row = checkbox.closest('tr');
            rowsToDelete.push(row);
        });

        // ลบแถวที่เลือก
        rowsToDelete.forEach(row => {
            row.remove();
        });
    });

    // การค้นหา
    document.getElementById('search-box').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const rows = document.querySelectorAll('#product-table-body tr');

        rows.forEach(row => {
            const productName = row.cells[3].textContent.toLowerCase();
            if (productName.includes(searchQuery)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
    </script>

</body>

</html>