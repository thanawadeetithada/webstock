<?php
require 'config.php'; // เชื่อมต่อฐานข้อมูล

if (session_status() === PHP_SESSION_NONE) {
    session_start(); // เริ่ม session หากยังไม่มี
}

// ตรวจสอบว่า username อยู่ใน session หรือไม่
$username = $_SESSION['username'] ?? null;

// ดึงค่า telegram_chat_id จากฐานข้อมูล
$chat_id = null; // ค่าเริ่มต้น
if ($username) {
    $query = "SELECT telegram_chat_id FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $chat_id = $row['telegram_chat_id'];
    }
    $stmt->close();
}

// ตรวจสอบหากคำขอเป็น AJAX ไม่ให้โหลด HTML
if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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

    header button {
        background-color: transparent;
        padding: 0px;
        border: 0px;
        color: white;
        outline: none !important;
    }

    header i {
        font-size: 1.5rem;
    }

    header span {
        font-size: larger;
    }

    header .right-section {
        display: flex;
    }

    header .sidebar {
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

    header .sidebar a {
        display: block;
        padding: 10px 15px;
        text-decoration: none;
        color: white;
        font-size: 16px;
        border-bottom: 1px solid #444;
    }

    header .sidebar a:hover {
        background-color: #BFBBBA;
        color: #333;
    }

    header .sidebar-btn {
        position: absolute;
        top: 20px;
        left: 20px;
        color: white;
    }

    header .menu-btn {
        font-size: 1.5rem;
    }

    header .content {
        transition: margin-left .3s;
        padding: 20px;
        height: 92vh;
        margin-top: 8vh;
    }

    .notification-btn {
        position: relative;
        background: transparent;
        border: none;
        color: white;
        cursor: pointer;
        padding: 0;
        margin-right: 15px;
        outline: none;
    }

    .notification-btn i {
        font-size: 1.5rem;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: red;
        color: white;
        font-size: 0.8rem;
        padding: 2px 6px;
        border-radius: 50%;
        font-weight: bold;
        line-height: 1;
    }

    .notification-btn:hover .notification-badge {
        background-color: #ff6363;
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
            <button type="button" class="notification-btn" id="send-notification" 
                data-chat-id="<?php echo htmlspecialchars($chat_id ?? ''); ?>">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">0</span>
            </button>
            <button type="button">
                <i class="fas fa-user mr-3"></i>
            </button>
            <form action="" method="POST">
                <button type="submit" name="logout">
                    <i class="fa-solid fa-lock mr-2"></i><span>Log Out</span>
                </button>
            </form>
        </div>

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
    </header>

    <script>
    document.getElementById("menu-toggle").addEventListener("click", function() {
        const sidebar = document.getElementById("sidebar");
        if (sidebar.style.left === "0px") {
            sidebar.style.left = "-250px";
        } else {
            sidebar.style.left = "0";
        }
    });

    document.getElementById("send-notification").addEventListener("click", function () {
        const BOT_TOKEN = "8059073921:AAHnfXZ_PqsGsgwtetimOkTuvH1KgbR-v9k";
        const chat_id = this.dataset.chatId; // ดึง Chat ID จาก data attribute
        const MESSAGE = "Hi! แจ้งเตือนจากระบบสำเร็จ";
        console.log('chat ID:', chat_id);

        if (!chat_id) {
            alert("ไม่มี Chat ID สำหรับผู้ใช้นี้");
            return;
        }

        // ส่งข้อความไปยัง Telegram API
        const url = `https://api.telegram.org/bot${BOT_TOKEN}/sendMessage?chat_id=${chat_id}&text=${encodeURIComponent(MESSAGE)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.ok) {
                    alert("ส่งข้อความสำเร็จ!");
                } else {
                    alert("การส่งข้อความล้มเหลว: " + data.description);
                }
            })
            .catch(error => {
                console.error("Error sending notification:", error);
            });
    });
    </script>
</body>
</html>
<?php
}
?>