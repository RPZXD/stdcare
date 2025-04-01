<?php
require_once "../../config/Database.php";
require_once "../../class/Teacher.php";
require_once "../../class/Student.php";

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

$type = $_POST['type'] ?? '';
$search = $_POST['search'] ?? '';

$data = [];

if ($type === 'teacher') {
    $query = "SELECT * FROM teacher WHERE Teach_name LIKE :search OR Teach_id LIKE :search LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif ($type === 'student') {
    // ปรับคำสั่ง SQL ให้ค้นหาชื่อเต็ม
    $query = "SELECT * FROM student 
              WHERE CONCAT(Stu_name, ' ', Stu_sur) LIKE :search 
              OR Stu_name LIKE :search 
              OR Stu_sur LIKE :search 
              OR Stu_id LIKE :search 
              LIMIT 10";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':search', '%' . $search . '%');
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode($data);
?>