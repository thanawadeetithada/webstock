<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$user_logged_in = isset($_SESSION['username']) ? $_SESSION['username'] : null;
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
        <a href="sell_products.php">ขายสินค้า</a>
        <a href="#">บันทึกข้อมูลสินค้า</a>
        <a href="#">คลังสินค้า</a>
        <a href="#">ค้นหาสินค้าหมดอายุ</a>
        <a href="#">ตัดของเสียจากสต็อก</a>
        <a href="#">รายงานสินค้าที่ตัดออกจากสต็อก</a>
        <a href="#">สถิติของเสีย</a>
        <a href="#">สร้างบาร์โค้ด</a>
    </div>

    <script>
        document.getElementById("menu-toggle").addEventListener("click", function() {
            const sidebar = document.getElementById("sidebar");
            if (sidebar.style.left === "0px") {
                sidebar.style.left = "-250px";
            } else {
                sidebar.style.left = "0";
            }
        });
    </script>
</body>

</html>
