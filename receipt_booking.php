<?php
session_start();
include 'db.php';

if (!isset($_GET['invoice_id'])) {
    die("ไม่พบหมายเลขใบแจ้งชำระเงิน");
}

$booking_id = $_GET['invoice_id'];
$userrole = $_SESSION['userrole'];
$stmt = $conn->prepare("SELECT * FROM invoice WHERE invoice_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    die("ไม่พบข้อมูลการจอง");
}

$invoice_items = [$invoice];
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ใบเสร็จ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <style>
    body {
        font-family: 'Prompt', sans-serif;
        padding: 0;
        background-color: #f4f4f4;
        display: flex;
        flex-direction: column;
        min-height: 100vh;
        margin: 0;
    }

    .content-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-grow: 1;
    }

    .invoice-container {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        max-width: 700px;
        margin: 20px;
        width: 100%;
    }

    h2,
    h3 {
        text-align: center;
    }

    .invoice-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .customer-info {
        padding: 10px;
        background: #f9f9f9;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    .table-container {
        width: 100%;
        border-collapse: collapse;
    }

    .table-container th,
    .table-container td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }

    .table-container th:first-child,
    .table-container td:first-child {
        text-align: left;
    }

    .table-container th {
        background: #e0e0e0;
    }

    .total-section {
        text-align: right;
        margin-top: 20px;
    }

    .total-section p {
        font-size: 16px;
        display: flex;
        justify-content: flex-end;
        gap: 40px;
        width: 100%;
        margin: 5px 0;
    }

    .total-section .amount {
        min-width: 80px;
        text-align: right;
        display: inline-block;
    }

    .status {
        font-weight: bold;
        color: red;
    }
    .nav-item a {
        color: white;
        margin-right: 1rem;
    }

    .navbar {
        padding: 20px;
    }

    .nav-link:hover {
        color: white;
    }
    </style>
</head>

<body>

    <body>
        <?php if ($userrole === 'admin'): ?>
        <nav class="navbar navbar-dark bg-dark px-3">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <i class="fa-solid fa-bars text-white" data-bs-toggle="offcanvas" data-bs-target="#sidebarMenu"
                    style="cursor: pointer;"></i>
                <div class="nav-item">
                    <a class="nav-link" href="logout.php"><i class="fa-solid fa-user"></i>&nbsp;&nbsp;Logout</a>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-start bg-dark text-white" tabindex="-1" id="sidebarMenu">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title">รายการ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="list-unstyled">
                    <li><a href="admin_dashboard.php" class="text-white text-decoration-none d-block py-2"><i
                                class="fa-solid fa-chart-line"></i> หน้าหลัก</a></li>
                    <li><a href="add_room.php" class="text-white text-decoration-none d-block py-2"><i
                                class="fa-regular fa-money-bill-1"></i> ตั้งค่าราคาห้องพัก</a></li>
                    <li><a href="dashboard_room.php" class="text-white text-decoration-none d-block py-2"><i
                                class="fa-solid fa-bed"></i> รายละเอียดห้องพัก</a></li>
                    <li><a href="dashboard_user.php" class="text-white text-decoration-none d-block py-2"><i
                                class="fa-solid fa-user"></i> รายชื่อลูกค้า</a></li>
                    <li><a href="dashboard_booking.php" class="text-white text-decoration-none d-block py-2"><i
                                class="fa-solid fa-suitcase"></i> สถานะการจอง</a></li>
                    <li>
                        <a href="view_messages.php" class="text-white text-decoration-none d-block py-2">
                            <i class="fa-solid fa-comment"></i> ข้อความจากผู้ใช้งาน
                            <span id="notification-badge" class="badge bg-danger" style="display: none;"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <?php endif; ?>

        <main class="content-wrapper">
            <div class="invoice-container">
                <br><br>
                <h2>ใบเสร็จ</h2>
                <br><br>
                <p style="text-align: right;">เลขที่ใบเสร็จ <?php echo htmlspecialchars($invoice['invoice_number']); ?>
                </p>
                <p style="text-align: right;">วันที่ชำระเงิน
                   <?php 
    $date = new DateTime($booking['created_at']);
    $date->modify('+12 hours'); // เพิ่ม 15 ชั่วโมง
    echo $date->format("d-m-Y เวลา H:i"); 
?>

                </p>

                <div class="customer-info">
                    <strong>ข้อมูลลูกค้า</strong><br>
                    ชื่อ นามสกุล
                    <?php echo htmlspecialchars($invoice['first_name'] . " " . $invoice['last_name']); ?><br>
                </div>

                <table class="table-container">
                    <tr>
                        <th>ประเภทที่พัก x คืน</th>
                        <th>จำนวนคน</th>
                        <th>ราคา</th>
                        <th>รวม</th>
                    </tr>
                    <?php
                $total = 0;
                foreach ($invoice_items as $item):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['room_type'] . " (เลขห้อง " . $item['room_number'] . ") x " . $item['nights'] . " คืน"); ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['guest_count']); ?></td>
                        <td>฿<?php echo number_format($item['price'], 2); ?></td>
                        <td>฿<?php echo htmlspecialchars($item['total_amount']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
                <br>
                <div class="total-section">
                    <p><strong>ยอดรวม:</strong> <span
                            class="amount">฿<?php echo number_format($item['total_amount'], 2); ?></span></p>
                    <p><strong>ภาษี (7%):</strong> <span
                            class="amount">฿<?= number_format($item['paid_amount'] - $item['total_amount'], 2) ?></span>
                    </p>

                    <br>

                    <p><strong>ยอดรวมสุทธิ:</strong> <span
                            class="amount">฿<?php echo number_format($item['paid_amount'], 2); ?></span></p>
                    <p><strong>ยอดชำระ:</strong> <span
                            class="amount">฿<?php echo number_format($item['paid_amount'], 2); ?></span></p>

                    <?php if ($item['status_payment'] === 'paid'): ?>
                    <p class="status paid">จ่ายแล้ว</p>
                    <?php elseif ($item['status_payment'] === 'pending'): ?>
                    <p class="status pending">รอชำระเงิน</p>
                    <?php else: ?>
                    <p class="status unknown">สถานะไม่ทราบ</p>
                    <?php endif; ?>
                </div>

                <?php if ($userrole === 'user'): ?>
                <div class="text-center mt-4">
                    <a href="service.php" class="btn btn-primary">กลับไปที่การจอง</a>
                </div>
                <?php endif; ?>
            </div>
        </main>
        <script>
        function checkNotifications() {
            fetch('check_notifications.php')
                .then(response => response.json())
                .then(data => {
                    console.log("Notification Data:", data);
                    let notificationBadge = document.getElementById("notification-badge");
                    if (data.unread_count > 0) {
                        notificationBadge.innerText = data.unread_count;
                        notificationBadge.style.display = "inline-block";
                    } else {
                        notificationBadge.style.display = "none";
                    }
                })
                .catch(error => console.error("Error fetching notifications:", error));
        }

        setInterval(checkNotifications, 1000);
        checkNotifications();
        </script>
    </body>

</html>