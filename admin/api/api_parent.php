<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Parent.php");

// ตั้งค่า token ที่ต้องตรงกับฝั่ง client
define('API_TOKEN_KEY', 'YOUR_SECURE_TOKEN_HERE');

// ตรวจสอบ token
$token = $_GET['token'] ?? $_POST['token'] ?? '';
if ($token !== API_TOKEN_KEY) {
    echo json_encode(['success' => false, 'message' => 'Invalid token']);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$parent = new StudentParent($db);

switch ($action) {
    case 'list':
        $class = $_GET['class'] ?? '';
        $room = $_GET['room'] ?? '';
        $data = $parent->fetchFilteredParents($class, $room);
        echo json_encode($data);
        break;

    case 'get':
        $stu_id = $_GET['id'] ?? '';
        if (!$stu_id) {
            echo json_encode(['error' => true, 'message' => 'Missing id']);
            exit;
        }
        $data = $parent->getParentById($stu_id);
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => true, 'message' => 'Not found']);
        }
        break;

    case 'update':
        $parent->Stu_id = $_POST['editStu_id'] ?? '';
        $parent->Father_name = $_POST['editFather_name'] ?? '';
        $parent->Father_occu = $_POST['editFather_occu'] ?? '';
        $parent->Father_income = $_POST['editFather_income'] ?? '';
        $parent->Mother_name = $_POST['editMother_name'] ?? '';
        $parent->Mother_occu = $_POST['editMother_occu'] ?? '';
        $parent->Mother_income = $_POST['editMother_income'] ?? '';
        $parent->Par_name = $_POST['editPar_name'] ?? '';
        $parent->Par_relate = $_POST['editPar_relate'] ?? '';
        $parent->Par_occu = $_POST['editPar_occu'] ?? '';
        $parent->Par_income = $_POST['editPar_income'] ?? '';
        $parent->Par_addr = $_POST['editPar_addr'] ?? '';
        $parent->Par_phone = $_POST['editPar_phone'] ?? '';
        $result = $parent->updateParentInfo();
        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Update failed']);
        }
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>
