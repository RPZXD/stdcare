<?php
session_start();
header('Content-Type: application/json');

// --- Original Model Includes ---
require_once __DIR__ . '/../models/StudentRfid.php';
require_once __DIR__ . '/../models/Student.php'; // ตัวที่ (อาจจะ) มีปัญหา

use App\Models\StudentRfid;
use App\Models\Student as NamespacedStudent; // <-- เปลี่ยนชื่อคลาสนี้ไปก่อน

// --- New/Working Model Includes (จาก rfid.php) ---
require_once __DIR__ . '/../config/Database.php'; 
require_once __DIR__ . '/../class/Student.php';  // <-- นี่คือคลาส Student ที่เราจะใช้ (จาก /class/)

$rfidModel = new StudentRfid();
// $studentModel = new NamespacedStudent(); // ไม่ต้องสร้างตัวแปรนี้ถ้าไม่ได้ใช้

$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

function requireOfficer() {
    if (!isset($_SESSION['Officer_login'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

try {
    switch ($action) {
        // --- นี่คือ ACTION ใหม่ที่เราเพิ่มเข้ามา ---
        case 'listStudents':
            requireOfficer();
            
            // ใช้คลาส Student (จาก /class/Student.php) ที่ทำงานได้
            $connectDB = new Database("phichaia_student");
            $db = $connectDB->getConnection();
            
            // นี่คือ new Student() จาก /class/Student.php
            $workingStudentModel = new Student($db); 
            
            // สันนิษฐานว่าคลาสนี้มีเมธอด getAll()
            $studentList = $workingStudentModel->getAll();
            echo json_encode($studentList);
            break;
        // --- จบ Action ใหม่ ---

        case 'list':
            requireOfficer();
            $list = $rfidModel->getAll();
            // เติมชื่อเต็ม
            foreach ($list as &$row) {
                $row['stu_name'] = trim(($row['stu_name'] ?? '') . ' ' . ($row['stu_sur'] ?? ''));
            }
            echo json_encode($list);
            break;
        case 'get':
            requireOfficer();
            $id = $_GET['id'] ?? $_POST['id'] ?? '';
            $row = $rfidModel->getById($id);
            echo json_encode($row);
            break;
        case 'getByRfid':
            requireOfficer();
            $rfid_code = $_GET['rfid_code'] ?? $_POST['rfid_code'] ?? '';
            $row = $rfidModel->getByRfid($rfid_code);
            echo json_encode($row);
            break;
        case 'getByStudent':
            requireOfficer();
            $stu_id = $_GET['stu_id'] ?? $_POST['stu_id'] ?? '';
            $row = $rfidModel->getByStudent($stu_id);
            echo json_encode($row ?: null); // <-- แก้ไขตรงนี้
            break;
        case 'register':
            requireOfficer();
            $stu_id = $_POST['stu_id'] ?? '';
            $rfid_code = $_POST['rfid_code'] ?? '';
            if (!$stu_id || !$rfid_code) {
                echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }
            $result = $rfidModel->register($stu_id, $rfid_code);
            echo json_encode($result);
            break;
        case 'update':
            requireOfficer();
            $id = $_POST['id'] ?? '';
            $rfid_code = $_POST['rfid_code'] ?? '';
            if (!$id || !$rfid_code) {
                echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }
            $result = $rfidModel->update($id, $rfid_code);
            echo json_encode($result);
            break;
        case 'delete':
            requireOfficer();
            $id = $_POST['id'] ?? '';
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'ข้อมูลไม่ครบถ้วน']);
                exit;
            }
            $result = $rfidModel->delete($id);
            echo json_encode(['success' => $result]);
            break;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage()]);
}