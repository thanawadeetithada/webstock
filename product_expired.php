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
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: absolute;
        top: 20px;
        left: 20px;
        right: 20px;
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
            <div class="print-left">
                <button><i class="fa-solid fa-print"></i>
                    <p>พิมพ์รายงาน</p>
                </button>
            </div>
            <div class="print-right">
                <button id="downloadExcelBtn"><i class="fa-solid fa-file-excel"></i>
                    <p>ดาวน์โหลด Excel</p>
                </button>
            </div>
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
            <button id="searchBtn" class="btn btn-primary mr-3">ค้นหา</button>
            <button id="clearBtn" class="btn btn-danger" disabled>ล้างการค้นหา</button>
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
                    <th>ตำแหน่งสินค้า</th>
                </tr>
            </thead>
            <tbody id="product-table-body">
                <tr>
                    <td colspan="9">กรุณาค้นหาข้อมูล</td>
                </tr>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

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

    document.getElementById("downloadExcelBtn").addEventListener("click", function() {
        const startDate = document.getElementById("startDate").value || "ไม่ระบุ";
        const endDate = document.getElementById("endDate").value || "ไม่ระบุ";

        // ดึงข้อมูลจากตาราง HTML
        const table = document.querySelector("table");
        const ws = XLSX.utils.table_to_sheet(table); // แปลงข้อมูลตารางเป็น Worksheet
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "รายงานสินค้าหมดอายุ");

        // ตั้งชื่อไฟล์
        let fileName;
        if (startDate === "ไม่ระบุ" && endDate === "ไม่ระบุ") {
            fileName = "รายงานสินค้าหมดอายุ.xlsx";
        } else {
            fileName = `รายงานสินค้าหมดอายุ${startDate}_ถึง_${endDate}.xlsx`;
        }

        // ดาวน์โหลดไฟล์ Excel
        XLSX.writeFile(wb, fileName);
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
                const stickerText = row.sticker_color ? row.sticker_color : 'ไม่มีวันหมดอายุ';

                const newRow = `
                <tr>
                    <td>${row.product_code}</td>
                    <td>${row.product_name}</td>
                    <td>${row.quantity}</td>
                    <td>${row.unit}</td>
                    <td>${row.unit_cost}</td>
                    <td>${row.received_date}</td>
                    <td>${row.expiration_date}</td>
                    <td style="${stickerStyle}">${stickerText}</td>
                    <td>${row.category}</td>
                    <td>${row.position}</td>
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
                        const stickerText = row.sticker_color ? row.sticker_color :
                            'ไม่มีวันหมดอายุ';

                        const newRow = `
                            <tr>
                                <td>${row.product_code}</td>
                                <td>${row.product_name}</td>
                                <td>${row.quantity}</td>
                                <td>${row.unit}</td>
                                <td>${row.unit_cost}</td>
                                <td>${row.received_date}</td>
                                <td>${row.expiration_date}</td>
                                <td style="${stickerStyle}">${stickerText}</td>
                                <td>${row.category}</td>
                                <td>${row.position}</td>
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
            displayProducts(allProducts);
            searchBtn.disabled = true;
            clearBtn.disabled = true;
        } else if (!startDate || !endDate || new Date(endDate) < new Date(startDate)) {
            searchBtn.disabled = true;
            clearBtn.disabled = false;
        } else {
            searchBtn.disabled = false;
            clearBtn.disabled = false;
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

    document.getElementById("clearBtn").addEventListener("click", function() {

        document.getElementById("startDate").value = "";
        document.getElementById("endDate").value = "";

        toggleSearchButton();
    });
    </script>

</body>

</html>