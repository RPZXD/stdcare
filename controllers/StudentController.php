<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../models/Student.php';

use App\Models\Student;

$studentModel = new Student();

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

try {
switch ($action) {
        case 'list':
            echo json_encode($studentModel->getAll());
            break;

        // --- ADDED: Action ใหม่สำหรับ Server-Side Processing ---
        case 'list_ssp':
            $params = $_POST;
            $result = $studentModel->getStudentsForDatatable($params);
            echo json_encode($result);
            break;
            
        // --- ADDED: Action ใหม่สำหรับดึงข้อมูล Dropdown ---
        case 'get_filters':
            $filters = $studentModel->getMajorAndRoomFilters();
            echo json_encode($filters);
            break;

        case 'get':
            $id = $_GET['id'] ?? $_POST['id'] ?? '';
            echo json_encode($studentModel->getById($id));
            break;
            
        case 'create':
            $data = [
                'Stu_id' => $_POST['Stu_id'],
                'Stu_no' => $_POST['Stu_no'] ?? null,
                'Stu_password' => $_POST['Stu_password'] ?? $_POST['Stu_id'],
                'Stu_sex' => $_POST['Stu_sex'] ?? null,
                'Stu_pre' => $_POST['Stu_pre'] ?? null,
                'Stu_name' => $_POST['Stu_name'],
                'Stu_sur' => $_POST['Stu_sur'] ?? '',
                'Stu_major' => $_POST['Stu_major'] ?? null,
                'Stu_room' => $_POST['Stu_room'] ?? null,
                'Stu_nick' => $_POST['Stu_nick'] ?? null,
                'Stu_birth' => $_POST['Stu_birth'] ?? null,
                'Stu_religion' => $_POST['Stu_religion'] ?? null,
                'Stu_blood' => $_POST['Stu_blood'] ?? null,
                'Stu_addr' => $_POST['Stu_addr'] ?? null,
                'Stu_phone' => $_POST['Stu_phone'] ?? null,
                'Father_name' => $_POST['Father_name'] ?? null,
                'Father_phone' => $_POST['Father_phone'] ?? null,
                'Mother_name' => $_POST['Mother_name'] ?? null,
                'Mother_phone' => $_POST['Mother_phone'] ?? null,
                'Par_name' => $_POST['Par_name'] ?? null,
                'Par_phone' => $_POST['Par_phone'] ?? null,
                'Risk_group' => $_POST['Risk_group'] ?? null,
                'Stu_picture' => $_POST['Stu_picture'] ?? null,
                'Stu_status' => $_POST['Stu_status'] ?? 1,
                'vehicle' => $_POST['vehicle'] ?? null,
                'Stu_citizenid' => $_POST['Stu_citizenid'] ?? null,
            ];
            $studentModel->create($data);
            echo json_encode(['success' => true]);
            break;
        case 'update':
            $id = $_POST['Stu_id'];
            $data = [
                'Stu_no' => $_POST['Stu_no'] ?? null,
                'Stu_sex' => $_POST['Stu_sex'] ?? null,
                'Stu_pre' => $_POST['Stu_pre'] ?? null,
                'Stu_name' => $_POST['Stu_name'],
                'Stu_sur' => $_POST['Stu_sur'] ?? '',
                'Stu_major' => $_POST['Stu_major'] ?? null,
                'Stu_room' => $_POST['Stu_room'] ?? null,
                'Stu_nick' => $_POST['Stu_nick'] ?? null,
                'Stu_birth' => $_POST['Stu_birth'] ?? null,
                'Stu_religion' => $_POST['Stu_religion'] ?? null,
                'Stu_blood' => $_POST['Stu_blood'] ?? null,
                'Stu_addr' => $_POST['Stu_addr'] ?? null,
                'Stu_phone' => $_POST['Stu_phone'] ?? null,
                'Father_name' => $_POST['Father_name'] ?? null,
                'Father_phone' => $_POST['Father_phone'] ?? null,
                'Mother_name' => $_POST['Mother_name'] ?? null,
                'Mother_phone' => $_POST['Mother_phone'] ?? null,
                'Par_name' => $_POST['Par_name'] ?? null,
                'Par_phone' => $_POST['Par_phone'] ?? null,
                'Risk_group' => $_POST['Risk_group'] ?? null,
                'Stu_picture' => $_POST['Stu_picture'] ?? null,
                'Stu_status' => $_POST['Stu_status'] ?? 1,
                'vehicle' => $_POST['vehicle'] ?? null,
                'Stu_citizenid' => $_POST['Stu_citizenid'] ?? null,
            ];
            $studentModel->update($id, $data);
            echo json_encode(['success' => true]);
            break;
        case 'delete':
            $id = $_POST['Stu_id'];
            $result = $studentModel->delete($id);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Not found or already deleted']);
            }
            break;
        case 'resetpwd':
            $id = $_POST['Stu_id'];
            $result = $studentModel->resetPassword($id);
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Reset password failed']);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    // --- CHANGED: แสดง error message จริงๆ เพื่อ debug ---
    echo json_encode(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}