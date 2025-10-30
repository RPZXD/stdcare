<?php
require_once __DIR__ . '/../models/LogModel.php';

class DatabaseLogger {
    private $logModel;

    /**
     * Constructor: รับการเชื่อมต่อ PDO และสร้าง instance ของ LogModel
     * @param PDO $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db) {
        $this->logModel = new LogModel($db);
    }

    /**
     * เมธอดหลักสำหรับบันทึก log
     * @param array $logData ข้อมูล log (โครงสร้างเดียวกับที่คุณใช้อยู่)
     */
    public function log($logData) {
        date_default_timezone_set('Asia/Bangkok');
        // ตรวจสอบและตั้งค่า default เพื่อป้องกัน key ไม่ครบ
        $dataToLog = [
            'user_id' => $logData['user_id'] ?? null,
            'role' => $logData['role'] ?? null,
            'ip_address' => $logData['ip_address'] ?? $_SERVER['REMOTE_ADDR'],
            'user_agent' => $logData['user_agent'] ?? $_SERVER['HTTP_USER_AGENT'],
            'access_time' => $logData['access_time'] ?? date("c"),
            'url' => $logData['url'] ?? $_SERVER['REQUEST_URI'],
            'method' => $logData['method'] ?? $_SERVER['REQUEST_METHOD'],
            'status_code' => $logData['status_code'] ?? 500, // 500 = Internal Error
            'referrer' => $logData['referrer'] ?? $_SERVER['HTTP_REFERER'] ?? null,
            'action_type' => $logData['action_type'] ?? 'general_log',
            'session_id' => $logData['session_id'] ?? session_id(),
            'message' => $logData['message'] ?? 'No message provided.'
        ];
        
        // ส่งข้อมูลให้ Model บันทึกลงฐานข้อมูล
        $this->logModel->createLog($dataToLog);
    }
}
?>