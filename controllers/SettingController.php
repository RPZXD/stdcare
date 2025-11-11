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
    $model = new SettingModel($pdo);

    // (3) ดึงข้อมูล Admin สำหรับ Log
    $admin_id = $_SESSION['Admin_login'] ?? 'system';
    $admin_role = $_SESSION['role'] ?? 'Admin';

    if (!isset($_SESSION['Admin_login'])) {
         throw new Exception('ไม่ได้รับอนุญาต', 403);
    }

    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {

        // Action ใหม่: อัปเดตเวลา
        case 'update_times':
            $model->updateTimeSettings($_POST);
            echo json_encode(['success' => true, 'message' => 'บันทึกการตั้งค่าเวลาสำเร็จ']);
            $logger->log([
                'user_id' => $admin_id, 'role' => $admin_role,
                'action_type' => 'settings_update_times', 'status_code' => 200,
                'message' => "Admin updated time settings"
            ]);
            break;
        
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
            $header = [
                'Stu_id', 'Stu_no', 'Stu_pre', 'Stu_name', 'Stu_sur', 'Stu_nick', 'Stu_birth',
                'Stu_sex', 'Stu_religion', 'Stu_blood', 'Stu_citizenid', 'Stu_phone', 'Stu_addr',
                'Stu_major', 'Stu_room', 'Stu_status', 'Risk_group', 'vehicle',
                'Father_name', 'Father_occu', 'Father_income',
                'Mother_name', 'Mother_occu', 'Mother_income',
                'Par_name', 'Par_relate', 'Par_occu', 'Par_income', 'Par_phone', 'Par_addr'
            ];

            // เพิ่มแถวตัวอย่างพร้อมคำแนะนำ
            $data = [
                [
                    '12345', // Stu_id
                    '1', // Stu_no
                    'เด็กชาย', // Stu_pre
                    'สมชาย', // Stu_name
                    'ใจดี', // Stu_sur
                    'สมชาย', // Stu_nick
                    '2008-05-15', // Stu_birth (YYYY-MM-DD)
                    'ชาย', // Stu_sex
                    'พุทธ', // Stu_religion
                    'O', // Stu_blood
                    '1234567890123', // Stu_citizenid
                    '0812345678', // Stu_phone
                    '123 ถนนตัวอย่าง ตำบลตัวอย่าง อำเภอตัวอย่าง จังหวัดตัวอย่าง 12345', // Stu_addr
                    '1', // Stu_major
                    '1', // Stu_room
                    '1', // Stu_status (1=ปกติ)
                    'ปกติ', // Risk_group
                    'รถจักรยานยนต์', // vehicle
                    'นายใจดี ใจดี', // Father_name
                    'รับจ้าง', // Father_occu
                    '15000', // Father_income
                    'นางใจดี ใจดี', // Mother_name
                    'แม่บ้าน', // Mother_occu
                    '0', // Mother_income
                    'นายใจดี ใจดี', // Par_name
                    'บิดา', // Par_relate
                    'รับจ้าง', // Par_occu
                    '15000', // Par_income
                    '0812345678', // Par_phone
                    '123 ถนนตัวอย่าง ตำบลตัวอย่าง อำเภอตัวอย่าง จังหวัดตัวอย่าง 12345' // Par_addr
                ]
            ];

            outputCsv('new_student_template.csv', $header, $data);
            exit;

        // (แทนที่ update_number_sample.php)
        case 'download_number_template':
            $db_data = $model->getStudentsForNumberUpdate();
            $header = ['Stu_id', 'Stu_major', 'Stu_room', 'Stu_no_new', 'Stu_no_old', 'fullname'];
            $csv_data = [];
            
            // เพิ่มแถวตัวอย่าง
            $csv_data[] = [
                'ตัวอย่าง', '3', '8', '15', '10', 'เด็กชายสมชาย ใจดี (กรุณากรอกเลขที่ใหม่ในคอลัมน์ Stu_no_new)'
            ];
            
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

        // (ใหม่) ดาวน์โหลดเทมเพลตเลขที่แบบรายห้อง
        case 'download_number_template_by_room':
            $class = $_GET['pe'] ?? '';
            $room = $_GET['room'] ?? '';

            if (empty($class) || empty($room)) {
                throw new Exception('กรุณาระบุชั้นและห้อง');
            }

            $db_data = $model->getStudentsForNumberUpdateByRoom($class, $room);
            if (empty($db_data)) {
                echo "<script>alert('ไม่พบข้อมูลนักเรียนในชั้น $class ห้อง $room'); window.close();</script>";
                exit;
            }

            $header = ['Stu_id', 'Stu_major', 'Stu_room', 'Stu_no_new', 'Stu_no_old', 'fullname'];
            $csv_data = [];
            
            // เพิ่มแถวตัวอย่าง
            $csv_data[] = [
                'ตัวอย่าง', '3', '8', '15', '10', 'เด็กชายสมชาย ใจดี (กรุณากรอกเลขที่ใหม่ในคอลัมน์ Stu_no_new)'
            ];
            
            foreach ($db_data as $row) {
                $csv_data[] = [
                    $row['Stu_id'], $row['Stu_major'], $row['Stu_room'],
                    '', // Stu_no_new (ให้กรอก)
                    $row['Stu_no'], // Stu_no_old
                    $row['fullname']
                ];
            }
            outputCsv("update_number_template_c{$class}_r{$room}.csv", $header, $csv_data);
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

            // เพิ่ม error details ใน log ถ้ามี
            if (!empty($report['errors'])) {
                $logMessage .= ' | Errors: ' . implode('; ', array_slice($report['errors'], 0, 5)); // แสดงแค่ 5 อันแรก
                if (count($report['errors']) > 5) {
                    $logMessage .= ' ...และอีก ' . (count($report['errors']) - 5) . ' รายการ';
                }
            }

            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_update_number', 'status_code' => 200, 'message' => $logMessage]);

            // ส่ง error details กลับไปแสดงใน UI
            $totalProcessed = $report['success'] + $report['failed'];
            $responseMessage = sprintf('อัปเดตเลขที่นักเรียน ประมวลผลแล้ว: %d รายการ', $totalProcessed);

            if ($report['failed'] > 0) {
                $responseMessage .= sprintf(', มีปัญหา: %d รายการ', $report['failed']);
            }

            if (!empty($report['errors'])) {
                $responseMessage .= "\n\nรายละเอียดข้อผิดพลาด:\n" . implode("\n", array_slice($report['errors'], 0, 10));
                if (count($report['errors']) > 10) {
                    $responseMessage .= "\n...และอีก " . (count($report['errors']) - 10) . " รายการ";
                }
            } else {
                $responseMessage .= "\n\n✅ ไม่พบข้อผิดพลาดใดๆ";
            }

            echo json_encode(['success' => true, 'message' => $responseMessage]);
            break;

        // (API ใหม่ สำหรับฟอร์ม "อัปเดตเลขที่ แบบรายห้อง")
        case 'upload_number_data_by_room':
            if (!isset($_FILES['number_room_csv']) || $_FILES['number_room_csv']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('กรุณาเลือกไฟล์ CSV สำหรับอัปเดตเลขที่');
            }

            $class = $_POST['number_pe'] ?? '';
            $room = $_POST['number_room'] ?? '';

            if (empty($class) || empty($room)) {
                throw new Exception('กรุณาระบุชั้นและห้อง');
            }

            $csv = parseCsv($_FILES['number_room_csv']['tmp_name']);
            $report = $model->batchUpdateStudentNumbersByRoom($csv['data'], $class, $room);

            $logMessage = sprintf('Admin batch updated student numbers by room (Class:%s, Room:%s): Success=%d, Failed=%d',
                                $class, $room, $report['success'], $report['failed']);

            // เพิ่ม error details ใน log ถ้ามี
            if (!empty($report['errors'])) {
                $logMessage .= ' | Errors: ' . implode('; ', array_slice($report['errors'], 0, 5)); // แสดงแค่ 5 อันแรก
                if (count($report['errors']) > 5) {
                    $logMessage .= ' ...และอีก ' . (count($report['errors']) - 5) . ' รายการ';
                }
            }

            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_update_number_by_room', 'status_code' => 200, 'message' => $logMessage]);

            // ส่ง error details กลับไปแสดงใน UI
            $totalProcessed = $report['success'] + $report['failed'];
            $responseMessage = sprintf('อัปเดตเลขที่นักเรียน (ชั้น %s ห้อง %s) ประมวลผลแล้ว: %d รายการ',
                                     $class, $room, $totalProcessed);

            if ($report['failed'] > 0) {
                $responseMessage .= sprintf(', มีปัญหา: %d รายการ', $report['failed']);
            }

            if (!empty($report['errors'])) {
                $responseMessage .= "\n\nรายละเอียดข้อผิดพลาด:\n" . implode("\n", array_slice($report['errors'], 0, 10));
                if (count($report['errors']) > 10) {
                    $responseMessage .= "\n...และอีก " . (count($report['errors']) - 10) . " รายการ";
                }
            } else {
                $responseMessage .= "\n\n✅ ไม่พบข้อผิดพลาดใดๆ";
            }

            echo json_encode(['success' => true, 'message' => $responseMessage]);
            break;        // (แทนที่ update_data_student_upload.php)
        case 'upload_full_data':
             if (!isset($_FILES['student_csv']) || $_FILES['student_csv']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('กรุณาเลือกไฟล์ CSV สำหรับอัปเดตข้อมูลนักเรียน');
            }
            $csv = parseCsv($_FILES['student_csv']['tmp_name']);
            $report = $model->batchUpdateStudentData($csv['data'], $csv['header']);

            $logMessage = sprintf('Admin batch updated full student data: Success=%d, Failed=%d', $report['success'], $report['failed']);

            // เพิ่ม error details ใน log ถ้ามี
            if (!empty($report['errors'])) {
                $logMessage .= ' | Errors: ' . implode('; ', array_slice($report['errors'], 0, 5)); // แสดงแค่ 5 อันแรก
                if (count($report['errors']) > 5) {
                    $logMessage .= ' ...และอีก ' . (count($report['errors']) - 5) . ' รายการ';
                }
            }

            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_update_full', 'status_code' => 200, 'message' => $logMessage]);

            // ส่ง error details กลับไปแสดงใน UI
            $totalProcessed = $report['success'] + $report['failed'];
            $responseMessage = sprintf('อัปเดตข้อมูลนักเรียน ประมวลผลแล้ว: %d รายการ', $totalProcessed);

            if ($report['failed'] > 0) {
                $responseMessage .= sprintf(', มีปัญหา: %d รายการ', $report['failed']);
            }

            if (!empty($report['errors'])) {
                $responseMessage .= "\n\nรายละเอียดข้อผิดพลาด:\n" . implode("\n", array_slice($report['errors'], 0, 10));
                if (count($report['errors']) > 10) {
                    $responseMessage .= "\n...และอีก " . (count($report['errors']) - 10) . " รายการ";
                }
            } else {
                $responseMessage .= "\n\n✅ ไม่พบข้อผิดพลาดใดๆ";
            }

            echo json_encode(['success' => true, 'message' => $responseMessage]);
            break;

        // (ใหม่) เพิ่มนักเรียนใหม่จาก CSV
        case 'upload_new_student_data':
            if (!isset($_FILES['new_student_csv']) || $_FILES['new_student_csv']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('กรุณาเลือกไฟล์ CSV สำหรับเพิ่มนักเรียนใหม่');
            }
            $csv = parseCsv($_FILES['new_student_csv']['tmp_name']);
            $report = $model->batchInsertStudentData($csv['data']);

            $logMessage = sprintf('Admin batch inserted new students: Success=%d, Failed=%d', $report['success'], $report['failed']);

            // เพิ่ม error details ใน log ถ้ามี
            if (!empty($report['errors'])) {
                $logMessage .= ' | Errors: ' . implode('; ', array_slice($report['errors'], 0, 5)); // แสดงแค่ 5 อันแรก
                if (count($report['errors']) > 5) {
                    $logMessage .= ' ...และอีก ' . (count($report['errors']) - 5) . ' รายการ';
                }
            }

            $logger->log(['user_id' => $admin_id, 'role' => $admin_role, 'action_type' => 'student_csv_insert_new', 'status_code' => 200, 'message' => $logMessage]);

            // ส่ง error details กลับไปแสดงใน UI
            $totalProcessed = $report['success'] + $report['failed'];
            $responseMessage = sprintf('เพิ่มนักเรียนใหม่ ประมวลผลแล้ว: %d รายการ', $totalProcessed);

            if ($report['failed'] > 0) {
                $responseMessage .= sprintf(', มีปัญหา: %d รายการ', $report['failed']);
            }

            if (!empty($report['errors'])) {
                $responseMessage .= "\n\nรายละเอียดข้อผิดพลาด:\n" . implode("\n", array_slice($report['errors'], 0, 10));
                if (count($report['errors']) > 10) {
                    $responseMessage .= "\n...และอีก " . (count($report['errors']) - 10) . " รายการ";
                }
            } else {
                $responseMessage .= "\n\n✅ ไม่พบข้อผิดพลาดใดๆ";
            }

            echo json_encode(['success' => true, 'message' => $responseMessage]);
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