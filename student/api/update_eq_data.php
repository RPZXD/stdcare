<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/EQ.php');

$student_id = $_POST['stuId'] ?? null;
$pee = $_POST['pee'] ?? null;
$term = $_POST['term'] ?? null;

if (!$student_id || !$pee || !$term) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

// เก็บคำตอบ 52 ข้อ (ใช้ eq1 ถึง eq52)
$answers = [];
for ($i = 1; $i <= 52; $i++) {
    $key = "eq$i";
    $answers[$key] = $_POST[$key] ?? null;
}

try {
    $db = new Database("phichaia_student");
    $conn = $db->getConnection();
    $eq = new EQ($conn);

    $eq->updateEQData($student_id, $answers, $pee, $term);

    echo json_encode(['success' => true, 'message' => 'อัปเดตข้อมูล EQ สำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
