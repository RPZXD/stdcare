<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['error' => 'unauthorized']);
    exit;
}

require_once("../../config/Database.php");
require_once("../../class/StudentVisit.php");

$student_id = $_SESSION['Student_login'];
$db = new Database("phichaia_student");
$conn = $db->getConnection();
$visit = new StudentVisit($conn);

// รับค่า pee จาก query string ถ้ามี
$pee = isset($_GET['pee']) ? intval($_GET['pee']) : (date('Y') + 543);

$visits = [];
for ($i = 1; $i <= 2; $i++) {
    $data = $visit->getVisitData($student_id, $i, $pee);
    $visits[] = [
        'visit_no' => $i,
        'status' => $data ? 'saved' : 'unsaved'
    ];
}

echo json_encode(['visits' => $visits]);
