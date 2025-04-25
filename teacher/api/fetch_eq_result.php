<?php
require_once '../../config/Database.php';
require_once '../../class/EQ.php';

header('Content-Type: application/json');

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$eq = new EQ($db);

$result = [
    'success' => false,
    'data' => []
];

try {
    $students = $eq->getEQByClassAndRoom($class, $room, $pee, $term);

    foreach ($students as $stu) {
        $eqData = $eq->getEQData($stu['Stu_id'], $pee, $term);

        // คำนวณคะแนนรวม (ตัวอย่าง: รวม 52 ข้อ)
        $score = null;
        $level = '-';
        if ($eqData) {
            $score = 0;
            for ($i = 1; $i <= 52; $i++) {
                $score += intval($eqData["EQ$i"] ?? 0);
            }
            // ตัวอย่างเกณฑ์ระดับ (ควรปรับตามเกณฑ์จริง)
            if ($score >= 180) {
                $level = 'EQ สูง';
            } elseif ($score >= 130) {
                $level = 'EQ ปานกลาง';
            } else {
                $level = 'EQ ต่ำ';
            }
        }

        $result['data'][] = [
            'Stu_no' => $stu['Stu_no'],
            'Stu_id' => $stu['Stu_id'],
            'full_name' => $stu['full_name'],
            'eq_score' => $score,
            'eq_level' => $level
        ];
    }

    $result['success'] = true;
} catch (Exception $e) {
    $result['success'] = false;
    $result['error'] = $e->getMessage();
}

echo json_encode($result);
