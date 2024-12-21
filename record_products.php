<?php
ob_start();
session_start();
include 'config.php';

$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'check_duplicate' && isset($_GET['product_code'])) {
    header('Content-Type: application/json');
    $product_code = $_GET['product_code'];

    $check_sql = "SELECT product_code FROM products WHERE product_code = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $product_code);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo json_encode(['exists' => true]);
    } else {
        echo json_encode(['exists' => false]);
    }

    $stmt_check->close();
    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    $product_code = $_POST['product_code'];

    $delete_sql = "DELETE FROM products WHERE product_code = ?";
    $stmt_delete = $conn->prepare($delete_sql);
    $stmt_delete->bind_param("s", $product_code);

    if ($stmt_delete->execute()) {
        echo "ลบข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล: " . $stmt_delete->error;
    }

    $stmt_delete->close();
    $conn->close();
    exit();
}

if (isset($_GET['action']) && $_GET['action'] === 'fetch') {
    $search = isset($_GET['query']) ? $_GET['query'] : '';

    $sql = "SELECT product_code, product_name, product_model, production_date, shelf_life, expiry_date, sticker_color, reminder_date, received_date, quantity, unit, unit_cost, sender_code, sender_company, recorder, unit_price, category FROM products WHERE product_code LIKE ? OR product_name LIKE ?";
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(["error" => "SQL error: " . $conn->error]);
        exit();
    }

    $search_param = '%' . $search . '%';
    $stmt->bind_param('ss', $search_param, $search_param);

    if (!$stmt->execute()) {
        echo json_encode(["error" => "Execution error: " . $stmt->error]);
        exit();
    }

    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
    $stmt->close();
    $conn->close();
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    $product_code = $_POST['product_code'];
    $product_name = $_POST['product_name'];
    $product_model = $_POST['product_model'];
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

    $check_sql = "SELECT * FROM products WHERE product_code = ?";
    $stmt_check = $conn->prepare($check_sql);
    $stmt_check->bind_param("s", $product_code);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $sql = "UPDATE products SET
                    product_name = ?, product_model = ?, production_date = ?, shelf_life = ?,
                    expiry_date = ?, sticker_color = ?, reminder_date = ?, received_date = ?,
                    quantity = ?, unit = ?, unit_cost = ?, sender_code = ?, sender_company = ?,
                    recorder = ?, unit_price = ?, category = ?
                WHERE product_code = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssisssssdss",
            $product_name, $product_model, $production_date, $shelf_life, $expiry_date,
            $sticker_color, $reminder_date, $received_date, $quantity, $unit, $unit_cost,
            $sender_code, $sender_company, $recorder, $unit_price, $category, $product_code
        );

        if ($stmt->execute()) {
            echo "<script>alert('แก้ไขข้อมูลสำเร็จ');</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    } else {

        $sql = "INSERT INTO products (
    product_code, product_name, product_model, production_date, shelf_life,
    expiry_date, sticker_color, reminder_date, received_date, quantity,
    unit, unit_cost, sender_code, sender_company, recorder, unit_price, category
) VALUES (
    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssssssssisssssdss",
            $product_code, $product_name, $product_model, $production_date, $shelf_life,
            $expiry_date, $sticker_color, $reminder_date, $received_date, $quantity,
            $unit, $unit_cost, $sender_code, $sender_company, $recorder, $unit_price, $category
        );

        if ($stmt->execute()) {
            echo "<script>alert('เพิ่มข้อมูลสำเร็จ');</script>";
        } else {
            echo "<script>alert('เกิดข้อผิดพลาด: " . $stmt->error . "');</script>";
        }

        $stmt->close();
    }

    $stmt_check->close();
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
    .header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background-color: #222222;
        padding: 20px;
        height: 8vh;
        width: 100vw;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 10;
    }

    button {
        background-color: transparent;
        padding: 0px;
        border: 0px;
        color: white;
        outline: none !important;
    }

    i {
        font-size: 1.5rem;
    }

    span {
        font-size: larger;
    }

    .right-section {
        display: flex;
    }

    .sidebar {
        position: fixed;
        top: 8vh;
        left: -250px;
        width: 250px;
        height: 92vh;
        background-color: #333;
        color: white;
        padding-top: 20px;
        transition: 0.3s;
        z-index: 9;
    }

    .sidebar a {
        display: block;
        padding: 10px 15px;
        text-decoration: none;
        color: white;
        font-size: 16px;
        border-bottom: 1px solid #444;
    }

    .sidebar a:hover {
        background-color: #BFBBBA;
        color: #333;
    }

    .sidebar-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        color: white;
    }

    .menu-btn {
        font-size: 1.5rem;
    }

    .content {
        transition: margin-left .3s;
        padding: 20px;
        height: 92vh;
        margin-top: 8vh;
    }

    /* header */
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding:  0px 20px 10px 20px;
    }

    ::-webkit-scrollbar {
        display: none;
    }

    .container {
        max-width: 80%;
        margin: auto;
        background: #fff;
        padding: 0.2rem 2.5rem 0.2rem 2.5rem;
        border-radius: 8px;
        box-shadow: 0px 0px 10px 5px rgba(0, 0, 0, 0.1);
        margin-top: 6.5rem;
    }

    h2 {
        text-align: center;
        margin-top: 1.5rem;
        margin-bottom: 0.5rem;
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
        margin-right: 1rem;
    }

    .form-container {
        background: #cfd8e5;
        padding: 2rem 2rem 1rem 2rem;
        border-radius: 10px;
        margin: 1.5rem 0 1.3rem 0;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        justify-content: center;
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

    ul {
        list-style: none;
        padding-left: 15px;
        margin-left: 0;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    </style>
</head>

<body>
    <header class="header">
        <div class="left-section">
            <button type="button" class="menu-btn" id="menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
        <div class="right-section">
            <button type="button">
                <i class="fas fa-user mr-3"></i>
            </button>
            <form action="" method="POST">
                <button type="submit" name="logout">
                    <i class="fa-solid fa-lock mr-2"></i><span>Log Out</span>
                </button>
            </form>
        </div>
    </header>
    <div id="sidebar" class="sidebar">
        <a href="main.php">หน้าหลัก</a>
        <a href="sell_products.php">ขายสินค้า</a>
        <a href="record_products.php">บันทึกข้อมูลสินค้า</a>
        <a href="warehouse.php">คลังสินค้า</a>
        <a href="product_expired.php">ค้นหาสินค้าหมดอายุ</a>
        <a href="waste_stock.php">ตัดของเสียจากสต็อก</a>
        <a href="report_stock.php">รายงานสินค้าที่ตัดออกจากสต็อก</a>
        <a href="waste_chart.php">สถิติของเสีย</a>
        <a href="generate_QR.php">สร้างบาร์โค้ด</a>
    </div>

    <div class="container">
        <h2>ข้อมูลสินค้า</h2>
        <div class="button-group">
            <button class="delete-button">
                <i class="fa-solid fa-trash"></i>ลบข้อมูล
            </button>
            <!-- <button class="add-button">
                <i class="fa-solid fa-circle-plus"></i>เพิ่ม
            </button> -->
            <div class="import-button">
                <input type="file" id="uploadExcel" accept=".xlsx, .xls" class="d-none">
                <button id="uploadButton" class="btn btn-link">
                    <i class="fa-regular fa-file-excel"></i><br>
                    นำเข้าข้อมูลสินค้า
                </button>
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
                <label for="product_model">รุ่นการผลิต</label>
                <input type="text" id="product_model" name="product_model" required>
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
            <div class="form-row" style="position: relative; display: inline-block;">
                <label for="sticker_color" style="margin-right: 10px; margin-left: 5%;">สีสติ๊กเกอร์</label>
                <div style="position: relative; width: 100%;">
                    <input type="text" id="sticker_color" name="sticker_color" required
                        style="padding-right: 30px; width: 90%; box-sizing: border-box; margin-left: 5%; margin-right: 10px">
                    <i class="fa fa-info-circle" id="hint-icon"
                        style="position: absolute; top: 40%; right: 35px; transform: translateY(-50%); cursor: pointer; color: #666;"></i>
                </div>
                <div id="hint-box"
                    style="display: none; background: #f9f9f9; border: 1px solid #ccc; padding: 10px; border-radius: 5px; position: absolute; left: 60%; max-width: 300px; font-size: 14px;">
                    <ul style="list-style: none; padding-left: 10px; margin: 0;">
                        <li><span style="background-color: #E3D200; color: #000;">หมดอายุเดือน 1</span></li>
                        <li><span style="background-color: #00C4B4; color: #fff;">หมดอายุเดือน 2</span></li>
                        <li><span style="background-color: #EE700E; color: #fff;">หมดอายุเดือน 3</span></li>
                        <li><span style="background-color: #EA12B1; color: #fff;">หมดอายุเดือน 4</span></li>
                        <li><span style="background-color: #C8E9F0; color: #000;">หมดอายุเดือน 5</span></li>
                        <li><span style="background-color: #02A737; color: #fff;">หมดอายุเดือน 6</span></li>
                        <li><span style="background-color: #EAEAA2; color: #000;">หมดอายุเดือน 7</span></li>
                        <li><span style="background-color: #00A1CD; color: #fff;">หมดอายุเดือน 8</span></li>
                        <li><span style="background-color: #AA7964; color: #fff;">หมดอายุเดือน 9</span></li>
                        <li><span style="background-color: #F4D3DC; color: #000;">หมดอายุเดือน 10</span></li>
                        <li><span style="background-color: #B9F4A2; color: #000;">หมดอายุเดือน 11</span></li>
                        <li><span style="background-color: #FFFFFF; color: #000; border: 1px solid #ccc;">หมดอายุเดือน
                                12</span></li>
                        <li><span style="background-color: #999999; color: #fff;">ไม่มีวันหมดอายุ</span></li>
                    </ul>
                </div>
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

        <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editProductModalLabel">เลือกสินค้าที่ต้องการแก้ไข</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchProduct" class="form-control mb-3"
                            placeholder="ค้นหาโดยใช้รหัสสินค้า หรือ ชื่อสินค้า...">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>จำนวน</th>
                                    <th>เลือก</th>
                                </tr>
                            </thead>
                            <tbody id="productTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="deleteProductModal" tabindex="-1" aria-labelledby="deleteProductModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteProductModalLabel">เลือกสินค้าที่ต้องการลบ</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="searchDeleteProduct" class="form-control mb-3"
                            placeholder="ค้นหาโดยใช้รหัสสินค้า หรือ ชื่อสินค้า...">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>รหัสสินค้า</th>
                                    <th>ชื่อสินค้า</th>
                                    <th>จำนวน</th>
                                    <th>ลบ</th>
                                </tr>
                            </thead>
                            <tbody id="deleteProductTableBody">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
</body>
<script>
    const hintIcon = document.getElementById('hint-icon');
    const hintBox = document.getElementById('hint-box');

    // เมื่อคลิกที่ไอคอน ให้แสดงหรือซ่อน Hint Box
    hintIcon.addEventListener('click', () => {
        hintBox.style.display = hintBox.style.display === 'none' ? 'block' : 'none';
    });

    // เมื่อคลิกที่อื่น ให้ซ่อน Hint Box
    document.addEventListener('click', (event) => {
        if (!hintBox.contains(event.target) && event.target !== hintIcon) {
            hintBox.style.display = 'none';
        }
    });

document.getElementById("menu-toggle").addEventListener("click", function() {
    const sidebar = document.getElementById("sidebar");
    if (sidebar.style.left === "0px") {
        sidebar.style.left = "-250px";
    } else {
        sidebar.style.left = "0";
    }
});

$('.edit-button').click(function() {
    $('#editProductModal').modal('show');
    $('#productTableBody').empty();
    $('table').hide();
});

$('.delete-button').click(function() {
    $('#deleteProductModal').modal('show');
    $('#deleteProductTableBody').empty();
    $('table').hide();
});

$('#searchProduct').on('input', function() {
    let query = $(this).val().trim();

    if (query === '') {
        $('table').hide();
        $('#productTableBody').empty();
    } else {
        $('table').show();
        loadProducts(query);
    }
});

$('#searchDeleteProduct').on('input', function() {
    let query = $(this).val().trim();

    if (query === '') {
        $('table').hide();
        $('#deleteProductTableBody').empty();
    } else {
        $('table').show();
        loadProducts(query, 'delete');
    }
});

function loadProducts(query = '', action = 'edit') {
    $.ajax({
        url: 'record_products.php',
        type: 'GET',
        data: {
            query: query,
            action: 'fetch'
        },
        success: function(response) {
            try {
                let products = JSON.parse(response);
                let tableBody = action === 'delete' ? $('#deleteProductTableBody') : $('#productTableBody');
                tableBody.empty();

                if (products.error) {
                    alert(products.error);
                    $('table').hide();
                    return;
                }

                if (products.length > 0) {
                    products.forEach(product => {
                        let buttonHTML = action === 'delete' ?
                            `<button class="btn btn-danger delete-product" data-product-code="${product.product_code}">ลบ</button>` :
                            `<button class="btn btn-warning edit-product" data-product='${JSON.stringify(product)}'>แก้ไข</button>`;

                        tableBody.append(`
                            <tr>
                                <td>${product.product_code}</td>
                                <td>${product.product_name}</td>
                                <td>${product.quantity}</td>
                                <td>${buttonHTML}</td>
                            </tr>
                        `);
                    });
                    $('table').show();
                } else {
                    tableBody.append(`
                        <tr>
                            <td colspan="4" class="text-center text-danger">ไม่พบรายการ</td>
                        </tr>
                    `);
                    $('table').show();
                }

                $('.edit-product').off('click').on('click', function() {
                    let product = $(this).data('product');
                    populateForm(product);
                    $('#editProductModal').modal('hide');
                });

                $('.delete-product').off('click').on('click', function() {
                    let productCode = $(this).data('product-code');
                    deleteProduct(productCode);
                });

            } catch (e) {
                alert('เกิดข้อผิดพลาดในการแปลงข้อมูล: ' + e.message);
                $('table').hide();
            }
        },
        error: function(xhr, status, error) {
            alert('เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error);
            $('table').hide();
        }
    });
}

function deleteProduct(productCode) {
    if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสินค้านี้?')) {
        $.ajax({
            url: 'record_products.php',
            type: 'POST',
            data: {
                action: 'delete',
                product_code: productCode
            },
            success: function(response) {
                alert(response);
                refreshProductTable();
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการลบข้อมูล');
            }
        });
    }
}

function refreshProductTable() {
    $.ajax({
        url: 'record_products.php?action=fetch',
        type: 'GET',
        success: function(response) {
            var rows = '';
            var products = JSON.parse(response);

            $('#deleteProductTableBody').empty();
            products.forEach(function(product) {
                rows += '<tr data-product-code="' + product.product_code + '">' +
                    '<td>' + product.product_code + '</td>' +
                    '<td>' + product.product_name + '</td>' +
                    '<td>' + product.quantity + '</td>' +
                    '<td><button class="btn btn-danger" onclick="deleteProduct(\'' + product
                    .product_code + '\')">ลบ</button></td>' +
                    '</tr>';
            });

            $('#deleteProductTableBody').append(rows);
        },
        error: function() {
            alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
        }
    });
}

function populateForm(product) {
    $('#product_code').val(product.product_code).prop('readonly', true);
    $('#product_name').val(product.product_name);
    $('#product_model').val(product.product_model || '');
    $('#production_date').val(product.production_date || '');
    $('#shelf_life').val(product.shelf_life || '');
    $('#expiry_date').val(product.expiry_date || '');
    $('#sticker_color').val(product.sticker_color || '');
    $('#reminder_date').val(product.reminder_date || '');
    $('#received_date').val(product.received_date || '');
    $('#quantity').val(product.quantity);
    $('#unit').val(product.unit || '');
    $('#unit_cost').val(product.unit_cost || '');
    $('#sender_code').val(product.sender_code || '');
    $('#sender_company').val(product.sender_company || '');
    $('#recorder').val(product.recorder || '');
    $('#unit_price').val(product.unit_price || '');
    $('#category').val(product.category || '');
}

$(document).on('click', '.edit-product', function() {
    let product = $(this).data('product');
    populateForm(product);
    $('#editProductModal').modal('hide');
}); ///คืออะไร

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

document.getElementById('uploadButton').addEventListener('click', function() {
    document.getElementById('uploadExcel').click();
});

document.getElementById('uploadExcel').addEventListener('change', function(event) {
    let file = event.target.files[0];
    if (file) {
        importExcel();
    } else {
        alert('กรุณาเลือกไฟล์ Excel');
    }
});

function importExcel() {
    let fileInput = document.getElementById('uploadExcel');
    let file = fileInput.files[0];

    if (!file) {
        alert('กรุณาเลือกไฟล์ Excel ก่อน');
        return;
    }

    let reader = new FileReader();

    reader.onload = function(event) {
        let data = new Uint8Array(event.target.result);
        let workbook = XLSX.read(data, {
            type: 'array'
        });
        let firstSheetName = workbook.SheetNames[0];
        let worksheet = workbook.Sheets[firstSheetName];

        let jsonData = XLSX.utils.sheet_to_json(worksheet, {
            header: 1
        });

        postExcelDataToDatabase(jsonData);
    };

    reader.readAsArrayBuffer(file);
}

function formatDateToISO(dateString) {
    if (!dateString || typeof dateString !== 'string') {
        console.warn('Invalid date string:', dateString);
        return null;
    }

    const parts = dateString.split('-');
    if (parts.length === 3) {
        let day = parts[0].padStart(2, '0');
        let month = parts[1].padStart(2, '0');
        let year = parts[2].length === 2 ? '20' + parts[2] : parts[2];

        return `${year}-${month}-${day}`;
    }

    console.warn('Invalid date format:', dateString);
    return null;
}


function postExcelDataToDatabase(data) {
    if (data.length < 2) {
        alert('ไฟล์ไม่มีข้อมูลที่สามารถบันทึกได้');
        return;
    }

    function checkDuplicate(product_code) {
        return fetch(`record_products.php?action=check_duplicate&product_code=${encodeURIComponent(product_code)}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(text => {
                try {
                    return JSON.parse(text).exists;
                } catch (error) {
                    console.error('Error parsing JSON:', error, 'Response Text:', text);
                    throw error;
                }
            });
    }

    data.slice(1).forEach((row, index) => {
        let product_code = row[0] || '';

        if (!product_code) {
            alert(`แถวที่ ${index + 2} ไม่มี รหัสสินค้า`);
            return;
        }

        checkDuplicate(product_code).then(isDuplicate => {
            if (isDuplicate) {
                alert(`ข้อมูลซ้ำ: รหัสสินค้า "${product_code}" มีข้อมูลแล้ว`);
            } else {
                let formData = new FormData();
                formData.append('save', 'true');
                formData.append('product_code', product_code);
                formData.append('product_name', row[1] || '');
                formData.append('product_model', row[2] || '');
                formData.append('production_date', row[3] ? formatDateToISO(row[3]) : ''); // วันที่ผลิต
                formData.append('shelf_life', row[4] || '');
                formData.append('expiry_date', row[5] ? formatDateToISO(row[5]) : ''); // วันหมดอายุ
                formData.append('sticker_color', row[6] || '');
                formData.append('reminder_date', row[7] ? formatDateToISO(row[7]) :
                    ''); // เตือนล่วงหน้า
                formData.append('received_date', row[8] ? formatDateToISO(row[8]) : ''); // วันรับเข้า
                formData.append('quantity', row[9] || '');
                formData.append('unit', row[10] || '');
                formData.append('unit_cost', row[11] || '');
                formData.append('sender_code', row[12] || '');
                formData.append('sender_company', row[13] || '');
                formData.append('recorder', row[14] || '');
                formData.append('unit_price', row[15] || '');
                formData.append('category', row[16] || '');

                fetch('record_products.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(result => {
                        alert(`บันทึกสำเร็จ: รหัสสินค้า "${product_code}"`);
                    })
                    .catch(error => {
                        console.error(`เกิดข้อผิดพลาดกับ รหัสสินค้า "${product_code}":`, error);
                    });
            }
        });
    });
}
</script>

</html>