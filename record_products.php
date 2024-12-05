<?php
session_start();
include('include/header.php');
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    // ดึงข้อมูลจากฟอร์ม
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $model_year = $_POST['model_year'];
    $production_date = $_POST['production_date'];
    $shelf_life = $_POST['shelf_life'];
    $expiry_date = $_POST['expiry_date'];
    $sticker_color = $_POST['sticker_color'];
    $reminder_date = $_POST['reminder_date'];
    $received_date = $_POST['received_date'];
    $quantity = $_POST['quantity'];
    $unit = $_POST['unit'];
    $unit_cost = $_POST['unit_cost'];
    $sender_code = $_POST['sender_code'];
    $sender_company = $_POST['sender_company'];
    $recorder = $_POST['recorder'];
    $unit_price = $_POST['unit_price'];
    $category = $_POST['category'];

   // เตรียมคำสั่ง SQL
$sql = "INSERT INTO products (
    product_code, product_name, model_year, production_date, shelf_life, 
    expiry_date, sticker_color, reminder_date, received_date, quantity, 
    unit, unit_cost, sender_code, sender_company, recorder, unit_price, category
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

// เตรียม statement
$stmt = $conn->prepare($sql);
if ($stmt) {
    // ผูกค่ากับ placeholder
    $stmt->bind_param(
        "ssssssssisssssdss", // กำหนดประเภทข้อมูลที่ตรงกับแต่ละคอลัมน์
        $product_code, $product_name, $model_year, $production_date, $shelf_life,
        $expiry_date, $sticker_color, $reminder_date, $received_date, $quantity,
        $unit, $unit_cost, $sender_code, $sender_company, $recorder, $unit_price, $category
    );

    // ดำเนินการคำสั่ง SQL
    if ($stmt->execute()) {
        echo "<script>alert('บันทึกข้อมูลสำเร็จ');</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
    }

    // ปิด statement
    $stmt->close();
} else {
    echo "<script>alert('เกิดข้อผิดพลาด: ไม่สามารถเตรียมคำสั่งได้');</script>";
}

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

    .form-container {
        background: #cfd8e5;
        padding: 2rem 2rem 1rem 2rem;
        border-radius: 10px;
        margin: 1.5rem 0 3rem 0;
        display: grid;
        /* ใช้ Grid Layout */
        grid-template-columns: repeat(3, 1fr);
        /* แบ่งเป็น 3 คอลัมน์ */
        gap: 1rem;
        /* ช่องว่างระหว่างคอลัมน์และแถว */
        justify-content: center;
        /* จัดกลางทั้งแนวนอน */
    }

    .form-row {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .form-row label {
        margin-bottom: 5px;
        font-weight: bold;
        color: #003d99;
        width: 90%;
    }

    .form-row input,
    .form-row select {
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        flex-shrink: 1;
        width: 90%;
    }

    .submit-button {
        grid-column: span 3;
        display: flex;
        justify-content: center;
        margin-top: 1rem;
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

        <form action="record_products.php" method="POST" class="form-container" id="productForm">
            <div class="form-row">
                <label>รหัสสินค้า</label>
                <input type="text" id="product_code" name="product_code" required>
            </div>
            <div class="form-row">
                <label>ชื่อสินค้า</label>
                <input type="text" id="product_name" name="product_name" required>
            </div>
            <div class="form-row">
                <label for="model_year">รุ่นการผลิต</label>
                <input type="date" id="model_year" name="model_year" required>
            </div>
            <div class="form-row">
                <label for="production_date">วันผลิต</label>
                <input type="date" id="production_date" name="production_date" required>
            </div>
            <div class="form-row">
                <label for="shelf_life">อายุสินค้า(วัน)</label>
                <input type="number" id="shelf_life" name="shelf_life" required>
            </div>
            <div class="form-row">
                <label for="expiry_date">วันหมดอายุ</label>
                <input type="date" id="expiry_date" name="expiry_date" required>
            </div>
            <div class="form-row">
                <label for="sticker_color">สีสติ๊กเกอร์</label>
                <input type="text" id="sticker_color" name="sticker_color" required>
            </div>
            <div class="form-row">
                <label for="reminder_date">เตือนล่วงหน้า</label>
                <input type="date" id="reminder_date" name="reminder_date" required>
            </div>
            <div class="form-row">
                <label for="received_date">วันรับเข้า</label>
                <input type="date" id="received_date" name="received_date" required>
            </div>
            <div class="form-row">
                <label for="quantity">จำนวน</label>
                <input type="number" id="quantity" name="quantity" required>
            </div>
            <div class="form-row">
                <label for="unit">หน่วย</label>
                <input type="text" id="unit" name="unit" required>
            </div>
            <div class="form-row">
                <label for="unit_cost">ราคาทุนต่อหน่วย</label>
                <input type="number" id="unit_cost" name="unit_cost" required>
            </div>
            <div class="form-row">
                <label for="sender_code">รหัสผู้ส่ง</label>
                <input type="text" id="sender_code" name="sender_code" required>
            </div>
            <div class="form-row">
                <label for="sender_company">ชื่อบริษัทผู้ส่ง</label>
                <input type="text" id="sender_company" name="sender_company" required>
            </div>
            <div class="form-row">
                <label for="recorder">ผู้บันทึกข้อมูล</label>
                <input type="text" id="recorder" name="recorder" required>
            </div>
            <div class="form-row">
                <label for="unit_price">ราคาขายต่อหน่วย(บาท)</label>
                <input type="number" id="unit_price" name="unit_price" required>
            </div>
            <div class="form-row">
                <label for="category">หมวดหมู่สินค้า</label>
                <input type="text" id="category" name="category" required>
            </div>
            <div class="submit-button">
                <button type="button" class="btn btn-success" id="confirmButton">บันทึกข้อมูล</button>
                <button type="reset" class="btn btn-secondary" id="resetButton">ล้างข้อมูล</button>
                <button name="save" class="btn btn-primary" id="saveButton">ยืนยันข้อมูลสินค้า</button>
                <button type="button" class="btn btn-warning" id="editButton">แก้ไข</button>
                <button type="button" class="btn btn-danger" id="cancelButton">ยกเลิก</button>
            </div>
        </form>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
<script>
$(document).ready(function() {
    $('#saveButton').hide();
    $('#editButton').hide();
    $('#cancelButton').hide();

    $('#confirmButton').click(function(event) {
        event.preventDefault();

        $('input, select').each(function() {
            var value = $(this).val();
            $(this).prop('readonly', true);
            $(this).css({
                'background-color': '#cfd8e5',
            });

        });
        $('#saveButton').show();
        $('#editButton').show();
        $('#cancelButton').show();
        $('#confirmButton').hide();
        $('#resetButton').hide();
    });

    $('#resetButton').click(function() {
        $('#productForm')[0].reset();

        $('#saveButton').hide();
        $('#editButton').hide();
        $('#cancelButton').hide();
        $('#confirmButton').show();
        $('#resetButton').show();
    });

    $('#cancelButton').click(function() {
        $('#productForm')[0].reset();
        $('input, select').each(function() {
            $(this).prop('readonly', false);
            $(this).css({
                'background-color': '',
            });
        });

        $('#saveButton').hide();
        $('#editButton').hide();
        $('#cancelButton').hide();
        $('#confirmButton').show();
        $('#resetButton').show();
    });

    $('#editButton').click(function() {
        $('input, select').each(function() {
            $(this).prop('readonly', false);
            $(this).css({
                'background-color': '',
            });
        });

        $('#saveButton').hide();
        $('#editButton').hide();
        $('#cancelButton').hide();
        $('#confirmButton').show();
        $('#resetButton').show();
    });
});
</script>

</html>