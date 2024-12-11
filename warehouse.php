<?php
session_start();
include('include/header.php');
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';

$total_items = 0;
$total_quantity = 0;
$total_price = 0;

// ดึงข้อมูลหมวดหมู่สินค้าจากฐานข้อมูล
$sql_category = "SELECT DISTINCT category FROM products";
$result_category = $conn->query($sql_category);

$sql_unit = "SELECT DISTINCT unit FROM products";
$result_unit = $conn->query($sql_unit);

$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiry_date, sticker_color, category 
        FROM products WHERE status = 'active'";
$result = $conn->query($sql);
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
            <input type="text" id="search-box" placeholder="ค้นหาชื่อสินค้า/รหัสสินค้า">
            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <div class="right-section">
                <div class="dropdown-container">
                    <label for="productCategory">หมวดหมู่สินค้า</label>
                    <select id="productCategory" name="productCategory">
                        <option value="">ทั้งหมด</option>
                        <?php
                        if ($result_category->num_rows > 0) {
                            while ($row = $result_category->fetch_assoc()) {
                                echo "<option value='" . $row['category'] . "'>" . $row['category'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>ไม่มีหมวดหมู่</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="dropdown-container">
                    <label for="unit">หน่วย</label>
                    <select id="unit" name="unit">
                        <option value="">ทั้งหมด</option>
                        <?php
                        if ($result_unit->num_rows > 0) {
                            while ($row = $result_unit->fetch_assoc()) {
                                echo "<option value='" . $row['unit'] . "'>" . $row['unit'] . "</option>";
                            }
                        } else {
                            echo "<option value=''>ไม่มีหน่วย</option>";
                        }
                        ?>
                    </select>
                </div>

                <button type="button" class="btn btn-outline-danger" id="resetButton">All
                    ดูสินค้าทั้งหมดในสต็อกทิ้งหมด</button>
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
            <tbody id="product-table-body">
                <?php
                $no = 1;
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo "<tr data-product-code='" . $row['product_code'] . "'>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['product_code'] .  "</td>";
                        echo "<td>" . $row['product_name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['unit'] . "</td>";
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
    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('search-box').value = '';
        document.getElementById('productCategory').value = '';
        document.getElementById('unit').value = '';
        const searchQuery = '';
        const categoryFilter = '';
        const unitFilter = '';

        const rows = Array.from(document.querySelectorAll('#product-table-body tr'));

        const filteredRows = rows.filter(row => {
            const productCode = row.cells[1].textContent.toLowerCase();
            const productName = row.cells[2].textContent.toLowerCase();
            const productCategory = row.cells[7].textContent.toLowerCase();
            const unit = row.cells[4].textContent.toLowerCase();

            return (productCode.includes(searchQuery) || productName.includes(searchQuery)) &&
                (categoryFilter === '' || productCategory.includes(categoryFilter)) &&
                (unitFilter === '' || unit.includes(unitFilter));
        });

        const sortedRows = filteredRows.sort((a, b) => {
            const codeA = a.cells[1].textContent.toLowerCase();
            const codeB = b.cells[1].textContent.toLowerCase();
            return codeA.localeCompare(codeB);
        });

        const tableBody = document.getElementById('product-table-body');
        tableBody.innerHTML = '';

        sortedRows.forEach(row => tableBody.appendChild(row));
    });
    </script>

</body>

</html>