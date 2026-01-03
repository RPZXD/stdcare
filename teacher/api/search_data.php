<?php
/**
 * Search Data API - Smart Search
 * Support searching by name, id, nickname, phone, parent name, class/room
 */
header('Content-Type: application/json');
require_once "../../config/Database.php";
require_once "../../class/Teacher.php";
require_once "../../class/Student.php";

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$type = $_POST['type'] ?? '';
$search = trim($_POST['search'] ?? '');

if (empty($search)) {
    echo json_encode([]);
    exit;
}

$data = [];

if ($type === 'teacher') {
    // ค้นครู: ชื่อ, รหัส, เบอร์โทร, กลุ่มสาระ
    $query = "SELECT * FROM teacher 
              WHERE ( Teach_name LIKE :search 
              OR Teach_id LIKE :search 
              OR Teach_phone LIKE :search 
              OR Teach_major LIKE :search )
              AND Teach_status = 1 
              LIMIT 20";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

} elseif ($type === 'student') {
    // ค้นนักเรียน: ชื่อ, นามสกุล, ชื่อเล่น, เบอร์นักเรียน, เบอร์พ่อแม่, ชื่อพ่อแม่, ชั้น/ห้อง
    
    // จัดการกรณีพิมพ์ "ม.3/5"
    if (preg_match('/ม\.?\s*(\d+)\s*\/\s*(\d+)/u', $search, $matches)) {
        $major = $matches[1];
        $room = $matches[2];
        $query = "SELECT * FROM student WHERE Stu_major = :major AND Stu_room = :room AND Stu_status = 1 ORDER BY Stu_no ASC";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':major', $major);
        $stmt->bindValue(':room', $room);
    } else {
        $query = "SELECT * FROM student 
                  WHERE ( CONCAT(Stu_name, ' ', Stu_sur) LIKE :search 
                  OR Stu_name LIKE :search 
                  OR Stu_sur LIKE :search 
                  OR Stu_id LIKE :search 
                  OR Stu_nick LIKE :search 
                  OR Stu_phone LIKE :search 
                  OR Stu_addr LIKE :search
                  OR Par_name LIKE :search 
                  OR Par_phone LIKE :search 
                  OR Father_name LIKE :search 
                  OR Mother_name LIKE :search )
                  AND Stu_status = 1
                  LIMIT 20";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':search', '%' . $search . '%');
    }
    
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($data);
?>