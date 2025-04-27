<?php
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");
include_once("../../class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

$major = isset($_GET['major']) ? intval($_GET['major']) : 0;
$room = isset($_GET['room']) ? intval($_GET['room']) : 0;

if ($major < 1 || $major > 6 || $room < 1) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentVisit = new StudentVisit($db);

$pee = $user->getPee();

$data = $studentVisit->fetchStudentsWithVisitStatus($major, $room, $pee);

echo json_encode(['success' => true, 'data' => $data]);
