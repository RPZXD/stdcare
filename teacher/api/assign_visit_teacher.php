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

// We accept POST request
$data = json_decode(file_get_contents('php://input'), true);
$stuId = $data['stuId'] ?? null;
$assignedTeacher = $data['assignedTeacher'] ?? null;

if (!$stuId) {
    echo json_encode(['success' => false, 'message' => 'Missing student ID']);
    exit();
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();
    
    // Update the assigned teacher in student_gps
    $stmt = $conn->prepare("UPDATE student_gps SET assigned_teacher = :teacher WHERE Stu_id = :id");
    $stmt->bindParam(':teacher', $assignedTeacher);
    $stmt->bindParam(':id', $stuId);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
