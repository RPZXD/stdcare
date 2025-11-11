<?php
// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');

// เรียกใช้คลาสเชื่อมต่อฐานข้อมูลตัวใหม่
require_once __DIR__ . '/../../classes/DatabaseUsers.php';
use App\DatabaseUsers;

try {
    // เชื่อมต่อฐานข้อมูล
    $db = new DatabaseUsers();
    $pdo = $db->getPDO();

    // ดึง action types ที่ไม่ซ้ำกัน
    $actionStmt = $pdo->query("SELECT DISTINCT action_type FROM app_logs WHERE action_type IS NOT NULL AND action_type != '' ORDER BY action_type");
    $actions = $actionStmt->fetchAll(PDO::FETCH_COLUMN);

    // ดึง status codes ที่ไม่ซ้ำกัน
    $statusStmt = $pdo->query("SELECT DISTINCT status_code FROM app_logs WHERE status_code IS NOT NULL ORDER BY status_code");
    $statuses = $statusStmt->fetchAll(PDO::FETCH_COLUMN);

    // จัดรูปแบบข้อมูล
    $actionOptions = [];
    foreach ($actions as $action) {
        $actionOptions[] = [
            'value' => $action,
            'label' => ucwords(str_replace('_', ' ', $action))
        ];
    }

    $statusOptions = [];
    foreach ($statuses as $status) {
        $statusOptions[] = [
            'value' => $status,
            'label' => $status == 200 ? 'Success' : 'Fail (' . $status . ')'
        ];
    }

    echo json_encode([
        'success' => true,
        'actions' => $actionOptions,
        'statuses' => $statusOptions
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>