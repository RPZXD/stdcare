<?php
header('Content-Type: application/json');

// ตัวอย่าง: ดึงห้องเรียนจากฐานข้อมูล student (Stu_room) และรวมกับ Stu_major เพื่อให้ได้ชื่อห้องเรียน เช่น "1/1", "2/3" ฯลฯ
require_once __DIR__ . '/../config/Database.php';

$connectDB = new \Database("phichaia_student");
$db = $connectDB->getConnection();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        // ดึงห้องเรียนทั้งหมดที่มีนักเรียนอยู่จริง
        $sql = "SELECT DISTINCT CONCAT(Stu_major, '/', Stu_room) AS name
                FROM student
                WHERE Stu_status IN (1,2,3,4,9) AND Stu_major IS NOT NULL AND Stu_room IS NOT NULL
                ORDER BY Stu_major, Stu_room";
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($rooms);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
}
