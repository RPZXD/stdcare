<?php
/**
 * Image Proxy API
 * Converts external images to base64 for html2canvas export
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$url = $_GET['url'] ?? '';

if (empty($url)) {
    echo json_encode(['success' => false, 'error' => 'No URL provided']);
    exit;
}

// Validate URL
if (!filter_var($url, FILTER_VALIDATE_URL)) {
    echo json_encode(['success' => false, 'error' => 'Invalid URL']);
    exit;
}

// Only allow specific domains for security
$allowedDomains = [
    'std.phichai.ac.th',
    'phichai.ac.th',
    'localhost'
];

$parsedUrl = parse_url($url);
$host = $parsedUrl['host'] ?? '';

$allowed = false;
foreach ($allowedDomains as $domain) {
    if (strpos($host, $domain) !== false) {
        $allowed = true;
        break;
    }
}

if (!$allowed) {
    echo json_encode(['success' => false, 'error' => 'Domain not allowed']);
    exit;
}

try {
    // Fetch image with cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    curl_close($ch);
    
    if ($httpCode !== 200 || empty($imageData)) {
        echo json_encode(['success' => false, 'error' => 'Failed to fetch image']);
        exit;
    }
    
    // Detect image type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->buffer($imageData);
    
    if (strpos($mimeType, 'image') === false) {
        $mimeType = 'image/jpeg'; // Default fallback
    }
    
    // Convert to base64
    $base64 = base64_encode($imageData);
    $dataUri = "data:{$mimeType};base64,{$base64}";
    
    echo json_encode([
        'success' => true,
        'data' => $dataUri
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
