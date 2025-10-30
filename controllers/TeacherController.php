<?php
session_start(); // (ต้องมี session)
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
// (1) เรียกใช้คลาสหลักทั้งหมด
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/../controllers/DatabaseLogger.php'; // (Logger)
require_once __DIR__ . '/../models/Teacher.php';

use App\DatabaseUsers;
use App\Models\Teacher;

try {
    // (2) สร้างการเชื่อมต่อ, Logger, และ Model
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $logger = new DatabaseLogger($pdo);
    $teacherModel = new Teacher($db); // (ส่ง $db object เข้าไป)

    // (3) ดึงข้อมูล Admin สำหรับ Log
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

    switch ($action) {
        case 'list':
            echo json_encode($teacherModel->getAll());
            break;

        case 'get':
            $id = $_GET['id'] ?? $_POST['id'] ?? '';
            echo json_encode($teacherModel->getById($id));
            break;

        //
        // !! KEV: แก้ไขจุดนี้ (create) !!
        //
        case 'create':
            // (อ่านจาก 'add...' ตามชื่อในฟอร์ม HTML)
            $teach_id = $_POST['addTeach_id'] ?? ''; 
            try {
                if (empty($teach_id)) {
                    throw new Exception('รหัสครู (Teach_id) ห้ามว่าง');
                }
                
                // (ตรวจสอบ ID ซ้ำก่อน)
                if ($teacherModel->getById($teach_id)) {
                     throw new Exception("รหัสครู $teach_id นี้มีอยู่ในระบบแล้ว");
                }

                $data = [
                    'Teach_id' => $teach_id,
                    'Teach_name' => $_POST['addTeach_name'],
                    'Teach_major' => $_POST['addTeach_major'],
                    'Teach_status' => $_POST['addTeach_status'],
                    'role_std' => $_POST['addrole_std']
                ];
                $teacherModel->create($data);
                
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_create_success',
                    'status_code' => 200,
                    'message' => "Admin created teacher ID: $teach_id. Name: " . $data['Teach_name']
                ]);
                echo json_encode(['success' => true]);

            } catch (Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_create_fail',
                    'status_code' => 500,
                    'message' => "Failed to create teacher ID: $teach_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        //
        // !! KEV: แก้ไขจุดนี้ (update) !!
        //
        case 'update':
            // (อ่านจาก 'edit...' ตามชื่อในฟอร์ม HTML)
            $teach_id = $_POST['editTeach_id'] ?? '';
            $teach_id_old = $_POST['editTeach_id_old'] ?? '';
            try {
                 if (empty($teach_id) || empty($teach_id_old)) {
                    throw new Exception('รหัสครู (Teach_id) ห้ามว่าง');
                 }

                $data = [
                    'Teach_name' => $_POST['editTeach_name'],
                    'Teach_major' => $_POST['editTeach_major'],
                    'Teach_status' => $_POST['editTeach_status'],
                    'role_std' => $_POST['editrole_std'],
                    // (เพิ่ม 2 field นี้สำหรับ Model)
                    'Teach_id_new' => $teach_id,
                    'Teach_id_old' => $teach_id_old
                ];
                
                // (เราจะส่ง ID เดิมไปให้ Model)
                $teacherModel->update($teach_id_old, $data);
                
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_update_success',
                    'status_code' => 200,
                    'message' => "Admin updated teacher ID: $teach_id. Name: " . $data['Teach_name']
                ]);
                echo json_encode(['success' => true]);

            } catch (Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_update_fail',
                    'status_code' => 500,
                    'message' => "Failed to update teacher ID: $teach_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'delete':
            $teach_id = $_POST['id'] ?? ''; // (JavaScript ส่ง 'id')
            try {
                if (empty($teach_id)) throw new Exception('Teacher ID is empty');
                
                $result = $teacherModel->delete($teach_id); // (Soft delete)
                
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_delete_success',
                    'status_code' => 200,
                    'message' => "Admin deactivated (soft delete) teacher ID: $teach_id"
                ]);
                echo json_encode(['success' => $result]);
                
            } catch (Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_delete_fail',
                    'status_code' => 500,
                    'message' => "Failed to delete teacher ID: $teach_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        case 'resetpwd':
            $teach_id = $_POST['id'] ?? ''; // (JavaScript ส่ง 'id')
             try {
                if (empty($teach_id)) throw new Exception('Teacher ID is empty');
                
                $result = $teacherModel->resetPassword($teach_id);
                
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_resetpwd_success',
                    'status_code' => 200,
                    'message' => "Admin reset password for teacher ID: $teach_id"
                ]);
                echo json_encode(['success' => $result]);

            } catch (Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'teacher_resetpwd_fail',
                    'status_code' => 500,
                    'message' => "Failed to reset password for teacher ID: $teach_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    // (Catch หลัก)
    http_response_code(500);
    echo json_encode(['error' => 'General Controller error: ' . $e->getMessage()]);
}
?>