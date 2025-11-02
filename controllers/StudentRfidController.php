<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok'); // (เพิ่ม)

// (1) !! KEV: เรียกใช้คลาสที่จำเป็นทั้งหมด !!
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/DatabaseLogger.php'; // (เพิ่ม)
require_once __DIR__ . '/../models/StudentRfid.php';
require_once __DIR__ . '/../models/Student.php';

use App\DatabaseUsers;
use App\Models\StudentRfid;
use App\Models\Student;

// (2) !! KEV: สร้างการเชื่อมต่อ, Logger, และ Models !!
try {
    $db = new DatabaseUsers();
    $pdo = $db->getPDO(); // (เพิ่ม)
    $logger = new DatabaseLogger($pdo); // (เพิ่ม)
    
    $rfidModel = new StudentRfid($db); // (แก้ไข: ส่ง $db)
    $studentModel = new Student($db); // (แก้ไข: ส่ง $db)

    // (3) !! KEV: ดึงข้อมูล User สำหรับ Log !!
    // (อนุญาตทั้ง Officer และ Admin)
    $user_id = $_SESSION['Officer_login'] ?? $_SESSION['Admin_login'] ?? 'system';
    $user_role = $_SESSION['role'] ?? 'Officer'; 

} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database connection error: ' . $e->getMessage()]);
    exit;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

function requireOfficer() {
    // (อนุญาตทั้ง Officer และ Admin)
    if (!isset($_SESSION['Officer_login']) && !isset($_SESSION['Admin_login'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

try {
    switch ($action) {
        case 'list':
            // (โค้ด case 'list' เดิมของคุณ)
            // (หน้านี้ดูเหมือนจะใช้ list_ssp และ search_student แทน)
            requireOfficer();
            echo json_encode($rfidModel->getAll());
            break;

        case 'list_ssp':
            requireOfficer();
            $params = $_POST;
            $result = $rfidModel->getRfidsForDatatable($params);
            echo json_encode($result);
            break;
            
        case 'search_student':
            requireOfficer();
            $major = $_POST['major'] ?? '';
            $room = $_POST['room'] ?? '';
            $result = $studentModel->getStudentsWithoutRfid($major, $room); // (ต้องมีเมธอดนี้ใน Student.php)
            echo json_encode($result);
            break;
            
        case 'register':
            requireOfficer();
            $stu_id = $_POST['stu_id'] ?? '';
            $rfid_code = $_POST['rfid_code'] ?? '';
            try {
                if (!$stu_id || !$rfid_code) throw new \Exception('ข้อมูลไม่ครบถ้วน');
                
                $result = $rfidModel->register($stu_id, $rfid_code);
                if (!$result['success']) throw new \Exception($result['error']);

                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_register_success', 'status_code' => 200,
                    'message' => "User registered RFID: $rfid_code for Stu_id: $stu_id"
                ]);
                echo json_encode($result);
            
            } catch (\Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_register_fail', 'status_code' => 500,
                    'message' => "Failed to register RFID for Stu_id: $stu_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;
            
        case 'update':
            requireOfficer();
            $id = $_POST['id'] ?? '';
            $rfid_code = $_POST['rfid_code'] ?? '';
            try {
                if (!$id || !$rfid_code) throw new \Exception('ข้อมูลไม่ครบถ้วน');
                
                $result = $rfidModel->update($id, $rfid_code);
                if (!$result['success']) throw new \Exception($result['error']);
                
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_update_success', 'status_code' => 200,
                    'message' => "User updated RFID ID: $id to: $rfid_code"
                ]);
                echo json_encode($result);

            } catch (\Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_update_fail', 'status_code' => 500,
                    'message' => "Failed to update RFID ID: $id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            break;

        case 'download_unregistered_csv':
            requireOfficer(); 
            $major = $_GET['major'] ?? '';
            $room = $_GET['room'] ?? '';
            
            try {
                if (empty($major) || empty($room)) {
                     echo "<script>alert('กรุณาเลือกชั้นและห้องก่อนดาวน์โหลด'); window.close();</script>";
                     exit;
                }
                
                // (1. เรียกเมธอดใหม่จาก StudentModel)
                $data = $studentModel->getStudentsWithRfidForCsv($major, $room);

                $filename = "rfid_template_M{$major}_{$room}.csv";
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                $output = fopen('php://output', 'w');
                fputs($output, "\xEF\xBB\xBF"); // BOM

                $header = ['stu_id', 'stu_name', 'stu_sur', 'rfid_code'];
                fputcsv($output, $header);
                
                // (2. วนลูปข้อมูล (ถ้ามี) และใส่ rfid_code ที่มีอยู่เดิม)
                foreach ($data as $row) {
                    fputcsv($output, [
                        $row['Stu_id'],
                        $row['Stu_name'],
                        $row['Stu_sur'],
                        $row['rfid_code'] ?? '' // (ใส่รหัสเดิมที่มี)
                    ]);
                }
                fclose($output);

                $logMessage = "User downloaded RFID template (full room) for M.{$major}/{$room}. Found: " . count($data) . " students.";
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_download_template', 'status_code' => 200,
                    'message' => $logMessage
                ]);
                exit;

            } catch (\Exception $e) {
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_download_fail', 'status_code' => 500,
                    'message' => "Failed to download RFID template. Error: " . $e->getMessage()
                ]);
                echo "Error: " . $e->getMessage();
                exit;
            }
            break;
            
        case 'delete':
            requireOfficer();
            $id = $_POST['id'] ?? '';
            try {
                if (!$id) throw new \Exception('ข้อมูลไม่ครบถ้วน');
                
                $result = $rfidModel->delete($id);
                
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_delete_success', 'status_code' => 200,
                    'message' => "User deleted RFID ID: $id"
                ]);
                echo json_encode(['success' => $result]);

            } catch (\Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $user_id, 'role' => $user_role,
                    'action_type' => 'rfid_delete_fail', 'status_code' => 500,
                    'message' => "Failed to delete RFID ID: $id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
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