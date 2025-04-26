<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Wroom.php";

$major = $_POST['major'] ?? '';
$room = $_POST['room'] ?? '';
$pee = $_POST['pee'] ?? '';
$term = $_POST['term'] ?? '';
$positions = $_POST['position'] ?? [];
$stdids = $_POST['stdid'] ?? [];
$maxim = $_POST['maxim'] ?? '';

// ตรวจสอบจำนวนแต่ละตำแหน่ง
$positionLimits = [
    "1" => 1,
    "2" => 1,
    "3" => 1,
    "4" => 1,
    "5" => 1,
    "10" => 1,
    "11" => 1,
    "6" => 4,
    "7" => 4,
    "8" => 4,
    "9" => 4
];
$count = [];
foreach ($positions as $val) {
    if ($val && isset($positionLimits[$val])) {
        $count[$val] = ($count[$val] ?? 0) + 1;
    }
}
$over = [];
foreach ($positionLimits as $key => $limit) {
    if (($count[$key] ?? 0) > $limit) {
        $over[] = $key;
    }
}
if (!empty($over)) {
    echo json_encode([
        'success' => false,
        'message' => 'เลือกตำแหน่งเกินจำนวนที่กำหนด: ' . implode(', ', $over)
    ]);
    exit;
}

$db = (new Database("phichaia_student"))->getConnection();
$wroom = new Wroom($db);

$success = $wroom->saveWroom($major, $room, $pee, $term, $positions, $stdids, $maxim);

echo json_encode(['success' => $success]);
