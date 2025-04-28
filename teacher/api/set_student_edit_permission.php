<?php
header('Content-Type: application/json');
$file = __DIR__ . '/student_edit_permission.json';

$room_key = $_POST['room_key'] ?? '';
$allowEdit = isset($_POST['allowEdit']) ? (bool)$_POST['allowEdit'] : false;
$by = $_POST['by'] ?? '';
$timestamp = date('Y-m-d H:i:s');

if (!$room_key) {
    echo json_encode(['success' => false, 'msg' => 'no room_key']);
    exit;
}

// อ่านข้อมูลเดิม
$data = [];
if (file_exists($file)) {
    $data = json_decode(file_get_contents($file), true);
}
if (!isset($data['permissions'])) $data['permissions'] = [];

// อัปเดตเฉพาะห้อง
$data['permissions'][$room_key] = [
    'allowEdit' => $allowEdit,
    'by' => $by,
    'timestamp' => $timestamp
];

// เขียนกลับ
file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);
