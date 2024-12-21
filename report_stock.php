<?php
session_start();
include 'include/header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if (isset($_GET['startDate']) && isset($_GET['endDate'])) {
    include 'config.php';

    $startDate = $_GET['startDate'];
    $endDate = $_GET['endDate'];

    $sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiry_date, sticker_color, out_stock_date
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

$sql_status = "SELECT DISTINCT status FROM products";
$result_status = $conn->query($sql_status);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';

$total_items = 0;
$total_quantity = 0;
$total_price = 0;

$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, expiry_date, sticker_color, out_stock_date , status
FROM products
WHERE status IN ('out', 'sell')";
$result = $conn->query($sql);

$sticker_styles = [
    'หมดอายุเดือน 1' => 'background-color: #E3D200; color: #000;',
    'หมดอายุเดือน 2' => 'background-color: #00C4B4; color: #fff;',
    'หมดอายุเดือน 3' => 'background-color: #EE700E; color: #fff;',
    'หมดอายุเดือน 4' => 'background-color: #EA12B1; color: #fff;',
    'หมดอายุเดือน 5' => 'background-color: #C8E9F0; color: #000;',
    'หมดอายุเดือน 6' => 'background-color: #02A737; color: #fff;',
    'หมดอายุเดือน 7' => 'background-color: #EAEAA2; color: #000;',
    'หมดอายุเดือน 8' => 'background-color: #00A1CD; color: #fff;',
    'หมดอายุเดือน 9' => 'background-color: #AA7964; color: #fff;',
    'หมดอายุเดือน 10' => 'background-color: #F4D3DC; color: #000;',
    'หมดอายุเดือน 11' => 'background-color: #B9F4A2; color: #000;',
    'หมดอายุเดือน 12' => 'background-color: #FFFFFF; color: #000; border: 1px solid #ccc;',
];
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
    @media print {
        body * {
            visibility: hidden;
        }

        #printHeader,
        table,
        table * {
            visibility: visible;
        }

        #printHeader {
            position: absolute;
            top: 0;
            left: 0;
            text-align: center;
            width: 100%;
            margin-bottom: 20px;
        }

        table {
            position: absolute;
            top: 100px;
            left: 0;
            width: 100%;
        }

        .print-buttons {
            display: none;
        }
    }

    #printHeader {
        display: none;
    }

    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding:  0px 20px 10px 20px;
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
        background-color: white;
    }

    button:disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    ::-webkit-scrollbar {
        display: none;
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

                <div class="dropdown-container">
                    <label for="productCategory">สถานะ</label>
                    <select id="productCategory" name="productCategory">
                        <option value="">ทั้งหมด</option>
                        <?php
if ($result_status->num_rows > 0) {
    while ($row = $result_status->fetch_assoc()) {
        echo "<option value='" . $row['status'] . "'>" . $row['status'] . "</option>";
    }
}
?>
                    </select>
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
                    <th>สีสติ๊กเกอร์</th>
                    <th>ราคา</th>
                    <th>วันหมดอายุ</th>
                    <th>วันตัดสต็อก</th>
                    <th>สถานะ</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <?php
$no = 1;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sticker_color = $row['sticker_color'];
        $sticker_style = isset($sticker_styles[$sticker_color]) ? $sticker_styles[$sticker_color] : 'background-color: #999999; color: #fff;';

        echo "<tr data-product-code='" . htmlspecialchars($row['product_code']) . "'>";
        echo "<td>" . $no++ . "</td>";
        echo "<td>" . htmlspecialchars($row['product_code']) . "</td>";
        echo "<td>" . htmlspecialchars($row['product_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
        echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
        echo "<td><button style='width: 100%; $sticker_style' disabled>" . htmlspecialchars($sticker_color) . "</button></td>";
        echo "<td>" . htmlspecialchars($row['unit_cost']) . "</td>";
        echo "<td>" . htmlspecialchars($row['expiry_date']) . "</td>";
        echo "<td>" . (!empty($row['out_stock_date']) ? htmlspecialchars($row['out_stock_date']) : 'ไม่มีข้อมูล') . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='10'>ไม่พบข้อมูลสินค้า</td></tr>";
}
?>
            </tbody>
        </table>
    </div>

    <script>
    document.querySelector(".print-buttons button").addEventListener("click", function() {
        const startDate = document.getElementById("startDate").value || "ไม่ระบุ";
        const endDate = document.getElementById("endDate").value || "ไม่ระบุ";

        let existingHeader = document.getElementById("printHeader");
        if (existingHeader) {
            existingHeader.remove();
        }

        let printHeader = document.createElement("div");
        printHeader.id = "printHeader";
        printHeader.innerHTML = `<h4>วันที่: ${startDate} - ${endDate}</h4>`;

        let table = document.querySelector("table");
        table.parentElement.insertBefore(printHeader, table);

        setTimeout(() => {
            window.print();
        }, 100);
    });

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

        renderSortedRows(filteredRows);
    });


    function toggleSearchButton() {
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;
        const searchBtn = document.getElementById("searchBtn");

        if (!startDate || !endDate || new Date(endDate) < new Date(startDate)) {
            searchBtn.disabled = true;
        } else {
            searchBtn.disabled = false;
        }
    }

    document.getElementById("startDate").addEventListener("input", toggleSearchButton);
    document.getElementById("endDate").addEventListener("input", function() {
        const startDate = new Date(document.getElementById("startDate").value);
        const endDate = new Date(this.value);

        if (endDate < startDate) {
            alert("วันที่สิ้นสุดต้องมากกว่าหรือเท่ากับวันที่เริ่มต้น");
            this.value = "";
        }

        toggleSearchButton();
    });
    toggleSearchButton();

    // เก็บแถวต้นฉบับไว้ในตัวแปรเมื่อโหลดหน้าเว็บ
    const originalRows = Array.from(document.querySelectorAll("#product-table-body tr"));

    document.getElementById("productCategory").addEventListener("change", function() {
        const selectedStatus = this.value;
        const filteredRows = originalRows.filter((row) => {
            const statusCell = row.cells[9]; // คอลัมน์ "สถานะ" (index 9)
            return selectedStatus === "" || (statusCell && statusCell.textContent.trim() ===
                selectedStatus);
        });

        renderSortedRows(filteredRows);
    });

    function renderSortedRows(rows) {
        rows.sort((a, b) => {
            const indexA = parseInt(a.cells[0].textContent.trim());
            const indexB = parseInt(b.cells[0].textContent.trim());
            return indexA - indexB;
        });

        const tableBody = document.getElementById("product-table-body");
        tableBody.innerHTML = "";

        if (rows.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='10'>ไม่พบข้อมูล</td></tr>";
        } else {
            rows.forEach((row) => {
                tableBody.appendChild(row.cloneNode(true));
            });
        }
    }
    </script>

</body>

</html>