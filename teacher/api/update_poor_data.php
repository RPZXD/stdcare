<?php
require_once "../../config/Database.php";
require_once "../../class/Poor.php";

header('Content-Type: application/json'); // Ensure JSON response header

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $poor = new Poor($db);

    try {
        $poor->updatePoorStudent(
            $_POST['student'], 
            $_POST['number'], 
            $_POST['reason'], 
            $_POST['received'], 
            $_POST['detail']
        );
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
