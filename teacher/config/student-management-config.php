<?php
/**
 * Student Management System Configuration
 * Final optimization settings
 */

// Image upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('UPLOAD_PATH', '../photo/');
define('DEFAULT_AVATAR', '../dist/img/default-avatar.svg');

// Image optimization settings
define('IMAGE_QUALITY', 85);
define('MAX_IMAGE_WIDTH', 800);
define('MAX_IMAGE_HEIGHT', 800);
define('THUMBNAIL_SIZE', 200);

// UI Settings
define('CARDS_PER_PAGE', 20);
define('SEARCH_DEBOUNCE_DELAY', 300);
define('ANIMATION_DURATION', 300);

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour
define('CSRF_TOKEN_EXPIRE', 1800); // 30 minutes

// Feature flags
define('ENABLE_IMAGE_CROPPING', true);
define('ENABLE_DRAG_DROP', true);
define('ENABLE_DARK_MODE', true);
define('ENABLE_ANIMATIONS', true);
define('ENABLE_NOTIFICATIONS', true);

// Performance settings
define('ENABLE_CACHING', false); // Set to true in production
define('CACHE_DURATION', 300); // 5 minutes

/**
 * Get configuration value
 */
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

/**
 * Validate file upload
 */
function validateUpload($file) {
    $errors = [];
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'ขนาดไฟล์ใหญ่เกินไป (ไม่เกิน ' . (MAX_FILE_SIZE / 1024 / 1024) . 'MB)';
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp'
    ];
    
    if (!array_key_exists($mimeType, $allowedMimes)) {
        $errors[] = 'ประเภทไฟล์ไม่ถูกต้อง อนุญาตเฉพาะ: ' . implode(', ', ALLOWED_EXTENSIONS);
    }
    
    return $errors;
}

/**
 * Generate CSRF token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token']) || 
        !isset($_SESSION['csrf_token_time']) || 
        (time() - $_SESSION['csrf_token_time']) > CSRF_TOKEN_EXPIRE) {
        
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_time'] = time();
    }
    
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && 
           hash_equals($_SESSION['csrf_token'], $token) &&
           isset($_SESSION['csrf_token_time']) &&
           (time() - $_SESSION['csrf_token_time']) <= CSRF_TOKEN_EXPIRE;
}

/**
 * Sanitize filename
 */
function sanitizeFilename($filename) {
    // Remove any path traversal attempts
    $filename = basename($filename);
    
    // Remove special characters except dots and dashes
    $filename = preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
    
    // Limit length
    if (strlen($filename) > 100) {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = substr(pathinfo($filename, PATHINFO_FILENAME), 0, 90);
        $filename = $name . '.' . $extension;
    }
    
    return $filename;
}

/**
 * Create responsive image sizes
 */
function createResponsiveImages($sourcePath, $targetPath) {
    $sizes = [
        'thumbnail' => 200,
        'small' => 400,
        'medium' => 800
    ];
    
    $created = [];
    
    foreach ($sizes as $size => $width) {
        $targetFile = str_replace('.', "_$size.", $targetPath);
        if (resizeImage($sourcePath, $targetFile, $width, $width)) {
            $created[$size] = $targetFile;
        }
    }
    
    return $created;
}

/**
 * Log system events
 */
function logEvent($level, $message, $context = []) {
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'level' => $level,
        'message' => $message,
        'context' => $context,
        'user_id' => $_SESSION['Teacher_login'] ?? 'anonymous',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ];
    
    $logFile = '../logs/system.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, json_encode($logEntry) . "\n", FILE_APPEND | LOCK_EX);
}

?>
