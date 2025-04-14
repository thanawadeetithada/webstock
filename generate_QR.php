<?php
use Picqer\Barcode\BarcodeGeneratorPNG;

session_start();
require 'vendor/autoload.php';
include 'config.php';
// include('include/header.php');

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
    $position = $_POST['position']; 

    if (!empty($oldProductCode) && !empty($expiryDate)) {
        // แปลงวันที่จาก YYYY-MM-DD เป็น DDMMYY
        $formattedDate = date('dmy', strtotime($expiryDate));

        // สร้างรหัสสินค้าใหม่ตามฟอร์แมตที่ต้องการ
        $newProductCode = $oldProductCode . 'E' . $formattedDate;

        // อัปเดตข้อมูลในฐานข้อมูล (update แทน insert)
        $stmt = $conn->prepare("UPDATE products SET product_code = ?, expiration_date = ?, position = ? WHERE product_code = ?");
        $stmt->bind_param("ssss", $newProductCode, $expiryDate, $position, $oldProductCode);
        $stmt->execute();

        // สร้างบาร์โค้ด
        $generator = new BarcodeGeneratorPNG();
        $barcode = $generator->getBarcode($newProductCode, $generator::TYPE_CODE_128);

        // ขยายขนาดภาพบาร์โค้ดเป็น 2 เท่า
        $barcodeImage = imagecreatefromstring($barcode);
        $originalWidth = imagesx($barcodeImage);
        $originalHeight = imagesy($barcodeImage);

        // เพิ่มขนาดเป็น 2 เท่า
        $newWidth = $originalWidth * 1.5;
        $newHeight = $originalHeight * 2.5;

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

        $backgroundColor = imagecolorallocate($image, 255, 255, 255);  // สีขาว
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // เติมสีพื้นหลัง
        imagefilledrectangle($image, 0, 0, $newWidth, $finalHeight, $backgroundColor);

        // วางภาพบาร์โค้ดที่ขยายขนาดลงบนภาพใหม่
        imagecopy($image, $resizedBarcodeImage, 0, 0, 0, 0, $newWidth, $newHeight);

        // เพิ่มพื้นที่สำหรับกรอบ
        $outerWidth = $newWidth;
        $outerHeight = $finalHeight + 40;
        $outerImage = imagecreatetruecolor($outerWidth, $outerHeight);

        // สีพื้นหลังใหม่ (เช่น สีขาว)
        $outerBackgroundColor = imagecolorallocate($outerImage, 255, 255, 255);  // สีขาว
        imagefilledrectangle($outerImage, 0, 0, $outerWidth, $outerHeight, $outerBackgroundColor);

        // วางภาพบาร์โค้ดและข้อความ
        imagecopy($outerImage, $image, 0, 0, 0, 0, $newWidth, $finalHeight);

        // กำหนดสีกรอบให้ชัดเจน (สีดำ)
        $borderColor = imagecolorallocate($outerImage, 0, 0, 0);  // สีดำ
        // กรอบรอบทั้งบาร์โค้ดและข้อความ
        imagerectangle($outerImage, 0, 0, $newWidth - 1, $outerHeight - 1, $borderColor);  // กรอบรอบทั้งภาพและข้อความ

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
        $spacePerChar = $barcodeWidth / $charCount; // ขนาดพื้นที่ที่ใช้สำหรับแต่ละตัวอักษร

        // วางข้อความทีละตัวจากซ้ายไปขวา
        $currentX = -30;
        for ($i = 0; $i < $charCount; $i++) {
            imagettftext($outerImage, $boldFontSize, 0, $currentX + 40, $outerHeight - 60, $textColor, $boldFont, $newProductCode[$i]);
            $currentX = round($spacePerChar * $i);
        }

        // ข้อความแสดงตำแหน่งสินค้า
        $positionText = "ตำแหน่งสินค้า: " . $position;

        // คำนวณความกว้างของข้อความตำแหน่ง
        $textBoxPosition = imagettfbbox($fontSize, 0, $font, $positionText);
        $textWidthPosition = $textBoxPosition[2] - $textBoxPosition[0];

        // คำนวณตำแหน่ง x ให้ข้อความตำแหน่งอยู่ชิดซ้าย (หรือจะให้ขวาก็ปรับเป็นเหมือนด้านล่าง)
        $leftX = 10;

        // แสดงข้อความตำแหน่งสินค้า ที่บรรทัดเหนือข้อความวันหมดอายุ
        imagettftext($outerImage, $fontSize, 0, $leftX, $outerHeight - 10, $textColor, $font, $positionText);


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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

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
        position: relative;
    }

    .search-container {
        display: flex;
        justify-content: center;
        margin-bottom: 25px;
        gap: 20px;
        width: 100%;
    }

    .form-group label {
        font-weight: bold;
        margin-bottom: 5px;
        color: #003d99;
    }

    .center-button {
        display: flex;
        justify-content: center;
        margin-bottom: 2.5rem;
    }

    .select2-container--default .select2-selection--single {
        height: 38px;
        border: 1px solid #ced4da;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        height: 100%;
        display: flex;
        align-items: center;
    }

    .select2-container--default .select2-selection--single .select2-selection__clear {
        display: none;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
    }
    </style>
</head>

<body>
    <div class="container">
        <form method="POST" action="">
            <div class="search-container">
                <div class="form-group">
                    <label for="oldProductCode">รหัสสินค้าเดิม</label><br>
                    <select class="form-control" id="oldProductCode" name="oldProductCode" required>
                        <option value="">เลือกสินค้าจากรายการ</option>
                        <?php
$query = "SELECT product_code FROM products";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . htmlspecialchars($row['product_code']) . '">' . htmlspecialchars($row['product_code']) . '</option>';
}
?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="expiryDate">วันหมดอายุ</label>
                    <input type="date" class="form-control" id="expiryDate" name="expiryDate" required>
                </div>
                <div class="form-group">
                    <label for="position">ตำแหน่งสินค้า</label>
                    <input type="text" class="form-control" id="position" name="position"
                        placeholder="กรอกตำแหน่งสินค้า">
                </div>
            </div>
            <div class="center-button">
                <button type="submit" class="btn btn-primary">สร้างบาร์โค้ด</button>
            </div>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    $('#oldProductCode').select2({
        placeholder: 'เลือกสินค้าจากรายการ',
        allowClear: true
    });

    // เมื่อมีการเปลี่ยนค่าใน select
    $('#oldProductCode').on('change', function() {
        const selectedCode = $(this).val();

        if (selectedCode) {
            // ส่ง AJAX เพื่อดึงข้อมูลสินค้า
            $.ajax({
                url: 'get_product_details.php', // URL ของไฟล์ที่ใช้ดึงข้อมูล
                method: 'GET',
                data: {
                    product_code: selectedCode
                },
                success: function(response) {
                    const data = JSON.parse(response);

                    if (data.error) {
                        alert(data.error);
                    } else {
                        // เติมข้อมูลลงในฟอร์ม
                        $('#expiryDate').val(data.expiration_date);
                        $('#position').val(data.position);
                    }
                },
                error: function() {
                    alert('เกิดข้อผิดพลาดในการดึงข้อมูลสินค้า');
                }
            });
        } else {
            // เคลียร์ค่าเมื่อไม่มีการเลือกสินค้า
            $('#expiryDate').val('');
            $('#position').val('');
        }
    });
});
</script>

</html>