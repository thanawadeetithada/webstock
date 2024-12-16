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
    $query = $conn->prepare("SELECT * FROM products WHERE product_code = ? AND status != 'OUT'");
    $query->bind_param("s", $search_code);
    $query->execute();
    $result = $query->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'product' => $product,
        ]);
    } else {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'ไม่พบข้อมูลสินค้า',
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

    #action-buttons-row {
        text-align: center;
    }

    .action-buttons button {
        margin-right: 10px;
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
                    <td colspan="7">ไม่พบข้อมูลสินค้า</td>
                </tr>
            </tbody>
            <tfoot>
                <tr id="action-buttons-row">
                    <td colspan="3" id="total-items-and-quantity"><strong>รวม</strong> 0 <strong>รายการ</strong> 0
                        <strong>ชิ้น</strong>
                    </td>
                    <td colspan="2" id="total-price">0.00<strong> บาท</strong></td>
                    <td colspan="2">
                        <button id="cut-stock-btn" class="btn btn-primary">ตัดสต็อก</button>
                        <button id="delete-items-btn" class="btn btn-danger">ลบรายการ</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    <script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('table tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function updateTotalItemsAndQuantity() {
        const tbody = document.querySelector('table tbody');
        const rows = tbody.querySelectorAll('tr');
        let totalItems = 0;
        let totalQuantity = 0;

        rows.forEach(row => {
            const quantityCell = row.cells[3]; // เซลล์จำนวน (คอลัมน์ที่ 4 - index 3)
            if (quantityCell) {
                const quantity = parseInt(quantityCell.textContent) || 0;
                totalItems += 1;
                totalQuantity += quantity;
            }
        });

        // อัปเดตช่องแสดงผลรวมจำนวนรายการและจำนวนชิ้น
        document.getElementById('total-items-and-quantity').innerHTML =
            `<strong>รวม</strong> ${totalItems} <strong>รายการ</strong> ${totalQuantity} <strong>ชิ้น</strong>`;
    }

    function calculateTotalPrice() {
        const tbody = document.querySelector('table tbody');
        let totalPrice = 0;

        tbody.querySelectorAll('tr').forEach(row => {
            const priceCell = row.cells[4]; // เซลล์ราคา/หน่วย (คอลัมน์ที่ 5 - index 4)
            const unitCell = row.cells[3];
            if (priceCell) {
                const price = parseFloat(priceCell.textContent) || 0;
                const unit = parseFloat(unitCell.textContent) || 0;

                totalPrice += price * unit;
            }
        });

        // อัปเดตช่องแสดงผลรวมใน tfoot
        document.getElementById('total-price').innerHTML = totalPrice.toFixed(2) + ' <strong>บาท</strong>';
    }

    // เรียกฟังก์ชันนี้หลังจากเพิ่มสินค้าไปยังตาราง
    function addProductToTable(product) {
        const tbody = document.querySelector('table tbody');

        // ตรวจสอบว่ามี product_code ซ้ำกันไหม
        const existingCodes = Array.from(tbody.querySelectorAll('tr')).map(row => {
            return row.cells[5]?.textContent;
        });

        if (existingCodes.includes(product.product_code)) {
            alert('มีข้อมูลสินค้านี้อยู่แล้ว');
            return; // หยุดการทำงานหากพบว่า product_code ซ้ำ
        }

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

        // เรียกฟังก์ชันคำนวณผลรวมราคาและจำนวนรายการ
        calculateTotalPrice();
        updateTotalItemsAndQuantity();
    }

    // เรียกฟังก์ชันคำนวณผลรวมหลังจากลบแถว
    document.getElementById('delete-items-btn').addEventListener('click', function() {
        const tbody = document.querySelector('table tbody');
        const checkboxes = tbody.querySelectorAll('input[type="checkbox"]:checked');

        checkboxes.forEach(checkbox => {
            checkbox.closest('tr').remove();
        });

        // ถ้าไม่มีแถวข้อมูลเหลือ ให้เพิ่มแถว "ไม่พบข้อมูลสินค้า"
        if (tbody.querySelectorAll('tr').length === 0) {
            const noDataRow = document.createElement('tr');
            noDataRow.innerHTML = `<td colspan="7">ไม่พบข้อมูลสินค้า</td>`;
            tbody.appendChild(noDataRow);
        }

        // เอาเครื่องหมายถูกใน checkbox ที่หัวตารางออก
        document.getElementById('select-all').checked = false;

        // อัปเดตผลรวมราคา
        calculateTotalPrice();
        updateTotalItemsAndQuantity();
    });

    // การค้นหาสินค้า
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

    document.getElementById('cut-stock-btn').addEventListener('click', function() {
    const tbody = document.querySelector('table tbody');
    const rows = tbody.querySelectorAll('tr');
    const productsToUpdate = [];

    rows.forEach(row => {
        const productCode = row.cells[5].textContent; // รหัสสินค้า (คอลัมน์ที่ 6 - index 5)
        productsToUpdate.push(productCode);
    });

    if (productsToUpdate.length === 0) {
        alert('ไม่มีสินค้าที่จะตัดสต็อก');
        return;
    }

    // แสดง Alert เพื่อยืนยันการตัดสต็อก
    if (confirm('คุณต้องการตัดสต็อกสินค้าทั้งหมดนี้ใช่หรือไม่?')) {
        // ส่งข้อมูลไปยังเซิร์ฟเวอร์ผ่าน fetch
        fetch('cut_stock.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ products: productsToUpdate })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('ตัดสต็อกเรียบร้อยแล้ว');
                // ลบแถวทั้งหมดออกจากตารางหลังตัดสต็อก
                tbody.innerHTML = '<tr><td colspan="7">ไม่พบข้อมูลสินค้า</td></tr>';
                updateTotalItemsAndQuantity();
                calculateTotalPrice();
            } else {
                alert('เกิดข้อผิดพลาดในการตัดสต็อก');
            }
        })
        .catch(error => console.error('Error:', error));
    }
});

    </script>

</body>

</html>