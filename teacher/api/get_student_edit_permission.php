<?php
header('Content-Type: application/json');
$file = __DIR__ . '/student_edit_permission.json';

$room_key = $_GET['room_key'] ?? '';

if (!file_exists($file)) {
    echo json_encode(['allowEdit' => false]);
    exit;
}

$data = json_decode(file_get_contents($file), true);

if ($room_key && isset($data['permissions'][$room_key])) {
    echo json_encode($data['permissions'][$room_key], JSON_UNESCAPED_UNICODE);
} else {
    echo json_encode(['allowEdit' => false, 'by' => '', 'timestamp' => '']);
}
