<?php
header('Content-Type: application/json');
session_start();
if (!isset($_SESSION['Student_login'])) {
    echo json_encode(['success' => false, 'message' => 'กรุณาเข้าสู่ระบบ']);
    exit;
}

require_once("../../config/Database.php");
require_once("../../class/Student.php");

$db = new Database("phichaia_student");
$conn = $db->getConnection();
$student = new Student($conn);

$Stu_id = $_SESSION['Student_login'];

// รับค่าจากฟอร์ม
$fields = [
    'Stu_sex','Stu_pre','Stu_name','Stu_sur','Stu_major','Stu_room','Stu_nick','Stu_birth','Stu_religion','Stu_blood',
    'Stu_addr','Stu_phone','Father_name','Father_occu','Father_income','Mother_name','Mother_occu','Mother_income',
    'Par_name','Par_relate','Par_occu','Par_income','Par_addr','Par_phone'
];
$data = [];
foreach ($fields as $f) {
    $data[$f] = $_POST[$f] ?? null;
}

$result = $student->updateStudentInfoByStudent($Stu_id, $data);

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
}
