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
        gap: 60px;
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
                        <?php
                            $currentYear = date("Y");
                            $startYear = 2000;
                            $endYear = $currentYear + 10;

                            echo '<select name="year" id="year">';
                            for ($year = $startYear; $year <= $endYear; $year++) {
                                echo '<option value="' . $year . '">' . $year . '</option>';
                            }
                                echo '</select>';
                        ?>
                    </form>
                </div>

                <div class="dropdown-container">
                    <label for="month">ของเสียในแต่ละเดือน</label>
                    <form>
                        <?php
                           $months = [
                            '01' => 'มกราคม', 
                            '02' => 'กุมภาพันธ์', 
                            '03' => 'มีนาคม', 
                            '04' => 'เมษายน', 
                            '05' => 'พฤษภาคม', 
                            '06' => 'มิถุนายน', 
                            '07' => 'กรกฎาคม', 
                            '08' => 'สิงหาคม', 
                            '09' => 'กันยายน', 
                            '10' => 'ตุลาคม', 
                            '11' => 'พฤศจิกายน', 
                            '12' => 'ธันวาคม'
                            ];

                                echo '<select name="month" id="month">';
                            foreach ($months as $monthNumber => $monthName) {
                                echo '<option value="' . $monthNumber . '">' . $monthName . '</option>';
                            }
                                echo '</select>';
                        ?>
                    </form>
                </div>

                <button type="button" disabled class="btn btn-outline-danger">
                    <h5>จำนวนของเสียทั้งหมด</h5>
                    <h5>798</h5>
                </button>
                <button type="button" disabled class="btn btn-outline-info">
                    <h5>มูลค่าของเสียทั้งหมด</h5>
                    <p>ราคาทุนรวม</p>
                    <h5>1,035 บาท</h5>
                </button>
                <button type="button" disabled class="btn btn-outline-success">
                    <h5>มูลค่ากำไร</h5>
                    <p>ขาย-ทุน</p>
                    <h5>5,036 บาท</h5>
                </button>

            </div>
        </div>

        <label>SHOW chart</label>
    </div>
</body>

</html>