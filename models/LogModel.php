<?php
class LogModel {
    private $conn;
    private $tableName = "app_logs"; // ชื่อตารางที่เราเพิ่งสร้าง

    /**
     * Constructor เพื่อรับการเชื่อมต่อฐานข้อมูล
     * @param PDO $db การเชื่อมต่อ PDO
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * สร้าง log ใหม่ในฐานข้อมูล
     * @param array $logData ข้อมูล log แบบ Associative array
     * @return bool True ถ้าสำเร็จ, False ถ้าล้มเหลว
     */
    public function createLog($logData) {
        $sql = "INSERT INTO " . $this->tableName . " (
                    user_id, role, ip_address, user_agent, access_time, 
                    url, method, status_code, referrer, 
                    action_type, session_id, message
                ) VALUES (
                    :user_id, :role, :ip_address, :user_agent, :access_time, 
                    :url, :method, :status_code, :referrer, 
                    :action_type, :session_id, :message
                )";

        $stmt = $this->conn->prepare($sql);

        // แปลงเวลา ISO 8601 (จาก date("c")) เป็นรูปแบบ MySQL DATETIME
        try {
            $accessTime = new DateTime($logData['access_time']);
            $mysqlTime = $accessTime->format('Y-m-d H:i:s');
        } catch (Exception $e) {
            $mysqlTime = date('Y-m-d H:i:s'); // ใช้เวลาปัจจุบันถ้าแปลงล้มเหลว
        }

        // Bind parameters
        $stmt->bindParam(":user_id", $logData['user_id']);
        $stmt->bindParam(":role", $logData['role']);
        $stmt->bindParam(":ip_address", $logData['ip_address']);
        $stmt->bindParam(":user_agent", $logData['user_agent']);
        $stmt->bindParam(":access_time", $mysqlTime);
        $stmt->bindParam(":url", $logData['url']);
        $stmt->bindParam(":method", $logData['method']);
        $stmt->bindParam(":status_code", $logData['status_code'], PDO::PARAM_INT);
        $stmt->bindParam(":referrer", $logData['referrer']);
        $stmt->bindParam(":action_type", $logData['action_type']);
        $stmt->bindParam(":session_id", $logData['session_id']);
        $stmt->bindParam(":message", $logData['message']);

        try {
            if ($stmt->execute()) {
                return true;
            }
        } catch (PDOException $e) {
            // กรณีฐานข้อมูลมีปัญหา (เช่น ปิดการเชื่อมต่อ)
            // คุณสามารถ log ลงไฟล์สำรองฉุกเฉินได้ที่นี่
            error_log("Database Logger Error: " . $e->getMessage());
            return false;
        }
        return false;
    }
}
?>