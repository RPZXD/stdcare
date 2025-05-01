<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";

// ตรวจสอบ method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// ตรวจสอบ parameter
if (!isset($_POST['id'], $_POST['class'], $_POST['room'], $_POST['term'], $_POST['pee'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$pictureIdx = intval($_POST['id']); // index ของรูป (0-3)
$class = $_POST['class'];
$room = $_POST['room'];
$term = $_POST['term'];
$pee = $_POST['pee'];

// เชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// ดึงข้อมูลแถวเดียว
$stmt = $db->prepare("SELECT picture1, picture2, picture3, picture4 FROM tb_picmeeting WHERE Stu_major = ? AND Stu_room = ? AND term = ? AND pee = ? LIMIT 1");
$stmt->execute([$class, $room, $term, $pee]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Picture record not found']);
    exit;
}

$pictureFields = ['picture1', 'picture2', 'picture3', 'picture4'];
if (!isset($pictureFields[$pictureIdx])) {
    echo json_encode(['success' => false, 'message' => 'Invalid picture index']);
    exit;
}

$picField = $pictureFields[$pictureIdx];
$picFile = $row[$picField];

if (empty($picFile)) {
    echo json_encode(['success' => false, 'message' => 'No image found in this slot']);
    exit;
}

// ลบไฟล์ภาพ
$imagePath = '../../teacher/uploads/picmeeting' . $term . $pee . '/' . $picFile;
if (file_exists($imagePath)) {
    @unlink($imagePath);
}

// อัปเดตฐานข้อมูลให้ค่านี้เป็นค่าว่าง
$stmt = $db->prepare("UPDATE tb_picmeeting SET $picField = NULL WHERE Stu_major = ? AND Stu_room = ? AND term = ? AND pee = ?");
if ($stmt->execute([$class, $room, $term, $pee])) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update database']);
}
