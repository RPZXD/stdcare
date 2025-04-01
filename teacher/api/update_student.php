<?php
require_once "../../config/Database.php";
require_once "../../class/Student.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize Student class
$student = new Student($db);

// Set student properties from POST data
$student->Stu_id = $_POST['Stu_id'];
$student->Stu_pre = $_POST['Stu_pre'];
$student->Stu_name = $_POST['Stu_name'];
$student->Stu_sur = $_POST['Stu_sur'];
$student->Stu_no = $_POST['Stu_no'];
$student->Stu_password = $_POST['Stu_password'];
$student->Stu_sex = $_POST['Stu_sex'];
$student->Stu_major = $_POST['Stu_major'];
$student->Stu_room = $_POST['Stu_room'];
$student->Stu_nick = $_POST['Stu_nick'];
$student->Stu_birth = $_POST['Stu_birth'];
$student->Stu_religion = $_POST['Stu_religion'];
$student->Stu_blood = $_POST['Stu_blood'];
$student->Stu_addr = $_POST['Stu_addr'];
$student->Stu_phone = $_POST['Stu_phone'];
$student->Par_phone = $_POST['Par_phone'];
$student->Stu_citizenid = $_POST['Stu_citizenid'];
$student->Father_name = $_POST['Father_name'];
$student->Father_occu = $_POST['Father_occu'];
$student->Father_income = $_POST['Father_income'];
$student->Mother_name = $_POST['Mother_name'];
$student->Mother_occu = $_POST['Mother_occu'];
$student->Mother_income = $_POST['Mother_income'];
$student->Par_name = $_POST['Par_name'];
$student->Par_relate = $_POST['Par_relate'];
$student->Par_occu = $_POST['Par_occu'];
$student->Par_income = $_POST['Par_income'];
$student->Par_addr = $_POST['Par_addr'];
$student->Stu_status = $_POST['Stu_status'];
$student->OldStu_id = $_POST['Stu_id']; // Assuming OldStu_id is the same as Stu_id for update

// Update student information
if ($student->updateStudentInfo()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false]);
}
?>
