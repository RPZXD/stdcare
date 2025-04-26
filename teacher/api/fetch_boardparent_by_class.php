<?php
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/BoardParent.php";

if (!isset($_GET['class']) || !isset($_GET['pee'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit;
}

$class = $_GET['class'];
$pee = $_GET['pee'];

try {
    $connectDB = new Database("phichaia_student");
    $db = $connectDB->getConnection();
    $boardParent = new BoardParent($db);

    // ดึงข้อมูลทุกห้องในระดับชั้นนั้นๆ
    $stmt = $db->prepare("SELECT * FROM tb_parnet WHERE parn_lev = :class AND parn_pee = :pee AND parn_pos = 1 ORDER BY parn_room ASC, parn_pos ASC");
    $stmt->bindParam(':class', $class, PDO::PARAM_STR);
    $stmt->bindParam(':pee', $pee, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
