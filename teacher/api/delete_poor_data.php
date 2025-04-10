<?php
require_once "../../config/Database.php";
require_once "../../class/Poor.php";

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['id'])) {
        echo json_encode(['success' => false, 'message' => 'Missing Poor ID']);
        exit;
    }

    $id = $_POST['id'];

    $database = new Database("phichaia_student");
    $db = $database->getConnection();
    $Poor = new Poor($db);

    try {
        if ($Poor->deletePoorById($id)) {
            echo json_encode(['success' => true, 'message' => 'ลบข้อมูลสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูลที่ต้องการลบ']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>
