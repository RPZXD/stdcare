<?php
require_once '../../config/Database.php';
require_once '../../class/Screeningdata.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$screening = new ScreeningData($db);

// รับข้อมูลจากฟอร์ม
$data = $_POST;

// แปลงข้อมูล array เป็น json string สำหรับฟิลด์ที่เป็นกลุ่ม checkbox/radio หลายค่า
$arrayFields = [
    'special_ability_detail', 'study_risk', 'study_problem', 'health_risk', 'health_problem',
    'economic_risk', 'economic_problem', 'welfare_risk', 'welfare_problem',
    'drug_risk', 'drug_problem', 'violence_risk', 'violence_problem',
    'sex_risk', 'sex_problem', 'game_risk', 'game_problem',
    'it_risk', 'it_problem'
];

// กรณีความสามารถพิเศษ (special_ability_detail) ต้องรวมข้อมูลจาก special_0[], special_1[], ...
if (isset($data['special_ability']) && $data['special_ability'] === 'มี') {
    $special_ability_detail = [];
    foreach ($data as $key => $val) {
        if (preg_match('/^special_(\d+)$/', $key, $m)) {
            $special_ability_detail['special_' . $m[1]] = $val;
        }
    }
    $data['special_ability_detail'] = !empty($special_ability_detail) ? json_encode($special_ability_detail, JSON_UNESCAPED_UNICODE) : null;
} else {
    $data['special_ability_detail'] = null;
}

// ฟิลด์ checkbox อื่นๆ
foreach ($arrayFields as $field) {
    if (isset($data[$field])) {
        if (is_array($data[$field])) {
            $data[$field] = json_encode($data[$field], JSON_UNESCAPED_UNICODE);
        }
    } else {
        $data[$field] = null;
    }
}

// ฟิลด์ radio เฉพาะข้อ 10
if (isset($data['special_need_status']) && $data['special_need_status'] === 'มี') {
    $data['special_need_type'] = $data['special_need_type'] ?? '';
} else {
    $data['special_need_type'] = null;
}

// ฟิลด์หลัก (ลบ term ออก)
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

// เรียกใช้ฟังก์ชันใน ScreeningData
$result = $screening->updateScreening($data, $fields);

echo json_encode($result);
