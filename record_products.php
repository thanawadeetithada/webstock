<?php
session_start();
include('include/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
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
        padding: 30px;
        border-radius: 10px;
        margin: 20px 0 50px 0;
        display: flex;
        flex-wrap: wrap;
        row-gap: 2rem;
        column-gap: 4rem;
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
        width: 12%;
        margin: 1rem;
    }

    .form-row {
        width: calc(29%);
        display: flex;
        flex-direction: column;
        margin-left: 15px;
    }

    .form-row label {
        margin-right: 10px;
        font-weight: bold;
        color: #003d99;
    }

    .form-row input,
    .form-row select {
        padding: 10px;
        margin-top: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        flex-shrink: 1;
        width: -webkit-fill-available;
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
                <input type="text" value="M123456">
            </div>
            <div class="form-row">
                <label>ชื่อสินค้า</label>
                <input type="text" placeholder="ชื่อสินค้า">
            </div>
            <div class="form-row">
                <label>รุ่นการผลิต</label>
                <div class="model-input">
                    <input type="date">
                </div>
            </div>
            <div class="form-row">
                <label>วันผลิต</label>
                <div class="date-input">
                    <input type="date">
                </div>
            </div>
            <div class="form-row">
                <label>อายุสินค้า(วัน)</label>
                <div class="shelf-life-input">
                    <input type="number">
                </div>
            </div>
            <div class="form-row">
                <label>วันหมดอายุ</label>
                <div class="date-input">
                    <input type="date">
                </div>
            </div>
            <div class="form-row">
                <label>สีสติ๊กเกอร์</label>
                <input type="text">
            </div>
            <div class="form-row">
                <label>เตือนล่วงหน้า</label>
                <input type="text" disabled>
            </div>
            <div class="form-row">
                <label>วันรับเข้า</label>
                <div class="date-input">
                    <input type="date">
                </div>
            </div>
            <div class="form-row">
                <label>จำนวน</label>
                <input type="number" placeholder="จำนวน">
            </div>
            <div class="form-row">
                <label>หน่วย</label>
                <input type="number">
            </div>
            <div class="form-row">
                <label>ราคาทุนต่อหน่วย(บาท)</label>
                <input type="number">
            </div>
            <div class="form-row">
                <label>รหัสผู้ส่ง</label>
                <input type="text">
            </div>
            <div class="form-row">
                <label>ชื่อบริษัทผู้ส่ง</label>
                <input type="text">
            </div>
            <div class="form-row">
                <label>ผู้บันทึกข้อมูล</label>
                <input type="text">
            </div>
            <div class="form-row">
                <label>ราคาขายต่อหน่วย(บาท)</label>
                <input type="number">
            </div>
            <div class="form-row">
                <label>หมวดหมู่สินค้า</label>
                <input type="text" placeholder="หมวดหมู่สินค้า">
            </div>
            <div class="submit-button">
                <button class="btn btn-primary">บันทึกข้อมูล</button>
                <button class="btn btn-secondary">ล้างข้อมูล</button>
            </div>
        </div>

        <div class="form-container">
            <h3>โปรดตรวจสอบข้อมูล</h3>
            <div class="form-row">
                <label>รหัสสินค้า</label>
            </div>
            <div class="form-row">
                <label>ชื่อสินค้า</label>
            </div>
            <div class="form-row">
                <label>รุ่นการผลิต</label>
            </div>
            <div class="form-row">
                <label>วันผลิต</label>
            </div>
            <div class="form-row">
                <label>อายุสินค้า(วัน)</label>
            </div>
            <div class="form-row">
                <label>วันหมดอายุ</label>
            </div>
            <div class="form-row">
                <label>สีสติ๊กเกอร์</label>
            </div>
            <div class="form-row">
                <label>เตือนล่วงหน้า</label>
            </div>
            <div class="form-row">
                <label>วันรับเข้า</label>
            </div>
            <div class="form-row">
                <label>จำนวน</label>
            </div>
            <div class="form-row">
                <label>หน่วย</label>
            </div>
            <div class="form-row">
                <label>ราคาทุนต่อหน่วย</label>
            </div>
            <div class="form-row">
                <label>รหัสผู้ส่ง</label>
            </div>
            <div class="form-row">
                <label>ชื่อบริษัทผู้ส่ง</label>
            </div>
            <div class="form-row">
                <label>ผู้บันทึกข้อมูล</label>
            </div>
            <div class="form-row">
                <label>ราคาขายต่อหน่วย(บาท)</label>
            </div>
            <div class="form-row">
                <label>หมวดหมู่สินค้า</label>
            </div>
            <div class="submit-button">
                <button class="btn btn-success">ยืนยันข้อมูลสินค้า</button>
                <button class="btn btn-warning">แก้ไข</button>
                <button class="btn btn-danger">ยกเลิก</button>
            </div>
        </div>
    </div>
</body>

</html>