<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Behavior.php");
include_once("../../class/UserLogin.php");

define('API_TOKEN_KEY', 'YOUR_SECURE_TOKEN_HERE');

// ตรวจสอบ token
$token = $_REQUEST['token'] ?? '';
if ($token !== API_TOKEN_KEY) {
    echo json_encode(['success' => false, 'error' => 'Invalid token']);
    exit;
}

$action = $_GET['action'] ?? '';
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$behavior = new Behavior($db);
$user = new UserLogin($db);

switch ($action) {
    case 'list':
        // ดึงข้อมูลพฤติกรรมทั้งหมด (term/pee สามารถปรับให้รับจาก client ได้)
        $term = $user->getTerm() ?: ((date('n') >= 5 && date('n') <= 10) ? 1 : 2);
        $pee = $user->getPee() ?: (date('Y') + 543);
        $result = $behavior->getAllBehaviors($term, $pee);
        echo json_encode($result ?: []);
        break;

    case 'get':
        $id = $_GET['id'] ?? '';
        if (!$id) {
            echo json_encode(['error' => true, 'message' => 'Missing id']);
            exit;
        }
        $data = $behavior->getBehaviorById($id);
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => true, 'message' => 'Not found']);
        }
        break;

    case 'create':
        // รับข้อมูลจาก POST
        $behavior->stu_id = $_POST['addStu_id'] ?? '';
        // แปลงปี ค.ศ. เป็น พ.ศ.
        $behavior_date = $_POST['addBehavior_date'] ?? '';
        if ($behavior_date) {
            $parts = explode('-', $behavior_date);
            if (count($parts) === 3) {
                $parts[0] = strval(intval($parts[0]) + 543);
                $behavior->behavior_date = implode('-', $parts);
            } else {
                $behavior->behavior_date = $behavior_date;
            }
        } else {
            $behavior->behavior_date = '';
        }
        $behavior->behavior_type = $_POST['addBehavior_type'] ?? '';
        $behavior->behavior_name = $_POST['addBehavior_name'] ?? '';
        $behavior->behavior_score = $_POST['addBehavior_score'] ?? '';
        $behavior->teach_id = $_POST['addTeach_id'] ?? '';
        $behavior->term = $_POST['addBehavior_term'] ?? '';
        $behavior->pee = $_POST['addBehavior_pee'] ?? '';
        if ($behavior->create()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Create failed']);
        }
        break;

    case 'update':
        // รับข้อมูลจาก POST
        $id = $_POST['editBehavior_id'] ?? '';
        $stu_id = $_POST['editStu_id'] ?? '';
        // แปลงปี ค.ศ. เป็น พ.ศ.
        $behavior_date = $_POST['editBehavior_date'] ?? '';
        if ($behavior_date) {
            $parts = explode('-', $behavior_date);
            if (count($parts) === 3) {
                $parts[0] = strval(intval($parts[0]) + 543);
                $behavior_date = implode('-', $parts);
            }
        }
        $behavior_type = $_POST['editBehavior_type'] ?? '';
        $behavior_name = $_POST['editBehavior_name'] ?? '';
        $behavior_score = $_POST['editBehavior_score'] ?? '';
        $teach_id = $_POST['editTeach_id'] ?? '';
        $term = $_POST['editBehavior_term'] ?? '';
        $pee = $_POST['editBehavior_pee'] ?? '';
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing id']);
            exit;
        }
        // อัปเดตข้อมูล
        $query = "UPDATE behavior SET 
            stu_id = :stu_id,
            behavior_date = :behavior_date,
            behavior_type = :behavior_type,
            behavior_name = :behavior_name,
            behavior_score = :behavior_score,
            teach_id = :teach_id,
            behavior_term = :term,
            behavior_pee = :pee
            WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':stu_id', $stu_id);
        $stmt->bindParam(':behavior_date', $behavior_date);
        $stmt->bindParam(':behavior_type', $behavior_type);
        $stmt->bindParam(':behavior_name', $behavior_name);
        $stmt->bindParam(':behavior_score', $behavior_score);
        $stmt->bindParam(':teach_id', $teach_id);
        $stmt->bindParam(':term', $term);
        $stmt->bindParam(':pee', $pee);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        break;

    case 'delete':
        // รับ id จาก POST
        $id = $_POST['id'] ?? '';
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'Missing id']);
            exit;
        }
        if ($behavior->deleteBehavior($id)) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Delete failed']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
        break;
}
