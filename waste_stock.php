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
    <title>ตัดของเสียจากสต็อก</title>
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

    .search-info span.label {
            font-weight: bold;
            padding: 15px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <input type="text" placeholder="ค้นหาสินค้า...">
            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <div class="search-info">
                <span class="label">วันที่</span> <?= $current_date ?>
                <span class="label">เวลา</span> <?= $current_time ?>
                <span class="label">ผู้ทำการขาย</span> <?= $username ?>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                <th><input type="checkbox" id="select-all"></th>
                    <th>No.</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคา/หน่วย</th>
                    <th>รหัสสินค้า</th>
                    <th>วันหมดอายุสินค้า</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            foreach ($products as $index => $product) {
                echo "<tr>";
                echo "<td><input type='checkbox' class='select-item' data-index='$index'></td>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . $product['name'] . "</td>";
                echo "<td>" . $product['unit'] . "</td>";
                echo "<td>" . number_format($product['price'], 2) . " บาท</td>";
                echo "<td>" . $product['quantity'] . "</td>";
                echo "<td>" . $product['name'] . "</td>";
                echo "</tr>";
            }
            ?>
            </tbody>
            <tfoot>
                <tr class="footer-row">
                    <td colspan="1"></td>
                    <td colspan="2">รวม <?= $total_items ?> รายการ <?= number_format($total_quantity) ?> ชิ้น</td>
                    <td colspan="2">รวม <?= number_format($total_price, 2) ?> บาท</td>
                    <td colspan="2">
                        <button class="btn btn-danger" id="delete-selected">ลบรายการ</button>
                        <button class="btn btn-danger" id="delete-selected">ตัดสต็อก</button>
                    </td>
                </tr>
            </tfoot>
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