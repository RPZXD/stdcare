<?php
require_once "../../config/Database.php";
require_once "../../class/Poor.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $studentId = $_GET['id'];

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $poor = new Poor($db);

    try {
        $query = "SELECT * FROM tb_poor WHERE Stu_id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $studentId);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($data) {
            echo json_encode(['success' => true, 'data' => $data]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบข้อมูล']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถดึงข้อมูลได้: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'คำขอไม่ถูกต้อง']);
}
?>
