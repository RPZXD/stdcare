<?php
require_once "../../config/Database.php";
require_once "../../class/Poor.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $teacherId = $_POST['teacherid'] ?? null;
    $number = $_POST['number'] ?? null;
    $studentId = $_POST['student'] ?? null;
    $reason = $_POST['reason'] ?? null;
    $received = $_POST['received'] ?? null;
    $detail = $_POST['detail'] ?? null;

    if (!$teacherId || !$number || !$studentId || !$reason || !$received) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $poor = new Poor($db);

    try {
        $poor->insertPoorStudent($teacherId, $number, $studentId, $reason, $received, $detail);
        echo json_encode(['success' => true, 'message' => 'Data inserted successfully']);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Failed to insert data: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
