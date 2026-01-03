<?php
session_start();
header('Content-Type: application/json');

// Check Officer Login
if (!isset($_SESSION['Officer_login'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

require_once("../../config/Database.php");

$stu_id = $_POST['Stu_id'] ?? '';
if (!$stu_id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสนักเรียน']);
    exit;
}

// Enhanced file validation
if (!isset($_FILES['profile_pic']) || $_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบไฟล์หรือเกิดข้อผิดพลาด']);
    exit;
}

$file = $_FILES['profile_pic'];

// Validate file size (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน 5MB)']);
    exit;
}

// Validate file type
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mimeType, $allowedMimes)) {
    echo json_encode(['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPEG, PNG, GIF, WebP']);
    exit;
}

// Get file extension
$extMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];
$ext = $extMap[$mimeType];

// Generate unique filename
$timestamp = time();
$newFileName = $stu_id . '_' . $timestamp . '.' . $ext;
$uploadDir = '../../photo/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$targetPath = $uploadDir . $newFileName;

// Remove old files
$oldFiles = glob($uploadDir . $stu_id . '_*.*');
foreach ($oldFiles as $oldFile) {
    @unlink($oldFile);
}

// Process and Save
try {
    if (!processAndSaveImage($file['tmp_name'], $targetPath, $mimeType)) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถประมวลผลรูปภาพได้']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    exit;
}

// Update database
try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $stmt = $conn->prepare("UPDATE student SET Stu_picture = :pic WHERE Stu_id = :id");
    $stmt->bindParam(":pic", $newFileName);
    $stmt->bindParam(":id", $stu_id);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true, 
            'message' => 'อัปโหลดรูปโปรไฟล์สำเร็จ',
            'filename' => $newFileName
        ]);
    } else {
        @unlink($targetPath);
        echo json_encode(['success' => false, 'message' => 'บันทึกข้อมูลไม่สำเร็จ']);
    }
} catch (Exception $e) {
    @unlink($targetPath);
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล']);
}

function processAndSaveImage($sourcePath, $targetPath, $mimeType) {
    switch ($mimeType) {
        case 'image/jpeg': $image = imagecreatefromjpeg($sourcePath); break;
        case 'image/png': $image = imagecreatefrompng($sourcePath); break;
        case 'image/gif': $image = imagecreatefromgif($sourcePath); break;
        case 'image/webp': $image = imagecreatefromwebp($sourcePath); break;
        default: return false;
    }
    if (!$image) return false;
    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);
    $maxSize = 800;
    if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
        $ratio = min($maxSize / $originalWidth, $maxSize / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        imagedestroy($image);
        $image = $newImage;
    }
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg': $result = imagejpeg($image, $targetPath, 85); break;
        case 'image/png': $result = imagepng($image, $targetPath, 6); break;
        case 'image/gif': $result = imagegif($image, $targetPath); break;
        case 'image/webp': $result = imagewebp($image, $targetPath, 85); break;
    }
    imagedestroy($image);
    return $result;
}
