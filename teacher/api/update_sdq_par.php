<?php
require_once '../../config/Database.php';
require_once '../../class/SDQ.php';

header('Content-Type: application/json');

try {
    // Initialize database connection
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();

    // Initialize SDQ class
    $sdq = new SDQ($db);

    // Get POST data
    $student_id = $_POST['student_id'] ?? null;
    $pee = $_POST['pee'] ?? null;
    $term = $_POST['term'] ?? null;
    $memo = $_POST['memo'] ?? '';
    $answers = [];

    // Extract answers from POST data
    for ($i = 1; $i <= 25; $i++) {
        $answers["q$i"] = $_POST["q$i"] ?? null;
    }

    // Validate required fields
    if (!$student_id || !$pee || !$term) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Save SDQ self-assessment data
    $sdq->saveSDQpar($student_id, $answers, $memo, $pee, $term);

    // Respond with success
    echo json_encode(['success' => true, 'message' => 'SDQ Parent-assessment updated successfully.']);
} catch (Exception $e) {
    // Handle errors
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
