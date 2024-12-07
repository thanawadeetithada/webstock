<?php
session_start();
include('include/header.php');
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$sql = "SELECT product_code, product_name, quantity, unit, unit_cost FROM products";
$result = $conn->query($sql);

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'ผู้ใช้';

$current_date = date('d/m/Y');
$current_time = date('H:i:s');

$total_items = 0;
$total_quantity = 0;
$total_price = 0;

if (isset($_POST['product_code'])) {
    $product_codes = json_decode($_POST['product_code']); // รับค่าเป็นอาร์เรย์ของ product_code
    
    if (!empty($product_codes)) {
        // สร้าง SQL สำหรับลบสินค้า
        $product_codes_placeholder = implode(",", array_fill(0, count($product_codes), "?"));
        $sql = "DELETE FROM products WHERE product_code IN ($product_codes_placeholder)";
        
        if ($stmt = $conn->prepare($sql)) {
            // ผูกค่าพารามิเตอร์
            $stmt->bind_param(str_repeat('s', count($product_codes)), ...$product_codes); // 's' ใช้สำหรับ string
            if ($stmt->execute()) {
                echo "ลบรายการสินค้าสำเร็จ!"; // ส่งข้อความเมื่อสำเร็จ
            } else {
                echo "ไม่สามารถลบสินค้าบางรายการได้"; // ส่งข้อความเมื่อเกิดข้อผิดพลาด
            }
            $stmt->close();
        } else {
            echo "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL"; // ส่งข้อความเมื่อเกิดข้อผิดพลาดในการเตรียม SQL
        }
    } else {
        echo "ไม่มีสินค้าที่เลือก"; // ส่งข้อความเมื่อไม่มีสินค้าที่เลือก
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง"; // ส่งข้อความเมื่อไม่มีข้อมูลที่คาดหวัง
}


$sql = "SELECT product_code, product_name, quantity, unit, unit_cost FROM products";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ขายสินค้า</title>
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
            <button>
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
            <div class="search-info">
                <span class="label">วันที่</span> <?= $current_date ?>
                <span class="label">เวลา</span> <?= $current_time ?>
                <span class="label">ผู้ทำการขาย</span> <?= $username ?>
            </div>
        </div>
        <table>
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all"></th>
                    <th>No.</th>
                    <th>จำนวน</th>
                    <th>ชื่อสินค้า</th>
                    <th>หน่วย</th>
                    <th>ราคาทุนต่อหน่วย</th>
                </tr>
            </thead>
            <tbody>
                <?php
    $no = 1;
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo "<tr data-product-code='" . $row['product_code'] . "'>";
            echo "<td><input type='checkbox' class='select-item' data-product-code='" . $row['product_code'] . "'></td>";
            echo "<td>" . $no++ . "</td>";
            echo "<td>" . $row['quantity'] . "</td>";
            echo "<td>" . $row['product_name'] . "</td>";
            echo "<td>" . $row['unit'] . "</td>";
            echo "<td>" . number_format($row['unit_cost'], 2) . " บาท</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='6'>ไม่พบข้อมูลสินค้า</td></tr>";
    }
    ?>
            </tbody>
            <tfoot>
                <tr class="footer-row">
                    <td colspan="1"></td>
                    <td colspan="2">รวม <?= $total_items ?> รายการ <?= number_format($total_quantity) ?> ชิ้น</td>
                    <td colspan="2">รวม <?= number_format($total_price, 2) ?> บาท</td>
                    <td>
                        <button class="btn btn-danger" id="delete-selected">ลบรายการ</button>
                        <button class="btn btn-success">ชำระเงิน</button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <script>
    document.getElementById('select-all').addEventListener('change', function() {
        const isChecked = this.checked;
        const checkboxes = document.querySelectorAll('.select-item');

        checkboxes.forEach(checkbox => {
            checkbox.checked = isChecked;
        });
    });

    document.getElementById('delete-selected').addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.select-item:checked');
        const selectedProductCodes = [];

        selectedCheckboxes.forEach(checkbox => {
            const productCode = checkbox.getAttribute('data-product-code');
            selectedProductCodes.push(productCode);
        });

        if (selectedProductCodes.length > 0) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    alert('ยันยันการลบรายการนี้ใช่ไหม?');
                    location.reload();
                } else {
                    alert('Error: Could not delete selected products.');
                }
            };

            xhr.send('product_code=' + JSON.stringify(selectedProductCodes));
        } else {
            alert('กรุณาเลือกสินค้าที่จะลบ');
        }
    });

    function calculateTotals() {
        const rows = document.querySelectorAll('table tbody tr');

        let totalItems = 0;
        let totalQuantity = 0;
        let totalPrice = 0;

        rows.forEach(row => {
            const noDataCell = row.querySelector('td[colspan="6"]');
            if (noDataCell && noDataCell.textContent.includes('ไม่พบข้อมูลสินค้า')) {
                return;
            }

            const quantityCell = row.querySelector('td:nth-child(3)');
            const priceCell = row.querySelector('td:nth-child(6)');

            if (row.style.display !== 'none') {
                totalItems++;
                if (quantityCell && priceCell) {
                    const quantity = parseInt(quantityCell.textContent, 10);
                    const price = parseFloat(priceCell.textContent.replace(' บาท', '').replace(',', ''));
                    totalQuantity += quantity;
                    totalPrice += (quantity * price);
                }
            }
        });

        const footerRow = document.querySelector('tfoot .footer-row');
        footerRow.querySelector('td:nth-child(2)').textContent = `รวม ${totalItems} รายการ ${totalQuantity} ชิ้น`;
        footerRow.querySelector('td:nth-child(3)').textContent = `รวม ${totalPrice.toFixed(2)} บาท`;
    }


    document.getElementById('search-box').addEventListener('input', function() {
        const searchQuery = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');

        let totalItems = 0;
        let totalQuantity = 0;
        let totalPrice = 0;

        rows.forEach(row => {
            let found = false;
            const cells = row.querySelectorAll('td');
            const quantityCell = cells[2];
            const priceCell = cells[5];

            cells.forEach(cell => {
                if (cell.textContent.toLowerCase().includes(searchQuery)) {
                    found = true;
                }
            });

            if (found) {
                row.style.display = '';
                totalItems++;

                if (quantityCell && priceCell) {
                    const quantity = parseInt(quantityCell.textContent, 10);
                    const price = parseFloat(priceCell.textContent.replace(' บาท', '').replace(',',
                        ''));
                    totalQuantity += quantity;
                    totalPrice += (quantity * price);
                }
            } else {
                row.style.display = 'none';
            }
        });

        const footerRow = document.querySelector('tfoot .footer-row');
        footerRow.querySelector('td:nth-child(2)').textContent =
            `รวม ${totalItems} รายการ ${totalQuantity} ชิ้น`;
        footerRow.querySelector('td:nth-child(3)').textContent = `รวม ${totalPrice.toFixed(2)} บาท`;
    });

    window.onload = function() {
        calculateTotals();
    };
    </script>

</body>

</html>

<?php

$conn->close();
?>