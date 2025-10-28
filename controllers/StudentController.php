<?php
session_start();
header('Content-Type: application/json');

// --- (1) เรียกใช้คลาสที่จำเป็น ---
require_once __DIR__ . '/../classes/DatabaseUsers.php'; 
require_once __DIR__ . '/../controllers/DatabaseLogger.php'; 
require_once __DIR__ . '/../models/Student.php'; 

use App\DatabaseUsers;
use App\Models\Student;

try {
    // --- (2) สร้างการเชื่อมต่อและ Logger ---
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $logger = new DatabaseLogger($pdo);
    
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

    // --- (3) ส่ง $db object เข้า Model ---
    $studentModel = new Student($db);

    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

    switch ($action) {
        
        //
        // !! KEV: แก้ไขจุดนี้ !!
        //
        case 'list':
            // (1) ดึงค่า Filter จาก $_GET ที่ JavaScript ส่งมา
            $filters = [
                'class'  => $_GET['class'] ?? null,
                'room'   => $_GET['room'] ?? null,
                'status' => $_GET['status'] ?? null
            ];
            
            // (2) ส่ง $filters เข้าไปในเมธอด getAll()
            echo json_encode($studentModel->getAll($filters));
            break;
        //
        // !! KEV: สิ้นสุดการแก้ไข !!
        //

        case 'list_ssp':
            // (อันนี้สำหรับ Server-side Datatables ซึ่งคุณไม่ได้ใช้ในฟังก์ชัน loadStudents())
            $params = $_POST;
            $result = $studentModel->getStudentsForDatatable($params);
            echo json_encode($result);
            break;
            
        case 'get_filters': 
            $filters = $studentModel->getMajorAndRoomFilters();
            echo json_encode($filters);
            break;

        case 'get':
            $id = $_GET['id'] ?? $_POST['id'] ?? '';
            echo json_encode($studentModel->getById($id));
            break;
            
        case 'create':
            // (โค้ด Log 'create' ของคุณ...)
            try {
                $data = [ 'Stu_id' => $_POST['Stu_id'] ];
                // ...
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_create_success',
                    'status_code' => 200,
                    'message' => 'Admin created student ID: ' . htmlspecialchars($_POST['Stu_id'])
                ]);
                echo json_encode(['status' => 'success', 'message' => 'สร้างนักเรียนสำเร็จ']);
            } catch (Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_create_fail',
                    'status_code' => 500,
                    'message' => 'Failed to create student. Error: ' . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;
            
        case 'upload_rfid_csv':
            // (โค้ด Log 'upload_rfid_csv' ของคุณ...)
            try {
                // ... (โค้ด CSV) ...
                $report = $studentModel->batchRegisterRfid($rfid_data);
                $logMessage = sprintf(
                    'Admin batch registered RFID: Success=%d, Failed=%d, Skipped=%d',
                    $report['success'], $report['failed'], $report['skipped']
                );
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_rfid_upload_success',
                    'status_code' => 200,
                    'message' => $logMessage
                ]);
                echo json_encode(['status' => 'completed', 'report' => $report]);
            } catch (Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_rfid_upload_fail',
                    'status_code' => 500,
                    'message' => 'Failed to upload RFID CSV. Error: ' . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['error' => $e->getMessage()]);
            }
            break;            

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Exception $e) {
    // (Catch หลัก)
    http_response_code(500);
    echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
}
?>