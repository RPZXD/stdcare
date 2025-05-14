<?php
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");
include_once("../../class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentVisit = new StudentVisit($db);

$term = $user->getTerm();
$pee = $user->getPee();

// กำหนด mapping ระดับชั้นกับ major/room
$levels = [
    ['label' => 'ม.1', 'major' => 1],
    ['label' => 'ม.2', 'major' => 2],
    ['label' => 'ม.3', 'major' => 3],
    ['label' => 'ม.4', 'major' => 4],
    ['label' => 'ม.5', 'major' => 5],
    ['label' => 'ม.6', 'major' => 6],
];

$result = [];
foreach ($levels as $level) {
    $major = $level['major'];
    // นับจำนวนนักเรียนทั้งหมดในแต่ละชั้น
    $stmt = $db->prepare("SELECT COUNT(*) FROM student WHERE Stu_major = :major AND Stu_status = 1");
    $stmt->bindParam(':major', $major, PDO::PARAM_INT);
    $stmt->execute();
    $total = (int)$stmt->fetchColumn();

    // นักเรียนที่เยี่ยมบ้านแล้ว (ภาคเรียนที่ 1)
    $stmt1 = $db->prepare("SELECT COUNT(DISTINCT student.Stu_id) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_status = 1 AND visithome.Term = 1 AND visithome.Pee = :pee");
    $stmt1->bindParam(':major', $major, PDO::PARAM_INT);
    $stmt1->bindParam(':pee', $pee, PDO::PARAM_INT);
    $stmt1->execute();
    $visited_term1 = (int)$stmt1->fetchColumn();

    // นักเรียนที่เยี่ยมบ้านแล้ว (ภาคเรียนที่ 2)
    $stmt2 = $db->prepare("SELECT COUNT(DISTINCT student.Stu_id) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_status = 1 AND visithome.Term = 2 AND visithome.Pee = :pee");
    $stmt2->bindParam(':major', $major, PDO::PARAM_INT);
    $stmt2->bindParam(':pee', $pee, PDO::PARAM_INT);
    $stmt2->execute();
    $visited_term2 = (int)$stmt2->fetchColumn();

    $percent_term1 = $total > 0 ? round(($visited_term1 / $total) * 100, 2) : 0;
    $percent_term2 = $total > 0 ? round(($visited_term2 / $total) * 100, 2) : 0;

    $result[] = [
        'class' => $level['label'],
        'major' => $major,
        'visited_term1' => $visited_term1,
        'percent_term1' => $percent_term1,
        'visited_term2' => $visited_term2,
        'percent_term2' => $percent_term2,
        'total' => $total
    ];
}

echo json_encode(['success' => true, 'data' => $result]);
