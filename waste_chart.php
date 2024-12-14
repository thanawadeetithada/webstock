<?php
session_start();
include 'include/header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// นับจำนวนสินค้าในฐานข้อมูลที่มี expiry_date น้อยกว่าวันนี้
$sql_count = "SELECT COUNT(*) AS total_waste 
              FROM products 
              WHERE expiry_date < CURDATE()";
$result_count = $conn->query($sql_count);

if ($result_count->num_rows > 0) {
    $row_count = $result_count->fetch_assoc();
    $total_waste = $row_count['total_waste'];
} else {
    $total_waste = 0;
}

// คำนวณมูลค่าของเสียทั้งหมด (ราคาทุนรวม)
$sql_value = "SELECT SUM(unit_price) AS total_value 
              FROM products 
              WHERE expiry_date < CURDATE()"; // เงื่อนไข expiry_date < วันนี้
$result_value = $conn->query($sql_value);

if ($result_value->num_rows > 0) {
    $row_value = $result_value->fetch_assoc();
    $total_value = $row_value['total_value']; // เก็บมูลค่ารวมที่ได้จากฐานข้อมูล
} else {
    $total_value = 0; // กรณีไม่มีข้อมูล
}

// ดึงข้อมูลปี
$sql = "SELECT DISTINCT YEAR(expiry_date) AS expiry_year FROM products ORDER BY expiry_year DESC";
$result = $conn->query($sql);

$years = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $years[] = $row['expiry_year'];
    }
}

// ดึงข้อมูลเดือน
$sql = "SELECT DISTINCT MONTH(expiry_date) AS expiry_month FROM products ORDER BY expiry_month";
$result = $conn->query($sql);

$months = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $months[] = $row['expiry_month'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติของเสีย</title>
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
        justify-content: center;
        align-items: center;
        margin-bottom: 30px;
        width: 100%;
    }

    .dropdown-container {
        margin: 0;
        display: flex;
        flex-direction: column;
        font-family: Arial, sans-serif;
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
        width: 150px;
        outline: none;
    }

    .center-section button {
        padding: 20px;
    }

    .center-section button p {
        margin-bottom: 5px;
    }

    .center-section {
        display: flex;
        align-items: flex-end;
        gap: 2rem;
    }

    .btn.disabled,
    .btn:disabled {
        opacity: 1;
    }
    </style>
</head>
<body>
<div class="container">
        <div class="search-container">
            <div class="center-section">
                <div class="dropdown-container">
                    <label for="year">ของเสียในแต่ละปี</label>
                    <form>
                        <select name="year">
                            <?php
foreach ($years as $year) {
    echo "<option value=\"$year\">$year</option>";
}
?>
                        </select>
                    </form>
                </div>

                <div class="dropdown-container">
                    <label for="month">ของเสียในแต่ละเดือน</label>
                    <form>
                        <select name="month">
                            <?php
$month_names = [
    1 => "มกราคม",
    2 => "กุมภาพันธ์",
    3 => "มีนาคม",
    4 => "เมษายน",
    5 => "พฤษภาคม",
    6 => "มิถุนายน",
    7 => "กรกฎาคม",
    8 => "สิงหาคม",
    9 => "กันยายน",
    10 => "ตุลาคม",
    11 => "พฤศจิกายน",
    12 => "ธันวาคม",
];
foreach ($months as $month) {
    echo "<option value=\"$month\">{$month_names[$month]}</option>";
}
?>
                        </select>
                    </form>
                </div>

                <button type="button" disabled class="btn btn-outline-danger">
                    <h5>จำนวนของเสียทั้งหมด</h5>
                    <h5><?php echo $total_waste; ?></h5>
                </button>
                <button type="button" disabled class="btn btn-outline-info">
                    <h5>มูลค่าของเสียทั้งหมด</h5>
                    <p>ราคาทุนรวม</p>
                    <h5><?php echo number_format($total_value, 2); ?> บาท</h5>
                </button>
                <button type="button" disabled class="btn btn-outline-success">
                    <h5>มูลค่ากำไร</h5>
                    <p>ขาย-ทุน</p>
                    <h5><?php echo number_format($total_value, 2); ?> บาท</h5>
                </button>

            </div>
        </div>
        <h3 class="text-center">จำนวนของเสีย</h3>
        <canvas id="wasteChart" width="400" height="200"></canvas>
    </div>
</body>
</html>
