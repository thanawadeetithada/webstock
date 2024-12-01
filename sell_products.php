<?php
session_start();
include('include/header.php');

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
    <title>ตารางสินค้า</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        table {
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            text-align: center;
            padding: 10px;
        }
        th {
            background-color: #f8f9fa;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .search-container {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            background-color: #C1BBBD;
        }
        .search-container input {
            width: 40%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin: 10px;
        }
        .search-info {
            font-size: 14px;
            color: #333;
            text-align: right;
            width: 55%;
        }
        .search-info span {
            display: inline-block;
            margin-right: 15px;
        }
        .footer-row td {
            display: flex;
            
            font-weight: bold;
        }
        .footer-row .total-price {
            color: green;
        }
        .footer-row button {
            margin-left: 10px;
        }
    </style>
</head>

<body>

<div class="container">
    <h2 class="text-center my-4">ตารางแสดงสินค้า</h2>
    
    <div class="search-container">
        <input type="text" placeholder="ค้นหาสินค้า..." id="search-box">
        
        <div class="search-info">
            <span>วันที่: <?= $current_date ?> </span>
            <span>เวลา: <?= $current_time ?> </span>
            <span>ผู้ทำการขาย: <?= $username ?></span>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th><input type="checkbox" id="select-all"></th>
                <th>No.</th>
                <th>จำนวน</th>
                <th>ชื่อสินค้า</th>
                <th>หน่วย</th>
                <th>ราคา</th>
            </tr>
        </thead>
        <tbody id="product-table-body">
            <?php
            $no = 1;
            foreach ($products as $index => $product) {
                echo "<tr>";
                echo "<td><input type='checkbox' class='select-item' data-index='$index'></td>";
                echo "<td>" . $no++ . "</td>";
                echo "<td>" . $product['quantity'] . "</td>";
                echo "<td>" . $product['name'] . "</td>";
                echo "<td>" . $product['unit'] . "</td>";
                echo "<td>" . number_format($product['price'], 2) . " บาท</td>";
                echo "</tr>";
            }
            ?>
        </tbody>

        <!-- แถวรวมล่างสุด -->
        <tfoot>
            <tr class="footer-row">
                <td colspan="1"></td>
                <td colspan="2">รวม <?= $total_items ?> รายการ <?= number_format($total_quantity) ?> ชิ้น</td>
                <td colspan="2">รวม <?= number_format($total_price, 2) ?> บาท</td>
                <td colspan="1">
                    <button class="btn btn-danger" id="delete-selected">ลบรายการ</button>
                    <button class="btn btn-success">ชำระเงิน</button>
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
