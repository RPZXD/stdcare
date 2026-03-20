<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Screeningdata.php";

$student_id = $_GET['student_id'] ?? '';
$pee = $_GET['pee'] ?? '';

if (empty($student_id) || empty($pee)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing student_id or pee'
    ]);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $screening = new ScreeningData($db);
    $data = $screening->getScreeningDataByStudentId($student_id, $pee);

    if (!empty($data)) {
        echo json_encode([
            'status' => 'success',
            'data' => $data
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No screening data found for this student and year.'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
