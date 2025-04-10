<?php
require_once '../config/Database.php';
require_once '../config/Setting.php';
require_once '../class/Student.php';
require_once '../class/Utils.php';

$rfid = $_GET['id'];

if (!empty($rfid)) {
    $database = new Database("phichaia_student");
    $db = $database->getConnection();
    $student = new Student($db);
    $student_info = $student->getStudentInfoByRfid($rfid);

    if ($student_info) {
        // Add concatenated full name field
        $student_info['full_name'] = $student_info['Stu_pre'] . $student_info['Stu_name'] . ' ' . $student_info['Stu_sur'];
        echo json_encode($student_info);
    } else {
        echo json_encode(["message" => "Student not found."]);
    }
} else {
    echo json_encode(["message" => "RFID is required."]);
}
echo json_encode(["message" => "Invalid request."]);
?>