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
        // --- ADDED: Action ใหม่สำหรับ Export CSV ---
        case 'export_csv':
            try {
                $major = $_GET['major'] ?? '';
                $room = $_GET['room'] ?? '';

                if (empty($major) || empty($room)) {
                    http_response_code(400);
                    echo "กรุณาระบุ major และ room";
                    exit;
                }

                $data = $studentModel->getStudentsForCsvExport($major, $room);

                if (empty($data)) {
                    http_response_code(404);
                    echo "ไม่พบข้อมูลนักเรียนสำหรับห้อง ม.$major/$room";
                    exit;
                }

                $filename = "rfid_template_m{$major}_{$room}.csv";

                // ตั้งค่า Header ให้ดาวน์โหลด
                header('Content-Type: text/csv; charset=utf-8');
                header('Content-Disposition: attachment; filename="' . $filename . '"');

                // สร้าง File Pointer ไปยัง output
                $output = fopen('php://output', 'w');
                
                // --- สำคัญ: เพิ่ม BOM สำหรับ Excel ให้อ่านไทยได้ ---
                fputs($output, "\xEF\xBB\xBF"); 

                // เขียนหัวตาราง (Headers)
                fputcsv($output, ['stu_id', 'stu_no', 'name', 'rfid_code']);

                // เขียนข้อมูล
                foreach ($data as $row) {
                    $csvRow = [
                        $row['Stu_id'],
                        $row['Stu_no'],
                        trim(($row['Stu_pre'] ?? '') . ($row['Stu_name'] ?? '') . ' ' . ($row['Stu_sur'] ?? '')),
                        $row['rfid_code'] // ใส่รหัส RFID ที่มีอยู่ (ถ้ามี)
                    ];
                    fputcsv($output, $csvRow);
                }

                fclose($output);
                exit; // จบการทำงานทันที

            } catch (Exception $e) {
                http_response_code(500);
                echo "Error: " . $e->getMessage();
            }
            break;

        // --- ADDED: Action ใหม่สำหรับ Import CSV ---
        case 'import_csv':
            header('Content-Type: application/json'); // ตรวจสอบว่ามี header นี้
            $report = ['success' => 0, 'failed' => 0, 'skipped' => 0, 'errors' => []];

            if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] != UPLOAD_ERR_OK) {
                http_response_code(400);
                echo json_encode(['error' => 'ไม่ได้อัปโหลดไฟล์ หรือไฟล์มีปัญหา']);
                exit;
            }

            $filePath = $_FILES['csv_file']['tmp_name'];
            
            // เปิดไฟล์ CSV
            if (($handle = fopen($filePath, "r")) !== FALSE) {
                $rfid_data = [];
                
                // --- อ่าน Header ---
                $header = fgetcsv($handle);
                // ตรวจสอบ Header ที่จำเป็น (stu_id, rfid_code)
                if ($header === false || !in_array('stu_id', $header) || !in_array('rfid_code', $header)) {
                    fclose($handle);
                    http_response_code(400);
                    echo json_encode(['error' => 'ไฟล์ CSV ต้องมีคอลัมน์ stu_id และ rfid_code']);
                    exit;
                }
                
                // หาตำแหน่งคอลัมน์
                $stuIdIndex = array_search('stu_id', $header);
                $rfidCodeIndex = array_search('rfid_code', $header);

                // วนลูปอ่านข้อมูลทีละแถว
                while (($data = fgetcsv($handle)) !== FALSE) {
                    $stu_id = $data[$stuIdIndex] ?? null;
                    $rfid_code = $data[$rfidCodeIndex] ?? null;

                    // เพิ่มข้อมูลสำหรับการ Batch
                    if ($stu_id && $rfid_code) {
                         $rfid_data[] = [
                            'stu_id' => $stu_id,
                            'rfid_code' => $rfid_code
                         ];
                    }
                }
                fclose($handle);

                // ถ้ามีข้อมูล
                if (!empty($rfid_data)) {
                    // เรียก Model ให้ทำงาน
                    $report = $studentModel->batchRegisterRfid($rfid_data);
                    echo json_encode(['status' => 'completed', 'report' => $report]);
                } else {
                    echo json_encode(['status' => 'empty', 'message' => 'ไม่พบข้อมูล RFID ที่จะลงทะเบียนในไฟล์']);
                }

            } else {
                http_response_code(500);
                echo json_encode(['error' => 'ไม่สามารถเปิดไฟล์ CSV ที่อัปโหลดได้']);
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