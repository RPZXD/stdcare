<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$class = $_GET['class'] ?? null;
$room = $_GET['room'] ?? null;
$pee = $_GET['pee'] ?? null; // Optional, but can be used for filtering

if (!$class || !$room) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid class or room parameters']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $BoardParent = new BoardParent($db);

    $data = $BoardParent->getBoardParentByClassAndRoom($class, $room, $pee);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
