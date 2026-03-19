<?php
require_once "../../config/Database.php";
require_once "../../class/SDQ.php";

header('Content-Type: application/json');

$class = $_GET['class'] ?? '';
$room = $_GET['room'] ?? '';
$pee = $_GET['pee'] ?? '';

if (empty($class) || empty($room) || empty($pee)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required parameters']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $sdq = new SDQ($db);

    $results = $sdq->bulkCopySDQTerm1ToTerm2($class, $room, $pee);

    echo json_encode([
        'status' => 'success',
        'details' => $results
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
