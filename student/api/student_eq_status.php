<?php
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once('../../config/Database.php');
require_once('../../class/EQ.php');

$student_id = $_GET['stuId'] ?? null;
$pee = $_GET['pee'] ?? null;
$term = $_GET['term'] ?? null;

if (!$student_id || !$pee || !$term) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$eq = new EQ($conn);

$data = $eq->getEQData($student_id, $pee, $term);

$result = [
    'self' => [
        'status' => $data ? 'saved' : 'not_saved'
    ]
];

echo json_encode($result);
