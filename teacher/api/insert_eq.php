<?php
require_once "../../config/Database.php";
require_once "../../class/EQ.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = (new Database("phichaia_student"))->getConnection();
    $eq = new EQ($db);

    // List of required fields
    $requiredFields = [
        "q1", "q2", "q3", "q4", "q5", "q6", "q7", "q8", "q9", "q10",
        "q11", "q12", "q13", "q14", "q15", "q16", "q17", "q18", "q19", "q20",
        "q21", "q22", "q23", "q24", "q25", "q26", "q27", "q28", "q29", "q30",
        "q31", "q32", "q33", "q34", "q35", "q36", "q37", "q38", "q39", "q40",
        "q41", "q42", "q43", "q44", "q45", "q46", "q47", "q48", "q49", "q50",
        "q51", "q52", "student_id", "pee", "term"
    ];

    $missingFields = [];
    $data = [];

    // Check for missing fields
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            $missingFields[] = $field;
        } else {
            $data[$field] = $_POST[$field];
        }
    }

    if (!empty($missingFields)) {
        echo json_encode([
            'success' => false,
            'message' => 'ข้อมูลไม่ครบถ้วน',
            'missing_fields' => $missingFields
        ]);
        exit;
    }

    try {
        // Extract specific fields
        $student_id = $data['student_id'];
        $pee = $data['pee'];
        $term = $data['term'];

        // Collect answers for EQ questions
        $answers = [];
        for ($i = 1; $i <= 52; $i++) {
            $answers["q$i"] = $data["q$i"];
        }

        // Save EQ data
        $eq->saveEQData($student_id, $answers, $pee, $term);
        echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
