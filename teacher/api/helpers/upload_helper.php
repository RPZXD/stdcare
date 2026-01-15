<?php
/**
 * Upload Error Helper Functions
 * ช่วยแจ้ง error message ที่ถูกต้องตามสาเหตุจริงของการอัปโหลดไฟล์
 */

/**
 * Get human-readable upload error message in Thai
 * @param int $errorCode PHP upload error code
 * @param string $fieldName Name of the field for better context (optional)
 * @return string Error message in Thai
 */
function getUploadErrorMessage($errorCode, $fieldName = 'ไฟล์') {
    switch ($errorCode) {
        case UPLOAD_ERR_INI_SIZE:
            return "$fieldName มีขนาดใหญ่เกินกว่าที่เซิร์ฟเวอร์อนุญาต กรุณาลดขนาดไฟล์แล้วลองใหม่";
        case UPLOAD_ERR_FORM_SIZE:
            return "$fieldName มีขนาดใหญ่เกินไป กรุณาลดขนาดไฟล์แล้วลองใหม่";
        case UPLOAD_ERR_PARTIAL:
            return "$fieldName อัปโหลดไม่สมบูรณ์ กรุณาลองใหม่อีกครั้ง";
        case UPLOAD_ERR_NO_FILE:
            return "กรุณาเลือก$fieldName";
        case UPLOAD_ERR_NO_TMP_DIR:
            return "เกิดข้อผิดพลาดในการอัปโหลด กรุณาติดต่อผู้ดูแลระบบ (ไม่พบ temp folder)";
        case UPLOAD_ERR_CANT_WRITE:
            return "เกิดข้อผิดพลาดในการบันทึกไฟล์ กรุณาติดต่อผู้ดูแลระบบ";
        case UPLOAD_ERR_EXTENSION:
            return "การอัปโหลดถูกบล็อกโดยระบบ กรุณาติดต่อผู้ดูแลระบบ";
        case UPLOAD_ERR_OK:
            return ""; // No error
        default:
            return "เกิดข้อผิดพลาดในการอัปโหลด$fieldName กรุณาลองใหม่";
    }
}

/**
 * Check file upload and return appropriate error response if any
 * @param string $fileKey The key in $_FILES array
 * @param string $fieldName Name of the field for error messages
 * @param bool $required Whether the file is required
 * @param int $maxSize Maximum file size in bytes (default 5MB)
 * @return array|null Returns error array if error, null if OK
 */
function validateFileUpload($fileKey, $fieldName = 'ไฟล์', $required = true, $maxSize = 5242880) {
    // Check if file exists
    if (!isset($_FILES[$fileKey])) {
        if ($required) {
            return ['success' => false, 'message' => "กรุณาเลือก$fieldName"];
        }
        return null;
    }

    $file = $_FILES[$fileKey];
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] === UPLOAD_ERR_NO_FILE && !$required) {
            return null; // Optional file not provided, this is OK
        }
        return ['success' => false, 'message' => getUploadErrorMessage($file['error'], $fieldName)];
    }
    
    // Check file size (for cases where file passed PHP limit but exceeds our custom limit)
    if ($file['size'] > $maxSize) {
        $maxMB = round($maxSize / 1048576, 1);
        return ['success' => false, 'message' => "$fieldName มีขนาดใหญ่เกินไป (สูงสุด {$maxMB}MB)"];
    }
    
    return null; // No error
}

/**
 * Validate multiple file uploads
 * @param string $fileKey The key in $_FILES array (for multiple files)
 * @param string $fieldName Name of the field for error messages  
 * @param int $minRequired Minimum number of files required (0 for optional)
 * @param int $maxFiles Maximum number of files allowed
 * @param int $maxSize Maximum file size per file in bytes
 * @return array|null Returns error array if error, null if OK
 */
function validateMultipleFileUploads($fileKey, $fieldName = 'ไฟล์', $minRequired = 0, $maxFiles = 10, $maxSize = 5242880) {
    if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey]['name'])) {
        if ($minRequired > 0) {
            return ['success' => false, 'message' => "กรุณาเลือก$fieldName อย่างน้อย $minRequired ไฟล์"];
        }
        return null;
    }
    
    $files = $_FILES[$fileKey];
    $successCount = 0;
    
    foreach ($files['error'] as $key => $error) {
        if ($error === UPLOAD_ERR_NO_FILE) {
            continue; // Skip empty slots
        }
        
        if ($error !== UPLOAD_ERR_OK) {
            $fileNum = $key + 1;
            return ['success' => false, 'message' => getUploadErrorMessage($error, "$fieldName ที่ $fileNum")];
        }
        
        if ($files['size'][$key] > $maxSize) {
            $fileNum = $key + 1;
            $maxMB = round($maxSize / 1048576, 1);
            return ['success' => false, 'message' => "$fieldName ที่ $fileNum มีขนาดใหญ่เกินไป (สูงสุด {$maxMB}MB)"];
        }
        
        $successCount++;
        
        if ($successCount > $maxFiles) {
            return ['success' => false, 'message' => "อัปโหลดได้สูงสุด $maxFiles ไฟล์เท่านั้น"];
        }
    }
    
    if ($successCount < $minRequired) {
        return ['success' => false, 'message' => "กรุณาเลือก$fieldName อย่างน้อย $minRequired ไฟล์"];
    }
    
    return null;
}

/**
 * Validate image file type
 * @param string $tmpPath Temporary file path
 * @param string $fieldName Field name for error messages
 * @return array|null Returns error array if invalid, null if OK
 */
function validateImageType($tmpPath, $fieldName = 'รูปภาพ') {
    $imageInfo = @getimagesize($tmpPath);
    if (!$imageInfo) {
        return ['success' => false, 'message' => "$fieldName ไม่ใช่ไฟล์รูปภาพที่ถูกต้อง"];
    }
    
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/bmp'];
    if (!in_array($imageInfo['mime'], $allowedTypes)) {
        return ['success' => false, 'message' => "รูปแบบ$fieldName ไม่ถูกต้อง (รองรับ: JPEG, PNG, GIF, WebP, BMP)"];
    }
    
    return null;
}
?>
