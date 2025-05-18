<?php
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");
include_once("../../class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

$major = isset($_GET['major']) ? intval($_GET['major']) : 0;
if ($major < 1 || $major > 6) {
    echo json_encode(['success' => false, 'message' => 'major ไม่ถูกต้อง']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentVisit = new StudentVisit($db);

$term = $user->getTerm();
$pee = $user->getPee();

// ดึงรายชื่อห้องทั้งหมดในระดับชั้นนี้
$stmt = $db->prepare("SELECT DISTINCT Stu_room FROM student WHERE Stu_major = :major AND Stu_status = 1 ORDER BY Stu_room ASC");
$stmt->bindParam(':major', $major, PDO::PARAM_INT);
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_COLUMN);

$result = [];
foreach ($rooms as $room) {
    // นักเรียนทั้งหมดในห้องนี้
    $stmtTotal = $db->prepare("SELECT COUNT(*) FROM student WHERE Stu_major = :major AND Stu_room = :room AND Stu_status = 1");
    $stmtTotal->bindParam(':major', $major, PDO::PARAM_INT);
    $stmtTotal->bindParam(':room', $room, PDO::PARAM_INT);
    $stmtTotal->execute();
    $total = (int)$stmtTotal->fetchColumn();

    // นักเรียนที่เยี่ยมบ้านแล้วในห้องนี้ (ภาคเรียนที่ 1)
    $stmtVisited1 = $db->prepare("SELECT COUNT(DISTINCT student.Stu_id) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_room = :room AND student.Stu_status = 1 AND visithome.Term = 1 AND visithome.Pee = :pee");
    $stmtVisited1->bindParam(':major', $major, PDO::PARAM_INT);
    $stmtVisited1->bindParam(':room', $room, PDO::PARAM_INT);
    $stmtVisited1->bindParam(':pee', $pee, PDO::PARAM_INT);
    $stmtVisited1->execute();
    $visited_term1 = (int)$stmtVisited1->fetchColumn();

    // นักเรียนที่เยี่ยมบ้านแล้วในห้องนี้ (ภาคเรียนที่ 2)
    $stmtVisited2 = $db->prepare("SELECT COUNT(DISTINCT student.Stu_id) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_room = :room AND student.Stu_status = 1 AND visithome.Term = 2 AND visithome.Pee = :pee");
    $stmtVisited2->bindParam(':major', $major, PDO::PARAM_INT);
    $stmtVisited2->bindParam(':room', $room, PDO::PARAM_INT);
    $stmtVisited2->bindParam(':pee', $pee, PDO::PARAM_INT);
    $stmtVisited2->execute();
    $visited_term2 = (int)$stmtVisited2->fetchColumn();

    $percent_term1 = $total > 0 ? round(($visited_term1 / $total) * 100, 2) : 0;
    $percent_term2 = $total > 0 ? round(($visited_term2 / $total) * 100, 2) : 0;

    $result[] = [
        'room' => $room,
        'total' => $total,
        'visited_term1' => $visited_term1,
        'percent_term1' => $percent_term1,
        'visited_term2' => $visited_term2,
        'percent_term2' => $percent_term2
    ];
}

echo json_encode(['success' => true, 'data' => $result]);
