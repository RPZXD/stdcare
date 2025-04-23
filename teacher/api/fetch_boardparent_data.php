<?php
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $studentId = $_GET['id'];
    $pee = $_GET['pee'] ?? null; // Optional, but can be used for filtering

    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $BoardParent = new BoardParent($db);

    try {
        $query = "SELECT * FROM tb_parnet WHERE Stu_id = :id AND parn_pee = :pee";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $studentId, PDO::PARAM_INT);
        $stmt->bindParam(':pee', $pee, PDO::PARAM_STR); // Ensure pee is treated as a string
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
