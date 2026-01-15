<?php
session_start();
header('Content-Type: application/json');

// Enhanced security and validation
if (!isset($_SESSION['Teacher_login'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

require_once("../../config/Database.php");

$stu_id = $_POST['Stu_id'] ?? '';
if (!$stu_id) {
    echo json_encode(['success' => false, 'message' => 'ไม่พบรหัสนักเรียน']);
    exit;
}

// Enhanced file validation with proper error messages
if (!isset($_FILES['profile_pic'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเลือกรูปโปรไฟล์']);
    exit;
}

if ($_FILES['profile_pic']['error'] !== UPLOAD_ERR_OK) {
    $error = $_FILES['profile_pic']['error'];
    $errorMsg = '';
    switch ($error) {
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            $errorMsg = 'รูปภาพมีขนาดใหญ่เกินไป กรุณาลดขนาดไฟล์แล้วลองใหม่';
            break;
        case UPLOAD_ERR_PARTIAL:
            $errorMsg = 'การอัปโหลดไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง';
            break;
        case UPLOAD_ERR_NO_FILE:
            $errorMsg = 'กรุณาเลือกรูปโปรไฟล์';
            break;
        default:
            $errorMsg = 'เกิดข้อผิดพลาดในการอัปโหลด กรุณาลองใหม่';
    }
    echo json_encode(['success' => false, 'message' => $errorMsg]);
    exit;
}

$file = $_FILES['profile_pic'];

// Validate file size (5MB max)
if ($file['size'] > 5 * 1024 * 1024) {
    echo json_encode(['success' => false, 'message' => 'ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน 5MB)']);
    exit;
}

// Validate file type using MIME type for better security
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mimeType = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
if (!in_array($mimeType, $allowedMimes)) {
    echo json_encode(['success' => false, 'message' => 'อนุญาตเฉพาะไฟล์รูปภาพ JPEG, PNG, GIF, WebP']);
    exit;
}

// Get file extension based on MIME type
$extMap = [
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/gif' => 'gif',
    'image/webp' => 'webp'
];
$ext = $extMap[$mimeType];

// Generate unique filename with timestamp to prevent caching issues
$timestamp = time();
$newFileName = $stu_id . '_' . $timestamp . '.' . $ext;
$uploadDir = '../../photo/';

// Create directory if not exists with proper permissions
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถสร้างโฟลเดอร์สำหรับเก็บรูปภาพได้']);
        exit;
    }
}

$targetPath = $uploadDir . $newFileName;

// Remove old profile pictures for this student
$oldFiles = glob($uploadDir . $stu_id . '_*.*');
foreach ($oldFiles as $oldFile) {
    @unlink($oldFile);
}

// Process and optimize image
try {
    if (!processAndSaveImage($file['tmp_name'], $targetPath, $mimeType)) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถประมวลผลรูปภาพได้']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการประมวลผลรูปภาพ: ' . $e->getMessage()]);
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
        // Log the activity
        logActivity($conn, $_SESSION['Teacher_login'], "Updated profile picture for student: $stu_id");
        
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

/**
 * Process and optimize image for better performance
 */
function processAndSaveImage($sourcePath, $targetPath, $mimeType) {
    // Create image resource based on type
    switch ($mimeType) {
        case 'image/jpeg':
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $image = imagecreatefrompng($sourcePath);
            break;
        case 'image/gif':
            $image = imagecreatefromgif($sourcePath);
            break;
        case 'image/webp':
            $image = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Get original dimensions
    $originalWidth = imagesx($image);
    $originalHeight = imagesy($image);

    // Calculate new dimensions (max 800x800 for optimization)
    $maxSize = 800;
    if ($originalWidth > $maxSize || $originalHeight > $maxSize) {
        $ratio = min($maxSize / $originalWidth, $maxSize / $originalHeight);
        $newWidth = (int)($originalWidth * $ratio);
        $newHeight = (int)($originalHeight * $ratio);

        // Create new image with optimized size
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preserve transparency for PNG
        if ($mimeType === 'image/png') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
            imagefilledrectangle($newImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        imagedestroy($image);
        $image = $newImage;
    }

    // Save optimized image
    $result = false;
    switch ($mimeType) {
        case 'image/jpeg':
            $result = imagejpeg($image, $targetPath, 85); // 85% quality
            break;
        case 'image/png':
            $result = imagepng($image, $targetPath, 6); // Compression level 6
            break;
        case 'image/gif':
            $result = imagegif($image, $targetPath);
            break;
        case 'image/webp':
            $result = imagewebp($image, $targetPath, 85); // 85% quality
            break;
    }

    imagedestroy($image);
    return $result;
}

/**
 * Log activity for audit trail
 */
function logActivity($conn, $userId, $activity) {
    try {
        $stmt = $conn->prepare("INSERT INTO activity_log (user_id, activity, timestamp) VALUES (:user_id, :activity, NOW())");
        $stmt->bindParam(":user_id", $userId);
        $stmt->bindParam(":activity", $activity);
        $stmt->execute();
    } catch (Exception $e) {
        // Log error but don't fail the main operation
        error_log("Failed to log activity: " . $e->getMessage());
    }
}
?>
