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
        padding: 0px 20px 10px 20px;
    }

    .container {
        max-width: fit-content;
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

    table {
        width: 100%;
        border-collapse: collapse;
    }

    table th,
    table td {
        white-space: nowrap;
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

    .action-buttons button {
        margin-right: 10px;
    }

    .form-btn form {
        display: flex;
        justify-content: center;
        gap: 1rem;
    }

    .custom-prompt-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .custom-prompt-box {
        background: #fff;
        padding: 20px;
        border-radius: 8px;
        width: auto;
        text-align: center;

        input,
        input:focus {
            border-radius: 8px;
            width: 50%;
            border: 1px solid #4c4d4f;
            padding-left: 0.5rem;
            outline: none;
        }
    }

    .custom-prompt-buttons {
        margin-top: 10px;
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    #custom-prompt-error {
        color: red;
        display: none;
        margin-top: 16px;
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
                <input type="text" id="search-box" placeholder="ค้นหารหัสสินค้า...">
                <i class="fa-solid fa-magnifying-glass"></i>
            </div>
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
                    <th>No.</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>หน่วย</th>
                    <th>ราคา/หน่วย</th>
                    <th>รหัสสินค้า</th>
                    <th>วันหมดอายุสินค้า</th>
                    <th>ตำแหน่งสินค้า</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="9">ไม่พบข้อมูลสินค้า</td>
                </tr>
            </tbody>
            <tfoot>
                <tr id="action-buttons-row">
                    <td colspan="4" id="total-items-and-quantity"><strong>รวม</strong> 0 <strong>รายการ</strong> 0
                        <strong>ชิ้น</strong>
                    </td>
                    <td colspan="3" id="total-price">0.00<strong> บาท</strong></td>
                    <td colspan="2" class="form-btn">
                        <form id="payment-form" action="process_out_stock.php" method="POST">
                            <input type="hidden" name="products" id="products-input">
                            <input type="hidden" name="total_price" id="total-price-input">
                            <button type="submit" id="out-stock-btn" class="btn btn-primary">ตัดสต็อก</button>
                            <button id="delete-items-btn" class="btn btn-danger" type="button">ลบรายการ</button>
                        </form>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div id="custom-prompt" class="custom-prompt-overlay" style="display: none;">
        <div class="custom-prompt-box">
            <p id="custom-prompt-message"></p>
            <input type="number" id="custom-prompt-input" min="1">
            <p id="custom-prompt-error">กรุณาใส่จำนวนสินค้าที่ถูกต้อง</p>
            <div class="custom-prompt-buttons">
                <button id="custom-prompt-ok" class="btn btn-primary btn-sm">ตกลง</button>
                <button id="custom-prompt-cancel" class="btn btn-danger btn-sm">ยกเลิก</button>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('select-all').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('table tbody input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    function showCustomPrompt(message, maxQuantity, callback) {
        const promptOverlay = document.getElementById('custom-prompt');
        const promptMessage = document.getElementById('custom-prompt-message');
        const promptInput = document.getElementById('custom-prompt-input');
        const errorText = document.getElementById('custom-prompt-error');

        promptMessage.textContent = message;
        promptInput.value = 1;
        promptOverlay.style.display = 'flex';
        errorText.style.display = 'none';
        promptInput.focus();

        document.getElementById('custom-prompt-ok').onclick = function() {
            const quantityValue = parseInt(promptInput.value);
            if (!isNaN(quantityValue) && quantityValue > 0 && quantityValue <= maxQuantity) {
                promptOverlay.style.display = 'none';
                callback(quantityValue);
            } else {
                errorText.style.display = 'block';
            }
        };

        document.getElementById('custom-prompt-cancel').onclick = function() {
            promptOverlay.style.display = 'none';
            callback(null);
        };
    }


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

        document.getElementById('total-price').innerHTML = totalPrice.toFixed(2) + ' <strong>บาท</strong>';
    }

    function formatPosition(position) {
        if (!position || position.length === 0) return "ตำแหน่งไม่ระบุ";

        let row = "-";
        let floor = "-";
        let slot = "-";

        if (position.length === 3) {
            row = position[0];
            floor = position[1];
            slot = position[2];
        } else if (position.length === 2) {
            if (!isNaN(position[0]) && !isNaN(position[1])) {
                floor = position[0];
                slot = position[1];
            } else {
                row = position[0];
                floor = position[1];
            }
        } else if (position.length === 1) {
            if (!isNaN(position[0])) {
                floor = position[0];
            } else {
                row = position[0];
            }
        }

        return `แถว ${row} ชั้น ${floor} ช่อง ${slot}`;
    }

    function addProductToTable(product) {
        const tbody = document.querySelector('table tbody');

        const existingCodes = Array.from(tbody.querySelectorAll('input[name="product_code"]')).map(input => {
            return input.value;
        });

        const noDataRow = tbody.querySelector('tr td[colspan="9"]');
        if (noDataRow) {
            noDataRow.parentElement.remove();
        }

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
       <td><input type="checkbox"></td>
        <td>${tbody.rows.length + 1}</td>
        <td>${product.product_name}</td>
        <td>${product.quantity}</td>
        <td>${product.unit}</td>
        <td>${product.unit_price}</td>
        <td>${product.product_code}</td>
        <td>${product.expiration_date}</td>
        <td>${formatPosition(product.position)}</td>
 
        <input type="hidden" name="unit" value="${product.unit}">
        <input type="hidden" name="product_model" value="${product.product_model}">
        <input type="hidden" name="production_date" value="${product.production_date}">
        <input type="hidden" name="shelf_life" value="${product.shelf_life}">
        <input type="hidden" name="sticker_color" value="${product.sticker_color}">
        <input type="hidden" name="reminder_date" value="${product.reminder_date}">
        <input type="hidden" name="received_date" value="${product.received_date}">
        <input type="hidden" name="unit_cost" value="${product.unit_cost}">
        <input type="hidden" name="sender_code" value="${product.sender_code}">
        <input type="hidden" name="sender_company" value="${product.sender_company}">
        <input type="hidden" name="recorder" value="${product.recorder}">
        <input type="hidden" name="category" value="${product.category}">
        <input type="hidden" name="status" value="${product.status}">
     `;

        tbody.appendChild(newRow);
        calculateTotalPrice();
        updateTotalItemsAndQuantity();
    }

    document.getElementById('out-stock-btn').addEventListener('click', function(event) {
        if (allProducts.length === 0) {
            alert('กรุณาค้นหาสินค้าก่อนตัดสต็อก');
            event.preventDefault();
            return;
        }

        // เก็บข้อมูลทั้งหมดในฟิลด์ hidden
        document.getElementById('products-input').value = JSON.stringify(allProducts);

        // คำนวณราคาทั้งหมด
        const totalPrice = allProducts.reduce((sum, product) => {
            const quantity = parseInt(product.quantity || 0);
            const unitPrice = parseFloat(product.unit_price || 0);
            return sum + (quantity * unitPrice);
        }, 0);

        document.getElementById('total-price-input').value = totalPrice.toFixed(2);
    });

    document.getElementById('delete-items-btn').addEventListener('click', function() {
        const tbody = document.querySelector('table tbody');
        const checkboxes = tbody.querySelectorAll('input[type="checkbox"]:checked');

        checkboxes.forEach((checkbox) => {
            const row = checkbox.closest('tr');
            const productCode = row.cells[5].textContent;
            allProducts = allProducts.filter((product) => product.product_code !== productCode);
            row.remove();
        });

        if (tbody.querySelectorAll('tr').length === 0) {
            const noDataRow = document.createElement('tr');
            noDataRow.innerHTML = `<td colspan="9">ไม่พบข้อมูลสินค้า</td>`;
            tbody.appendChild(noDataRow);
        }

        document.getElementById('select-all').checked = false;
        calculateTotalPrice();
        updateTotalItemsAndQuantity();
    });

    // ตัวแปรเก็บสินค้าทั้งหมด
    let allProducts = [];

    document.getElementById('search-box').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const searchValue = this.value.trim();

            if (searchValue) {
                fetch('', {
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

                            // ตรวจสอบว่ามีสินค้านี้อยู่แล้วในตารางหรือไม่
                            const tbody = document.querySelector('table tbody');
                            const existingCodes = Array.from(tbody.querySelectorAll(
                                    'input[name="product_code"]'))
                                .map(input => input.value);

                            if (existingCodes.includes(product.product_code)) {
                                alert('มีข้อมูลสินค้านี้อยู่แล้ว');
                            } else {
                                let maxQuantity = parseInt(product.quantity);
                                showCustomPrompt(`กรุณาใส่จำนวนสินค้า (มี ${maxQuantity} ชิ้นในสต็อก)`,
                                    maxQuantity,
                                    function(quantityValue) {
                                        if (quantityValue !== null) {
                                            product.quantity = quantityValue;
                                            addProductToTable(product);
                                            allProducts.push(product);
                                        }
                                    });
                            }
                        } else {
                            alert(data.message);
                        }
                        document.getElementById('search-box').value = '';
                    })
                    .catch(error => console.error('Error:', error));
            }
        }
    });

    document.querySelector('.fa-magnifying-glass').addEventListener('click', function() {
        const searchValue = document.getElementById('search-box').value.trim();

        if (searchValue) {
            fetch('', {
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

                        // ตรวจสอบว่ามีสินค้านี้อยู่แล้วในตารางหรือไม่
                        const tbody = document.querySelector('table tbody');
                        const existingCodes = Array.from(tbody.querySelectorAll(
                                'input[name="product_code"]'))
                            .map(input => input.value);

                        if (existingCodes.includes(product.product_code)) {
                            alert('มีข้อมูลสินค้านี้อยู่แล้ว');
                        } else {
                            let maxQuantity = parseInt(product.quantity);
                            showCustomPrompt(`กรุณาใส่จำนวนสินค้า (มี ${maxQuantity} ชิ้นในสต็อก)`,
                                maxQuantity,
                                function(quantityValue) {
                                    if (quantityValue !== null) {
                                        product.quantity = quantityValue;
                                        addProductToTable(product);
                                        allProducts.push(product);
                                    }
                                });
                        }
                    } else {
                        alert(data.message);
                    }
                    document.getElementById('search-box').value = '';
                })
                .catch(error => console.error('Error:', error));
        }
    });

    document.getElementById('custom-prompt-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('custom-prompt-ok').click();
        }
    });
    </script>
</body>

</html>