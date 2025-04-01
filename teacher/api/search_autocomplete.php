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
    // ค้นหาข้อมูลในตารางครู
    $query = "SELECT Teach_id, Teach_name FROM teacher WHERE Teach_name LIKE :term LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':term', '%' . $term . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $data[] = [
            'label' => $row['Teach_name'], // ชื่อครู
            'value' => $row['Teach_id'] // รหัสครู
        ];
    }
} elseif ($type === 'student') {
    // ค้นหาข้อมูลในตารางนักเรียน
    $query = "SELECT Stu_id, CONCAT(Stu_name, ' ', Stu_sur) AS full_name FROM student WHERE Stu_name LIKE :term OR Stu_sur LIKE :term LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':term', '%' . $term . '%');
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($results as $row) {
        $data[] = [
            'label' => $row['full_name'], // ชื่อนักเรียน
            'value' => $row['Stu_id'] // รหัสนักเรียน
        ];
    }
}

// ส่งข้อมูลกลับในรูปแบบ JSON
echo json_encode($data);
?>