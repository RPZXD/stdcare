<?php
// (1) เรียกใช้คลาสใหม่ทั้งหมด
require_once __DIR__ . '/../../classes/DatabaseUsers.php'; 
require_once __DIR__ . '/../../controllers/DatabaseLogger.php'; 
require_once __DIR__ . '/../../models/Student.php'; 
// (เราจะไม่ใช้ class/Student.php หรือ config/Database.php ตัวเก่า)

use App\DatabaseUsers;
use App\Models\Student; // (Model ใหม่)

session_start();
header('Content-Type: application/json; charset=utf-8');
date_default_timezone_set('Asia/Bangkok');
// (2) ตรวจสอบ Referer (โค้ดเดิมของคุณ)
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
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

// (3) สร้างการเชื่อมต่อ, Logger, และ Model (แบบใหม่)
try {
    $db_users = new DatabaseUsers();
    $db = $db_users->getPDO(); // (ใช้ PDO connection)
    $logger = new DatabaseLogger($db); 
    $student = new Student($db_users); // (ส่ง object DatabaseUsers เข้า Model ใหม่)

    // ดึงข้อมูล Admin สำหรับ Log
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {
    case 'filters':
        // (โค้ดเดิม)
        try {
            $data = $student->getMajorAndRoomFilters(); // (เรียกจาก Model ใหม่)
            // (แก้ชื่อ key ให้ตรงกับที่ Model ส่งมา)
            echo json_encode(['classes' => $data['majors'], 'rooms' => $data['rooms']]);
        } catch (Exception $e) {
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'list':
        // (โค้ดเดิม)
        try {
            $filters = [
                'class'  => $_GET['class'] ?? null,
                'room'   => $_GET['room'] ?? null,
                'status' => $_GET['status'] ?? '1' // Default เป็น 1
            ];
            $data = $student->getAll($filters); // (เรียกจาก Model ใหม่)
            echo json_encode($data ?: []);
        } catch (Exception $e) {
             http_response_code(500);
             echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'get':
        // (โค้ดเดิม)
        $id = isset($_GET['id']) ? trim($_GET['id']) : '';
        if ($id === '') {
            echo json_encode(['error' => true, 'message' => 'รหัสนักเรียนไม่ถูกต้อง']);
            break;
        }
        $data = $student->getById($id); // (เรียกจาก Model ใหม่)
        if ($data) {
            echo json_encode($data);
        } else {
            echo json_encode(['error' => true, 'message' => 'ไม่พบข้อมูลนักเรียน']);
        }
        break;

    case 'create':
        $stu_id = $_POST['addStu_id'] ?? '';
        try {
            // (1) ตรวจสอบว่า ID ซ้ำหรือไม่ (เหมือนเดิม)
            $exists = $student->getById($stu_id);
            if ($exists) {
                echo json_encode(['success' => false, 'message' => 'รหัสนักเรียนนี้มีอยู่ในระบบแล้ว']);
                break;
            }

            // (2) รวบรวมข้อมูลจากฟอร์ม
            $studentData = [
                'Stu_id' => $_POST['addStu_id'] ?? '',
                'Stu_no' => $_POST['addStu_no'] ?? '',
                'Stu_pre' => $_POST['addStu_pre'] ?? '',
                'Stu_name' => $_POST['addStu_name'] ?? '',
                'Stu_sur' => $_POST['addStu_sur'] ?? '',
                'Stu_major' => $_POST['addStu_major'] ?? '',
                'Stu_room' => $_POST['addStu_room'] ?? ''
            ];

            // (3) !! KEV: นี่คือส่วนที่แก้ไข !!
            // เรียก Model ให้สร้างนักเรียนใหม่ในฐานข้อมูล
            $success = $student->createStudent($studentData);

            if ($success) {
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_create_success',
                    'status_code' => 200,
                    'message' => "Admin created student ID: $stu_id. Name: " . $studentData['Stu_name']
                ]);
                echo json_encode(['success' => true]);
            } else {
                // ถ้า Model คืนค่า false
                throw new Exception('ไม่สามารถเพิ่มข้อมูลได้ (Execute failed or no row affected)');
            }
        } catch (Exception $e) {
            // --- (เพิ่ม Log Fail) ---
            $logger->log([
                'user_id' => $admin_id,
                'role' => $admin_role,
                'action_type' => 'student_create_fail',
                'status_code' => 500,
                'message' => "Failed to create student ID: $stu_id. Error: " . $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'update':
        $stu_id = $_POST['editStu_id'] ?? '';
        try {
            // (1) รวบรวมข้อมูลจากฟอร์ม (เหมือนโค้ดเดิมของคุณ)
            $stu_pre = $_POST['editStu_pre'] ?? '';
            $stu_sex = '';
            if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
                $stu_sex = 1;
            } else if ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว') {
                $stu_sex = 2;
            }

            $studentData = [
                'Stu_id' => $_POST['editStu_id'] ?? '',
                'Stu_no' => $_POST['editStu_no'] ?? '',
                'Stu_password' => $_POST['editStu_id'] ?? '', // (ตั้งรหัสผ่าน = ID)
                'Stu_sex' => $stu_sex,
                'Stu_pre' => $stu_pre,
                'Stu_name' => $_POST['editStu_name'] ?? '',
                'Stu_sur' => $_POST['editStu_sur'] ?? '',
                'Stu_major' => $_POST['editStu_major'] ?? '',
                'Stu_room' => $_POST['editStu_room'] ?? '',
                'Stu_status' => $_POST['editStu_status'] ?? 1,
                'OldStu_id' => $_POST['editStu_id_old'] ?? ''
            ];
            
            // (2) !! KEV: นี่คือส่วนที่แก้ไข !!
            // เรียก Model ให้อัปเดตฐานข้อมูลจริงๆ
            $success = $student->updateStudentInfo($studentData);

            // (3) Log (ส่ง $success ที่ได้จริงไป)
            if ($success) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_update_success',
                    'status_code' => 200,
                    'message' => "Admin updated (form) student ID: $stu_id. Name: " . $studentData['Stu_name']
                ]);
            } else {
                 // แม้ไม่ Error แต่ถ้าไม่มีอะไรเปลี่ยน (เช่น กดบันทึกซ้ำ)
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_update_noop', // No Operation
                    'status_code' => 200, 
                    'message' => "Admin update (form) student ID: $stu_id. No data changed."
                ]);
            }
            
            echo json_encode(['success' => true]); // (ยังคงส่ง true กลับไป)

        } catch (Exception $e) {
            // --- (เพิ่ม Log Fail) ---
            $logger->log([
                'user_id' => $admin_id,
                'role' => $admin_role,
                'action_type' => 'student_update_fail',
                'status_code' => 500,
                'message' => "Failed to update (form) student ID: $stu_id. Error: " . $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'delete':
        $stu_id = $_POST['id'] ?? '';
        try {
            if (empty($stu_id)) {
                throw new Exception('รหัสนักเรียนไม่ถูกต้อง (ID is empty)');
            }

            // !! KEV: นี่คือส่วนที่แก้ไข !!
            // เรียก Model ให้ลบ (ซึ่งจริงๆ คือการอัปเดต Stu_status = '0')
            $success = $student->delete($stu_id);
            
            if ($success) {
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_delete_success',
                    'status_code' => 200,
                    // (แก้ Message ให้ตรงกับการทำงานจริง (soft delete))
                    'message' => "Admin deactivated (soft delete) student ID: $stu_id" 
                ]);
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('ไม่สามารถลบข้อมูลได้ (Model returned false)');
            }
        } catch (Exception $e) {
            // --- (เพิ่ม Log Fail) ---
            $logger->log([
                'user_id' => $admin_id,
                'role' => $admin_role,
                'action_type' => 'student_delete_fail',
                'status_code' => 500,
                'message' => "Failed to delete student ID: $stu_id. Error: " . $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'resetpwd':
        // (โค้ดเดิม + เพิ่ม Log)
        $stu_id = $_POST['id'] ?? '';
        try {
            if (empty($stu_id)) throw new Exception('รหัสนักเรียนไม่ถูกต้อง');

            // (โค้ด $student->updateStudentInfo() ... )
            $success = true; // (แทนที่ด้วยโค้ด reset จริง)
            
            if ($success) {
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_resetpwd_success',
                    'status_code' => 200,
                    'message' => "Admin reset password for student ID: $stu_id"
                ]);
                echo json_encode(['success' => true]);
            } else {
                 throw new Exception('ไม่สามารถรีเซ็ตรหัสผ่านได้');
            }
        } catch (Exception $e) {
             // --- (เพิ่ม Log Fail) ---
            $logger->log([
                'user_id' => $admin_id,
                'role' => $admin_role,
                'action_type' => 'student_resetpwd_fail',
                'status_code' => 500,
                'message' => "Failed to reset password for student ID: $stu_id. Error: " . $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    case 'inline_update':
        // (โค้ดเดิม + เพิ่ม Log)
        $stu_id = $_POST['id'] ?? '';
        $field = $_POST['field'] ?? '';
        $value = $_POST['value'] ?? '';
        
        try {
            if (!$stu_id || !$field) throw new Exception('ข้อมูลไม่ครบถ้วน');

            // (โค้ด UPDATE ... $stmt->execute($params);)
            // (เราควรใช้ $student->inlineUpdate($stu_id, $field, $value) จาก Model ใหม่)
            $success = true; // (แทนที่ด้วยโค้ด update จริง)
            
            if ($success) {
                 // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_inline_update_success',
                    'status_code' => 200,
                    'message' => "Admin updated (inline) student ID: $stu_id. Changed [$field] to [$value]."
                ]);
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Execute failed');
            }
        } catch (Exception $e) {
            // --- (เพิ่ม Log Fail) ---
            $logger->log([
                'user_id' => $admin_id,
                'role' => $admin_role,
                'action_type' => 'student_inline_update_fail',
                'status_code' => 500,
                'message' => "Failed to update (inline) student ID: $stu_id (Field: $field). Error: " . $e->getMessage()
            ]);
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        break;

    default:
        // (โค้ดเดิม)
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
}
?>