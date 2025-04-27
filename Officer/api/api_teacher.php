<?php
include_once("../../config/Database.php");
include_once("../../class/Teacher.php");

header('Content-Type: application/json; charset=utf-8');

// Define your API token key here (change to a secure value in production)
define('API_TOKEN_KEY', 'YOUR_SECURE_TOKEN_HERE');

// Function to check token from GET or POST
function check_api_token() {
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    if ($token !== API_TOKEN_KEY) {
        echo json_encode(['success' => false, 'message' => 'Invalid or missing API token']);
        exit;
    }
}
check_api_token();

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$teacher = new Teacher($db);

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'list':
        $data = $teacher->userTeacher();
        echo json_encode($data ?: []);
        break;
    case 'get':
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if ($id === '') {
            echo json_encode(['error' => true, 'message' => 'รหัสครูไม่ถูกต้อง']);
            break;
        }
        $data = $teacher->getTeacherById($id);
        if (is_array($data) && isset($data[0]) && !empty($data[0]['Teach_id'])) {
            echo json_encode($data[0]);
        } else {
            // สามารถ log error ได้ที่นี่ถ้าต้องการ
            echo json_encode(['error' => true, 'message' => 'ไม่พบข้อมูลครู หรือข้อมูลไม่สมบูรณ์']);
        }
        break;
    case 'create':
        // รับค่าจากฟอร์ม add
        $teacher->Teach_id = $_POST['addTeach_id'] ?? '';
        $teacher->Teach_password = $_POST['addTeach_id'] ?? '';
        $teacher->Teach_name = $_POST['addTeach_name'] ?? '';
        $teacher->Teach_major = $_POST['addTeach_major'] ?? '';
        $teacher->Teach_class = $_POST['addTeach_class'] ?? '';
        $teacher->Teach_room = $_POST['addTeach_room'] ?? '';
        $teacher->Teach_status = $_POST['addTeach_status'] ?? 1;
        $teacher->role_std = $_POST['addrole_std'] ?? '';
        $success = $teacher->create();
        echo json_encode(['success' => $success]);
        break;
    case 'update':
        // รับค่าจากฟอร์ม edit
        $teacher->Teach_id_old = $_POST['editTeach_id_old'] ?? '';
        $teacher->Teach_id = $_POST['editTeach_id'] ?? '';
        $teacher->Teach_name = $_POST['editTeach_name'] ?? '';
        $teacher->Teach_major = $_POST['editTeach_major'] ?? '';
        $teacher->Teach_class = $_POST['editTeach_class'] ?? '';
        $teacher->Teach_room = $_POST['editTeach_room'] ?? '';
        $teacher->Teach_status = $_POST['editTeach_status'] ?? 1;
        $teacher->role_std = $_POST['editrole_std'] ?? '';
        $success = $teacher->update();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
