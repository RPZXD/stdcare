<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $class = $_GET['class'] ?? null;
    $room = $_GET['room'] ?? null;

    if (!$class || !$room) {
        echo json_encode(['success' => false, 'message' => 'Missing class or room parameters']);
        exit;
    }

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $student = new Student($db);

    try {
        $data = $student->fetchFilteredStudents($class, $room);
        echo json_encode(['success' => true, 'data' => $data]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
