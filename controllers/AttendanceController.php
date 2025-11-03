<?php
// (ปรับ Path ตามโครงสร้างของคุณ)
require_once(__DIR__ . "/../classes/DatabaseUsers.php");
require_once(__DIR__ . "/../models/SettingModel.php");
require_once(__DIR__ . "/../models/AttendanceModel.php");
require_once(__DIR__ . "/../class/UserLogin.php"); 

use App\DatabaseUsers;
use App\Models\SettingModel;
use App\Models\AttendanceModel;

header('Content-Type: application/json');
date_default_timezone_set('Asia/Bangkok');

try {
    // 1. Setup Database and Models
    $connectDB = new DatabaseUsers();
    $db = $connectDB->getPDO();
    
    $settingsModel = new SettingModel($db);
    $attendanceModel = new AttendanceModel($db);
    
    // ดึงการตั้งค่าเวลา (ใช้สำหรับทุก action)
    $timeSettings = $settingsModel->getAllTimeSettings();

    // 2. Routing (ดูว่า Frontend ขอ action อะไร)
    $action = $_GET['action'] ?? '';

    switch ($action) {
        
        // Action: ดึงประวัติทั้งหมดสำหรับตาราง
        case 'get_log':
            $data = $attendanceModel->getTodayAttendanceLog($timeSettings);
            echo json_encode(['data' => $data]);
            break;

        // Action: ดึงการสแกนล่าสุดสำหรับแสดงผล
        case 'get_last_scan':
            $data = $attendanceModel->getLastScan($timeSettings);
            echo json_encode($data);
            break;

        // Action: ประมวลผลการสแกนบัตร
        case 'scan':
            $rfid = $_POST['rfid'] ?? '';
            $device_id = intval($_POST['device_id'] ?? 1);

            if (empty($rfid)) {
                http_response_code(400);
                echo json_encode(['error' => 'ไม่พบรหัส RFID']);
                exit;
            }

            // (ดึง $term, $year จาก UserLogin class)
            $user = new UserLogin($db);
            $term = $user->getTerm();
            $year = $user->getPee();
            
            $result = $attendanceModel->processRfidScan($rfid, $device_id, $timeSettings, $term, $year);
            echo json_encode($result);
            break;

        default:
            http_response_code(404);
            echo json_encode(['error' => 'Invalid action']);
            break;
    }

} catch (\Exception $e) {
    // 3. Error Handling
    $code = $e->getCode() == 404 ? 404 : 500;
    http_response_code($code);
    echo json_encode(['error' => $e->getMessage()]);
}
?>