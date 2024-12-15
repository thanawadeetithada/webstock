<?php
use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();
require 'vendor/autoload.php';
include 'config.php';

$newProductCode = '';
$barcodeFile = '';
$error = '';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $oldProductCode = $_POST['oldProductCode'];
    $expiryDate = $_POST['expiryDate'];

    if (!empty($oldProductCode) && !empty($expiryDate)) {
        // แปลงวันที่จาก YYYY-MM-DD เป็น DDMMYY
        $formattedDate = date('dmy', strtotime($expiryDate));

        // สร้างรหัสสินค้าใหม่ตามฟอร์แมตที่ต้องการ
        $newProductCode = $oldProductCode . 'E' . $formattedDate;

        // บันทึกข้อมูลในฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO products (product_code, expiry_date) VALUES (?, ?)");
        $stmt->bind_param("ss", $newProductCode, $expiryDate);
        $stmt->execute();

        // สร้างบาร์โค้ด
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($newProductCode, $generator::TYPE_CODE_128);

        // สร้างภาพบาร์โค้ดพร้อมพื้นหลังและข้อความ
        $barcodeImage = imagecreatefromstring($barcode);
        $width = imagesx($barcodeImage);
        $height = imagesy($barcodeImage) + 40; // เพิ่มพื้นที่สำหรับข้อความด้านล่าง

        // สร้างภาพใหม่พร้อมพื้นหลังสีเขียว
        $image = imagecreatetruecolor($width, $height);
        $backgroundColor = imagecolorallocate($image, 204, 255, 204); // สีเขียวอ่อน
        $textColor = imagecolorallocate($image, 0, 0, 0); // สีดำ

        imagefilledrectangle($image, 0, 0, $width, $height, $backgroundColor);
        imagecopy($image, $barcodeImage, 0, 0, 0, 0, $width, imagesy($barcodeImage));

        // เพิ่มข้อความตัวเลขด้านล่างบาร์โค้ด
        $font = __DIR__ . '/font/THSarabunNew.ttf'; 
        $fontSize = 18;
        imagettftext($image, $fontSize, 0, 10, $height - 20, $textColor, $font, $newProductCode);
        $consumeBeforeText = "ควรบริโภคก่อนวันที่ : " . date('d/m/Y', strtotime($expiryDate));
        imagettftext($image, $fontSize, 0, 10, $height - 5, $textColor, $font, $consumeBeforeText);

        // บันทึกภาพ
        $barcodeFile = 'barcodes/' . $newProductCode . '.png';
        imagepng($image, $barcodeFile);
        imagedestroy($image);
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    }
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

    .center-button {
        display: flex;
        justify-content: center;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="form-group">
                <label for="oldProductCode">รหัสสินค้าเดิม</label>
                <input type="text" class="form-control" id="oldProductCode" name="oldProductCode" required>
            </div>
            <div class="form-group">
                <label for="expiryDate">วันหมดอายุ</label>
                <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
            </div>
            <button type="submit" class="btn btn-primary">สร้างบาร์โค้ด</button>

            <?php if (!empty($newProductCode) && !empty($barcodeFile)): ?>
                <div class="text-center mt-4">
                    <h5>รหัสใหม่ของสินค้า: <?php echo htmlspecialchars($newProductCode); ?></h5>
                    <img src="<?php echo htmlspecialchars($barcodeFile); ?>" alt="Barcode">
                </div>
            <?php elseif (!empty($error)): ?>
                <div class="alert alert-danger mt-4"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </form>
    </div>
</body>

</html>
