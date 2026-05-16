<?php
/**
 * API: Save GPS (save_gps.php)
 * Saves student home coordinates to database
 */
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../../config/Database.php';

$stuId = $_SESSION['Student_login'];
$lat = $_POST['latitude'] ?? null;
$lng = $_POST['longitude'] ?? null;
$acc = $_POST['accuracy'] ?? null;

if (!$lat || !$lng) {
    echo json_encode(['success' => false, 'message' => 'Invalid data']);
    exit();
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();

    $sql = "INSERT INTO student_gps (Stu_id, latitude, longitude, accuracy) 
            VALUES (:stuId, :lat, :lng, :acc)
            ON DUPLICATE KEY UPDATE 
            latitude = :lat, longitude = :lng, accuracy = :acc, updated_at = CURRENT_TIMESTAMP";
    
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':stuId', $stuId);
    $stmt->bindParam(':lat', $lat);
    $stmt->bindParam(':lng', $lng);
    $stmt->bindParam(':acc', $acc);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
