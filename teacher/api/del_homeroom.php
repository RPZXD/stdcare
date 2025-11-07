<?php
require_once "../../config/Database.php";
require_once "../../controllers/HomeroomController.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing homeroom ID']);
        exit;
    }

    $id = $_POST['id'];

    $database = new Database("phichaia_student");
    $db = $database->getConnection();
    $homeroom = new HomeroomController($db);

    if ($homeroom->deleteHomeroom($id)) {
        echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลได้']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
