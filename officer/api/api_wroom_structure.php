<?php
require_once("../../config/Database.php");
require_once("../../class/Wroom.php");
require_once("../../class/Teacher.php");

header('Content-Type: application/json; charset=utf-8');

$major = $_GET['major'] ?? '';
$room = $_GET['room'] ?? '';
$pee = date('Y') + 543;

if (!$major || !$room) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$wroomObj = new Wroom($db);
$teacherObj = new Teacher($db);

// ดึงครูที่ปรึกษา
$advisors = $teacherObj->getTeachersByClassAndRoom($major, $room);

// ดึงข้อมูลคณะกรรมการ
$wroom = $wroomObj->getWroomStudents($major, $room, $pee);

// ดึง maxim
$maxim = $wroomObj->getMaxim($major, $room, $pee);

// ตำแหน่ง (สำหรับโครงสร้าง)
$positions = [
    "1" => "หัวหน้าห้อง",
    "2" => "รองหัวหน้าฝ่ายการเรียน",
    "3" => "รองหัวหน้าฝ่ายการงาน",
    "4" => "รองหัวหน้าฝ่ายกิจกรรม",
    "5" => "รองหัวหน้าฝ่ายสารวัตรนักเรียน",
    "10" => "เลขานุการ",
    "11" => "ผู้ช่วยเลขานุการ",
    "6" => "นักเรียนแกนนำฝ่ายการเรียน",
    "7" => "นักเรียนแกนนำฝ่ายการงาน",
    "8" => "นักเรียนแกนนำฝ่ายกิจกรรม",
    "9" => "นักเรียนแกนนำฝ่ายสารวัตรนักเรียน",
];

// จัดกลุ่มตามตำแหน่ง
$grouped = [];
foreach ($wroom as $stu) {
    $pos = $stu['wposit'];
    if (!isset($grouped[$pos])) $grouped[$pos] = [];
    $grouped[$pos][] = $stu;
}

echo json_encode([
    'advisors' => $advisors,
    'positions' => $positions,
    'grouped' => $grouped,
    'maxim' => $maxim
]);
