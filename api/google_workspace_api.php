<?php
session_start();
// ตั้งค่า header สำหรับ JSON response
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบสิทธิ์การเข้าถึง (เฉพาะแอดมินหรือครูที่ได้รับอนุญาต)
// หากระบบของคุณมีการตรวจสอบสิทธิ์ที่รัดกุมกว่านี้ ให้นำมาใส่ที่นี่
if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access.']);
    exit();
}

require_once '../controllers/GoogleWorkspaceController.php';
use Controllers\GoogleWorkspaceController;

// ตรวจสอบว่าเป็น POST request หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit();
}

// รับข้อมูล JSON จาก Request Body (Axios / Fetch API นิยมส่งแบบนี้)
$inputJSON = file_get_contents('php://input');
$input = json_decode($inputJSON, TRUE); // Convert JSON into array

// ถ้ารับแบบ Form Data
if (empty($input)) {
    $input = $_POST;
}

$action = $input['action'] ?? '';

if (empty($action)) {
    echo json_encode(['status' => 'error', 'message' => 'Action is required.']);
    exit();
}

$controller = new GoogleWorkspaceController();

try {
    switch ($action) {
        case 'reset_password':
            $email = $input['email'] ?? '';
            $newPassword = $input['new_password'] ?? '';
            $stu_id = $input['stu_id'] ?? '';
            
            if (empty($email) || empty($newPassword)) {
                echo json_encode(['status' => 'error', 'message' => 'Email and new password are required.']);
                exit();
            }

            // เพิ่มความปลอดภัย ตรวจสอบโดเมน
            if (strpos($email, '@phichai.ac.th') === false) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid email domain. Must be @phichai.ac.th']);
                exit();
            }

            // เรียกใช้ Controller ส่งคำสั่งไป GAS
            $result = $controller->updatePassword($email, $newPassword);
            
            // ถ้า GAS คืนค่าสำเร็จ และมีการส่ง stu_id มาด้วย ให้บันทึกลงฐานข้อมูล
            if (isset($result['status']) && $result['status'] === 'success' && !empty($stu_id)) {
                require_once '../config/Database.php';
                require_once '../classes/DatabaseUsers.php';
                require_once '../models/Student.php';
                
                $connectDB = new \App\DatabaseUsers();
                $studentModel = new \App\Models\Student($connectDB);
                $studentModel->updateGooglePassword($stu_id, $newPassword);
            }
            
            echo json_encode($result);
            break;

        case 'get_info':
            $email = $input['email'] ?? '';
            if (empty($email)) {
                echo json_encode(['status' => 'error', 'message' => 'Email is required.']);
                exit();
            }
            $result = $controller->getUserInfo($email);
            echo json_encode($result);
            break;

        default:
            echo json_encode(['status' => 'error', 'message' => 'Unknown action.']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
