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

$data = json_decode(file_get_contents('php://input'), true);
$assignments = $data['assignments'] ?? null;

if (!is_array($assignments)) {
    echo json_encode(['success' => false, 'message' => 'Invalid assignments data']);
    exit();
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();
    
    $conn->beginTransaction();
    
    $stmt = $conn->prepare("UPDATE student_gps SET visit_day = :day WHERE Stu_id = :id");
    
    foreach ($assignments as $stuId => $day) {
        $dayVal = ($day === null || $day === '') ? null : intval($day);
        $stmt->bindValue(':day', $dayVal, $dayVal === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':id', $stuId, PDO::PARAM_STR);
        $stmt->execute();
    }
    
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    if (isset($conn) && $conn->inTransaction()) {
        $conn->rollBack();
    }
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
