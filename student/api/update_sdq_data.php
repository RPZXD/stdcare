<?php
require_once("../../config/Database.php");
require_once("../../class/SDQ.php");

header('Content-Type: application/json');

$type = $_POST['type'] ?? null; // 'self' หรือ 'parent'
$stuId = $_POST['stuId'] ?? null;
$pee = $_POST['pee'] ?? null;
$term = $_POST['term'] ?? null;
$memo = $_POST['memo'] ?? '';
$answers = [];
for ($i = 1; $i <= 25; $i++) {
    // รองรับทั้ง sdq1 และ q1
    if (isset($_POST["sdq$i"])) {
        $answers["q$i"] = $_POST["sdq$i"];
    } elseif (isset($_POST["q$i"])) {
        $answers["q$i"] = $_POST["q$i"];
    } else {
        $answers["q$i"] = null;
    }
}

if (!$type || !$stuId || !$pee || !$term) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);

try {
    if ($type === 'self') {
        $sdq->saveSDQSelf($stuId, $answers, $memo, $pee, $term);
    } elseif ($type === 'parent') {
        $sdq->saveSDQpar($stuId, $answers, $memo, $pee, $term);
    } else {
        throw new Exception('ประเภทไม่ถูกต้อง');
    }
    echo json_encode(['success' => true, 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
