<?php
header('Content-Type: application/json');
include_once("../../config/Database.php");
include_once("../../class/Student.php");

define('API_TOKEN_KEY', 'YOUR_SECURE_TOKEN_HERE');
$token = $_REQUEST['token'] ?? '';
if ($token !== API_TOKEN_KEY) {
    echo json_encode(['error' => 'Invalid token']);
    exit;
}

$stu_id = $_GET['stu_id'] ?? '';
if (!$stu_id) {
    echo json_encode(['error' => 'Missing stu_id']);
    exit;
}

$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();
$student = new Student($db);

$stmt = $db->prepare("SELECT Stu_id, Stu_no, Stu_pre, Stu_name, Stu_sur, Stu_major, Stu_room, Stu_picture FROM student WHERE Stu_id = :stu_id AND Stu_status = 1 LIMIT 1");
$stmt->bindParam(':stu_id', $stu_id);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    echo json_encode($data);
} else {
    echo json_encode([]);
}
