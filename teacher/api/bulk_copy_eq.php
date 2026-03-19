<?php
require_once "../../config/Database.php";
require_once "../../class/EQ.php";

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
    $eq = new EQ($db);

    $result = $eq->bulkCopyEQTerm1ToTerm2($class, $room, $pee);

    if (isset($result['message'])) {
        echo json_encode(['status' => 'warning', 'message' => $result['message']]);
    } else {
        echo json_encode([
            'status' => 'success',
            'success_count' => $result['success'],
            'skip_count' => $result['skip'],
            'fail_count' => $result['fail']
        ]);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
