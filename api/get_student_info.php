<?php
require_once '../config/Database.php';
require_once '../config/Setting.php';
require_once '../class/Student.php';
require_once '../class/Utils.php';

$rfid = $_GET['id'];

if (!empty($rfid)) {
    $database = new Database_User();
    $db = $database->getConnection();
    $student = new Student($db);
    $student_info = $student->getStudentInfoByRfid($rfid);

    if ($student_info) {
        echo json_encode($student_info);
    } else {
        echo json_encode(["message" => "Student not found."]);
    }
} else {
    echo json_encode(["message" => "RFID is required."]);
}
?>