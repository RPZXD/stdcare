<?php
require_once "../../config/Database.php";
require_once "../../class/StudentVisit.php";

$db = (new Database("phichaia_student"))->getConnection();
$visitHome = new StudentVisit($db);

// Image utility functions
function convertImageToJpeg($sourcePath, $targetPath, $quality = 85) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $sourceImage = null;
    switch ($imageInfo['mime']) {
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($sourcePath);
            break;
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    // Enable transparency for PNG/GIF conversion
    $targetImage = imagecreatetruecolor(imagesx($sourceImage), imagesy($sourceImage));
    $white = imagecolorallocate($targetImage, 255, 255, 255);
    imagefill($targetImage, 0, 0, $white);
    imagecopy($targetImage, $sourceImage, 0, 0, 0, 0, imagesx($sourceImage), imagesy($sourceImage));
    
    $result = imagejpeg($targetImage, $targetPath, $quality);
    
    imagedestroy($sourceImage);
    imagedestroy($targetImage);
    
    return $result;
}

function resizeImage($sourcePath, $targetPath, $maxWidth = 800, $maxHeight = 600, $quality = 85) {
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) return false;
    
    $sourceWidth = $imageInfo[0];
    $sourceHeight = $imageInfo[1];
    
    // Calculate new dimensions
    $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
    $newWidth = round($sourceWidth * $ratio);
    $newHeight = round($sourceHeight * $ratio);
    
    // Create source image
    $sourceImage = null;
    switch ($imageInfo['mime']) {
        case 'image/jpeg':
        case 'image/jpg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($sourcePath);
            break;
        default:
            return false;
    }
    
    if (!$sourceImage) return false;
    
    // Create target image
    $targetImage = imagecreatetruecolor($newWidth, $newHeight);
    $white = imagecolorallocate($targetImage, 255, 255, 255);
    imagefill($targetImage, 0, 0, $white);
    
    // Resize and copy
    imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $sourceWidth, $sourceHeight);
    
    // Save as JPEG
    $result = imagejpeg($targetImage, $targetPath, $quality);
    
    imagedestroy($sourceImage);
    imagedestroy($targetImage);
    
    return $result;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'stuId' => $_POST['stuId'] ?? null,
        'term' => $_POST['term'] ?? null,
        'pee' => $_POST['pee'] ?? null,
        'vh1' => $_POST['vh1'] ?? null,
        'vh2' => $_POST['vh2'] ?? null,
        'vh3' => $_POST['vh3'] ?? null,
        'vh4' => $_POST['vh4'] ?? null,
        'vh5' => $_POST['vh5'] ?? null,
        'vh6' => $_POST['vh6'] ?? null,
        'vh7' => $_POST['vh7'] ?? null,
        'vh8' => $_POST['vh8'] ?? null,
        'vh9' => $_POST['vh9'] ?? null,
        'vh10' => $_POST['vh10'] ?? null,
        'vh11' => $_POST['vh11'] ?? null,
        'vh12' => $_POST['vh12'] ?? null,
        'vh13' => $_POST['vh13'] ?? null,
        'vh14' => $_POST['vh14'] ?? null,
        'vh15' => $_POST['vh15'] ?? null,
        'vh16' => $_POST['vh16'] ?? null,
        'vh17' => $_POST['vh17'] ?? null,
        'vh18' => $_POST['vh18'] ?? null,
        'vh20' => $_POST['vh20'] ?? null,
    ];

    // Validate required fields (vh1 to vh18)
    for ($i = 1; $i <= 18; $i++) {
        if (empty($data["vh$i"])) {
            echo json_encode(['success' => false, 'message' => "หัวข้อที่ $i ไม่สามารถเว้นว่างได้"]);
            exit;
        }
    }    // Handle file uploads with enhanced image processing
    $uploadDir = "../uploads/visithome" . ($_POST['pee'] - 543) . "/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true); // Create the directory if it doesn't exist
    }

    for ($i = 1; $i <= 5; $i++) {
        $fileKey = "image$i";
        if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
            $tempPath = $_FILES[$fileKey]['tmp_name'];
            $originalName = $_FILES[$fileKey]['name'];
            $fileSize = $_FILES[$fileKey]['size'];
            
            // Validate file size (5MB max)
            if ($fileSize > 5 * 1024 * 1024) {
                echo json_encode(['success' => false, 'message' => "ไฟล์ $fileKey มีขนาดใหญ่เกินไป (สูงสุด 5MB)"]);
                exit;
            }
            
            // Validate file type
            $imageInfo = getimagesize($tempPath);
            if (!$imageInfo) {
                echo json_encode(['success' => false, 'message' => "ไฟล์ $fileKey ไม่ใช่รูปภาพที่ถูกต้อง"]);
                exit;
            }
            
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
            if (!in_array($imageInfo['mime'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => "รูปแบบไฟล์ $fileKey ไม่ถูกต้อง"]);
                exit;
            }
            
            // Generate filename
            $fileName = "{$data['stuId']}_term{$data['term']}_image{$i}_" . uniqid() . ".jpg";
            $filePath = $uploadDir . $fileName;
            
            // Process image: resize and convert to JPEG
            $processed = false;
            
            // First try to resize and convert
            if (resizeImage($tempPath, $filePath, 800, 600, 85)) {
                $processed = true;
            } 
            // If resize fails, try direct conversion
            elseif (convertImageToJpeg($tempPath, $filePath, 85)) {
                $processed = true;
            }
            // Fallback: direct copy for JPEG files
            elseif ($imageInfo['mime'] === 'image/jpeg' && move_uploaded_file($tempPath, $filePath)) {
                $processed = true;
            }
            
            if ($processed) {
                $data["picture$i"] = $fileName; // Add only successfully uploaded files to the data array
            } else {
                echo json_encode(['success' => false, 'message' => "ไม่สามารถประมวลผลไฟล์ $fileKey ได้"]);
                exit;
            }
        } else {
            unset($data["picture$i"]); // Remove picture key if no file is uploaded
        }
    }

    // Update visit data
    if ($visitHome->updateVisitData($data)) {
        echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>