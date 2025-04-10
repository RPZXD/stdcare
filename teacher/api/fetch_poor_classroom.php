<?php
require_once "../../config/Database.php";
require_once "../../class/Poor.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$class = $_GET['class'] ?? null;
$room = $_GET['room'] ?? null;

if (!$class || !$room) {
    echo json_encode(['success' => false, 'message' => 'Missing or invalid class or room parameters']);
    exit;
}

try {
    $db = (new Database("phichaia_student"))->getConnection();
    $poor = new Poor($db);

    $data = $poor->getPoorByClassAndRoom($class, $room);
    echo json_encode(['success' => true, 'data' => $data]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
