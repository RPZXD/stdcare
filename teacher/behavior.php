<?php 
/**
 * Behavior Controller - MVC Entry Point
 * Handles session, data preparation, and includes view
 */

session_start();
date_default_timezone_set('Asia/Bangkok');

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize classes
$user = new UserLogin($db);
$teacher = new Teacher($db);

// Fetch terms and pee
$term = $user->getTerm();
$pee = $user->getPee();

// Check login
if (isset($_SESSION['Teacher_login'])) {
    $userid = $_SESSION['Teacher_login'];
    $userData = $user->userData($userid);
} else {
    $sw2 = new SweetAlert2(
        'คุณยังไม่ได้เข้าสู่ระบบ',
        'error',
        '../login.php'
    );
    $sw2->renderAlert();
    exit;
}

// Prepare data for view
$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];
$currentDate = date("Y-m-d");

// Page title
$pageTitle = "บันทึกพฤติกรรม";

// Behavior types for dropdown
$behaviorTypes = [
    "หนีเรียนหรือออกนอกสถานศึกษา" => 10,
    "เล่นการพนัน" => 20,
    "มาโรงเรียนสาย" => 5,
    "แต่งกาย/ทรงผมผิดระเบียบ" => 5,
    "พกพาอาวุธหรือวัตถุระเบิด" => 20,
    "เสพสุรา/เครื่องดื่มที่มีแอลกอฮอล์" => 20,
    "สูบบุหรี่" => 30,
    "เสพยาเสพติด" => 30,
    "ลักทรัพย์ กรรโชกทรัพย์" => 30,
    "ก่อเหตุทะเลาะวิวาท" => 20,
    "แสดงพฤติกรรมทางชู้สาว" => 20,
    "จอดรถในที่ห้ามจอด" => 10,
    "แสดงพฤติกรรมก้าวร้าว" => 10,
    "มีพฤติกรรมที่ไม่พึงประสงค์อื่นๆ" => 5
];

// Include the layout with view
include '../views/teacher/behavior.php';
?>
