<?php
require_once '../../config/Database.php';
require_once '../../class/SDQ.php';

header('Content-Type: application/json');

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (!$class || !$room || !$pee || !$term) {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);

// ดึงรายชื่อนักเรียนในห้อง
$query = "
    SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur
    FROM student
    WHERE Stu_major = :class AND Stu_room = :room AND Stu_status = 1
    ORDER BY Stu_no ASC
";
$stmt = $db->prepare($query);
$stmt->execute([
    ':class' => $class,
    ':room' => $room
]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

function calc_sdq_score($answers) {
    // หมวดคะแนนปัญหา (4 หมวดแรก)
    $cat = [
        'emotion' => [3,8,13,16,24],
        'conduct' => [5,12,18,22],
        'hyper' => [2,10,15,21],
        'peer' => [6,11,14,19,23]
    ];
    $sum = 0;
    foreach ($cat as $qs) {
        foreach ($qs as $q) {
            $sum += (int)($answers["q$q"] ?? 0);
        }
    }
    return $sum;
}
function sdq_result_text($score) {
    if ($score >= 20) return 'มีปัญหา';
    if ($score >= 14) return 'ภาวะเสี่ยง';
    return 'ปกติ';
}

$data = [];
foreach ($students as $stu) {
    $stu_id = $stu['Stu_id'];
    $full_name = trim($stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur']);
    // Self
    $self = $sdq->getSDQSelfData($stu_id, $pee, $term);
    $std_score = null;
    $std_result = null;
    if (!empty($self['answers'])) {
        $std_score = calc_sdq_score($self['answers']);
        $std_result = sdq_result_text($std_score);
    }
    // Teach
    $teach = $sdq->getSDQTeachData($stu_id, $pee, $term);
    $teach_score = null;
    $teach_result = null;
    if (!empty($teach['answers'])) {
        $teach_score = calc_sdq_score($teach['answers']);
        $teach_result = sdq_result_text($teach_score);
    }
    // Par
    $par = $sdq->getSDQParData($stu_id, $pee, $term);
    $par_score = null;
    $par_result = null;
    if (!empty($par['answers'])) {
        $par_score = calc_sdq_score($par['answers']);
        $par_result = sdq_result_text($par_score);
    }

    $data[] = [
        'Stu_no' => $stu['Stu_no'],
        'Stu_id' => $stu_id,
        'full_name' => $full_name,
        'std_score' => $std_score,
        'std_result' => $std_result,
        'teach_score' => $teach_score,
        'teach_result' => $teach_result,
        'par_score' => $par_score,
        'par_result' => $par_result
    ];
}

echo json_encode(['success' => true, 'data' => $data]);
