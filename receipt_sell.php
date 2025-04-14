<?php
session_start();
include 'config.php';

// date_default_timezone_set('Asia/Bangkok');
$invoice_id = $_GET['invoice_id'] ?? null;

if (!$invoice_id) {
    die("ไม่พบข้อมูลใบเสร็จ");
}

// ดึงข้อมูลใบเสร็จจากฐานข้อมูล
$sql = "SELECT * FROM invoice WHERE invoice_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

if (!$invoice) {
    die("ไม่พบข้อมูลใบเสร็จ");
}

$datetime = new DateTime($invoice['issue_date'], new DateTimeZone('UTC'));
$datetime->modify('+14 hours');
$payment_date = $datetime->format("d/m/Y H:i:s");

// ดึงข้อมูลสินค้าจาก sell_product_details
$sell_id = $invoice['sell_id'];
$sql_details = "SELECT * FROM sell_product_details WHERE sell_id = ?";
$stmt_details = $conn->prepare($sql_details);
$stmt_details->bind_param("i", $sell_id);
$stmt_details->execute();
$details_result = $stmt_details->get_result();
$products = $details_result->fetch_all(MYSQLI_ASSOC);

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

    h2 {
        text-align: center;
    }

    .table-container {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .table-container th, .table-container td {
        padding: 10px;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }

    .table-container th {
        background: #e0e0e0;
    }

    .total-section {
        text-align: right;
        margin-top: 20px;
        font-size: 18px;
    }

    .btn-back {
        display: block;
        width: 100%;
        text-align: center;
        margin-top: 20px;
    }

    .btn {
        display: flex;
        justify-content: center;
        width: auto;
    }

    #printExcelBtn {
        margin-right: 15px;
    }

    
    @media print {
        .btn {
            display: none;
        }
    }
    
    </style>
</head>

<body>
    <main class="content-wrapper">
        <div class="invoice-container">
            <h2>ใบเสร็จ</h2>
            <p style="text-align: right;">เลขที่ใบเสร็จ: <strong><?php echo htmlspecialchars($invoice['invoice_number']); ?></strong></p>
            <p style="text-align: right;">วันที่ชำระเงิน: <strong><?php echo $payment_date; ?></strong></p>

            <table class="table-container">
                <tr>
                    <th>No.</th>
                    <th>ชื่อสินค้า</th>
                    <th>จำนวน</th>
                    <th>หน่วย</th>
                    <th>ราคา</th>
                </tr>
                <?php 
                $total_price = 0;
                foreach ($products as $index => $product): 
                    $subtotal = $product['quantity'] * $product['unit_price'];
                    $total_price += $subtotal;
                ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo htmlspecialchars($product['product_name']); ?></td>
                    <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    <td><?php echo htmlspecialchars($product['unit']); ?></td>
                    <td>฿<?php echo number_format($subtotal, 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            
            <div class="total-section">
                <p><strong>ยอดรวม:</strong> <span>฿<?php echo number_format($total_price, 2); ?></span></p>
            </div>
<div class="btn">
            <a id="printExcelBtn" class="btn btn-primary btn-back" onclick="printReport()">
                 พิมพ์ใบเสร็จ
            </a>
            <a href="sell_products.php" class="btn btn-primary btn-back">กลับไปที่การขาย</a>
            </div>
        </div>
    </main>

    <script>
    function printReport() {
        window.print();
    }
    </script>
</body>

</html>
