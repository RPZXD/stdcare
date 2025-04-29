<?php
require_once '../../config/Database.php';
require_once '../../class/SDQ.php';
require_once("../../class/UserLogin.php");

header('Content-Type: application/json');

$stu_id = $_GET['stu_id'] ?? '';
if (!$stu_id) {
    echo json_encode(['success' => false, 'msg' => 'Missing student id']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$sdq = new SDQ($db);
$user = new UserLogin($db);

$term = $user->getTerm();
$pee = $user->getPee();

$query = "SELECT * FROM student WHERE Stu_id = :id LIMIT 1";
$stmt = $db->prepare($query);
$stmt->bindParam(":id", $stu_id);
$stmt->execute();
$stu = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$stu) {
    echo json_encode(['success' => false, 'msg' => 'Student not found']);
    exit;
}

$existingData = $sdq->getSDQSelfData($stu_id, $pee, $term);
$answers = $existingData['answers'] ?? [];
$memo = $existingData['memo'] ?? '';
$impact = $existingData['impact'] ?? [];

$categories = [
    'อารมณ์ 😖' => [3, 8, 13, 16, 24],
    'เกเร 😠' => [5, 12, 18, 22],
    'สมาธิ/ไฮเปอร์ ⚡' => [2, 10, 15, 21],
    'เพื่อน 🧍‍♂️🧍‍♀️' => [6, 11, 14, 19, 23],
    'จุดแข็ง 🤝' => [1, 4, 7, 9, 17, 20, 25],
];

$categoryScores = [];
foreach ($categories as $label => $questions) {
    $score = 0;
    foreach ($questions as $qnum) {
        $score += (int)($answers["q$qnum"] ?? 0);
    }
    $categoryScores[$label] = $score;
}
$totalProblemScore = $categoryScores['อารมณ์ 😖'] + $categoryScores['เกเร 😠'] + $categoryScores['สมาธิ/ไฮเปอร์ ⚡'] + $categoryScores['เพื่อน 🧍‍♂️🧍‍♀️'];

function scoreLevel($score, $category) {
    $cutoffs = [
        'อารมณ์ 😖' => [4, 6],
        'เกเร 😠' => [3, 5],
        'สมาธิ/ไฮเปอร์ ⚡' => [5, 7],
        'เพื่อน 🧍‍♂️🧍‍♀️' => [3, 6],
        'จุดแข็ง 🤝' => [5, 6],
    ];
    [$normal, $borderline] = $cutoffs[$category] ?? [0, 0];
    if ($category == 'จุดแข็ง 🤝') {
        return $score >= $borderline ? 'ปกติ/มีจุดแข็ง' : ($score >= $normal ? 'ภาวะเสี่ยง' : 'มีปัญหา');
    }
    return $score <= $normal ? 'ปกติ' : ($score <= $borderline ? 'ภาวะเสี่ยง' : 'มีปัญหา');
}
function impactText($score) {
    return $score == 0 ? 'ไม่มีปัญหา' : ($score == 1 ? 'มีปัญหาเล็กน้อย' : 'มีปัญหารุนแรง');
}
function impactColor($score) {
    return $score == 0 ? 'bg-green-500' : ($score == 1 ? 'bg-yellow-500' : 'bg-red-500');
}

// Impact fields (simulate if not present)
$impact = $existingData['impact'] ?? [
    'home' => 0, 'leisure' => 0, 'friend' => 0, 'classroom' => 0, 'burden' => 0
];

$categoryLevels = [];
foreach ($categoryScores as $label => $score) {
    $categoryLevels[$label] = scoreLevel($score, $label);
}
$impactTexts = [];
$impactColors = [];
foreach (['home','leisure','friend','classroom','burden'] as $k) {
    $impactTexts[$k] = impactText($impact[$k] ?? 0);
    $impactColors[$k] = impactColor($impact[$k] ?? 0);
}

echo json_encode([
    'success' => true,
    'student_name' => $stu['Stu_pre'] . $stu['Stu_name'] . ' ' . $stu['Stu_sur'],
    'student_no' => $stu['Stu_no'],
    'student_class' => $stu['Stu_major'],
    'student_room' => $stu['Stu_room'],
    'categoryScores' => $categoryScores,
    'categoryLevels' => $categoryLevels,
    'totalProblemScore' => $totalProblemScore,
    'memo' => $memo,
    'impactTexts' => $impactTexts,
    'impactColors' => $impactColors
]);
