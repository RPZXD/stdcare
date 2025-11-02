<?php
session_start();
date_default_timezone_set('Asia/Bangkok');
header('Content-Type: application/json');

// (1) เรียกใช้คลาสหลัก
require_once __DIR__ . '/../classes/DatabaseUsers.php';
require_once __DIR__ . '/DatabaseLogger.php'; 
require_once __DIR__ . '/../models/SettingModel.php'; 

use App\DatabaseUsers;
use App\Models\SettingModel;

// (ฟังก์ชันอ่าน CSV)
function parseCsv($filePath) {
    $csv_data = [];
    $header = null;
    if (($handle = fopen($filePath, 'r')) === FALSE) {
        throw new Exception('ไม่สามารถเปิดไฟล์ CSV ได้');
    }
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
    return ['header' => $header, 'data' => $csv_data];
}

// (ฟังก์ชันเขียน CSV)
function outputCsv($filename, $header, $data) {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputs($output, "\xEF\xBB\xBF"); // BOM for UTF-8 Excel
    fputcsv($output, $header);
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
}

try {
    // (2) สร้างการเชื่อมต่อ, Logger, และ Model
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();
    $logger = new DatabaseLogger($pdo);
    $model = new SettingModel($db);

    // (3) ดึงข้อมูล Admin สำหรับ Log
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

    if (!isset($_SESSION['Admin_login'])) {
         throw new Exception('ไม่ได้รับอนุญาต', 403);
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        
        // (แทนที่ update_pee_term.php)
        case 'update_term':
            $year = $_POST['academic_year'] ?? '';
            $term = $_POST['term'] ?? '';
            $model->updateTermPee($year, $term);
            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'settings_update_term', 'status_code' => 200, 'message' => "Admin updated term to: $year / $term"]);
            echo json_encode(['success' => true, 'message' => 'บันทึกปีการศึกษา/เทอม สำเร็จ']);
            break;

        // (แทนที่ promote_students.php)
        case 'promote_students':
            $result = $model->promoteStudents();
            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'settings_promote_students', 'status_code' => $result['success'] ? 200 : 500, 'message' => $result['message']]);
            echo json_encode($result);
            break;

        // (แทนที่ student_sample.php)
        case 'download_new_student_template':
            $header = ['Stu_id', 'Stu_no', 'Stu_pre', 'Stu_name', 'Stu_sur', 'Stu_major', 'Stu_room'];
            $data = [['12345', '1', 'เด็กชาย', 'สมชาย', 'ใจดี', '1', '1']];
            outputCsv('new_student_template.csv', $header, $data);
            exit;

        // (แทนที่ update_number_sample.php)
        case 'download_number_template':
            $db_data = $model->getStudentsForNumberUpdate();
            $header = ['Stu_id', 'Stu_major', 'Stu_room', 'Stu_no_new', 'Stu_no_old', 'fullname'];
            $csv_data = [];
            foreach ($db_data as $row) {
                $csv_data[] = [
                    $row['Stu_id'], $row['Stu_major'], $row['Stu_room'], 
                    '', // Stu_no_new (ให้กรอก)
                    $row['Stu_no'], // Stu_no_old
                    $row['fullname']
                ];
            }
            outputCsv('update_number_template.csv', $header, $csv_data);
            exit;
            
        // (แทนที่ update_datastudent_sample_dynamic.php)
        case 'download_full_data_template':
            $class = $_GET['pe'] ?? '';
            $room = $_GET['room'] ?? '';
            $db_data = $model->getStudentsForFullUpdate($class, $room);
            
            if (empty($db_data)) {
                echo "<script>alert('ไม่พบข้อมูลนักเรียนตามที่กรอง'); window.close();</script>";
                exit;
            }
            
            $header = array_keys($db_data[0]); // (ดึง Header ทั้งหมดจากฐานข้อมูล)
            outputCsv("student_data_c{$class}_r{$room}.csv", $header, $db_data);
            exit;

        // (API ใหม่ สำหรับฟอร์ม "อัปเดตเลขที่")
        case 'upload_number_data':
            if (!isset($_FILES['number_csv']) || $_FILES['number_csv']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('กรุณาเลือกไฟล์ CSV สำหรับอัปเดตเลขที่');
            }
            $csv = parseCsv($_FILES['number_csv']['tmp_name']);
            $report = $model->batchUpdateStudentNumbers($csv['data']);
            
            $logMessage = sprintf('Admin batch updated student numbers: Success=%d, Failed=%d', $report['success'], $report['failed']);
            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_update_number', 'status_code' => 200, 'message' => $logMessage]);
            echo json_encode(['success' => true, 'message' => $logMessage]);
            break;

        // (แทนที่ update_data_student_upload.php)
        case 'upload_full_data':
             if (!isset($_FILES['student_csv']) || $_FILES['student_csv']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('กรุณาเลือกไฟล์ CSV สำหรับอัปเดตข้อมูลนักเรียน');
            }
            $csv = parseCsv($_FILES['student_csv']['tmp_name']);
            $report = $model->batchUpdateStudentData($csv['data'], $csv['header']);
            
            $logMessage = sprintf('Admin batch updated full student data: Success=%d, Failed=%d', $report['success'], $report['failed']);
            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_update_full', 'status_code' => 200, 'message' => $logMessage]);
            echo json_encode(['success' => true, 'message' => $logMessage]);
            break;

        default:
            throw new Exception('Invalid action', 400);
    }
} catch (\Exception $e) {
    $code = $e->getCode() ?: 500;
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>