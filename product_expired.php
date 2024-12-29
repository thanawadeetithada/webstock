<?php
session_start();
include 'include/header.php';
include('config.php');


$sql = "SELECT product_code, product_name, quantity, unit, unit_cost, received_date, expiration_date, sticker_color, category, position
        FROM products WHERE expiration_date < CURRENT_DATE";

$result = $conn->query($sql);

$allProducts = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $allProducts[] = $row;
    }
} else {
    $allProducts = ["error" => "SQL error: " . $conn->error];
}

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
    'ไม่มีวันหมดอายุ' => 'background-color: #999999; color: #fff;',
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาสินค้าหมดอายุ</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

        td[style*="background-color"] {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
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

    .print-buttons button,
    .print-buttons button:focus {
        font-size: 16px;
        border-radius: 5px;
        color: black;
        border: none;
        cursor: pointer;
        background-color: white;
        outline: none;
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
            <button><i class="fa-solid fa-print"></i>
                <p>พิมพ์รายงาน</p>
            </button>
        </div>

        <div class="search-container">
            <div class="center-section">
                <div class="dropdown-container">
                    <label for="startDate">วันที่เริ่มต้น:</label>
                    <input type="date" id="startDate" class="form-control">
                </div>
                <label>ถึง</label>
                <div class="dropdown-container">
                    <label for="endDate">วันที่สิ้นสุด:</label>
                    <input type="date" id="endDate" class="form-control">
                </div>
            </div>
        </div>
        <div class="center-button">
            <button id="searchBtn" class="btn btn-primary">ค้นหา</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>รหัสสินค้า</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>หน่วย</th>
                    <th>ราคา</th>
                    <th>วันที่รับเข้า</th>
                    <th>วันหมดอายุ</th>
                    <th>สีสติ๊กเกอร์</th>
                    <th>หมวดหมู่สินค้า</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <tr>
                    <td colspan="9">กรุณาค้นหาข้อมูล</td>
                </tr>
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

    const allProducts = <?php echo json_encode($allProducts); ?>;
    const stickerStyles = <?php echo json_encode($sticker_styles); ?>;

    function displayProducts(products) {
        const tableBody = document.getElementById("product-table-body");
        tableBody.innerHTML = "";

        if (products.length === 0) {
            tableBody.innerHTML = "<tr><td colspan='9'>ไม่พบข้อมูลสินค้า</td></tr>";
        } else {
            products.forEach(row => {
                const stickerStyle = stickerStyles[row.sticker_color] || "";
                const newRow = `
                <tr>
                    <td>${row.product_code}</td>
                    <td>${row.product_name}</td>
                    <td>${row.quantity}</td>
                    <td>${row.unit}</td>
                    <td>${row.unit_cost}</td>
                    <td>${row.received_date}</td>
                    <td>${row.expiration_date}</td>
                    <td style="${stickerStyle}">${row.sticker_color}</td>
                    <td>${row.category}</td>
                </tr>`;
                tableBody.insertAdjacentHTML("beforeend", newRow);
            });
        }
    }
    document.addEventListener("DOMContentLoaded", () => {
        displayProducts(allProducts);
    });

    document.getElementById("searchBtn").addEventListener("click", function() {
        const startDateInput = document.getElementById("startDate").value;
        const endDateInput = document.getElementById("endDate").value;

        if (!startDateInput || !endDateInput) {
            alert("กรุณาระบุวันที่ให้ครบถ้วน");
            return;
        }

        const startDate = new Date(startDateInput).toISOString().split('T')[0];
        const endDate = new Date(endDateInput).toISOString().split('T')[0];

        fetch(`fetch_products_expiration.php?startDate=${startDate}&endDate=${endDate}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                const tableBody = document.getElementById("product-table-body");
                tableBody.innerHTML = "";

                if (data.length === 0) {
                    tableBody.innerHTML = "<tr><td colspan='9'>ไม่พบข้อมูลในช่วงวันที่ที่เลือก</td></tr>";
                } else {
                    data.forEach(row => {
                        const stickerStyle = stickerStyles[row.sticker_color] || "";
                        const newRow = `
                            <tr>
                                <td>${row.product_code}</td>
                                <td>${row.product_name}</td>
                                <td>${row.quantity}</td>
                                <td>${row.unit}</td>
                                <td>${row.unit_cost}</td>
                                <td>${row.received_date}</td>
                                <td>${row.expiration_date}</td>
                                <td style="${stickerStyle}">${row.sticker_color}</td>
                                <td>${row.category}</td>
                            </tr>`;
                        tableBody.insertAdjacentHTML("beforeend", newRow);
                    });
                }
            })
            .catch(error => {
                console.error("Error fetching data:", error);
                alert("เกิดข้อผิดพลาดในการดึงข้อมูล: " + error.message);
            });
    });

    function toggleSearchButton() {
        const startDate = document.getElementById("startDate").value;
        const endDate = document.getElementById("endDate").value;
        const searchBtn = document.getElementById("searchBtn");

        if (!startDate && !endDate) {
            // If both dates are empty, show all products
            displayProducts(allProducts);
            searchBtn.disabled = true;
        } else if (!startDate || !endDate || new Date(endDate) < new Date(startDate)) {
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
    </script>

</body>

</html>