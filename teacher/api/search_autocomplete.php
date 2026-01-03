<?php
require_once "../../config/Database.php";
require_once "../../class/Teacher.php";
require_once "../../class/Student.php";

// เชื่อมต่อฐานข้อมูล
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$type = $_GET['type'] ?? ''; // รับค่า type (teacher หรือ student)
$term = $_GET['term'] ?? ''; // รับคำค้นหา

if (empty($type) || empty($term)) {
    echo json_encode([]);
    exit;
}

$data = [];
if ($type === 'teacher') {
    // ค้นหาข้อมูลในตารางครู - เพิ่ม Teach_major
    $query = "SELECT Teach_id, Teach_name, Teach_major FROM teacher WHERE (Teach_name LIKE :term OR Teach_id LIKE :term) AND Teach_status = 1 LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':term', '%' . $term . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $major = !empty($row['Teach_major']) ? " ({$row['Teach_major']})" : "";
        $data[] = [
            'label' => $row['Teach_name'] . $major, // ชื่อครู (กลุ่มสาระ)
            'value' => $row['Teach_name'] // ใช้ชื่อเป็น value สำหรับค้นหา
        ];
    }
} elseif ($type === 'student') {
    // ค้นหาข้อมูลในตารางนักเรียน - เพิ่ม Stu_major, Stu_room
    $query = "SELECT Stu_id, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room 
              FROM student 
              WHERE (Stu_name LIKE :term OR Stu_sur LIKE :term OR Stu_id LIKE :term) 
              AND Stu_status = 1 
              LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':term', '%' . $term . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $fullName = $row['Stu_pre'] . $row['Stu_name'] . ' ' . $row['Stu_sur'];
        $classRoom = " (ม.{$row['Stu_major']}/{$row['Stu_room']})";
        $data[] = [
            'label' => $fullName . $classRoom, // ชื่อ (ม.ชั้น/ห้อง)
            'value' => $row['Stu_name'] . ' ' . $row['Stu_sur'] // ใช้ชื่อเป็น value สำหรับค้นหา
        ];
    }
}

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode($data);
?>