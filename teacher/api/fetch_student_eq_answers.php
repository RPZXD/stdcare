<?php
require_once "../../config/Database.php";
require_once "../../class/EQ.php";

header('Content-Type: application/json');

$student_id = $_GET['student_id'] ?? '';
$pee = $_GET['pee'] ?? '';
$term = $_GET['term'] ?? '';

if (empty($student_id) || empty($pee) || empty($term)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing parameter']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $eq = new EQ($db);
    $data = $eq->getEQData($student_id, $pee, $term);

    if ($data) {
        // Map keys to q1, q2, ...
        $mapped = [];
        foreach ($data as $key => $value) {
            $mapped[strtolower(str_replace('EQ', 'q', $key))] = $value;
        }
        echo json_encode(['status' => 'success', 'data' => $mapped]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No data found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
