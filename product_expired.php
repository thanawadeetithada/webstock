<?php
session_start();
include('include/header.php');
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    include('config.php');

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    $sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiry_date, sticker_color, category 
            FROM products 
            WHERE expiry_date BETWEEN ? AND ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["error" => "SQL error: " . $conn->error]);
        exit();
    }

    $stmt->bind_param("ss", $startDate, $endDate);
    if (!$stmt->execute()) {
        http_response_code(500);
        echo json_encode(["error" => "Execution error: " . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';

$total_items = 0;
$total_quantity = 0;
$total_price = 0;


$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiry_date, sticker_color, category FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาสินค้าหมดอายุ</title>
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
                    <input type="date" id="startDate">
                </div>
                <label>ถึง</label>
                <div class="dropdown-container">
                    <input type="date" id="endDate">
                </div>
            </div>
        </div>
        <div class="center-button">
            <button type="button" class="btn btn-primary" id="searchBtn">ค้นหา</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>ลำดับ</th>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>หน่วย</th>
                    <th>ราคา</th>
                    <th>สีสติ๊กเกอร์</th>
                    <th>วันหมดอายุ</th>
                    <th>หมวดหมู่สินค้า</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr data-product-code='" . $row['product_code'] . "'>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['product_code'] . "</td>";
                        echo "<td>" . $row['product_name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['unit'] . "</td>";
                        echo "<td>" . $row['unit_cost'] . "</td>";
                        echo "<td>" . $row['sticker_color'] . "</td>";
                        echo "<td>" . $row['expiry_date'] . "</td>";
                        echo "<td>" . $row['category'] . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>ไม่พบข้อมูลสินค้า</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
    document.getElementById("searchBtn").addEventListener("click", function() {
        const startDate = new Date(document.getElementById("startDate").value);
        const endDate = new Date(document.getElementById("endDate").value);

        if (!startDate || !endDate) {
            alert("กรุณาเลือกช่วงวันที่ให้ครบถ้วน");
            return;
        }
        const rows = Array.from(document.querySelectorAll("#product-table-body tr"));
        const filteredRows = rows.filter((row) => {
            const expiryDate = new Date(row.cells[7].textContent.trim()); // คอลัมน์ "วันหมดอายุ"
            return expiryDate >= startDate && expiryDate <= endDate;
        });
        filteredRows.sort((a, b) => {
            const dateA = new Date(a.cells[7].textContent.trim());
            const dateB = new Date(b.cells[7].textContent.trim());
            return dateA - dateB;
        });
        const tableBody = document.getElementById("product-table-body");
        tableBody.innerHTML = "";

        if (filteredRows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='9'>ไม่พบข้อมูลในช่วงวันที่ที่เลือก</td></tr>";
        } else {
            filteredRows.forEach((row) => {
                tableBody.appendChild(row);
            });
        }
    });
    </script>

</body>

</html>
