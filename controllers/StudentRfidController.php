<?php
session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../models/StudentRfid.php';
require_once __DIR__ . '/../models/Student.php';

use App\Models\StudentRfid;
use App\Models\Student;

$rfidModel = new StudentRfid();
$studentModel = new Student();

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
        case 'list':
            // ... (โค้ด case 'list' เดิมของคุณ) ...
            break;

        // --- ADDED: Action ใหม่สำหรับ Server-Side Processing (ตาราง RFID) ---
        case 'list_ssp':
            requireOfficer();
            $params = $_POST;
            $result = $rfidModel->getRfidsForDatatable($params);
            echo json_encode($result);
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
