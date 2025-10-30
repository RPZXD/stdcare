<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json');

// (1) เรียกใช้คลาสหลัก
require_once __DIR__ . '/../classes/DatabaseUsers.php';
//
// !! KEV: แก้ไขบรรทัดนี้ !!
// (Logger อยู่ในโฟลเดอร์เดียวกัน ไม่ต้องถอยหลัง ../)
//
require_once __DIR__ . '/DatabaseLogger.php'; 
//
//
require_once __DIR__ . '/../models/ParentModel.php';   // (Model ใหม่)

use App\DatabaseUsers;
use App\Models\ParentModel;

try {
    // (2) สร้างการเชื่อมต่อ, Logger, และ Model
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $logger = new DatabaseLogger($pdo);
    $parentModel = new ParentModel($db);

    // (3) ดึงข้อมูล Admin สำหรับ Log
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

    $action = $_GET['action'] ?? $_POST['action'] ?? 'list';

    switch ($action) {
        case 'list':
            $class = $_GET['class'] ?? '';
            $room = $_GET['room'] ?? '';
            $data = $parentModel->getParentDataFiltered($class, $room);
            echo json_encode($data);
            break;

        case 'get':
            $id = $_GET['id'] ?? '';
            $data = $parentModel->getParentById($id);
            echo json_encode($data);
            break;

        case 'update':
            $stu_id = $_POST['editStu_id'] ?? '';
            try {
                $parentModel->updateParentInfo($_POST); // ส่ง POST ทั้งหมดไป
                
                // --- (เพิ่ม Log Success) ---
                $logger->log([
                    'user_id' => $admin_id, 'role' => $admin_role,
                    'action_type' => 'parent_update_success', 'status_code' => 200,
                    'message' => "Admin updated parent info for Stu_id: $stu_id"
                ]);
                echo json_encode(['success' => true]);

            } catch (\Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $admin_id, 'role' => $admin_role,
                    'action_type' => 'parent_update_fail', 'status_code' => 500,
                    'message' => "Failed to update parent info for Stu_id: $stu_id. Error: " . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;
        
        case 'download_template':
            // --- (เพิ่ม) ดาวน์โหลดเทมเพลต (ที่ดึงข้อมูลจริง) ---
            
            // (1) อ่านค่าตัวกรองจาก URL
            $class = $_GET['class'] ?? '';
            $room = $_GET['room'] ?? '';

            // (2) ดึงข้อมูลจริงจาก Model (ใช้เมธอดเดียวกับที่ตารางใช้)
            $data = $parentModel->getParentDataFiltered($class, $room);

            $filename = "parent_data_c{$class}_r{$room}.csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            $output = fopen('php://output', 'w');
            
            // (BOM for UTF-8 Excel)
            fputs($output, "\xEF\xBB\xBF"); 
            
            // (3) สร้าง Header (ต้องตรงกับตอนอัปโหลด)
            $header = [
                'Stu_id', 'Father_name', 'Father_occu', 'Mother_name', 'Mother_occu', 
                'Par_name', 'Par_relate', 'Par_addr', 'Par_phone'
            ];
            fputcsv($output, $header);
            
            // (4) วนลูปข้อมูลจริงใส่ใน CSV
            // (หมายเหตุ: เราไม่เอา Father_income, Mother_income, Par_income มาในเทมเพลต)
            foreach ($data as $row) {
                 fputcsv($output, [
                    $row['Stu_id'] ?? '',
                    $row['Father_name'] ?? '',
                    $row['Father_occu'] ?? '',
                    $row['Mother_name'] ?? '',
                    $row['Mother_occu'] ?? '',
                    $row['Par_name'] ?? '',
                    $row['Par_relate'] ?? '',
                    $row['Par_addr'] ?? '',
                    $row['Par_phone'] ?? ''
                 ]);
            }
            
            fclose($output);
            exit;

        case 'upload_csv':
            // --- (เพิ่ม) อัปโหลด CSV ---
            try {
                if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('ไม่พบไฟล์ หรือ ไฟล์อัปโหลดมีปัญหา');
                }
                $fileTmpPath = $_FILES['csv_file']['tmp_name'];
                $csv_data = [];
                $header = null;
                
                if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                    if (($headerData = fgetcsv($handle)) !== FALSE) {
                        $headerData[0] = preg_replace('/^\x{FEFF}/u', '', $headerData[0]); // Remove BOM
                        $header = array_map('trim', $headerData);
                    } else {
                        throw new Exception('ไฟล์ CSV ว่างเปล่า');
                    }

                    while (($data = fgetcsv($handle)) !== FALSE) {
                        if (count($data) === count($header)) {
                            $csv_data[] = array_combine($header, $data);
                        }
                    }
                    fclose($handle);
                }

                if (empty($csv_data)) {
                     throw new Exception('ไม่พบข้อมูลในไฟล์ CSV');
                }

                // เรียก Model ให้อัปเดต
                $report = $parentModel->batchUpdateParentsCSV($csv_data);
                
                $logMessage = sprintf(
                    'Admin batch updated parents: Success=%d, Failed=%d',
                    $report['success'], $report['failed']
                );
                $logger->log([
                    'user_id' => $admin_id, 'role' => $admin_role,
                    'action_type' => 'parent_csv_upload_success', 'status_code' => 200,
                    'message' => $logMessage
                ]);
                echo json_encode(['status' => 'completed', 'report' => $report]);

            } catch (\Exception $e) {
                // --- (เพิ่ม Log Fail) ---
                $logger->log([
                    'user_id' => $admin_id, 'role' => $admin_role,
                    'action_type' => 'parent_csv_upload_fail', 'status_code' => 500,
                    'message' => 'Failed to upload CSV. Error: ' . $e->getMessage()
                ]);
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'General Controller error: ' . $e->getMessage()]);
}
?>