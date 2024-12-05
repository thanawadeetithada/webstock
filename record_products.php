<?php
session_start();
include('include/header.php');
include('config.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'save_product') {
    var_dump($_POST);
    $productCode = $_POST['product_code'];
    $productName = $_POST['product_name'];
    $modelYear = $_POST['model_year'];
    $productionDate = $_POST['production_date'];
    $shelfLife = $_POST['shelf_life'];
    $expiryDate = $_POST['expiry_date'];
    $stickerColor = $_POST['sticker_color'];
    $reminderDate = $_POST['reminder_date'];
    $receivedDate = $_POST['received_date'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $unitCost = $_POST['unit_cost'];
    $senderCode = $_POST['sender_code'];
    $senderCompany = $_POST['sender_company'];
    $recorder = $_POST['recorder'];
    $unitPrice = $_POST['unit_price'];
    $category = $_POST['category'];

    $stmt = $conn->prepare("INSERT INTO products 
                            (product_code, product_name, model_year, production_date, shelf_life, expiry_date, sticker_color, 
                            reminder_date, received_date, quantity, unit, unit_cost, sender_code, sender_company, recorder, 
                             unit_price, category
                             ) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->bind_param("sssssisssissssssss", 
$productCode, 
$productName, 
$modelYear, 
$productionDate, 
$shelfLife,
$expiryDate, 
$stickerColor, 
$reminderDate, 
$receivedDate, 
$quantity, 
$unit, 
$unitCost, 
$senderCode, 
$senderCompany, 
$recorder, 
$unitPrice, 
$category
);


    if ($stmt->execute()) {
        echo "Data saved successfully";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <title>บันทึกข้อมูลสินค้า</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
        overflow: scroll;
    }

    ::-webkit-scrollbar {
        display: none;
    }

    .container {
        max-width: 80%;
        margin: auto;
        background: #fff;
        padding: 2.5rem 2.5rem 0.2rem 2.5rem;
        border-radius: 8px;
        box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.1);
        margin-top: 6.5rem;
    }

    h2 {
        text-align: center;
        margin-top: 1rem;
        margin-bottom: 2.5rem;
        font-weight: bold;
    }


    .button-group {
        display: flex;
        justify-content: flex-start;
        gap: 20px;
        margin-bottom: 20px;
        width: 100%;
    }

    .button-group button {
        padding: 10px 20px;
        font-size: 14px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        color: white;
        outline: none;
    }

    .button-group button i {
        font-size: 20px;
        margin-right: 5px;
    }

    .button-group .delete-button {
        background-color: #dc3545;
        display: flex;
        align-items: center;
    }

    .button-group .add-button {
        background-color: #28a745;
        display: flex;
        align-items: center;
    }

    .button-group .edit-button {
        background-color: #ffc107;
        display: flex;
        align-items: center;
    }

    .import-button {
        display: flex;
        flex-direction: column;
        align-items: center;
        margin-left: auto;
    }

    .import-button button {
        padding: 0px;
        border: none;
        cursor: pointer;
        margin: 0px;
        color: black;
    }

    .import-button .import-icon {
        background-color: #007bff;
        color: white;
    }

    .import-button .import-text {
        background-color: #28a745;
        color: white;
    }

    .form-container {
        background: #cfd8e5;
        padding: 2rem 2rem 1rem 2rem;
        border-radius: 10px;
        margin: 1.5rem 0 3rem 0;
        display: flex;
        flex-wrap: wrap;
        row-gap: 0.5rem;
        column-gap: 1rem;
    }

    .form-container h3 {
        width: 100%;
        margin-bottom: 1rem;
        text-align: center;
        font-weight: bold;
    }

    .form-container .submit-button {
        text-align: center;
        width: 100%;
    }

    .form-container .submit-button button {
        margin: 1rem;
    }

    .form-row {
        width: calc(33%);
        display: flex;
        flex-direction: column;
    }

    .form-row label {
        margin-left: 15px;
        font-weight: bold;
        color: #003d99;
    }

    .form-row input,
    .form-row select {
        padding: 10px;
        margin: 15px;
        border-radius: 5px;
        border: 1px solid #ccc;
        flex-shrink: 1;
        width: -webkit-fill-available;
    }

    .check-container {
        background: #cfd8e5;
        padding: 2rem 2rem 1rem 2rem;
        border-radius: 10px;
        margin: 1.5rem 0 3rem 0;
        display: flex;
        flex-wrap: wrap;
        row-gap: 0.5rem;
        column-gap: 1rem;
        justify-content: center;
    }

    .check-container .form-row {
        width: calc(33% - 2rem);
        display: flex;
        flex-direction: column;
    }

    .modal-header .modal-title {
        margin: 0;
        font-size: 1.5rem;
        text-align: center;
    }

    .modal-body {
        text-align: center;

        p {
            font-size: 16px;
            margin: 0;
        }
    }

    .modal-footer {
        justify-content: center;

        button {
            width: 25%;
        }
    }

    #check-before {
        margin-left: auto;
    }

    #check-before div {
        margin-left: 15px;
    }
    </style>
</head>

<body>
    <div class="container">
        <h2>ข้อมูลสินค้า</h2>
        <div class="button-group">
            <button class="delete-button">
                <i class="fa-solid fa-trash"></i>ลบข้อมูล
            </button>
            <button class="add-button">
                <i class="fa-solid fa-circle-plus"></i>เพิ่ม
            </button>
            <div class="import-button">
                <button><i class="fa-regular fa-file-excel"></i></button>
                <button>นำเข้าข้อมูลสินค้า</button>
            </div>
            <button class="edit-button">
                <i class="fa-regular fa-pen-to-square"></i>แก้ไขข้อมูล
            </button>
        </div>

        <div class="form-container">
            <div class="form-row">
                <label>รหัสสินค้า</label>
                <input type="text" id="product_code">
            </div>
            <div class="form-row">
                <label>ชื่อสินค้า</label>
                <input type="text" id="product_name">
            </div>
            <div class="form-row">
                <label>รุ่นการผลิต</label>
                <div class="model-input">
                    <input type="date" id="model_year">
                </div>
            </div>
            <div class="form-row">
                <label>วันผลิต</label>
                <div class="date-input">
                    <input type="date" id="production_date">
                </div>
            </div>
            <div class="form-row">
                <label for="shelf_life">อายุสินค้า(วัน)</label>
                <input type="number" id="shelf_life">
            </div>
            <div class="form-row">
                <label for="expiry_date">วันหมดอายุ</label>
                <input type="date" id="expiry_date">
            </div>

            <div class="form-row">
                <label for="sticker_color">สีสติ๊กเกอร์</label>
                <input type="text" id="sticker_color">
            </div>

            <div class="form-row">
                <label for="reminder_date">เตือนล่วงหน้า</label>
                <input type="date" id="reminder_date">
            </div>

            <div class="form-row">
                <label for="received_date">วันรับเข้า</label>
                <input type="date" id="received_date">
            </div>

            <div class="form-row">
                <label for="quantity">จำนวน</label>
                <input type="number" id="quantity">
            </div>

            <div class="form-row">
                <label for="unit">หน่วย</label>
                <input type="text" id="unit">
            </div>

            <div class="form-row">
                <label for="unit_cost">ราคาทุนต่อหน่วย</label>
                <input type="number" id="unit_cost">
            </div>

            <div class="form-row">
                <label for="sender_code">รหัสผู้ส่ง</label>
                <input type="text" id="sender_code">
            </div>

            <div class="form-row">
                <label for="sender_company">ชื่อบริษัทผู้ส่ง</label>
                <input type="text" id="sender_company">
            </div>

            <div class="form-row">
                <label for="recorder">ผู้บันทึกข้อมูล</label>
                <input type="text" id="recorder">
            </div>

            <div class="form-row">
                <label for="unit_price">ราคาขายต่อหน่วย(บาท)</label>
                <input type="number" id="unit_price">
            </div>
            <div class="form-row">
                <label for="category">หมวดหมู่สินค้า</label>
                <input type="text" id="category">
            </div>
            <div class="submit-button">
                <button class="btn btn-primary" id="saveButton">บันทึกข้อมูล</button>
                <button class="btn btn-secondary">ล้างข้อมูล</button>
            </div>
        </div>

        <div class="form-container check-container d-none" id="checkContainer">
            <h3>โปรดตรวจสอบข้อมูล</h3>
            <div class="form-row" id="check-before">
                <label>รหัสสินค้า</label>
                <div id="check_product_code"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>ชื่อสินค้า</label>
                <div id="check_product_name"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>รุ่นการผลิต</label>
                <div id="check_model_year"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>วันผลิต</label>
                <div id="check_production_date"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>อายุสินค้า(วัน)</label>
                <div id="check_shelf_life"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>วันหมดอายุ</label>
                <div id="check_expiry_date"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>สีสติ๊กเกอร์</label>
                <div id="check_sticker_color"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>เตือนล่วงหน้า</label>
                <div id="check_reminder_date"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>วันรับเข้า</label>
                <div id="check_received_date"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>จำนวน</label>
                <div id="check_quantity"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>หน่วย</label>
                <div id="check_unit"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>ราคาทุนต่อหน่วย</label>
                <div id="check_unit_cost"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>รหัสผู้ส่ง</label>
                <div id="check_sender_code"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>ชื่อบริษัทผู้ส่ง</label>
                <div id="check_sender_company"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>ผู้บันทึกข้อมูล</label>
                <div id="check_recorder"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>ราคาขายต่อหน่วย(บาท)</label>
                <div id="check_unit_price"></div>
            </div>
            <div class="form-row" id="check-before">
                <label>หมวดหมู่สินค้า</label>
                <div id="check_category"></div>
            </div>
            <div class="form-row" id="check-before"></div>
            <div class="submit-button">
                <button class="btn btn-success" id="confirmButton">ยืนยันข้อมูลสินค้า</button>
                <button class="btn btn-warning">แก้ไข</button>
                <button class="btn btn-danger">ยกเลิก</button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel"
        aria-hidden="false" aria-modal="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title mx-auto" id="confirmModalLabel">ยืนยันการบันทึกข้อมูล</h5>
                </div>
                <div class="modal-body">
                    <p>คุณต้องการยืนยันข้อมูลสินค้านี้หรือไม่?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary rounded-pill" id="confirmSave">ยืนยัน</button>
                    <button type="button" class="btn btn-secondary rounded-pill" data-dismiss="modal">ยกเลิก</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
document.addEventListener('DOMContentLoaded', function() {

    function checkFormValidity() {
        const inputs = document.querySelectorAll('.form-container input');
        const selects = document.querySelectorAll('.form-container select');
        let formValid = true;

        inputs.forEach(input => {
            if (input.value.trim() === '') {
                formValid = false;
            }
        });

        selects.forEach(select => {
            if (select.selectedIndex === 0 || select.value === '') {
                formValid = false;
            }
        });

        const saveButton = document.getElementById('saveButton');
        if (formValid) {
            saveButton.disabled = false;
        } else {
            saveButton.disabled = true;
        }
    }

    const formInputs = document.querySelectorAll('.form-container input');
    const formSelects = document.querySelectorAll('.form-container select');

    formInputs.forEach(input => input.addEventListener('input', checkFormValidity));
    formSelects.forEach(select => select.addEventListener('change', checkFormValidity));

    document.querySelector('.btn.btn-danger').addEventListener('click', function() {
        document.getElementById('checkContainer').classList.add('d-none');
        document.querySelector('.form-container').style.display = 'flex';
        const inputs = document.querySelectorAll('.form-container input');
        const selects = document.querySelectorAll('.form-container select');

        inputs.forEach(input => {
            input.value = '';
        });

        selects.forEach(select => {
            select.selectedIndex = 0;
        });

        const checkElements = document.querySelectorAll('[id^="check_"]');
        checkElements.forEach(element => {
            element.innerText = '';
        });
        document.getElementById('saveButton').disabled = true;
    });

    document.querySelector('.btn.btn-warning').addEventListener('click', function() {
        document.getElementById('checkContainer').classList.add('d-none');
        document.querySelector('.form-container').style.display = 'flex';
        const checkElements = document.querySelectorAll('[id^="check_"]');
        checkElements.forEach(element => {
            element.innerText = '';
        });
        document.getElementById('saveButton').disabled = false;
    });

    document.querySelector('.btn.btn-secondary').addEventListener('click', function() {
        const inputs = document.querySelectorAll('.form-container input');
        const selects = document.querySelectorAll('.form-container select');
        inputs.forEach(input => input.value = '');
        selects.forEach(select => select.selectedIndex = 0);
        document.getElementById('saveButton').disabled = true;
        const checkElements = document.querySelectorAll('[id^="check_"]');
        checkElements.forEach(element => element.innerText = '');
        document.getElementById('checkContainer').classList.add('d-none');

    });

    document.getElementById('saveButton').addEventListener('click', function() {
        var productCode = document.getElementById('product_code').value;
        var productName = document.getElementById('product_name').value;
        var modelYear = document.getElementById('model_year').value;
        var productionDate = document.getElementById('production_date').value;
        var shelfLife = document.getElementById('shelf_life').value;
        var expiryDate = document.getElementById('expiry_date').value;
        var stickerColor = document.getElementById('sticker_color').value;
        var reminderDate = document.getElementById('reminder_date').value;
        var receivedDate = document.getElementById('received_date').value;
        var quantity = document.getElementById('quantity').value;
        var unit = document.getElementById('unit').value;
        var unitCost = document.getElementById('unit_cost').value;
        var senderCode = document.getElementById('sender_code').value;
        var senderCompany = document.getElementById('sender_company').value;
        var recorder = document.getElementById('recorder').value;
        var unitPrice = document.getElementById('unit_price').value;
        var category = document.getElementById('category').value;

        document.getElementById('check_product_code').innerText = productCode;
        document.getElementById('check_product_name').innerText = productName;
        document.getElementById('check_model_year').innerText = modelYear;
        document.getElementById('check_production_date').innerText = productionDate;
        document.getElementById('check_shelf_life').innerText = shelfLife;
        document.getElementById('check_expiry_date').innerText = expiryDate;
        document.getElementById('check_sticker_color').innerText = stickerColor;
        document.getElementById('check_reminder_date').innerText = reminderDate;
        document.getElementById('check_received_date').innerText = receivedDate;
        document.getElementById('check_quantity').innerText = quantity;
        document.getElementById('check_unit').innerText = unit;
        document.getElementById('check_unit_cost').innerText = unitCost;
        document.getElementById('check_sender_code').innerText = senderCode;
        document.getElementById('check_sender_company').innerText = senderCompany;
        document.getElementById('check_recorder').innerText = recorder;
        document.getElementById('check_unit_price').innerText = unitPrice;
        document.getElementById('check_category').innerText = category;
        document.getElementById('checkContainer').classList.remove('d-none');

        document.querySelector('.form-container').style.display = 'none';
    });
    checkFormValidity();
});

document.getElementById('confirmButton').addEventListener('click', function() {
        $('#confirmModal').modal('show');
});

document.getElementById('confirmSave').addEventListener('click', function() {
    var productCode = document.getElementById('product_code').value;
    var productName = document.getElementById('product_name').value;
    var modelYear = document.getElementById('model_year').value;
    var productionDate = document.getElementById('production_date').value;
    var shelfLife = document.getElementById('shelf_life').value;
    var expiryDate = document.getElementById('expiry_date').value;
    var stickerColor = document.getElementById('sticker_color').value;
    var reminderDate = document.getElementById('reminder_date').value;
    var receivedDate = document.getElementById('received_date').value;
    var quantity = document.getElementById('quantity').value;
    var unit = document.getElementById('unit').value;
    var unitCost = document.getElementById('unit_cost').value;
    var senderCode = document.getElementById('sender_code').value;
    var senderCompany = document.getElementById('sender_company').value;
    var recorder = document.getElementById('recorder').value;
    var unitPrice = document.getElementById('unit_price').value;
    var category = document.getElementById('category').value;

    $.ajax({
        url: '',
        type: 'POST',
        contentType: 'application/x-www-form-urlencoded',
        data: {
            action: 'save_product',
            product_code: productCode,
            product_name: productName,
            model_year: modelYear,
            production_date: productionDate,
            shelf_life: shelfLife,
            expiry_date: expiryDate,
            sticker_color: stickerColor,
            reminder_date: reminderDate,
            received_date: receivedDate,
            quantity: quantity,
            unit: unit,
            unit_cost: unitCost,
            sender_code: senderCode,
            sender_company: senderCompany,
            recorder: recorder,
            unit_price: unitPrice,
            category: category,
        },
        success: function(response) {
            alert('ข้อมูลถูกบันทึกเรียบร้อยแล้ว');
            $('#confirmModal').modal('hide');
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาด: ' + error);
        }
    });
});
</script>

</html>