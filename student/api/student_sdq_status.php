<?php
require_once("../../config/Database.php");
require_once("../../class/SDQ.php");

header('Content-Type: application/json');

$pee = isset($_GET['pee']) ? $_GET['pee'] : null;
$term = isset($_GET['term']) ? $_GET['term'] : null;
$stuId = isset($_GET['stuId']) ? $_GET['stuId'] : null;


if (!$pee || !$stuId || !$term) {
    echo json_encode([
        'success' => false,
        'message' => 'ข้อมูลไม่ครบถ้วน'
    ]);
    exit;
}

$db = (new Database("phichaia_student"))->getConnection();
$sdq = new SDQ($db);

// ตรวจสอบ SDQ self
$selfData = $sdq->getSDQSelfData($stuId, $pee, $term);
$selfStatus = !empty($selfData['answers']) ? 'saved' : 'not_saved';

// ตรวจสอบ SDQ parent
$parentData = $sdq->getSDQParData($stuId, $pee, $term);
$parentStatus = !empty($parentData['answers']) ? 'saved' : 'not_saved';

echo json_encode([
    'success' => true,
    'self' => [
        'status' => $selfStatus
    ],
    'parent' => [
        'status' => $parentStatus
    ]
]);
