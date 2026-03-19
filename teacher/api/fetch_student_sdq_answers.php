<?php
require_once "../../config/Database.php";
require_once "../../class/SDQ.php";

header('Content-Type: application/json');

$student_id = $_GET['student_id'] ?? '';
$type = $_GET['type'] ?? 'self';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (empty($student_id) || empty($pee) || empty($term)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameter']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $sdq = new SDQ($db);

    $method = $type === 'par' ? 'getSDQParData' : ($type === 'teach' ? 'getSDQTeachData' : 'getSDQSelfData');
    $data = $sdq->$method($student_id, $pee, $term);

    if ($data && !empty($data['answers'])) {
        echo json_encode(['status' => 'success', 'data' => $data]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No data found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
