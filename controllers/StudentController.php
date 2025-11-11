<?php
session_start();
header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');
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
    
    $admin_id = $_SESSION['Admin_login'] ?? $_SESSION['Officer_login'] ?? 'system';
    // use explicit isset() to avoid undefined index notice for Officer_login
    $admin_role = $_SESSION['role'] ?? (isset($_SESSION['Officer_login']) ? 'Officer' : 'Admin');
    $teach_id = $_SESSION['Teacher_login'] ?? $_SESSION['Officer_login'] ?? $admin_id;

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

        // ▼▼▼ เพิ่ม Case ใหม่สำหรับหน้านี้โดยเฉพาะ ▼▼▼
        case 'list_for_officer':
            // (1) ดึงค่า Filter จาก $_GET
            $filters = [
                'class'  => $_GET['class'] ?? null,
                'room'   => $_GET['room'] ?? null,
                'status' => $_GET['status'] ?? null
            ];
            
            // (2) ดึงข้อมูลนักเรียน
            $studentData = $studentModel->getAll($filters);
            
            // (3) ส่งข้อมูลกลับใน Format ที่ JavaScript (data_student.php) ต้องการ
            // คือมี success: true และ data: [...] ห่อหุ้มอยู่
            echo json_encode(['success' => true, 'data' => $studentData]);
            break;
        // ▲▲▲ สิ้นสุด Case ใหม่ ▲▲▲

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

        case 'search_student':
            // (Action นี้ถูกเรียกโดย rfid.php (ตารางนักเรียนที่ยังไม่มีบัตร))
            // (มันรับค่า POST จาก DataTables SSP)
            $params = $_POST;
            $result = $studentModel->getStudentsWithoutRfid($params);
            echo json_encode($result);
            break;

        case 'get':
            $id = $_GET['id'] ?? $_POST['id'] ?? '';
            echo json_encode($studentModel->getById($id));
            break;

        // ... (case อื่นๆ) ...

        case 'get_modal_student_data':
            // [1] สั่งให้ Browser รู้ว่านี่คือ HTML (เหมือนเดิม)
            header('Content-Type: text/html; charset=utf-8'); 

            $stu_id = $_GET['stu_id'];
            
            // [2] ดึงข้อมูลนักเรียน (ต้องมั่นใจว่า getById ดึง SELECT * นะครับ)
            $student_data = $studentModel->getById($stu_id); 
            
            // --- [3] สร้างตัวแปรช่วยแสดงผล (จาก Schema ที่คุณให้มา) ---
            
            // จัดการ Stu_status
            $status_map = [
                1 => 'ปกติ',
                2 => 'จบ',
                3 => 'ย้ายรร.',
                4 => 'ออกกลางคัน',
                9 => 'เสียชีวิต'
            ];
            $display_status = $status_map[$student_data['Stu_status']] ?? 'ไม่ระบุ (' . $student_data['Stu_status'] . ')';

            // จัดการ vehicle
            $vehicle_map = [
                0 => 'ไม่มี',
                1 => 'มี'
            ];
            $display_vehicle = $vehicle_map[$student_data['vehicle']] ?? 'ไม่ระบุ';

            // จัดการ Stu_sex (เดาว่า 1=ชาย, 2=หญิง)
            $sex_map = [
                1 => 'ชาย',
                2 => 'หญิง'
            ];
            $display_sex = $sex_map[$student_data['Stu_sex']] ?? 'ไม่ระบุ';

            // ฟังก์ชันช่วยแสดงผล (ป้องกันค่า NULL และ XSS)
            $val = function($key) use ($student_data) {
                // ถ้าค่าเป็น NULL หรือ "" ให้แสดงขีดกลาง '-'
                return htmlspecialchars($student_data[$key] ?? '-');
            };

            // --- [4] Echo HTML ของ Modal ตัวใหม่ ---
            // (ใช้ modal-xl เพื่อขยายขนาด)
            echo '
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="fas fa-user-circle mr-2"></i>
                                ข้อมูลนักเรียน: ' . $val('Stu_pre') . $val('Stu_name') . ' ' . $val('Stu_sur') . '
                            </h5>
                            <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            
                            <div class="row mb-3">
                                <div class="col-md-3 text-center">
                                    <img src="https://std.phichai.ac.th/photo/' . $val('Stu_picture') . '" 
                                         class="img-thumbnail rounded-circle mb-3 mx-auto d-block" 
                                         style="width: 250px; height: 250px; object-fit: cover;"
                                         onerror="this.src=\'../dist/img/default-avatar.svg\'">
                                    <h5 class="font-weight-bold text-center">รหัส: ' . $val('Stu_id') . '</h5>
                                    <p class="text-center"><strong>สถานะ:</strong> ' . htmlspecialchars($display_status) . '</p>
                                </div>
                                <div class="col-md-9">
                                    <h5><i class="fas fa-user-graduate mr-2"></i>ข้อมูลการศึกษา</h5>
                                    <hr class="mt-0">
                                    <div class="row">
                                        <div class="col-md-4"><p><strong>เลขที่:</strong> ' . $val('Stu_no') . '</p></div>
                                        <div class="col-md-4"><p><strong>ระดับชั้น:</strong> ' . $val('Stu_major') . '</p></div>
                                        <div class="col-md-4"><p><strong>ห้อง:</strong> ' . $val('Stu_room') . '</p></div>
                                    </div>

                                    <h5 class="mt-3"><i class="fas fa-id-card mr-2"></i>ข้อมูลส่วนตัว</h5>
                                    <hr class="mt-0">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p><strong>ชื่อเล่น:</strong> ' . $val('Stu_nick') . '</p>
                                            <p><strong>เพศ:</strong> ' . htmlspecialchars($display_sex) . '</p>
                                            <p><strong>เลข ปชช.:</strong> ' . $val('Stu_citizenid') . '</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>วันเกิด:</strong> ' . $val('Stu_birth') . '</p>
                                            <p><strong>ศาสนา:</strong> ' . $val('Stu_religion') . '</p>
                                            <p><strong>กรุ๊ปเลือด:</strong> ' . $val('Stu_blood') . '</p>
                                        </div>
                                        <div class="col-md-4">
                                            <p><strong>เบอร์โทร (นร.):</strong> ' . $val('Stu_phone') . '</p>
                                            <p><strong>การเดินทาง:</strong> ' . htmlspecialchars($display_vehicle) . '</p>
                                        </div>
                                        <div class="col-md-12">
                                            <p><strong>ที่อยู่ (นร.):</strong> ' . $val('Stu_addr') . '</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h5 class="mt-3"><i class="fas fa-users mr-2"></i>ข้อมูลครอบครัว</h5>
                            <hr class="mt-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6><strong>ข้อมูลบิดา</strong></h6>
                                    <p><strong>ชื่อ:</strong> ' . $val('Father_name') . '</p>
                                    <p><strong>อาชีพ:</strong> ' . $val('Father_occu') . '</p>
                                    <p><strong>รายได้:</strong> ' . $val('Father_income') . '</p>
                                </div>
                                <div class="col-md-6">
                                    <h6><strong>ข้อมูลมารดา</strong></h6>
                                    <p><strong>ชื่อ:</strong> ' . $val('Mother_name') . '</p>
                                    <p><strong>อาชีพ:</strong> ' . $val('Mother_occu') . '</p>
                                    <p><strong>รายได้:</strong> ' . $val('Mother_income') . '</p>
                                </div>
                            </div>

                            <h6 class="mt-3"><strong>ข้อมูลผู้ปกครอง (ที่ติดต่อได้)</strong></h6>
                            <hr class="mt-1">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>ชื่อ:</strong> ' . $val('Par_name') . '</p>
                                    <p><strong>ความสัมพันธ์:</strong> ' . $val('Par_relate') . '</p>
                                    <p><strong>อาชีพ:</strong> ' . $val('Par_occu') . '</p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>รายได้:</strong> ' . $val('Par_income') . '</p>
                                    <p><strong>เบอร์โทร:</strong> ' . $val('Par_phone') . '</p>
                                    <p><strong>ที่อยู่:</strong> ' . $val('Par_addr') . '</p>
                                </div>
                            </div>
                            
                            </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">ปิด</button>
                        </div>
                    </div>
                </div>
            ';

            // [5] หยุดการทำงาน (เหมือนเดิม)
            exit; 
        
        // ... (case อื่นๆ) ...
        

        case 'create':
            try {
                // รับข้อมูลจาก POST
                $data = [
                    'Stu_id' => $_POST['addStu_id'] ?? '',
                    'Stu_no' => $_POST['addStu_no'] ?? '',
                    'Stu_pre' => $_POST['addStu_pre'] ?? '',
                    'Stu_name' => $_POST['addStu_name'] ?? '',
                    'Stu_sur' => $_POST['addStu_sur'] ?? '',
                    'Stu_major' => $_POST['addStu_major'] ?? '',
                    'Stu_room' => $_POST['addStu_room'] ?? ''
                ];
                
                // ตรวจสอบข้อมูลที่จำเป็น
                if (empty($data['Stu_id']) || empty($data['Stu_name']) || empty($data['Stu_sur'])) {
                    throw new Exception('กรุณากรอกข้อมูลให้ครบถ้วน');
                }
                
                // เรียกใช้ Model เพื่อสร้างนักเรียน
                $result = $studentModel->createStudent($data);
                
                if ($result) {
                    // Log success
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_create_success',
                        'status_code' => 200,
                        'message' => 'Admin created student ID: ' . htmlspecialchars($data['Stu_id'])
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'สร้างนักเรียนสำเร็จ'
                    ]);
                } else {
                    throw new Exception('ไม่สามารถสร้างนักเรียนได้');
                }
                
            } catch (Exception $e) {
                // Log failure
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_create_fail',
                    'status_code' => 500,
                    'message' => 'Failed to create student. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'update':
            try {
                // รับข้อมูลจาก POST (จาก Edit Modal)
                $data = [
                    'OldStu_id' => $_POST['editStu_id_old'] ?? '', // รหัสเดิมสำหรับ WHERE clause
                    'Stu_id' => $_POST['editStu_id'] ?? '',
                    'Stu_no' => $_POST['editStu_no'] ?? '',
                    'Stu_pre' => $_POST['editStu_pre'] ?? '',
                    'Stu_name' => $_POST['editStu_name'] ?? '',
                    'Stu_sur' => $_POST['editStu_sur'] ?? '',
                    'Stu_major' => $_POST['editStu_major'] ?? '',
                    'Stu_room' => $_POST['editStu_room'] ?? '',
                    'Stu_status' => $_POST['editStu_status'] ?? '1'
                ];
                
                // คำนวณเพศจากคำนำหน้า (เหมือนใน createStudent)
                $stu_pre = $data['Stu_pre'];
                $stu_sex = '';
                if ($stu_pre === 'เด็กชาย' || $stu_pre === 'นาย') {
                    $stu_sex = 1;
                } else if ($stu_pre === 'เด็กหญิง' || $stu_pre === 'นางสาว') {
                    $stu_sex = 2;
                }
                $data['Stu_sex'] = $stu_sex;
                
                // ใช้รหัสนักเรียนเป็นรหัสผ่าน (หรือเก็บรหัสผ่านเดิมไว้)
                $data['Stu_password'] = $data['Stu_id'];
                
                // ตรวจสอบข้อมูลที่จำเป็น
                if (empty($data['OldStu_id']) || empty($data['Stu_id'])) {
                    throw new Exception('ไม่พบรหัสนักเรียน');
                }
                
                // เรียกใช้ Model เพื่ออัปเดต
                $result = $studentModel->updateStudentInfo($data);
                
                if ($result) {
                    // Log success
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_update_success',
                        'status_code' => 200,
                        'message' => 'Admin updated student ID: ' . htmlspecialchars($data['Stu_id'])
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'อัปเดตข้อมูลสำเร็จ'
                    ]);
                } else {
                    throw new Exception('ไม่สามารถอัปเดตข้อมูลได้');
                }
                
            } catch (Exception $e) {
                // Log failure
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_update_fail',
                    'status_code' => 500,
                    'message' => 'Failed to update student. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'delete':
            try {
                // รับ ID จาก POST
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    throw new Exception('ไม่พบรหัสนักเรียน');
                }
                
                // เรียกใช้ Model เพื่อลบ (จริงๆ คืออัปเดต status = 0)
                $result = $studentModel->delete($id);
                
                if ($result) {
                    // Log success
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_delete_success',
                        'status_code' => 200,
                        'message' => 'Admin deleted student ID: ' . htmlspecialchars($id)
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'ลบข้อมูลสำเร็จ'
                    ]);
                } else {
                    throw new Exception('ไม่สามารถลบข้อมูลได้');
                }
                
            } catch (Exception $e) {
                // Log failure
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_delete_fail',
                    'status_code' => 500,
                    'message' => 'Failed to delete student. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'resetpwd':
            try {
                // รับ ID จาก POST
                $id = $_POST['id'] ?? '';
                
                if (empty($id)) {
                    throw new Exception('ไม่พบรหัสนักเรียน');
                }
                
                // เรียกใช้ Model เพื่อรีเซ็ตรหัสผ่าน
                $result = $studentModel->resetPassword($id);
                
                if ($result) {
                    // Log success
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_reset_password_success',
                        'status_code' => 200,
                        'message' => 'Admin reset password for student ID: ' . htmlspecialchars($id)
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'รีเซ็ตรหัสผ่านสำเร็จ (รหัสผ่านใหม่คือรหัสนักเรียน)'
                    ]);
                } else {
                    throw new Exception('ไม่สามารถรีเซ็ตรหัสผ่านได้');
                }
                
            } catch (Exception $e) {
                // Log failure
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_reset_password_fail',
                    'status_code' => 500,
                    'message' => 'Failed to reset password. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'inline_update':
            try {
                // รับข้อมูลจาก POST
                $id = $_POST['id'] ?? '';
                $field = $_POST['field'] ?? '';
                $value = $_POST['value'] ?? '';
                
                if (empty($id) || empty($field)) {
                    throw new Exception('ข้อมูลไม่ครบถ้วน');
                }
                
                // เรียกใช้ Model เพื่ออัปเดต inline
                $result = $studentModel->inlineUpdate($id, $field, $value);
                
                if ($result) {
                    // Log success
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_inline_update_success',
                        'status_code' => 200,
                        'message' => "Admin inline updated student ID: $id, field: $field"
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'อัปเดตข้อมูลสำเร็จ'
                    ]);
                } else {
                    throw new Exception('ไม่สามารถอัปเดตข้อมูลได้');
                }
                
            } catch (Exception $e) {
                // Log failure
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_inline_update_fail',
                    'status_code' => 500,
                    'message' => 'Failed inline update. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'upload_rfid_csv':
            // (ไฟล์นี้ถูกเรียกจาก rfid.php และ settings.php)
            try {
                // (ใช้ชื่อ 'rfid_csv_file' ตามที่ฟอร์มส่งมา)
                if (!isset($_FILES['rfid_csv_file']) || $_FILES['rfid_csv_file']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('ไม่พบไฟล์ หรือ ไฟล์อัปโหลดมีปัญหา');
                }
                $fileTmpPath = $_FILES['rfid_csv_file']['tmp_name'];
                
                $rfid_data = [];
                $header = null;
                $stuIdIndex = -1;
                $rfidCodeIndex = -1;

                if (($handle = fopen($fileTmpPath, 'r')) !== FALSE) {
                    if (($headerData = fgetcsv($handle)) !== FALSE) {
                        $headerData[0] = preg_replace('/^\x{FEFF}/u', '', $headerData[0]); // Remove BOM
                        $header = array_map('trim', $headerData);
                        
                        // (หา index ของคอลัมน์ที่ต้องการ)
                        $stuIdIndex = array_search(strtolower('stu_id'), array_map('strtolower', $header));
                        $rfidCodeIndex = array_search(strtolower('rfid_code'), array_map('strtolower', $header));
                    }

                    if ($stuIdIndex === false || $rfidCodeIndex === false) {
                        throw new Exception('ไม่พบคอลัมน์ stu_id หรือ rfid_code ในไฟล์ CSV');
                    }

                    while (($data = fgetcsv($handle)) !== FALSE) {
                        $stu_id = $data[$stuIdIndex] ?? null;
                        $rfid_code = $data[$rfidCodeIndex] ?? null;

                        if ($stu_id && $rfid_code) {
                             $rfid_data[] = [
                                'stu_id' => $stu_id,
                                'rfid_code' => $rfid_code
                             ];
                        }
                    }
                    fclose($handle);

                    if (!empty($rfid_data)) {
                        // (เรียก Model เมธอดใหม่)
                        $report = $studentModel->batchRegisterOrUpdateRfid($rfid_data);
                        
                        // (อัปเดต Log)
                        $logMessage = sprintf(
                            'Admin batch register/update RFID: New=%d, Updated=%d, Failed=%d, Skipped=%d',
                            $report['success'], $report['updated'], $report['failed'], $report['skipped']
                        );
                        $logger->log([
                            'user_id' => $admin_id,
                            'role' => $admin_role,
                            'action_type' => 'student_rfid_upload_success',
                            'status_code' => 200,
                            'message' => $logMessage
                        ]);
                        
                        echo json_encode(['status' => 'completed', 'report' => $report]);
                    } else {
                        echo json_encode(['status' => 'empty', 'message' => 'ไม่พบข้อมูล RFID ที่จะลงทะเบียนในไฟล์']);
                    }

                } else {
                    throw new Exception('ไม่สามารถเปิดไฟล์ CSV ที่อัปโหลดได้');
                }
            } catch (Exception $e) {
                // --- (เพิ่ม Log Fail) ---
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
            
        // ▼▼▼ เพิ่ม Case ใหม่ ▼▼▼
        case 'print':
            $filters = [
                'class'  => $_GET['class'] ?? null,
                'room'   => $_GET['room'] ?? null,
                'status' => 1 // (บังคับสถานะ = 1 ให้ตรงกับหน้าเว็บ)
            ];
            
            // เรียกใช้ฟังก์ชันสร้าง HTML จาก Model
            $html = $studentModel->generatePrintableHtml($filters);
            
            // **สำคัญ** เปลี่ยน header กลับเป็น text/html เพื่อให้ browser แสดงผลถูกต้อง
            header('Content-Type: text/html; charset=utf-8');
            echo $html;
            break;
        
        case 'upload_photo':
            try {
                $stu_id = $_POST['stu_id'] ?? '';
                
                if (empty($stu_id)) {
                    throw new Exception('ไม่พบรหัสนักเรียน');
                }
                
                if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
                    throw new Exception('ไม่พบไฟล์รูปภาพ หรืออัปโหลดไม่สำเร็จ');
                }
                
                $file = $_FILES['photo'];
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB
                
                if (!in_array($file['type'], $allowed_types)) {
                    throw new Exception('ประเภทไฟล์ไม่ถูกต้อง (รองรับเฉพาะ JPG, PNG, GIF)');
                }
                
                if ($file['size'] > $max_size) {
                    throw new Exception('ขนาดไฟล์เกิน 5MB');
                }
                
                // สร้างชื่อไฟล์ใหม่
                $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                $new_filename = $stu_id . '.' . $extension;
                $upload_path = __DIR__ . '/../photo/' . $new_filename;
                
                // ลบรูปเก่า (ถ้ามี)
                $old_photo = $studentModel->getById($stu_id)['Stu_picture'] ?? '';
                if ($old_photo && file_exists(__DIR__ . '/../photo/' . $old_photo)) {
                    @unlink(__DIR__ . '/../photo/' . $old_photo);
                }
                
                // อัปโหลดรูปใหม่
                if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                    throw new Exception('ไม่สามารถอัปโหลดไฟล์ได้');
                }
                
                // อัปเดตชื่อไฟล์ในฐานข้อมูล
                $result = $studentModel->updatePhoto($stu_id, $new_filename);
                
                if ($result) {
                    $logger->log([
                        'user_id' => $admin_id,
                        'role' => $admin_role,
                        'action_type' => 'student_upload_photo_success',
                        'status_code' => 200,
                        'message' => 'Admin uploaded photo for student ID: ' . htmlspecialchars($stu_id)
                    ]);
                    
                    echo json_encode([
                        'success' => true,
                        'message' => 'อัปโหลดรูปภาพสำเร็จ',
                        'filename' => $new_filename
                    ]);
                } else {
                    throw new Exception('ไม่สามารถอัปเดตข้อมูลในฐานข้อมูลได้');
                }
                
            } catch (Exception $e) {
                $logger->log([
                    'user_id' => $admin_id,
                    'role' => $admin_role,
                    'action_type' => 'student_upload_photo_fail',
                    'status_code' => 500,
                    'message' => 'Failed to upload photo. Error: ' . $e->getMessage()
                ]);
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
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