<?php
require_once "../../config/Database.php";
require_once "../../class/SDQ.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$class = $_GET['class'] ?? null;
$room = $_GET['room'] ?? null;
$pee = $_GET['pee'] ?? null;

if (!$class || !$room || !$pee) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid class, room, or pee parameters']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $sdq = new SDQ($db);

    $data = $sdq->getSDQByClassAndRoom($class, $room, $pee);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
