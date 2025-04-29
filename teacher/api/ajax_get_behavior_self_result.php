<?php
header('Content-Type: application/json');
session_start();

include_once("../../config/Database.php");
include_once("../../class/Behavior.php");
include_once("../../class/UserLogin.php");

// ตรวจสอบสิทธิ์ครู
if (!isset($_SESSION['Teacher_login'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['stu_id']) || empty($_GET['stu_id'])) {
    echo json_encode(['success' => false, 'error' => 'Missing student id']);
    exit;
}

$stu_id = $_GET['stu_id'];

// เชื่อมต่อฐานข้อมูล
$db = (new Database("phichaia_student"))->getConnection();
$user = new UserLogin($db);
$term = $user->getTerm();
$pee = $user->getPee();

$behavior = new Behavior($db);
$behaviorList = $behavior->getBehaviorsByStudentId($stu_id, $term, $pee);

// เตรียมข้อมูลนักเรียน
$studentInfo = null;
if ($behaviorList && count($behaviorList) > 0) {
    $first = $behaviorList[0];
    $studentInfo = [
        'student_name' => $first['Stu_pre'] . $first['Stu_name'] . ' ' . $first['Stu_sur'],
        'student_no' => $first['Stu_no'] ?? '',
        'student_class' => $first['Stu_major'] ?? '',
        'student_room' => $first['Stu_room'] ?? ''
    ];
} else {
    // ดึงข้อมูลนักเรียนกรณีไม่มีการหักคะแนน
    $stmt = $db->prepare("SELECT Stu_pre, Stu_name, Stu_sur, Stu_no, Stu_major, Stu_room FROM student WHERE Stu_id = :stu_id LIMIT 1");
    $stmt->bindParam(':stu_id', $stu_id);
    $stmt->execute();
    $stu = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($stu) {
        $studentInfo = [
            'student_name' => $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'],
            'student_no' => $stu['Stu_no'],
            'student_class' => $stu['Stu_major'],
            'student_room' => $stu['Stu_room']
        ];
    }
}

// คำนวณคะแนนคงเหลือ
$total_score = 100;
if ($behaviorList && is_array($behaviorList)) {
    $sum = 0;
    foreach ($behaviorList as $b) {
        $sum += (int)$b['behavior_score'];
    }
    $total_score -= $sum;
}

// เตรียมข้อมูลสำหรับ frontend
echo json_encode([
    'success' => true,
    'studentInfo' => $studentInfo,
    'total_score' => $total_score,
    'behaviorList' => $behaviorList ? $behaviorList : []
]);
