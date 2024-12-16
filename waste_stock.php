<?php
session_start();
if (!isset($_POST['search'])) {
    include 'include/header.php';
}
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';
$total_items = 0;
$total_quantity = 0;
$total_price = 0;
$current_date = date('d/m/Y');
$current_time = date('H:i:s');

if (isset($_POST['search'])) {
    $search_code = $_POST['search'];
    $query = $conn->prepare("SELECT * FROM products WHERE product_code = ?");
    $query->bind_param("s", $search_code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'product' => $product
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลสินค้า'
        ]);
    }
    exit;
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
        top: 22vh;
        padding-bottom: 2px;
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
            <input type="text" id="search-box" placeholder="ค้นหาสินค้า...">
            <i class="fa-solid fa-magnifying-glass"></i>
            <div class="search-info">
                <span class="label">วันที่</span> <?=$current_date?>
                <span class="label">เวลา</span> <?=$current_time?>
                <span class="label">ผู้ทำการขาย</span> <?=$username?>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>ลำดับ</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>ราคา/หน่วย</th>
                    <th>รหัสสินค้า</th>
                    <th>วันหมดอายุสินค้า</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan='7'>ไม่พบข้อมูลสินค้า</td>
                </tr>
            </tbody>
        </table>
    </div>
    <script>
    document.getElementById('search-box').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchValue = this.value.trim();

            if (searchValue) {
                fetch('', { // ส่งไปที่ไฟล์เดียวกัน
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'search=' + encodeURIComponent(searchValue)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const product = data.product;
                        addProductToTable(product);
                    } else {
                        alert(data.message);
                    }
                    document.getElementById('search-box').value = ''; // Clear the input
                })
                .catch(error => console.error('Error:', error));
            }
        }
    });

    // ฟังก์ชันเพิ่มสินค้าไปยังตาราง
    function addProductToTable(product) {
        const tbody = document.querySelector('table tbody');
        
        // ลบแถว "ไม่พบข้อมูลสินค้า" ถ้ามีอยู่
        const noDataRow = tbody.querySelector('tr td[colspan="7"]');
        if (noDataRow) {
            noDataRow.parentElement.remove();
        }

        // เพิ่มแถวใหม่
        const newRow = document.createElement('tr');
        newRow.innerHTML = `
            <td><input type="checkbox"></td>
            <td>${tbody.rows.length + 1}</td>
            <td>${product.product_name}</td>
            <td>${product.quantity}</td>
            <td>${product.unit_price}</td>
            <td>${product.product_code}</td>
            <td>${product.expiry_date}</td>
        `;

        tbody.appendChild(newRow);
    }
    </script>
</body>

</html>