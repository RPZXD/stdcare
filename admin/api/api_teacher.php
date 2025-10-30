<?php
include_once("../../config/Database.php");
include_once("../../class/Teacher.php");
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json; charset=utf-8');
$allowed_referers = [
    'http://localhost/stdcare/admin/',
    'https://std.phichai.ac.th/admin/'
];
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$referer_ok = false;
foreach ($allowed_referers as $allowed) {
    if (strpos($referer, $allowed) === 0) {
        $referer_ok = true;
        break;
    }
}
if (!$referer_ok) {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'Forbidden'
    ]);
    exit;
}

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
        $teacher->Teach_password = $teacher->Teach_id; // เพิ่มบรรทัดนี้
        $success = $teacher->update();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $teacher->Teach_id = $_POST['id'] ?? '';
        $success = $teacher->delete();
        echo json_encode(['success' => $success]);
        break;
    // เพิ่ม action resetpwd
    case 'resetpwd':
        $teacher->Teach_id = $_POST['id'] ?? '';
        // รีเซ็ตรหัสผ่านเป็นรหัสครู
        $success = false;
        if ($teacher->Teach_id) {
            $success = $teacher->resetPasswordToId();
        }
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
