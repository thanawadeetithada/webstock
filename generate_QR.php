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
    <title>สร้างบาร์โค้ด</title>
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

    .show-section {
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
        width: 300px;
    }

    .dropdown-container label {
        font-weight: bold;
        margin-bottom: 5px;
        color: #003d99;
    }

    .center-button {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <div class="center-section">
                <div class="dropdown-container">
                    <label for="productCategory">รหัสสินค้าเดิม</label>
                    <input type="text" id="oldProductCode">
                </div>
                <div class="dropdown-container">
                    <label for="productCategory">วันหมดอายุ</label>
                    <input type="date" id="expiryDate">
                </div>
            </div>
        </div>
        <div class="center-button">
            <button type="button" class="btn btn-primary">สร้างบาร์โค้ด</button>
        </div>
        <div class="search-container">
            <div class="show-section">
                <div class="dropdown-container">
                    <input type="text" disabled placeholder="รหัสใหม่ของสินค้า" id="newProductCode">
                </div>
                <label>SHOW BAECODE</label>
            </div>
        </div>
    </div>
</body>

</html>