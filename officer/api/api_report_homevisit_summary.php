<?php
include_once("../../config/Database.php");
include_once("../../class/StudentVisit.php");
include_once("../../class/UserLogin.php");

header('Content-Type: application/json; charset=utf-8');

$major = isset($_GET['major']) ? intval($_GET['major']) : 0;
$room = isset($_GET['room']) ? $_GET['room'] : 0; // อาจเป็น "all"

if ($major < 1 || $major > 6) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$user = new UserLogin($db);
$studentVisit = new StudentVisit($db);

$pee = $user->getPee();

if ($room === "all") {
    // รวมทุกห้องในระดับชั้นนี้
    $stmt = $db->prepare("SELECT COUNT(DISTINCT Stu_id) FROM student WHERE Stu_major = :major AND Stu_status = 1");
    $stmt->bindParam(':major', $major, PDO::PARAM_INT);
    $stmt->execute();
    $total = (int)$stmt->fetchColumn();
} else {
    $room = intval($room);
    if ($room < 1) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ถูกต้อง']);
        exit;
    }
    $stmt = $db->prepare("SELECT COUNT(*) FROM student WHERE Stu_major = :major AND Stu_room = :room AND Stu_status = 1");
    $stmt->bindParam(':major', $major, PDO::PARAM_INT);
    $stmt->bindParam(':room', $room, PDO::PARAM_INT);
    $stmt->execute();
    $total = (int)$stmt->fetchColumn();
}

$summary = [];
for ($item_type = 1; $item_type <= 18; $item_type++) {
    $answers = $studentVisit->getAllAnswersForQuestion($item_type);
    $q = $studentVisit->getQuestionAnswer($item_type, 1); // Get question text
    $questionText = $q['question'];
    $row = [
        'question' => $questionText,
        'answers' => []
    ];
    if (is_array($answers)) {
        foreach ($answers as $idx => $answer) {
            $vh_col = "vh{$item_type}";
            $answer_idx = $idx + 1;
            if ($room === "all") {
                $stmt2 = $db->prepare("SELECT COUNT(DISTINCT student.Stu_id) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_status = 1 AND visithome.{$vh_col} = :answer_idx AND visithome.Pee = :pee AND visithome.Term = 1");
                $stmt2->bindParam(':major', $major, PDO::PARAM_INT);
            } else {
                $stmt2 = $db->prepare("SELECT COUNT(*) FROM student INNER JOIN visithome ON student.Stu_id = visithome.Stu_id WHERE student.Stu_major = :major AND student.Stu_room = :room AND student.Stu_status = 1 AND visithome.{$vh_col} = :answer_idx AND visithome.Pee = :pee AND visithome.Term = 1");
                $stmt2->bindParam(':major', $major, PDO::PARAM_INT);
                $stmt2->bindParam(':room', $room, PDO::PARAM_INT);
            }
            $stmt2->bindParam(':answer_idx', $answer_idx, PDO::PARAM_INT);
            $stmt2->bindParam(':pee', $pee, PDO::PARAM_INT);
            $stmt2->execute();
            $count = (int)$stmt2->fetchColumn();
            $percent = ($total > 0 && $count > 0) ? round(($count / $total) * 100, 2) : 0;
            $row['answers'][] = [
                'answer' => $answer,
                'count' => $count,
                'percent' => $percent
            ];
        }
    }
    $summary[] = $row;
}

echo json_encode(['success' => true, 'data' => $summary]);
