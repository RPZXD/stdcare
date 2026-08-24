<?php
session_start();
require_once "../../config/Database.php";
require_once "../../class/SDQ.php";

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['Teacher_login'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่มีสิทธิ์เข้าถึง (กรุณาเข้าสู่ระบบครู)']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
if (!$input) {
    $input = $_POST;
}

$student_id = $input['student_id'] ?? null;
$type = $input['type'] ?? 'self';
$pee = $input['pee'] ?? null;
$term = $input['term'] ?? null;

if (!$student_id || !$pee || !$term) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $sdq = new SDQ($db);

    $result = $sdq->deleteSDQ($type, $student_id, $pee, $term);
    if ($result) {
        echo json_encode(['success' => true, 'message' => 'ลบข้อมูล SDQ เรียบร้อยแล้ว นักเรียนสามารถทำแบบประเมินใหม่ได้']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้ หรือไม่พบข้อมูล']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
