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

$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiration_date, sticker_color, category , position
        FROM products WHERE expiration_date >= CURRENT_DATE";

$result = $conn->query($sql);

$sticker_styles = [
    'หมดอายุเดือน 1' => 'background-color: #FD3535; color: #000;',
    'หมดอายุเดือน 2' => 'background-color: #FFFF8A; color: #000;',
    'หมดอายุเดือน 3' => 'background-color: #99EBFF; color: #000;',
    'หมดอายุเดือน 4' => 'background-color: #05A854; color: #000;',
    'หมดอายุเดือน 5' => 'background-color: #FD8849; color: #000;',
    'หมดอายุเดือน 6' => 'background-color: #FE3998; color: #000;',
    'หมดอายุเดือน 7' => 'background-color: #0BE0D2; color: #000;',
    'หมดอายุเดือน 8' => 'background-color: #E6B751; color: #000;',
    'หมดอายุเดือน 9' => 'background-color: #FDC4EB; color: #000;',
    'หมดอายุเดือน 10' => 'background-color: #B9F4A2; color: #000;',
    'หมดอายุเดือน 11' => 'background-color: #CC99FF; color: #000;',
    'หมดอายุเดือน 12' => 'background-color: #999999; color: #000;',
    'ไม่มีวันหมดอายุ' => 'background-color: #FFFFFF; color: #000;',
];
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
        padding: 0px 20px 10px 20px;
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

    ::-webkit-scrollbar {
        display: none;
    }

    .search-btn {
        position: relative;
        width: 40%;
    }

    .search-btn input {
        width: 100%;
        padding: 10px 35px 10px 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        outline: none;
    }

    .search-btn i {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        color: gray;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <div class="search-btn">
                <input type="text" id="search-box" placeholder="ค้นหาชื่อสินค้า/รหัสสินค้า...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
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
                    ดูสินค้าในสต็อกทั้งหมด</button>
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
                        $sticker_color = $row['sticker_color'];
                $sticker_text = $sticker_color ?: 'ไม่มีวันหมดอายุ';
                $sticker_style = $sticker_styles[$sticker_color] ?? "background-color: #FFFFFF; color: #000;";
                
                        echo "<tr data-product-code='" . $row['product_code'] . "'>";
                        echo "<td>" . $no++ . "</td>";
                        echo "<td>" . $row['product_code'] .  "</td>";
                        echo "<td>" . $row['product_name'] . "</td>";
                        echo "<td>" . $row['quantity'] . "</td>";
                        echo "<td>" . $row['unit'] . "</td>";
                        echo "<td style='$sticker_style'>$sticker_text</td>";
                        echo "<td>" . $row['expiration_date'] . "</td>";
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
    const originalRows = Array.from(document.querySelectorAll("#product-table-body tr"));

    document.getElementById('resetButton').addEventListener('click', function() {
        document.getElementById('search-box').value = '';
        document.getElementById('productCategory').value = '';
        document.getElementById('unit').value = '';
        renderSortedRows(originalRows);
    });

    document.getElementById("productCategory").addEventListener("change", filterAndSortRows);
    document.getElementById("unit").addEventListener("change", filterAndSortRows);
    document.getElementById("search-box").addEventListener("input", filterAndSortRows);

    function filterAndSortRows() {
        const selectedCategory = document.getElementById("productCategory").value;
        const selectedUnit = document.getElementById("unit").value;
        const searchQuery = document.getElementById("search-box").value.trim().toLowerCase();

        const filteredRows = originalRows.filter((row) => {
            const categoryCell = row.cells[7];
            const unitCell = row.cells[4];
            const productNameCell = row.cells[2];
            const productCodeCell = row.cells[1];
            const matchesCategory = selectedCategory === "" || (categoryCell && categoryCell.textContent
                .trim() === selectedCategory);
            const matchesUnit = selectedUnit === "" || (unitCell && unitCell.textContent.trim() ===
                selectedUnit);
            const matchesSearch = searchQuery === "" ||
                (productNameCell && productNameCell.textContent.trim().toLowerCase().includes(searchQuery)) ||
                (productCodeCell && productCodeCell.textContent.trim().toLowerCase().includes(
                    searchQuery));

            return matchesCategory && matchesUnit && matchesSearch;
        });

        renderSortedRows(filteredRows);
    }

    function renderSortedRows(rows) {
        rows.sort((a, b) => {
            const indexA = parseInt(a.cells[0].textContent.trim());
            const indexB = parseInt(b.cells[0].textContent.trim());
            return indexA - indexB;
        });

        const tableBody = document.getElementById("product-table-body");
        tableBody.innerHTML = "";

        if (rows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='8'>ไม่พบข้อมูลสินค้า</td></tr>";
        } else {
            rows.forEach((row) => {
                tableBody.appendChild(row);
            });
        }
    }
    </script>
</body>

</html>