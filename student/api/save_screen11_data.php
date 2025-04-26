<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['success' => false, 'message' => 'ไม่ได้รับอนุญาต']);
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/Screeningdata.php');

$db = (new Database("phichaia_student"))->getConnection();
$screening = new ScreeningData($db);

// รับข้อมูลจาก POST
$data = [];
$fields = [
    'student_id', 'pee', 'special_ability', 'special_ability_detail',
    'study_status', 'study_risk', 'study_problem',
    'health_status', 'health_risk', 'health_problem',
    'economic_status', 'economic_risk', 'economic_problem',
    'welfare_status', 'welfare_risk', 'welfare_problem',
    'drug_status', 'drug_risk', 'drug_problem',
    'violence_status', 'violence_risk', 'violence_problem',
    'sex_status', 'sex_risk', 'sex_problem',
    'game_status', 'game_risk', 'game_problem',
    'special_need_status', 'special_need_type',
    'it_status', 'it_risk', 'it_problem'
];

foreach ($fields as $field) {
    if (isset($_POST[$field])) {
        // ถ้าเป็น array ให้เก็บเป็น JSON string
        if (is_array($_POST[$field])) {
            $data[$field] = json_encode($_POST[$field], JSON_UNESCAPED_UNICODE);
        } else {
            $data[$field] = $_POST[$field];
        }
    } else {
        $data[$field] = null;
    }
}

// special_ability_detail อาจถูก serialize เป็น JSON แล้วจากฟอร์ม
if (empty($data['special_ability_detail'])) {
    $data['special_ability_detail'] = null;
} else {
    // ถ้าไม่ใช่ valid JSON ให้ encode
    json_decode($data['special_ability_detail']);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data['special_ability_detail'] = json_encode($data['special_ability_detail'], JSON_UNESCAPED_UNICODE);
    }
}

$result = $screening->insertScreening($data);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
