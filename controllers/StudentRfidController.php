<?php
session_start();
header('Content-Type: application/json');

// --- Original Model Includes ---
require_once __DIR__ . '/../models/StudentRfid.php';
// require_once __DIR__ . '/../models/Student.php'; // ไม่ได้ใช้ตัวนี้

use App\Models\StudentRfid;
// use App\Models\Student as NamespacedStudent; 

// --- Working Model Includes (จาก rfid.php) ---
require_once __DIR__ . '/../config/Database.php'; 
// require_once __DIR__ . '/../class/Student.php';  // ไม่ต้อง include ตรงนี้ เพราะ Database จะจัดการ
// (หมายเหตุ: ผมเดาว่า class/Student.php ถูก auto-load หรือถูก include ใน Database.php)
// (แก้ไข: ถ้า class/Student.php ไม่ได้ถูก auto-load ให้เอาคอมเมนต์บรรทัดล่างออก)
require_once __DIR__ . '/../class/Student.php';


$rfidModel = new StudentRfid();
$action = $_GET['action'] ?? $_POST['action'] ?? 'list';

function requireOfficer() {
    if (!isset($_SESSION['Officer_login'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }
}

// --- [ใหม่] ฟังก์ชันสำหรับเชื่อมต่อ DB (เพื่อใช้ซ้ำ) ---
function getDbConnection() {
    // (ใช้ชื่อ DB ตามที่คุณระบุใน rfid.php)
    $connectDB = new Database("phichaia_student"); 
    return $connectDB->getConnection();
}


try {
    switch ($action) {

        // --- [ใหม่] ACTION สำหรับดึงตัวเลือก Dropdown ---
        case 'getFilterOptions':
            requireOfficer();
            $db = getDbConnection();
            
            $majors_query = $db->query("SELECT DISTINCT Stu_major FROM students WHERE Stu_major IS NOT NULL AND Stu_major != '' ORDER BY Stu_major");
            $majors = $majors_query->fetchAll(PDO::FETCH_COLUMN);
            
            $rooms_query = $db->query("SELECT DISTINCT Stu_room FROM students WHERE Stu_room IS NOT NULL AND Stu_room != '' ORDER BY Stu_room");
            $rooms = $rooms_query->fetchAll(PDO::FETCH_COLUMN);
            
            echo json_encode(['majors' => $majors, 'rooms' => $rooms]);
            break;

        // --- [ใหม่] ACTION สำหรับค้นหานักเรียน (สำคัญ) ---
        case 'searchStudents':
            requireOfficer();
            $db = getDbConnection();
            
            $search = $_GET['search'] ?? '';
            $major = $_GET['major'] ?? '';
            $room = $_GET['room'] ?? '';
            $limit_exceeded = false;

            // ต้องมีอย่างน้อย 1 เงื่อนไข
            if (empty($search) && empty($major) && empty($room)) {
                echo json_encode([]); // คืนค่าว่างถ้าไม่ระบุอะไรเลย
                exit;
            }

            // สร้าง Query
            $sql = "SELECT Stu_id, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_status FROM students WHERE Stu_status = 1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (Stu_id LIKE ? OR Stu_name LIKE ? OR Stu_sur LIKE ?)";
                $like_search = '%' . $search . '%';
                array_push($params, $like_search, $like_search, $like_search);
            }
            
            if (!empty($major)) {
                $sql .= " AND Stu_major = ?";
                $params[] = $major;
            }
            
            if (!empty($room)) {
                $sql .= " AND Stu_room = ?";
                $params[] = $room;
            }

            // --- [สำคัญ] ป้องกันการดึงข้อมูลมากเกินไป ---
            $sql .= " LIMIT 501"; // จำกัดที่ 501 แถว

            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $studentList = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // ถ้าผลลัพธ์เกิน 500 ให้ส่งสัญญาณเตือน
            if (count($studentList) > 500) {
                $limit_exceeded = true;
                array_pop($studentList); // เอาแถวที่ 501 ออก
            }

            // ถ้า JavaScript รองรับ (ในโค้ด JS ที่ให้ไปรองรับแล้ว)
            $response = [
                'data' => $studentList,
                'limit_exceeded' => $limit_exceeded
            ];
            
            // เปลี่ยนไปส่ง $studentList ตรงๆ
            echo json_encode($studentList);
            break;

        // --- [ใหม่] ACTION สำหรับดึงข้อมูลนักเรียน 1 คน ---
        case 'getStudentDetails':
            requireOfficer();
            $stu_id = $_GET['stu_id'] ?? '';
            if (empty($stu_id)) {
                echo json_encode(null);
                exit;
            }
            
            $db = getDbConnection();
            // เราใช้ Student class ที่ include มา
            $studentModel = new Student($db);
            
            // ผมเดาว่า class Student ของคุณมีเมธอด getById
            // (ถ้าไม่มี ให้ใช้ prepare statement แบบด้านล่างแทน)
            $student = $studentModel->getById($stu_id);
            
            /* // --- (สำรอง) ถ้าเมธอด getById ไม่มี หรือไม่ทำงาน ---
            $stmt = $db->prepare("SELECT * FROM students WHERE Stu_id = ?");
            $stmt->execute([$stu_id]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            */
            
            echo json_encode($student);
            break;


        // --- โค้ดเดิมสำหรับจัดการ RFID (ไม่ต้องแก้ไข) ---
        case 'list':
            requireOfficer();
            $list = $rfidModel->getAll();
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
            echo json_encode($row ?: null);
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
    echo json_encode(['error' => 'Internal Server Error', 'message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
}