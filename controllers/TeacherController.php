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

                // Prepare data including class/room and optional photo
                // Basic validation + normalization
                $teach_name = trim($_POST['addTeach_name'] ?? '');
                $teach_major = trim($_POST['addTeach_major'] ?? '');
                $teach_class = $_POST['addTeach_class'] ?? '';
                $teach_room = $_POST['addTeach_room'] ?? '';
                $teach_status = $_POST['addTeach_status'] ?? '1';
                $role_std = $_POST['addrole_std'] ?? '';

                if ($teach_name === '') throw new Exception('ชื่อ-สกุลห้ามว่าง');

                // Validate class/room: allow empty or integer
                if ($teach_class !== '' && !ctype_digit(strval($teach_class))) throw new Exception('ค่าชั้นต้องเป็นตัวเลขหรือเว้นว่าง');
                if ($teach_room !== '' && !ctype_digit(strval($teach_room))) throw new Exception('ค่าห้องต้องเป็นตัวเลขหรือเว้นว่าง');

                $data = [
                    'Teach_id' => $teach_id,
                    'Teach_name' => $teach_name,
                    'Teach_major' => $teach_major,
                    'Teach_class' => $teach_class !== '' ? (int)$teach_class : null,
                    'Teach_room' => $teach_room !== '' ? (int)$teach_room : null,
                    'Teach_status' => $teach_status,
                    'role_std' => $role_std
                ];

                // Do NOT update/upload Teach_photo here — DB stores filename only and external base URL is used.
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
                // ดึงข้อมูลเดิมเพื่อใช้ตรวจสอบรูป (ถ้ามี)
                $old = $teacherModel->getById($teach_id_old);

                // Basic validation + normalization
                $teach_name = trim($_POST['editTeach_name'] ?? '');
                $teach_major = trim($_POST['editTeach_major'] ?? '');
                $teach_class = $_POST['editTeach_class'] ?? '';
                $teach_room = $_POST['editTeach_room'] ?? '';
                $teach_status = $_POST['editTeach_status'] ?? '1';
                $role_std = $_POST['editrole_std'] ?? '';

                if ($teach_name === '') throw new Exception('ชื่อ-สกุลห้ามว่าง');
                if ($teach_class !== '' && !ctype_digit(strval($teach_class))) throw new Exception('ค่าชั้นต้องเป็นตัวเลขหรือเว้นว่าง');
                if ($teach_room !== '' && !ctype_digit(strval($teach_room))) throw new Exception('ค่าห้องต้องเป็นตัวเลขหรือเว้นว่าง');

                $data = [
                    'Teach_name' => $teach_name,
                    'Teach_major' => $teach_major,
                    'Teach_class' => $teach_class !== '' ? (int)$teach_class : null,
                    'Teach_room' => $teach_room !== '' ? (int)$teach_room : null,
                    'Teach_status' => $teach_status,
                    'role_std' => $role_std,
                    // (เพิ่ม 2 field นี้สำหรับ Model)
                    'Teach_id_new' => $teach_id,
                    'Teach_id_old' => $teach_id_old
                ];

                // Do NOT update Teach_photo on edit — keep existing filename from DB
                // (model.update will not change Teach_photo)

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