<?php
/**
 * Screen 11 Controller
 * MVC Pattern - Entry point for student 11-aspect screening page
 */

session_start();

require_once "../config/Database.php";
require_once "../class/UserLogin.php";
require_once "../class/Teacher.php";
require_once "../class/Utils.php";

// Initialize database connection
$connectDB = new Database("phichaia_student");
$db = $connectDB->getConnection();

// Initialize UserLogin class
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

// Store teacher data in session for layout
$_SESSION['teacher_data'] = $userData;

// Extract teacher information
$teacher_id = $userData['Teach_id'];
$teacher_name = $userData['Teach_name'];
$class = $userData['Teach_class'];
$room = $userData['Teach_room'];

// Get current date in Thai format
$currentDate = Utils::convertToThaiDatePlusNum(date("Y-m-d"));
$currentDate2 = Utils::convertToThaiDatePlus(date("Y-m-d"));

// Set page title
$title = 'การคัดกรองนักเรียนรายบุคคล 11 ด้าน';

// Load the view
include __DIR__ . '/../views/teacher/screen11.php';
?>