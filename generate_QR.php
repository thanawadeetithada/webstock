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
        // สร้างบาร์โค้ด
$generator = new BarcodeGeneratorPNG();
$barcode = $generator->getBarcode($newProductCode, $generator::TYPE_CODE_128);

// ขยายขนาดภาพบาร์โค้ดเป็น 2 เท่า
$barcodeImage = imagecreatefromstring($barcode);
$originalWidth = imagesx($barcodeImage);
$originalHeight = imagesy($barcodeImage);

// เพิ่มขนาดเป็น 2 เท่า
$newWidth = $originalWidth * 2;
$newHeight = $originalHeight * 3;

// สร้างภาพใหม่ที่ขยายขนาดและรองรับความโปร่งใส
$resizedBarcodeImage = imagecreatetruecolor($newWidth, $newHeight);
imagealphablending($resizedBarcodeImage, false);
imagesavealpha($resizedBarcodeImage, true);
$transparentColor = imagecolorallocatealpha($resizedBarcodeImage, 255, 255, 255, 127);
imagefill($resizedBarcodeImage, 0, 0, $transparentColor);

// ทำการยืดขยายภาพเดิมไปยังภาพใหม่
imagecopyresampled($resizedBarcodeImage, $barcodeImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

// เพิ่มพื้นที่สำหรับข้อความด้านล่าง
$finalHeight = $newHeight + 40;
$image = imagecreatetruecolor($newWidth, $finalHeight);

// สร้างสีพื้นหลังสีเขียวอ่อน
$backgroundColor = imagecolorallocate($image, 204, 255, 204);
$textColor = imagecolorallocate($image, 0, 0, 0);

// เติมสีพื้นหลัง
imagefilledrectangle($image, 0, 0, $newWidth, $finalHeight, $backgroundColor);

// วางภาพบาร์โค้ดที่ขยายขนาดลงบนภาพใหม่
imagecopy($image, $resizedBarcodeImage, 0, 0, 0, 0, $newWidth, $newHeight);

// เพิ่มพื้นหลังใหม่ที่ใหญ่กว่า
$outerWidth = $newWidth;
$outerHeight = $finalHeight + 70;
$outerImage = imagecreatetruecolor($outerWidth, $outerHeight);

// สีพื้นหลังใหม่ (เช่น สีเทา)
$outerBackgroundColor = imagecolorallocate($outerImage, 255, 255, 255);
imagefilledrectangle($outerImage, 0, 0, $outerWidth, $outerHeight, $outerBackgroundColor);

// วางภาพ QR code พร้อมพื้นสีเขียวตรงกลาง
imagecopy($outerImage, $image, 0, 20, 0, 0, $newWidth, $finalHeight);

// เพิ่มข้อความตัวเลขด้านล่างบาร์โค้ด
// เพิ่มข้อความตัวเลขด้านล่างบาร์โค้ด
$font = __DIR__ . '/font/THSarabunNew.ttf';
$boldFont = __DIR__ . '/font/THSarabunNewBold.ttf';
$fontSize = 18; // ขนาดฟอนต์ปกติ
$boldFontSize = 25;

// คำนวณความกว้างของพื้นที่บาร์โค้ด
$barcodeWidth = $newWidth; // ความกว้างของบาร์โค้ดที่ขยาย

// คำนวณจำนวนตัวอักษรทั้งหมดในรหัสสินค้า
$charCount = strlen($newProductCode);

// คำนวณระยะห่างของแต่ละตัวอักษรให้กระจายเต็มความกว้าง
$spacePerChar = $barcodeWidth / $charCount;  // ขนาดพื้นที่ที่ใช้สำหรับแต่ละตัวอักษร

// วางข้อความทีละตัวจากซ้ายไปขวา
$currentX = -30;
for ($i = 0; $i < $charCount; $i++) {
    // วางข้อความแต่ละตัว
    imagettftext($outerImage, $boldFontSize, 0, $currentX + 40, $outerHeight - 60, $textColor, $boldFont, $newProductCode[$i]);
    
    // ขยับตำแหน่ง X เพื่อให้ข้อความกระจาย
    $currentX = round($spacePerChar * $i);  // หรือ intval($spacePerChar * $i)

}

// ข้อความ "ควรบริโภคก่อนวันที่"
$consumeBeforeText = "ควรบริโภคก่อนวันที่ : " . date('d/m/Y', strtotime($expiryDate));

// คำนวณความกว้างของข้อความ
$textBoxConsume = imagettfbbox($fontSize, 0, $font, $consumeBeforeText);
$textWidthConsume = $textBoxConsume[2] - $textBoxConsume[0];

// คำนวณตำแหน่ง x ให้ข้อความอยู่ขวาสุด (เว้นขอบเล็กน้อย 10px)
$rightX = $outerWidth - $textWidthConsume - 10;

// แสดงข้อความ "ควรบริโภคก่อนวันที่" ที่ขวาสุด
imagettftext($outerImage, $fontSize, 0, $rightX, $outerHeight - 10, $textColor, $font, $consumeBeforeText);

// บันทึกภาพ
$barcodeFile = 'barcodes/' . $newProductCode . '.png';
imagepng($outerImage, $barcodeFile);

// ทำลายภาพเพื่อล้างหน่วยความจำ
imagedestroy($image);
imagedestroy($barcodeImage);
imagedestroy($resizedBarcodeImage);
imagedestroy($outerImage);

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
            <?php endif;?>
        </form>
    </div>
</body>

</html>
