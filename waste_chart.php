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

$months = range(1, 12); // [1, 2, 3, ..., 12]

//ดึงข้อมูลที่นับจำนวนสินค้าตาม category และ status ที่ต้องการ กราฟ
$sql_category = "SELECT category,
                        SUM(CASE WHEN status = 'sell' THEN 1 ELSE 0 END) AS sell_count,
                        SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) AS expired_count
                 FROM products
                 WHERE status IN ('sell', 'expired')
                 GROUP BY category";

$result_category = $conn->query($sql_category);

$categories = [];
$sell_counts = [];
$expired_counts = [];

if ($result_category->num_rows > 0) {
    while ($row = $result_category->fetch_assoc()) {
        $categories[] = $row['category']; // เก็บชื่อ category
        $sell_counts[] = $row['sell_count']; // จำนวนสินค้าสถานะ 'sell'
        $expired_counts[] = $row['expired_count']; // จำนวนสินค้าสถานะ 'expired'
    }
}

// กำหนดสีสำหรับแต่ละ category
$category_colors = [
    "ของใช้" => "#33FF33", // ของใช้
    "เครื่องปรุงรส" => "#FFFF33", // เครื่องปรุงรส
    "ขนม/เครื่องดื่ม" => "#007FFF", // ขนม/เครื่องดื่ม
    "อาหาร" => "#FF33FF", // อาหาร
];

// สร้างอาร์เรย์สีสำหรับกราฟ
$background_colors_sell = [];
$border_colors_sell = [];
$background_colors_expired = [];
$border_colors_expired = [];

foreach ($categories as $category) {
    // กำหนดสีให้กับแต่ละ category สำหรับ 'sell'
    if (array_key_exists($category, $category_colors)) {
        $background_colors_sell[] = $category_colors[$category];
        $border_colors_sell[] = $category_colors[$category];
    } else {
        // ถ้าไม่พบหมวดหมู่ในอาร์เรย์ ก็สามารถกำหนดสีที่ default ได้
        $background_colors_sell[] = '#CCCCCC'; // สีเทา
        $border_colors_sell[] = '#CCCCCC'; // สีเทา
    }

    // กำหนดสีให้กับแต่ละ category สำหรับ 'expired'
    if (array_key_exists($category, $category_colors)) {
        $background_colors_expired[] = $category_colors[$category];
        $border_colors_expired[] = $category_colors[$category];
    } else {
        // ถ้าไม่พบหมวดหมู่ในอาร์เรย์ ก็สามารถกำหนดสีที่ default ได้
        $background_colors_expired[] = '#CCCCCC'; // สีเทา
        $border_colors_expired[] = '#CCCCCC'; // สีเทา
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

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
                    <label for="year">ของเสียในแต่ละปี</label>
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
                    <label for="month">ของเสียในแต่ละเดือน</label>
                    <form>
                        <select name="month" id="monthDropdown">
                            <option value="">ทั้งหมด</option>
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
                    <h5>จำนวนของเสีย</h5>
                    <h5>ทั้งหมด</h5>
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
    // ดึง dropdown ของปีและเดือน
    const yearDropdown = document.getElementById('yearDropdown');
    const monthDropdown = document.getElementById('monthDropdown');

    // ฟังก์ชันสำหรับดึงข้อมูลกราฟ
    function fetchData() {
        const selectedYear = yearDropdown.value;
        const selectedMonth = monthDropdown.value;

        let url = 'fetch_waste_data.php?';
        if (selectedYear) url += `year=${selectedYear}&`;
        if (selectedMonth) url += `month=${selectedMonth}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                // อัปเดตข้อมูลในกราฟ
                wasteChart.data.labels = data.categories;
                wasteChart.data.datasets[0].data = data.sell_counts;
                wasteChart.data.datasets[1].data = data.expired_counts;

                wasteChart.update(); // อัปเดตกราฟ
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    // เพิ่ม Event Listener ให้ dropdown ปีและเดือน
    yearDropdown.addEventListener('change', fetchData);
    monthDropdown.addEventListener('change', fetchData);

    var ctx = document.getElementById('wasteChart').getContext('2d');
    var wasteChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($categories); ?>, // หมวดหมู่ที่ได้จากฐานข้อมูล
            datasets: [{
                    label: 'จำนวนสินค้าสถานะการขาย',
                    data: <?php echo json_encode($sell_counts); ?>, // จำนวนสินค้าสถานะ sell
                    backgroundColor: <?php echo json_encode($background_colors_sell); ?>, // สีตามแต่ละ category สำหรับ 'sell'
                    borderColor: <?php echo json_encode($border_colors_sell); ?>, // สีขอบตามแต่ละ category สำหรับ 'sell'
                    borderWidth: 1
                },
                {
                    label: 'จำนวนสินค้าสถานะหมดอายุ',
                    data: <?php echo json_encode($expired_counts); ?>, // จำนวนสินค้าสถานะ expired
                    backgroundColor: <?php echo json_encode($background_colors_expired); ?>, // สีตามแต่ละ category สำหรับ 'expired'
                    borderColor: <?php echo json_encode($border_colors_expired); ?>, // สีขอบตามแต่ละ category สำหรับ 'expired'
                    borderWidth: 1
                }
            ]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                display: false // ซ่อน legend (คำอธิบายชุดข้อมูล)
            },
                datalabels: {
                    anchor: 'center', // ตำแหน่งการแสดง (start, end, center)
                    align: 'center', // จัดตำแหน่งตามแกน (start, end, center)
                    color: '#000', // สีตัวเลข
                    font: {
                        weight: 'bold',
                        size: 18
                    },
                    formatter: function(value) {
                        return value; // แสดงค่าตัวเลขตรง ๆ
                    }
                }
            }
        },
        plugins: [ChartDataLabels] // เปิดใช้งาน ChartDataLabels
    });
    </script>

</body>

</html>