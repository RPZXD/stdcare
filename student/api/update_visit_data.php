<?php
header('Content-Type: application/json');
session_start();

include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");

$response = ['success' => false, 'message' => 'เกิดข้อผิดพลาด'];

try {
    $stuId = $_POST['stuId'] ?? '';
    $term = $_POST['term'] ?? '';
    $pee = $_POST['pee'] ?? '';

    if (!$stuId || !$term || !$pee) {
        throw new Exception('ข้อมูลไม่ครบถ้วน');
    }

    // เตรียมข้อมูลคำตอบ
    $data = [
        'stuId' => $stuId,
        'term' => $term,
        'pee' => $pee,
        'vh20' => $_POST['vh20'] ?? ''
    ];
    for ($i = 1; $i <= 18; $i++) {
        $data['vh' . $i] = $_POST['vh' . $i] ?? null;
    }

    // เชื่อมต่อฐานข้อมูลและสร้างอ็อบเจ็กต์ StudentVisit
    $db = (new Database("phichaia_student"))->getConnection();
    $visit = new StudentVisit($db);

    // อัปเดตข้อมูล
    $result = $visit->updateVisitData($data);
    if ($result) {
        $response = ['success' => true, 'message' => 'อัปเดตข้อมูลเรียบร้อยแล้ว'];
    } else {
        throw new Exception('ไม่สามารถอัปเดตข้อมูลได้');
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
