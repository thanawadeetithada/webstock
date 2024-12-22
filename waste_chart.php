<?php
session_start();
include 'include/header.php';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$current_year = date("Y"); // หาปีปัจจุบันใน PHP
$year_sql = "
    SELECT DISTINCT YEAR(expiration_date) AS year 
    FROM products
    WHERE YEAR(expiration_date) <= $current_year
    UNION
    SELECT DISTINCT YEAR(sell_date) AS year 
    FROM sell_product_details
    WHERE YEAR(sell_date) <= $current_year
    ORDER BY year DESC
";
$year_result = $conn->query($year_sql);
$years = [];
if ($year_result->num_rows > 0) {
    while ($row = $year_result->fetch_assoc()) {
        $years[] = $row['year'];
    }
}


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

$all_categories = ['ขนม/เครื่องดื่ม', 'ของใช้', 'อาหาร', 'เครื่องปรุงรส'];

$sell_data_sql = "
    SELECT category, COUNT(*) AS total
    FROM sell_product_details
    GROUP BY category
";
$sell_data_result = $conn->query($sell_data_sql);

// เติมข้อมูลให้ครบทุก category
$sell_data = [];
foreach ($all_categories as $category) {
    $sell_data[$category] = 0; // ตั้งค่าเริ่มต้นเป็น 0
}
if ($sell_data_result->num_rows > 0) {
    while ($row = $sell_data_result->fetch_assoc()) {
        $sell_data[$row['category']] = $row['total']; // อัปเดตข้อมูลที่มีอยู่จริง
    }
}

$expiration_data_sql = "
    SELECT category, COUNT(*) AS total
    FROM products
    WHERE expiration_date < CURDATE()
    GROUP BY category
";
$expiration_data_result = $conn->query($expiration_data_sql);

// เติมข้อมูลให้ครบทุก category
$expiration_data = [];
foreach ($all_categories as $category) {
    $expiration_data[$category] = 0; // ตั้งค่าเริ่มต้นเป็น 0
}
if ($expiration_data_result->num_rows > 0) {
    while ($row = $expiration_data_result->fetch_assoc()) {
        $expiration_data[$row['category']] = $row['total']; // อัปเดตข้อมูลที่มีอยู่จริง
    }
}


$expired_count_sql = "
    SELECT COUNT(*) AS total_expired
    FROM products
    WHERE expiration_date < CURDATE()
";

$expired_count_result = $conn->query($expired_count_sql);
$total_expired = 0; // ค่าเริ่มต้น
if ($expired_count_result->num_rows > 0) {
    $row = $expired_count_result->fetch_assoc();
    $total_expired = $row['total_expired'];
}

$expired_cost_sql = "
    SELECT SUM(unit_cost) AS total_expired_cost
    FROM products
    WHERE expiration_date < CURDATE()
";

$expired_cost_result = $conn->query($expired_cost_sql);
$total_expired_cost = 0; // ค่าเริ่มต้น
if ($expired_cost_result->num_rows > 0) {
    $row = $expired_cost_result->fetch_assoc();
    $total_expired_cost = $row['total_expired_cost'] ?? 0;
}

$profit_sql = "
    SELECT SUM(unit_price - unit_cost) AS total_profit
    FROM sell_product_details
";

$profit_result = $conn->query($profit_sql);
$total_profit = 0; // ค่าเริ่มต้น
if ($profit_result->num_rows > 0) {
    $row = $profit_result->fetch_assoc();
    $total_profit = $row['total_profit'] ?? 0;
}

echo json_encode([
    'sell_totals' => array_values($sell_data),
    'expiration_totals' => array_values($expiration_data),
    'total_expired' => $total_expired, // เพิ่มค่า total_expired
]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สถิติของเสีย</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 0px 20px 10px 20px;
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
        padding: 10px 10px 0px 10px;
    }

    .legend-container {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 16px;
    }

    .legend-color {
        width: 1.5rem;
        height: 1rem;
        display: inline-block;
        border-radius: 3px;
    }

    ::-webkit-scrollbar {
        display: none;
    }
    </style>
</head>

<body>
    <div class="container">
        <div class="search-container">
            <div class="center-section">
                <div class="dropdown-container">
                    <label for="yearDropdown">ของเสียในแต่ละปี</label>
                    <form>
                        <select name="year" id="yearDropdown">
                            <option value="">ทั้งหมด</option>
                            <?php
foreach ($years as $year) {
    echo "<option value=\"$year\">$year</option>";
}
?>
                        </select>
                    </form>
                </div>

                <div class="dropdown-container">
                    <label for="monthDropdown">ของเสียในแต่ละเดือน</label>
                    <form>
                        <select name="month" id="monthDropdown">
                            <option value="">ทั้งหมด</option>
                            <?php
foreach ($month_names as $month_number => $month_name) {
    echo "<option value=\"$month_number\">$month_name</option>";
}
?>
                        </select>
                    </form>
                </div>

                <button type="button" disabled class="btn btn-outline-danger">
                    <h5>จำนวนของเสีย</h5>
                    <h5>ทั้งหมด</h5>
                    <h5 id="totalExpired"><?php echo $total_expired; ?></h5>
                </button>
                <button type="button" disabled class="btn btn-outline-info">
                    <h5>มูลค่าของเสียทั้งหมด</h5>
                    <p>ราคาทุนรวม</p>
                    <h5><?php echo number_format($total_expired_cost, 2); ?> บาท</h5>
                </button>
                <button type="button" disabled class="btn btn-outline-success">
                    <h5>มูลค่ากำไร</h5>
                    <p>ขาย-ทุน</p>
                    <h5><?php echo number_format($total_profit, 2); ?> บาท</h5>
                </button>

            </div>
        </div>
        <h3 class="text-center">จำนวนของเสีย</h3>
        <div class="legend-container">
            <div class="legend-item">
                <span class="legend-color" style="background-color: #007FFF;"></span>
                <span>ขนม/เครื่องดื่ม</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #33FF33;"></span>
                <span>ของใช้</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #FF33FF;"></span>
                <span>อาหาร</span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #FFFF33;"></span>
                <span>เครื่องปรุงรส</span>
            </div>
        </div>
        <canvas id="wasteChart" width="350" height="130"></canvas>
    </div>

    <script>
    const data = <?php echo json_encode([
    'categories' => $all_categories,
    'sell_totals' => array_values($sell_data),
    'expiration_totals' => array_values($expiration_data),
]); ?>;

    const categories = data.categories;
    const sellTotals = data.sell_totals;
    const expirationTotals = data.expiration_totals;

    // กำหนดสีให้แต่ละหมวดหมู่
    const categoryColors = {
        'ขนม/เครื่องดื่ม': '#007FFF',
        'ของใช้': '#33FF33',
        'อาหาร': '#FF33FF',
        'เครื่องปรุงรส': '#FFFF33'
    };

    // แปลงสีของแต่ละหมวดหมู่
    const backgroundColors = categories.map(category => categoryColors[category] || '#CCCCCC');

    const ctx = document.getElementById('wasteChart').getContext('2d');
    const chart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categories,
            datasets: [{
                    label: 'จำนวนขาย',
                    data: sellTotals,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1
                },
                {
                    label: 'จำนวนหมดอายุ',
                    data: expirationTotals,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors,
                    borderWidth: 1
                }
            ],
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'จำนวน',
                    }
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    document.getElementById("yearDropdown").addEventListener("change", function() {
        fetchData();
    });

    document.getElementById("monthDropdown").addEventListener("change", function() {
        fetchData();
    });


    function fetchData() {
        const selectedYear = document.getElementById("yearDropdown").value;
        const selectedMonth = document.getElementById("monthDropdown").value;

        let url = 'fetch_waste_data.php?';
        if (selectedYear) url += `year=${selectedYear}&`;
        if (selectedMonth) url += `month=${selectedMonth}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                console.log("Fetched data:", data);

                // อัปเดตข้อมูลในกราฟ
                updateChart(chart, data.categories, data.sell_totals, data.expiration_totals);
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function updateChart(chart, categories, sellTotals, expirationTotals) {
        chart.data.labels = categories;
        chart.data.datasets[0].data = sellTotals; // อัปเดตข้อมูลจำนวนขาย
        chart.data.datasets[1].data = expirationTotals; // อัปเดตข้อมูลจำนวนหมดอายุ
        chart.update(); // ทำให้กราฟรีเฟรชข้อมูลใหม่
    }

    document.getElementById("yearDropdown").addEventListener("change", fetchData);
    document.getElementById("monthDropdown").addEventListener("change", fetchData);
    </script>

</body>

</html>