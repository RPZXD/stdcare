<?php
require_once "../../config/Database.php";
require_once "../../class/SDQ.php";

header('Content-Type: application/json');

try {
    // Initialize database connection
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    // Initialize SDQ class
    $sdq = new SDQ($db);

    // Get POST data
    $student_id = $_POST['student_id'] ?? null;
    $answers = array_filter($_POST, function($key) {
        return strpos($key, 'q') === 0; // Filter only question keys (e.g., q1, q2, ...)
    }, ARRAY_FILTER_USE_KEY);
    $memo = $_POST['memo'] ?? null;
    $pee = $_POST['pee'] ?? null;
    $term = $_POST['term'] ?? null;

    if (!$student_id || !$pee || !$term) {
        throw new Exception("ข้อมูลไม่ครบถ้วน");
    }

    // Validate that all questions are answered
    $total_questions = 25; // Total number of questions
    $answered_questions = count($answers);

    if ($answered_questions < $total_questions) {
        echo json_encode(['success' => false, 'message' => 'กรุณากรอกคำตอบให้ครบทุกข้อ']);
        exit;
    }

    foreach ($answers as $key => $value) {
        if ($value === '' || $value === null) {
            echo json_encode(['success' => false, 'message' => "คำถาม {$key} ยังไม่ได้กรอกคำตอบ"]);
            exit;
        }
    }

    // Save SDQ data
    $sdq->saveSDQpar($student_id, $answers, $memo, $pee, $term);

    echo json_encode(['success' => true, 'message' => 'บันทึกข้อมูลสำเร็จ']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
