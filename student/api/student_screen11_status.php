<?php
header('Content-Type: application/json');
require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

$pee = $_GET['pee'] ?? null;
$term = $_GET['term'] ?? null;
$stuId = $_GET['stuId'] ?? null;

if (!$pee || !$stuId) {
    echo json_encode(['error' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$screening = new ScreeningData($conn);

$data = $screening->getScreeningDataByStudentId($stuId, $pee);

$result = [
    'self' => [
        'status' => !empty($data) ? 'saved' : 'not_saved',
        'data' => $data
    ]
];

echo json_encode($result);
