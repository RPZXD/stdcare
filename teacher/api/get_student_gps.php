<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');

if (!isset($_SESSION['Teacher_login'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once __DIR__ . '/../../config/Database.php';

if (!isset($_GET['stuId'])) {
    echo json_encode(['success' => false, 'message' => 'Missing student ID']);
    exit();
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();
    
    $stmt = $conn->prepare("SELECT latitude, longitude FROM student_gps WHERE Stu_id = :id LIMIT 1");
    $stmt->bindParam(':id', $_GET['stuId']);
    $stmt->execute();
    $gps = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($gps) {
        echo json_encode(['success' => true, 'data' => $gps]);
    } else {
        echo json_encode(['success' => false, 'message' => 'No GPS data']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
