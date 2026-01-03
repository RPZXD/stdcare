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

// ดึงคะแนนเพิ่มจากกิจกรรมจิตอาสา (จาก phichaia_eventstd)
$bonus_points = 0;
try {
    include_once("../../classes/DatabaseEventstd.php");
    $eventDb = new \App\DatabaseEventstd();
    $eventPdo = $eventDb->getPDO();
    
    $sqlBonus = "SELECT COALESCE(SUM(a.hours), 0) AS bonus_hours
                 FROM student_activity_logs sal
                 INNER JOIN activities a ON sal.activity_id = a.id
                 WHERE sal.student_id = :stu_id 
                   AND a.category = 'จิตอาสา'
                   AND a.term = :term 
                   AND a.pee = :pee";
    $stmtBonus = $eventPdo->prepare($sqlBonus);
    $stmtBonus->execute(['stu_id' => $stu_id, 'term' => $term, 'pee' => $pee]);
    $resBonus = $stmtBonus->fetch();
    $bonus_points = (int)($resBonus['bonus_hours'] ?? 0);
} catch (Exception $e) {
    // กรณีไม่มีฐานข้อมูลหรือตารางนี้ให้เป็น 0
    $bonus_points = 0;
}

// คำนวณคะแนนคงเหลือ
$total_deduction = 0;
if ($behaviorList && is_array($behaviorList)) {
    foreach ($behaviorList as $b) {
        $total_deduction += (int)$b['behavior_score'];
    }
}
$net_score = 100 - $total_deduction + $bonus_points;
if($net_score < 0) $net_score = 0;
if($net_score > 100) $net_score = 100;

// เตรียมข้อมูลสำหรับ frontend
echo json_encode([
    'success' => true,
    'studentInfo' => $studentInfo,
    'total_score' => $net_score,
    'total_deduction' => $total_deduction,
    'bonus_points' => $bonus_points,
    'behaviorList' => $behaviorList ? $behaviorList : []
]);
