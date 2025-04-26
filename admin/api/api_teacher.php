<?php
include_once("../../config/Database.php");
include_once("../../class/Teacher.php");

header('Content-Type: application/json; charset=utf-8');

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
        $id = $_GET['id'] ?? '';
        $data = $teacher->getTeacherById($id);
        echo json_encode($data ? $data[0] : []);
        break;
    case 'create':
        $teacher->Teach_id = $_POST['Teach_id'] ?? '';
        $teacher->Teach_name = $_POST['Teach_name'] ?? '';
        $teacher->Teach_password = $_POST['Teach_password'] ?? '';
        $teacher->Teach_major = $_POST['Teach_major'] ?? '';
        $teacher->Teach_class = $_POST['Teach_class'] ?? '';
        $teacher->Teach_room = $_POST['Teach_room'] ?? '';
        $teacher->Teach_status = $_POST['Teach_status'] ?? 1;
        $teacher->role_std = $_POST['role_std'] ?? '';
        $success = $teacher->create();
        echo json_encode(['success' => $success]);
        break;
    case 'update':
        $teacher->Teach_id_old = $_POST['Teach_id_old'] ?? '';
        $teacher->Teach_id = $_POST['Teach_id'] ?? '';
        $teacher->Teach_name = $_POST['Teach_name'] ?? '';
        $teacher->Teach_password = $_POST['Teach_password'] ?? '';
        $teacher->Teach_major = $_POST['Teach_major'] ?? '';
        $teacher->Teach_class = $_POST['Teach_class'] ?? '';
        $teacher->Teach_room = $_POST['Teach_room'] ?? '';
        $teacher->Teach_status = $_POST['Teach_status'] ?? 1;
        $teacher->role_std = $_POST['role_std'] ?? '';
        $success = $teacher->update();
        echo json_encode(['success' => $success]);
        break;
    case 'delete':
        $teacher->Teach_id = $_POST['id'] ?? '';
        $success = $teacher->delete();
        echo json_encode(['success' => $success]);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
